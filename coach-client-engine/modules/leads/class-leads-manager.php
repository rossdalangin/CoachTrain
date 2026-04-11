<?php
/**
 * Leads Manager class.
 */
class CCE_Leads_Manager extends CCE_REST_Controller {

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/leads', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_leads' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_lead' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

	/**
	 * Get leads.
	 */
	public function get_leads( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'cce_leads';
		$leads = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC" );
		return $this->success( $leads );
	}

	/**
	 * Create lead.
	 */
	public function create_lead( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'cce_leads';

		$params = $request->get_params();

		$data = array(
			'first_name'   => sanitize_text_field( $params['first_name'] ),
			'last_name'    => sanitize_text_field( $params['last_name'] ),
			'email'        => sanitize_email( $params['email'] ),
			'phone'        => sanitize_text_field( $params['phone'] ),
			'status'       => 'cold',
            'crm_stage_id' => 1, // Default to 'New' stage
		);

		$result = $wpdb->insert( $table_name, $data );

		if ( false === $result ) {
			return $this->error( 'Failed to create lead' );
		}

		$data['id'] = $wpdb->insert_id;
		return $this->success( $data );
	}
}
