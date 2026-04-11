<?php
/**
 * Base class for REST API controllers.
 */
abstract class CCE_REST_Controller extends WP_REST_Controller {

	/**
	 * Namespace for this controller's routes.
	 */
	protected $namespace = 'cce/v1';

	/**
	 * Check if the user has permission to perform the request.
	 */
	public function check_permission( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Send a successful response.
	 */
	public function success( $data = array(), $status = 200 ) {
		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => $data,
			),
			$status
		);
	}

	/**
	 * Send an error response.
	 */
	public function error( $message = 'An error occurred', $code = 'error', $status = 400 ) {
		return new WP_Error( $code, $message, array( 'status' => $status ) );
	}
}
