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

	/**
	 * Send email using a template.
	 */
	public function send_template( $template_id, $lead_id ) {
		global $wpdb;
		$template = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_email_templates WHERE id = %d", $template_id ) );
		$lead     = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_leads WHERE id = %d", $lead_id ) );

		if ( ! $template || ! $lead ) {
			return false;
		}

		$content = $template->content;
		$placeholders = array(
			'{{first_name}}' => $lead->first_name,
			'{{last_name}}'  => $lead->last_name,
			'{{email}}'      => $lead->email,
		);

		$content = str_replace( array_keys( $placeholders ), array_values( $placeholders ), $content );

		return $this->send( $lead->email, $template->subject, $content );
	}

    /**
     * Send reminder email for a booking.
     */
    public function send_reminder( $booking_id ) {
        global $wpdb;
        $booking = $wpdb->get_row( $wpdb->prepare( "
            SELECT b.*, l.first_name, l.email
            FROM {$wpdb->prefix}cce_bookings b
            JOIN {$wpdb->prefix}cce_leads l ON b.lead_id = l.id
            WHERE b.id = %d
        ", $booking_id ) );

        if ( ! $booking ) return false;

        $subject = "Reminder: Your coaching session is coming up!";
        $message = "<h1>Hi " . esc_html( $booking->first_name ) . ",</h1><p>This is a reminder for your upcoming strategy session scheduled for " . esc_html( $booking->start_time ) . ".</p>";

        return $this->send( $booking->email, $subject, $message );
    }
}
