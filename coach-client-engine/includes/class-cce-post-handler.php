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
        add_action( 'admin_post_cce_generate_sample_data', array( $this, 'generate_sample_data' ) );
	}

    /**
     * Generate sample data.
     */
    public function generate_sample_data() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'cce_generate_sample_data_nonce' );

        if ( class_exists( 'CCE_Sample_Data' ) ) {
            CCE_Sample_Data::generate();
            wp_redirect( admin_url( 'admin.php?page=cce-settings&message=sample_data_success#status' ) );
            exit;
        }

        wp_redirect( admin_url( 'admin.php?page=cce-settings&message=sample_data_error#status' ) );
        exit;
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
            'user_id'              => get_current_user_id(),
			'title'                => sanitize_text_field( $_POST['title'] ),
			'price'                => (float) $_POST['price'],
			'type'                 => sanitize_text_field( $_POST['type'] ),
			'dream_outcome'        => sanitize_textarea_field( $_POST['dream_outcome'] ?? '' ),
			'perceived_likelihood' => sanitize_textarea_field( $_POST['perceived_likelihood'] ?? '' ),
			'time_delay'           => sanitize_textarea_field( $_POST['time_delay'] ?? '' ),
			'effort_sacrifice'     => sanitize_textarea_field( $_POST['effort_sacrifice'] ?? '' ),
            'upsell_offer_id'      => absint( $_POST['upsell_offer_id'] ?? 0 ),
            'downsell_offer_id'    => absint( $_POST['downsell_offer_id'] ?? 0 ),
            'order_bump_offer_id'  => absint( $_POST['order_bump_offer_id'] ?? 0 ),
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
        $user_id = get_current_user_id();
        $default_stage_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cce_crm_stages WHERE user_id = %d ORDER BY stage_order ASC LIMIT 1", $user_id ) ) ?: 1;

        $wpdb->insert( "{$wpdb->prefix}cce_leads", array(
            'user_id'      => $user_id,
            'first_name'   => sanitize_text_field( $_POST['first_name'] ),
            'last_name'    => sanitize_text_field( $_POST['last_name'] ),
            'email'        => sanitize_email( $_POST['email'] ),
            'status'       => 'cold',
            'crm_stage_id' => $default_stage_id,
            'secure_token' => bin2hex( random_bytes( 32 ) ),
            'created_at'   => current_time( 'mysql' ),
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
            'user_id'    => get_current_user_id(),
            'lead_id'    => absint( $_POST['lead_id'] ),
            'start_time' => sanitize_text_field( $_POST['start_time'] ),
            'timezone'   => sanitize_text_field( $_POST['timezone'] ),
            'status'     => 'confirmed',
            'created_at' => current_time( 'mysql' ),
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
        $user_id = get_current_user_id();
        $this->export_csv( $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_leads WHERE user_id = %d", $user_id ), ARRAY_A ), 'leads' );
    }

    /**
     * Export bookings to CSV.
     */
    public function export_bookings() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'cce_export_bookings_nonce' );
        global $wpdb;
        $user_id = get_current_user_id();
        $this->export_csv( $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_bookings WHERE user_id = %d", $user_id ), ARRAY_A ), 'bookings' );
    }

    /**
     * Export payments to CSV.
     */
    public function export_payments() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'cce_export_payments_nonce' );
        global $wpdb;
        $user_id = get_current_user_id();
        $this->export_csv( $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_payments WHERE user_id = %d", $user_id ), ARRAY_A ), 'payments' );
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
