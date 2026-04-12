<?php
/**
 * Checkout Manager class.
 */
class CCE_Checkout_Manager extends CCE_REST_Controller {

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/checkout/process', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'process_payment' ),
				'permission_callback' => '__return_true', // Public endpoint
			),
		) );
	}

	/**
	 * Process payment (Create Intent).
	 */
	public function process_payment( $request ) {
		global $wpdb;
		$params = $request->get_params();

		$offer_id = absint( $params['offer_id'] );
		$lead_id = absint( $params['lead_id'] );
		$gateway = sanitize_text_field( $params['gateway'] );

        // Get offer details
        $offer_model = new CCE_Offer_Model();
        $offer = $offer_model->get( $offer_id );
        if ( ! $offer ) {
            return $this->error( 'Offer not found' );
        }

		// Simulate payment intent creation
        $client_secret = '';
        if ( 'stripe' === $gateway ) {
            $stripe = new CCE_Stripe_Wrapper( get_option( 'cce_stripe_api_key' ) );
            $intent = $stripe->create_payment_intent( $offer->price );
            $client_secret = $intent['client_secret'];
        }

		$wpdb->insert( "{$wpdb->prefix}cce_payments", array(
			'lead_id'        => $lead_id,
			'offer_id'       => $offer_id,
			'transaction_id' => 'PENDING_' . time(),
			'gateway'        => $gateway,
			'amount'         => $offer->price,
			'status'         => 'pending',
		) );

		return $this->success( array(
            'message'       => 'Intent created',
            'client_secret' => $client_secret,
            'amount'        => $offer->price,
            'currency'      => $offer->currency
        ) );
	}
}
