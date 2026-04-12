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
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/portal/progress', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_progress' ),
				'permission_callback' => '__return_true', // Token-based
			),
		) );

        register_rest_route( $this->namespace, '/portal/onboarding/complete', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'complete_onboarding_step' ),
				'permission_callback' => '__return_true', // Token-based
			),
		) );
	}

    /**
     * Complete onboarding step.
     */
    public function complete_onboarding_step( $request ) {
        global $wpdb;
        $token = $_COOKIE['cce_lead_token'] ?? '';
        $lead = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cce_leads WHERE secure_token = %s", $token ) );

        if ( ! $lead ) {
            return $this->error( 'Unauthorized', 'unauthorized', 401 );
        }

        $step_name = sanitize_text_field( $request->get_param( 'step_name' ) );
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

        return $this->success( array( 'notes' => $notes ) );
    }

	/**
	 * Get coaching resources.
	 */
	public function get_resources( $request ) {
		// Example data structure
		$resources = array(
			array( 'id' => 1, 'title' => 'Client Welcome Pack', 'type' => 'PDF' ),
			array( 'id' => 2, 'title' => 'High-Ticket Sales Script', 'type' => 'PDF' ),
			array( 'id' => 3, 'title' => 'Onboarding Call Recording', 'type' => 'Video' ),
		);
		return $this->success( $resources );
	}
}
