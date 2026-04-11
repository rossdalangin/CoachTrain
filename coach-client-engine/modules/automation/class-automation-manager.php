<?php
/**
 * Automation Manager class.
 */
class CCE_Automation_Manager extends CCE_REST_Controller {

    /**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/automation/rules', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_rules' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

    /**
     * Get automation rules.
     */
    public function get_rules( $request ) {
        $rules = array(
            array( 'id' => 1, 'trigger' => 'on_optin', 'action' => 'send_email', 'delay' => 0 ),
            array( 'id' => 2, 'trigger' => 'on_booking', 'action' => 'add_tag', 'tag' => 'Booked' ),
        );
        return $this->success( $rules );
    }

	/**
	 * Trigger automation on opt-in.
	 */
	public function trigger_optin_automation( $lead_id ) {
		// logic for email delivery and tagging
        error_log( "Automation triggered for lead: $lead_id" );
	}

	/**
	 * Trigger automation on booking.
	 */
	public function trigger_booking_automation( $booking_id ) {
		// logic for reminders
        error_log( "Automation triggered for booking: $booking_id" );
	}
}
