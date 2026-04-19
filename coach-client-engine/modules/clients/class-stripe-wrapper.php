<?php
/**
 * Stripe API Wrapper.
 */
class CCE_Stripe_Wrapper {

	private $api_key;

	public function __construct( $api_key ) {
		$this->api_key = $api_key;
	}

	/**
	 * Create a Payment Intent.
	 */
	public function create_payment_intent( $amount, $currency = 'usd' ) {
		// Mock Stripe API call
		return array(
			'client_secret' => 'pi_' . wp_generate_password( 24, false ) . '_secret_' . wp_generate_password( 24, false ),
			'id'            => 'pi_' . wp_generate_password( 24, false ),
		);
	}
}
