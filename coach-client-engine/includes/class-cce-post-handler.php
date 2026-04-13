<?php
/**
 * Processes form submissions for the admin area.
 */
class CCE_Post_Handler {

	public function init() {
		add_action( 'admin_post_cce_save_offer', array( $this, 'save_offer' ) );
        add_action( 'admin_post_cce_save_lead', array( $this, 'save_lead' ) );
        add_action( 'admin_post_cce_save_booking', array( $this, 'save_booking' ) );
        add_action( 'admin_post_cce_export_leads', array( $this, 'export_leads' ) );
        add_action( 'admin_post_cce_export_bookings', array( $this, 'export_bookings' ) );
        add_action( 'admin_post_cce_export_payments', array( $this, 'export_payments' ) );
	}

	/**
	 * Save coaching offer.
	 */
	public function save_offer() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Unauthorized' );
		}

		check_admin_referer( 'cce_save_offer_nonce' );

		global $wpdb;
		$data = array(
			'title'                => sanitize_text_field( $_POST['title'] ),
			'price'                => (float) $_POST['price'],
			'type'                 => sanitize_text_field( $_POST['type'] ),
			'dream_outcome'        => sanitize_textarea_field( $_POST['dream_outcome'] ?? '' ),
			'perceived_likelihood' => sanitize_textarea_field( $_POST['perceived_likelihood'] ?? '' ),
			'time_delay'           => sanitize_textarea_field( $_POST['time_delay'] ?? '' ),
			'effort_sacrifice'     => sanitize_textarea_field( $_POST['effort_sacrifice'] ?? '' ),
		);

		$wpdb->insert( "{$wpdb->prefix}cce_offers", $data );

		wp_redirect( admin_url( 'admin.php?page=cce-clients&message=1' ) );
		exit;
	}

    /**
     * Save lead.
     */
    public function save_lead() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'cce_save_lead_nonce' );

        global $wpdb;
        $wpdb->insert( "{$wpdb->prefix}cce_leads", array(
            'first_name'   => sanitize_text_field( $_POST['first_name'] ),
            'last_name'    => sanitize_text_field( $_POST['last_name'] ),
            'email'        => sanitize_email( $_POST['email'] ),
            'status'       => 'cold',
            'crm_stage_id' => 1,
            'secure_token' => bin2hex( random_bytes( 32 ) ),
        ) );

        $lead_id = $wpdb->insert_id;
        do_action( 'cce_lead_created', $lead_id );

        wp_redirect( admin_url( 'admin.php?page=cce-leads&message=1' ) );
        exit;
    }

    /**
     * Save booking.
     */
    public function save_booking() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'cce_save_booking_nonce' );

        global $wpdb;
        $wpdb->insert( "{$wpdb->prefix}cce_bookings", array(
            'lead_id'    => absint( $_POST['lead_id'] ),
            'start_time' => sanitize_text_field( $_POST['start_time'] ),
            'timezone'   => sanitize_text_field( $_POST['timezone'] ),
            'status'     => 'confirmed',
        ) );

        $booking_id = $wpdb->insert_id;
        do_action( 'cce_booking_confirmed', $booking_id );

        wp_redirect( admin_url( 'admin.php?page=cce-bookings&message=1' ) );
        exit;
    }

    /**
     * Export leads to CSV.
     */
    public function export_leads() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'cce_export_leads_nonce' );
        global $wpdb;
        $this->export_csv( $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_leads", ARRAY_A ), 'leads' );
    }

    /**
     * Export bookings to CSV.
     */
    public function export_bookings() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'cce_export_bookings_nonce' );
        global $wpdb;
        $this->export_csv( $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_bookings", ARRAY_A ), 'bookings' );
    }

    /**
     * Export payments to CSV.
     */
    public function export_payments() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'cce_export_payments_nonce' );
        global $wpdb;
        $this->export_csv( $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_payments", ARRAY_A ), 'payments' );
    }

    /**
     * Generic CSV exporter.
     */
    private function export_csv( $data, $name ) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $name . '-' . date('Y-m-d') . '.csv"');
        $output = fopen('php://output', 'w');
        if ( ! empty( $data ) ) {
            fputcsv($output, array_keys($data[0]));
            foreach ($data as $row) fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }
}
