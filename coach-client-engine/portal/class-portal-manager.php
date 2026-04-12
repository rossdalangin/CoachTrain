<?php
/**
 * Client Portal Manager class.
 */
class CCE_Portal_Manager extends CCE_REST_Controller {

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/portal/resources', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_resources' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

	/**
	 * Get coaching resources.
	 */
	public function get_resources( $request ) {
		// Example data structure
		$resources = array(
			array( 'id' => 1, 'title' => 'Client Welcome Pack', 'type' => 'PDF' ),
			array( 'id' => 2, 'title' => 'High-Ticket Sales Script', 'type' => 'PDF' ),
			array( 'id' => 3, 'title' => 'Onboarding Call Recording', 'type' => 'Video' ),
		);
		return $this->success( $resources );
	}
}
