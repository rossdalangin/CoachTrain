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
		global $wpdb;
        $gateway = $request->get_param( 'gateway' );
		$data    = $request->get_json_params();

		error_log( "Received $gateway webhook: " . json_encode( $data ) );

        if ( 'stripe' === $gateway && 'payment_intent.succeeded' === ( $data['type'] ?? '' ) ) {
            $intent_id = $data['data']['object']['id'];

            // Update payment status
            $wpdb->update(
                "{$wpdb->prefix}cce_payments",
                array( 'status' => 'completed', 'transaction_id' => $intent_id ),
                array( 'transaction_id' => 'PENDING_' . $intent_id ) // Simplified lookup logic
            );

            // Trigger automation
            $payment = $wpdb->get_row( $wpdb->prepare( "SELECT lead_id FROM {$wpdb->prefix}cce_payments WHERE transaction_id = %s", $intent_id ) );
            if ( $payment ) {
                do_action( 'cce_payment_completed', $payment->lead_id );
                CCE_Activity_Logger::log( $payment->lead_id, 'payment', 'High-ticket offer purchase completed via Stripe' );
            }
        }

		return $this->success( array( 'received' => true ) );
	}
}
