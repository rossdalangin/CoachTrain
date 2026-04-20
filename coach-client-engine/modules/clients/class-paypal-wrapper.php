<?php
/**
 * PayPal API Wrapper.
 */
class CCE_Paypal_Wrapper {

	private $client_id;
	private $secret;

	public function __construct( $client_id, $secret ) {
		$this->client_id = $client_id;
		$this->secret    = $secret;
	}

	/**
	 * Create an Order.
	 */
	public function create_order( $amount, $currency = 'USD' ) {
		// Mock PayPal API call
		return array(
			'id'     => 'PAY-' . wp_generate_password( 16, false ),
			'status' => 'CREATED',
		);
	}
}
