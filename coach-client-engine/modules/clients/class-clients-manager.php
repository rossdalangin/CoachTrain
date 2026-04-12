<?php
/**
 * Clients Manager class.
 */
class CCE_Clients_Manager extends CCE_REST_Controller {

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/offers', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_offers' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_offer' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/offers/(?P<id>\d+)', array(
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_offer' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_offer' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

    /**
     * Update offer.
     */
    public function update_offer( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $params = $request->get_params();

        $data = array(
            'title'       => sanitize_text_field( $params['title'] ),
            'description' => sanitize_textarea_field( $params['description'] ?? '' ),
            'price'       => (float) $params['price'],
            'type'        => sanitize_text_field( $params['type'] ?? 'one-time' ),
        );

        $wpdb->update( "{$wpdb->prefix}cce_offers", $data, array( 'id' => $id ) );

        return $this->success( array( 'message' => 'Offer updated' ) );
    }

    /**
     * Delete offer.
     */
    public function delete_offer( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $wpdb->update( "{$wpdb->prefix}cce_offers", array( 'is_active' => 0 ), array( 'id' => $id ) );
        return $this->success( array( 'message' => 'Offer deleted' ) );
    }

	/**
	 * Get offers.
	 */
	public function get_offers( $request ) {
		$offer_model = new CCE_Offer_Model();
		return $this->success( $offer_model->get_all() );
	}

	/**
	 * Create offer.
	 */
	public function create_offer( $request ) {
		$params = $request->get_params();
		$offer_model = new CCE_Offer_Model();

		$data = array(
			'title'       => sanitize_text_field( $params['title'] ),
			'description' => sanitize_textarea_field( $params['description'] ?? '' ),
			'price'       => (float) $params['price'],
			'type'        => sanitize_text_field( $params['type'] ?? 'one-time' ),
		);

		$result = $offer_model->create( $data );

		if ( false === $result ) {
			return $this->error( 'Failed to create offer' );
		}

		return $this->success( array( 'id' => $GLOBALS['wpdb']->insert_id ) );
	}
}
