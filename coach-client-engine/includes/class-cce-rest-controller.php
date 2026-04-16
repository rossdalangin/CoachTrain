<?php
/**
 * Ensure REST API classes are loaded for admin-side usage.
 */
if ( ! class_exists( 'WP_REST_Controller' ) ) {
    require_once ABSPATH . 'wp-includes/rest-api/class-wp-rest-controller.php';
}
if ( ! class_exists( 'WP_REST_Request' ) ) {
    require_once ABSPATH . 'wp-includes/rest-api/class-wp-rest-request.php';
}
if ( ! class_exists( 'WP_REST_Response' ) ) {
    require_once ABSPATH . 'wp-includes/rest-api/class-wp-rest-response.php';
}

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

    /**
     * Get the current user ID.
     */
    public function get_current_user_id() {
        return get_current_user_id();
    }
}
