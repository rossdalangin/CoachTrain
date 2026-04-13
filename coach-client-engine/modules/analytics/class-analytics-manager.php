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

        register_rest_route( $this->namespace, '/analytics/recent-activity', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_recent_activity' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

    /**
     * Get recent activity.
     */
    public function get_recent_activity( $request ) {
        global $wpdb;
        $activities = $wpdb->get_results( "
            SELECT a.*, CONCAT(l.first_name, ' ', l.last_name) as lead_name
            FROM {$wpdb->prefix}cce_activity_log a
            LEFT JOIN {$wpdb->prefix}cce_leads l ON a.lead_id = l.id
            ORDER BY a.created_at DESC
            LIMIT 10
        " );
        return $this->success( $activities );
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

        $total_leads = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_leads" );
        $total_clients = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_payments WHERE status = 'completed'" );
        $total_bookings = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_bookings" );

		return $this->success( array(
			'leads_today'     => (int) $leads_count,
			'bookings_today'  => (int) $bookings_count,
			'revenue_today'   => (float) ($revenue ?? 0),
            'sales_today'     => (int) $sales_count,
            'total_visitors'  => (int) get_option( 'cce_total_visitors', 0 ),
            'total_leads'     => $total_leads,
            'total_bookings'  => $total_bookings,
            'total_clients'   => $total_clients,
            'lead_to_client'  => $total_leads > 0 ? round( ($total_clients / $total_leads) * 100, 1 ) : 0,
            'funnel_stats'    => $this->get_funnel_leads_stats(),
            'pipeline'        => array(
                array( 'label' => 'Total Visitors', 'value' => (int) get_option( 'cce_total_visitors', 0 ) ),
                array( 'label' => 'Captured Leads', 'value' => $total_leads ),
                array( 'label' => 'Bookings', 'value' => $total_bookings ),
                array( 'label' => 'Closed Clients', 'value' => $total_clients ),
            )
		) );
	}

    /**
     * Get lead counts per funnel source.
     */
    private function get_funnel_leads_stats() {
        global $wpdb;
        return $wpdb->get_results( "
            SELECT source as funnel_name, COUNT(*) as lead_count
            FROM {$wpdb->prefix}cce_leads
            WHERE source IS NOT NULL AND source != ''
            GROUP BY source
        " );
    }
}
