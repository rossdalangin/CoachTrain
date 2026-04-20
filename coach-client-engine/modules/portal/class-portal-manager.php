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

        register_rest_route( $this->namespace, '/portal/resources/track', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'track_resource_access' ),
				'permission_callback' => '__return_true',
			),
		) );
	}

    /**
     * Create resource.
     */
    public function create_resource( $request ) {
        global $wpdb;
        $user_id = $this->get_current_user_id();
        $params = $this->get_params( $request );

        $result = $wpdb->insert( "{$wpdb->prefix}cce_resources", array(
            'user_id'    => $user_id,
            'title'      => sanitize_text_field( $params['title'] ?? '' ),
            'category'   => sanitize_text_field( $params['category'] ?? 'Uncategorized' ),
            'type'       => sanitize_text_field( $params['type'] ?? '' ),
            'url'        => esc_url_raw( $params['url'] ?? '' ),
            'visibility' => sanitize_text_field( $params['visibility'] ?? 'public' ),
            'created_at' => current_time( 'mysql' ),
        ) );

        if ( false === $result ) return $this->error( 'Failed to create resource' );

        return $this->success( array( 'id' => $wpdb->insert_id ) );
    }

    /**
     * Delete resource.
     */
    public function delete_resource( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $wpdb->delete( "{$wpdb->prefix}cce_resources", array( 'id' => $id, 'user_id' => $user_id ) );
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
     * Track resource access.
     */
    public function track_resource_access( $request ) {
        global $wpdb;
        $resource_id = absint( $request->get_param( 'resource_id' ) );
        $token = $_COOKIE['cce_lead_token'] ?? '';

        $lead = $wpdb->get_row( $wpdb->prepare( "SELECT id, user_id FROM {$wpdb->prefix}cce_leads WHERE secure_token = %s", $token ) );
        if ( ! $lead ) return $this->error( 'Lead not authenticated' );

        $wpdb->insert( "{$wpdb->prefix}cce_resource_access", array(
            'user_id'     => $lead->user_id,
            'lead_id'     => $lead->id,
            'resource_id' => $resource_id,
            'accessed_at' => current_time( 'mysql' ),
        ) );

        return $this->success();
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
        $owner_id = 0;

        if ( ! empty( $token ) ) {
            $lead = $wpdb->get_row( $wpdb->prepare( "SELECT id, user_id FROM {$wpdb->prefix}cce_leads WHERE secure_token = %s", $token ) );
            if ( $lead ) {
                $owner_id = $lead->user_id;
                $is_client = (bool) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_payments WHERE lead_id = %d AND status = 'completed'", $lead->id ) );
            }
        }

        // If no token, maybe it's an admin request
        if ( ! $owner_id && current_user_can( 'manage_options' ) ) {
            $owner_id = get_current_user_id();
            $is_client = true; // Admins see everything
        }

        $query = "SELECT * FROM {$wpdb->prefix}cce_resources WHERE user_id = %d";
        $params = array( $owner_id );

        if ( ! $is_client ) {
            $query .= " AND visibility = 'public'";
        }
        $resources = $wpdb->get_results( $wpdb->prepare( $query, $params ) );

		return $this->success( $resources );
	}
}
