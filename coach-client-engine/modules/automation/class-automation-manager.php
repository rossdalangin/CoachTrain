<?php
/**
 * Automation Manager class.
 */
class CCE_Automation_Manager extends CCE_REST_Controller {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'cce_lead_created', array( $this, 'trigger_optin_automation' ) );
        add_action( 'cce_booking_confirmed', array( $this, 'trigger_booking_automation' ) );
        add_action( 'cce_delayed_email_event', array( $this, 'send_delayed_email' ), 10, 2 );
    }

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

        // Example: Send welcome email
        // $mailer = new CCE_Mailer();
        // $mailer->send_welcome_email( $lead_id );
	}

	/**
	 * Trigger automation on booking.
	 */
	public function trigger_booking_automation( $booking_id ) {
		// logic for reminders
        error_log( "Automation triggered for booking: $booking_id" );

        // Schedule a 24h reminder
        wp_schedule_single_event( time() + DAY_IN_SECONDS, 'cce_delayed_email_event', array( $booking_id, 'booking_reminder' ) );
	}

    /**
     * Send delayed email.
     */
    public function send_delayed_email( $id, $type ) {
        error_log( "Sending delayed email ($type) for ID: $id" );
        // $mailer = new CCE_Mailer();
        // $mailer->send_reminder( $id );
    }
}
