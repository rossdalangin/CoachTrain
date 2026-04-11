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
			'client_secret' => 'pi_' . bin2hex( random_bytes( 12 ) ) . '_secret_' . bin2hex( random_bytes( 12 ) ),
			'id'            => 'pi_' . bin2hex( random_bytes( 12 ) ),
		);
	}
}
