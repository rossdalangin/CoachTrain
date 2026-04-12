<?php
/**
 * Analytics Manager class.
 */
class CCE_Analytics_Manager extends CCE_REST_Controller {

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/analytics/summary', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_summary' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

	/**
	 * Get analytics summary.
	 */
	public function get_summary( $request ) {
		global $wpdb;

        $today = date('Y-m-d');

		$leads_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_leads WHERE DATE(created_at) = %s", $today ) );
		$bookings_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_bookings WHERE DATE(created_at) = %s", $today ) );
		$revenue = $wpdb->get_var( $wpdb->prepare( "SELECT SUM(amount) FROM {$wpdb->prefix}cce_payments WHERE status = 'completed' AND DATE(created_at) = %s", $today ) );
        $sales_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_payments WHERE status = 'completed' AND DATE(created_at) = %s", $today ) );

		return $this->success( array(
			'leads_today'    => (int) $leads_count,
			'bookings_today' => (int) $bookings_count,
			'revenue_today'  => (float) ($revenue ?? 0),
            'sales_today'    => (int) $sales_count,
		) );
	}
}
