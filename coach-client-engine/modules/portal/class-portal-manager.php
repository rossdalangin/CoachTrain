<?php
/**
 * Client Portal Manager class.
 */
class CCE_Portal_Manager extends CCE_REST_Controller {

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/portal/resources', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_resources' ),
				'permission_callback' => '__return_true', // Filtered by logic
			),
            array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_resource' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/portal/resources/(?P<id>\d+)', array(
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_resource' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/portal/progress', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_progress' ),
				'permission_callback' => '__return_true',
			),
		) );

        register_rest_route( $this->namespace, '/portal/onboarding/complete', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'complete_onboarding_step' ),
				'permission_callback' => '__return_true',
			),
		) );
	}

    /**
     * Create resource.
     */
    public function create_resource( $request ) {
        global $wpdb;
        $params = $request->get_params();

        $wpdb->insert( "{$wpdb->prefix}cce_resources", array(
            'title'      => sanitize_text_field( $params['title'] ),
            'type'       => sanitize_text_field( $params['type'] ),
            'url'        => esc_url_raw( $params['url'] ),
            'visibility' => sanitize_text_field( $params['visibility'] ?? 'public' ),
        ) );

        return $this->success( array( 'id' => $wpdb->insert_id ) );
    }

    /**
     * Delete resource.
     */
    public function delete_resource( $request ) {
        global $wpdb;
        $wpdb->delete( "{$wpdb->prefix}cce_resources", array( 'id' => absint( $request['id'] ) ) );
        return $this->success( array( 'message' => 'Resource deleted' ) );
    }

    /**
     * Complete onboarding step.
     */
    public function complete_onboarding_step( $request ) {
        global $wpdb;
        $token = $_COOKIE['cce_lead_token'] ?? '';
        $lead = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_leads WHERE secure_token = %s", $token ) );

        if ( ! $lead ) {
            return $this->error( 'Unauthorized', 'unauthorized', 401 );
        }

        $step_name = sanitize_text_field( $request->get_param( 'step_name' ) );

        $progress = json_decode( $lead->onboarding_progress ?: '[]', true );
        if ( ! in_array( $step_name, $progress ) ) {
            $progress[] = $step_name;
        }

        $wpdb->update(
            "{$wpdb->prefix}cce_leads",
            array( 'onboarding_progress' => json_encode( $progress ) ),
            array( 'id' => $lead->id )
        );

        CCE_Activity_Logger::log( $lead->id, 'milestone', "Onboarding step completed: $step_name" );

        return $this->success( array( 'message' => 'Step marked as complete' ) );
    }

    /**
     * Get lead progress notes.
     */
    public function get_progress( $request ) {
        global $wpdb;
        $token = $_COOKIE['cce_lead_token'] ?? '';
        $lead = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_leads WHERE secure_token = %s", $token ) );

        if ( ! $lead ) {
            return $this->error( 'Unauthorized', 'unauthorized', 401 );
        }

        $notes = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cce_activity_log WHERE lead_id = %d AND (activity_type = 'note' OR activity_type = 'milestone') ORDER BY created_at DESC",
            $lead->id
        ) );

        return $this->success( array( 'notes' => $notes, 'completed_steps' => json_decode( $lead->onboarding_progress ?: '[]' ) ) );
    }

	/**
	 * Get coaching resources.
	 */
	public function get_resources( $request ) {
		global $wpdb;
        $token = $_COOKIE['cce_lead_token'] ?? '';
        $is_client = false;

        if ( ! empty( $token ) ) {
            $lead_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cce_leads WHERE secure_token = %s", $token ) );
            if ( $lead_id ) {
                $is_client = (bool) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_payments WHERE lead_id = %d AND status = 'completed'", $lead_id ) );
            }
        }

        $query = "SELECT * FROM {$wpdb->prefix}cce_resources";
        if ( ! $is_client ) {
            $query .= " WHERE visibility = 'public'";
        }
        $resources = $wpdb->get_results( $query );

		return $this->success( $resources );
	}
}
