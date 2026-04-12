<?php
/**
 * Social Proof Manager class.
 */
class CCE_Proof_Manager extends CCE_REST_Controller {

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/proof/testimonials', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_testimonials' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

	/**
	 * Get testimonials.
	 */
	public function get_testimonials( $request ) {
		// Mock data
		$testimonials = array(
			array( 'id' => 1, 'client' => 'John Doe', 'content' => 'This coaching changed my life!', 'rating' => 5 ),
			array( 'id' => 2, 'client' => 'Jane Smith', 'content' => 'I doubled my revenue in 90 days.', 'rating' => 5 ),
		);
		return $this->success( $testimonials );
	}
}
