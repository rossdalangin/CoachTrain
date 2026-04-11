<?php
/**
 * Funnels Manager class.
 */
class CCE_Funnels_Manager extends CCE_REST_Controller {

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/funnels', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_funnels' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_funnel' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

	/**
	 * Get funnels.
	 */
	public function get_funnels( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'cce_funnels';
		$funnels = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC" );
		return $this->success( $funnels );
	}

	/**
	 * Create funnel.
	 */
	public function create_funnel( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'cce_funnels';

		$params = $request->get_params();

		$data = array(
			'title'  => sanitize_text_field( $params['title'] ),
			'type'   => sanitize_text_field( $params['type'] ),
			'status' => 'draft',
		);

		$result = $wpdb->insert( $table_name, $data );

		if ( false === $result ) {
			return $this->error( 'Failed to create funnel' );
		}

		$data['id'] = $wpdb->insert_id;
		return $this->success( $data );
	}
}
