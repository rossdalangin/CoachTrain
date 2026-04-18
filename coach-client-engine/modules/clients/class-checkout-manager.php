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
        $params = $this->get_params( $request );

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
		$params = $this->get_params( $request );

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
        $is_test = (bool) get_user_meta( $user_id, 'cce_test_mode', true );

        if ( $is_test ) {
            $redirect_url = add_query_arg( array(
                'cce_simulate_payment' => 1,
                'gateway' => $gateway,
                'offer_id' => $offer_id,
                'lead_id' => $lead_id,
                'nonce' => wp_create_nonce('cce_sim_payment')
            ), home_url('/') );
        } elseif ( 'stripe' === $gateway ) {
            $stripe = new CCE_Stripe_Wrapper( get_user_meta( $user_id, 'cce_stripe_api_key', true ) );
            // In a real implementation, we would create a Stripe Checkout Session here
            $redirect_url = 'https://checkout.stripe.com/pay/' . bin2hex(random_bytes(16));
        } elseif ( 'paypal' === $gateway ) {
            $paypal = new CCE_Paypal_Wrapper( get_user_meta( $user_id, 'cce_paypal_client_id', true ), '' );
            $redirect_url = 'https://www.paypal.com/checkoutnow?token=' . bin2hex(random_bytes(10));
        }

        $pending_id = 'PENDING_' . bin2hex( random_bytes( 8 ) );
		$wpdb->insert( "{$wpdb->prefix}cce_payments", array(
            'user_id'        => $user_id,
			'lead_id'        => $lead_id,
			'offer_id'       => $offer_id,
			'transaction_id' => $pending_id,
			'gateway'        => $gateway,
			'amount'         => $offer->price,
			'status'         => 'pending',
		) );

        // Track Conversion if in funnel
        $step_id = absint( $params['step_id'] ?? $_COOKIE['cce_active_funnel_step'] ?? 0 );
        if ( $step_id ) {
            $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}cce_funnel_steps SET conversions = conversions + 1 WHERE id = %d", $step_id ) );
        }

		return $this->success( array(
            'message'      => 'Redirecting to gateway...',
            'redirect_url' => $redirect_url,
            'amount'       => $offer->price,
            'currency'     => $offer->currency
        ) );
	}
}
