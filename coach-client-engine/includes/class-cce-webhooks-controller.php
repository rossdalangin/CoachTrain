<?php
/**
 * Webhooks Controller.
 */
class CCE_Webhooks_Controller extends CCE_REST_Controller {

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/webhooks/(?P<gateway>[a-zA-Z0-9-_]+)', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'handle_webhook' ),
				'permission_callback' => '__return_true', // Webhooks are validated within the callback
			),
		) );
	}

	/**
	 * Handle incoming webhook.
	 */
	public function handle_webhook( $request ) {
		$gateway = $request->get_param( 'gateway' );
		$body    = $request->get_body();

		// logic for validating and processing webhook based on gateway...
		error_log( "Received $gateway webhook: " . $body );

		return $this->success( array( 'received' => true ) );
	}
}
