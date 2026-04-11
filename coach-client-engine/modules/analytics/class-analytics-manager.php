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

		$leads_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_leads" );
		$bookings_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_bookings" );
		$revenue = $wpdb->get_var( "SELECT SUM(amount) FROM {$wpdb->prefix}cce_payments WHERE status = 'completed'" );

		return $this->success( array(
			'leads'    => (int) $leads_count,
			'bookings' => (int) $bookings_count,
			'revenue'  => (float) ($revenue ?? 0),
		) );
	}
}
