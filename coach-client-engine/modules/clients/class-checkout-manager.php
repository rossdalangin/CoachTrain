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
	 * Process payment.
	 */
	public function process_payment( $request ) {
		global $wpdb;
		$params = $request->get_params();

		$offer_id = absint( $params['offer_id'] );
		$lead_id = absint( $params['lead_id'] );
		$gateway = sanitize_text_field( $params['gateway'] );

		// logic for payment processing via wrappers...

		$wpdb->insert( "{$wpdb->prefix}cce_payments", array(
			'lead_id'        => $lead_id,
			'offer_id'       => $offer_id,
			'transaction_id' => 'TXN_' . time(),
			'gateway'        => $gateway,
			'amount'         => 0.0, // Should come from offer
			'status'         => 'completed',
		) );

		return $this->success( array( 'message' => 'Payment successful' ) );
	}
}
