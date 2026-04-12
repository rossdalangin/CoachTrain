<?php
/**
 * CRM Manager class.
 */
class CCE_CRM_Manager extends CCE_REST_Controller {

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/crm/stages', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_stages' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/crm/pipeline', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_pipeline' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/crm/leads/(?P<id>\d+)/notes', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'add_note' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

    /**
     * Add note to a lead.
     */
    public function add_note( $request ) {
        $lead_id = absint( $request['id'] );
        $note = sanitize_text_field( $request->get_param( 'note' ) );

        CCE_Activity_Logger::log( $lead_id, 'note', $note );

        return $this->success( array( 'message' => 'Note added' ) );
    }

	/**
	 * Get CRM stages.
	 */
	public function get_stages( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'cce_crm_stages';
		$stages = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY stage_order ASC" );
		return $this->success( $stages );
	}

    /**
     * Get Pipeline (Leads grouped by stages).
     */
    public function get_pipeline( $request ) {
        global $wpdb;
        $stages_table = $wpdb->prefix . 'cce_crm_stages';
        $leads_table = $wpdb->prefix . 'cce_leads';

        $stages = $wpdb->get_results( "SELECT * FROM $stages_table ORDER BY stage_order ASC" );
        $pipeline = array();

        foreach ( $stages as $stage ) {
            $leads = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM $leads_table WHERE crm_stage_id = %d",
                $stage->id
            ) );

            // Fetch activities for each lead
            foreach ( $leads as &$lead ) {
                $lead->activities = CCE_Activity_Logger::get_logs( $lead->id );
            }

            $pipeline[] = array(
                'stage' => $stage,
                'leads' => $leads
            );
        }

        return $this->success( $pipeline );
    }
}
