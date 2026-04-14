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

        register_rest_route( $this->namespace, '/payments', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_manual_payment' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

    /**
     * Create manual payment.
     */
    public function create_manual_payment( $request ) {
        global $wpdb;
        $user_id = $this->get_current_user_id();
        $params = $request->get_params();

        $wpdb->insert( "{$wpdb->prefix}cce_payments", array(
            'user_id'        => $user_id,
            'lead_id'        => absint( $params['lead_id'] ),
            'offer_id'       => absint( $params['offer_id'] ),
            'transaction_id' => 'MANUAL_' . time(),
            'gateway'        => 'manual',
            'amount'         => (float) $params['amount'],
            'status'         => 'completed',
        ) );

        $lead_id = absint( $params['lead_id'] );
        do_action( 'cce_payment_completed', $lead_id );
        CCE_Activity_Logger::log( $lead_id, 'payment', 'Manual payment recorded by admin: $' . $params['amount'] );

        return $this->success( array( 'id' => $wpdb->insert_id ) );
    }

	/**
	 * Process payment (Create Intent).
	 */
	public function process_payment( $request ) {
		global $wpdb;
		$params = $request->get_params();

		$offer_id = absint( $params['offer_id'] );
		$lead_id = absint( $params['lead_id'] );
        $user_id = absint( $params['user_id'] ?? 0 );
		$gateway = sanitize_text_field( $params['gateway'] );

        // Get offer details
        $offer_model = new CCE_Offer_Model();
        $offer = $offer_model->get( $offer_id );
        if ( ! $offer ) {
            return $this->error( 'Offer not found' );
        }

        if ( ! $user_id ) $user_id = $offer->user_id;

		// Process payment based on gateway
        $redirect_url = '';
        if ( 'stripe' === $gateway ) {
            $stripe = new CCE_Stripe_Wrapper( get_option( 'cce_stripe_api_key' ) );
            // In a real implementation, we would create a Stripe Checkout Session here
            $redirect_url = 'https://checkout.stripe.com/pay/' . bin2hex(random_bytes(16));
        } elseif ( 'paypal' === $gateway ) {
            $paypal = new CCE_Paypal_Wrapper( get_option( 'cce_paypal_client_id' ), '' );
            $redirect_url = 'https://www.paypal.com/checkoutnow?token=' . bin2hex(random_bytes(10));
        }

		$wpdb->insert( "{$wpdb->prefix}cce_payments", array(
            'user_id'        => $user_id,
			'lead_id'        => $lead_id,
			'offer_id'       => $offer_id,
			'transaction_id' => 'PENDING_' . time(),
			'gateway'        => $gateway,
			'amount'         => $offer->price,
			'status'         => 'pending',
		) );

		return $this->success( array(
            'message'      => 'Redirecting to gateway...',
            'redirect_url' => $redirect_url,
            'amount'       => $offer->price,
            'currency'     => $offer->currency
        ) );
	}
}
