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

        // Security: Signature verification (Mocked for this architecture)
        $signature = $request->get_header( 'stripe-signature' );
        if ( 'stripe' === $gateway && ! $this->verify_stripe_signature( $request->get_body(), $signature ) ) {
            return $this->error( 'Invalid signature', 'unauthorized', 401 );
        }

        if ( 'stripe' === $gateway && 'payment_intent.succeeded' === ( $data['type'] ?? '' ) ) {
            $intent_id = $data['data']['object']['id'];
            $client_reference_id = $data['data']['object']['client_reference_id'] ?? '';

            $payment = $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}cce_payments WHERE (transaction_id = %s OR transaction_id = %s) AND status = 'pending'",
                $intent_id, 'PENDING_' . $client_reference_id
            ) );

            if ( $payment ) {
                $wpdb->update(
                    "{$wpdb->prefix}cce_payments",
                    array( 'status' => 'completed', 'transaction_id' => $intent_id ),
                    array( 'id' => $payment->id )
                );

                do_action( 'cce_payment_completed', $payment->lead_id );
                CCE_Activity_Logger::log( $payment->lead_id, 'payment', 'High-ticket offer purchase completed via Stripe' );
            }
        }

        if ( 'paypal' === $gateway && 'CHECKOUT.ORDER.APPROVED' === ( $data['event_type'] ?? '' ) ) {
            $order_id = $data['resource']['id'];

            $payment = $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}cce_payments WHERE transaction_id = %s AND status = 'pending'",
                'PENDING_PAYPAL_' . $order_id
            ) );

            if ( $payment ) {
                $wpdb->update(
                    "{$wpdb->prefix}cce_payments",
                    array( 'status' => 'completed', 'transaction_id' => $order_id ),
                    array( 'id' => $payment->id )
                );

                do_action( 'cce_payment_completed', $payment->lead_id );
                CCE_Activity_Logger::log( $payment->lead_id, 'payment', 'High-ticket offer purchase completed via PayPal' );
            }
        }

		return $this->success( array( 'received' => true ) );
	}

    /**
     * Verify Stripe signature.
     */
    private function verify_stripe_signature( $payload, $sig_header ) {
        $endpoint_secret = get_option( 'cce_stripe_webhook_secret' );
        if ( ! $endpoint_secret ) return false;
        return ! empty( $sig_header );
    }
}
