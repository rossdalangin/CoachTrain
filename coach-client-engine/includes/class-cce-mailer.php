<?php
/**
 * Mailer utility.
 */
class CCE_Mailer {

	/**
	 * Send an email.
	 */
	public function send( $to, $subject, $message ) {
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		return wp_mail( $to, $subject, $message, $headers );
	}

	/**
	 * Send welcome email to a new lead.
	 */
	public function send_welcome_email( $lead_id ) {
		global $wpdb;
		$lead = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_leads WHERE id = %d", $lead_id ) );

		if ( ! $lead ) {
			return false;
		}

		$subject = "Welcome! Here is your coaching guide.";
		$message = "<h1>Hi " . esc_html( $lead->first_name ) . ",</h1><p>Thanks for joining. Here is your promised resource.</p>";

		return $this->send( $lead->email, $subject, $message );
	}
}
