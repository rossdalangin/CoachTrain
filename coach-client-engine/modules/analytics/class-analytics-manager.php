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
            'show_rate'       => $total_bookings > 0 ? round( ( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_bookings WHERE status = 'completed'" ) / $total_bookings ) * 100, 1 ) : 0,
            'funnel_stats'    => $this->get_funnel_leads_stats(),
            'projections'     => $this->get_projections(),
            'pending_tasks'   => $this->get_pending_tasks(),
            'pipeline'        => array(
                array( 'label' => 'Total Visitors', 'value' => (int) get_option( 'cce_total_visitors', 0 ) ),
                array( 'label' => 'Captured Leads', 'value' => $total_leads ),
                array( 'label' => 'Bookings', 'value' => $total_bookings ),
                array( 'label' => 'Closed Clients', 'value' => $total_clients ),
            )
		) );
	}

    /**
     * Get pending tasks across all leads.
     */
    private function get_pending_tasks() {
        global $wpdb;
        return $wpdb->get_results( "
            SELECT t.*, CONCAT(l.first_name, ' ', l.last_name) as lead_name
            FROM {$wpdb->prefix}cce_tasks t
            JOIN {$wpdb->prefix}cce_leads l ON t.lead_id = l.id
            WHERE t.status = 'pending'
            ORDER BY t.created_at ASC
            LIMIT 5
        " );
    }

    private function get_funnel_leads_stats() {
        global $wpdb;
        $funnels = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_funnels" );
        $stats = [];

        foreach ( $funnels as $f ) {
            $leads = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_leads WHERE source = %s", $f->title ) );

            // Sum visits from all steps in this funnel
            $visits = $wpdb->get_var( $wpdb->prepare( "SELECT SUM(visits) FROM {$wpdb->prefix}cce_funnel_steps WHERE funnel_id = %d", $f->id ) ) ?: 0;

            $stats[] = (object) array(
                'funnel_name' => $f->title,
                'lead_count'  => (int) $leads,
                'visits'      => (int) $visits,
                'rate'        => $visits > 0 ? round( ($leads / $visits) * 100, 1 ) : 0,
            );
        }

        return $stats;
    }

    /**
     * Get revenue projections based on average order value and lead velocity.
     */
    public function get_projections() {
        global $wpdb;
        $total_leads = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_leads" );
        $total_sales = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_payments WHERE status = 'completed'" );
        $total_revenue = (float) $wpdb->get_var( "SELECT SUM(amount) FROM {$wpdb->prefix}cce_payments WHERE status = 'completed'" );

        $conv_rate = $total_leads > 0 ? ($total_sales / $total_leads) : 0;
        $aov = $total_sales > 0 ? ($total_revenue / $total_sales) : 0;

        // Current 30 day velocity
        $leads_30 = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_leads WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)" );

        return array(
            'leads_next_30' => $leads_30,
            'projected_sales' => round($leads_30 * $conv_rate, 1),
            'projected_revenue' => round(($leads_30 * $conv_rate) * $aov, 2),
            'aov' => round($aov, 2)
        );
    }
}
