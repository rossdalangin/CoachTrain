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
				'methods'             => array( WP_REST_Server::EDITABLE, WP_REST_Server::CREATABLE ),
				'callback'            => array( $this, 'update_offer' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/offers/(?P<id>\d+)/duplicate', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'duplicate_offer' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/offers/(?P<id>\d+)/export', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'export_offer' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/offers/import', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'import_offer' ),
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
        $params = $this->get_params( $request );

        $data = array(
            'title'                => sanitize_text_field( $params['title'] ),
            'description'          => sanitize_textarea_field( $params['description'] ?? '' ),
            'price'                => (float) $params['price'],
            'type'                 => sanitize_text_field( $params['type'] ?? 'one-time' ),
            'dream_outcome'        => sanitize_textarea_field( $params['dream_outcome'] ?? '' ),
            'perceived_likelihood' => sanitize_textarea_field( $params['perceived_likelihood'] ?? '' ),
            'time_delay'           => sanitize_textarea_field( $params['time_delay'] ?? '' ),
            'effort_sacrifice'     => sanitize_textarea_field( $params['effort_sacrifice'] ?? '' ),
            'upsell_offer_id'      => absint( $params['upsell_offer_id'] ?? 0 ),
            'downsell_offer_id'    => absint( $params['downsell_offer_id'] ?? 0 ),
            'order_bump_offer_id'  => absint( $params['order_bump_offer_id'] ?? 0 ),
        );

        $user_id = $this->get_current_user_id();
        $wpdb->update( "{$wpdb->prefix}cce_offers", $data, array( 'id' => $id, 'user_id' => $user_id ) );

        return $this->success( array( 'message' => 'Offer updated' ) );
    }

    /**
     * Export offer as JSON.
     */
    public function export_offer( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $offer = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_offers WHERE id = %d AND user_id = %d", $id, $user_id ) );
        if ( ! $offer ) return $this->error( 'Offer not found' );

        return $this->success( array( 'offer' => $offer, 'v' => '1.0' ) );
    }

    /**
     * Import offer from JSON.
     */
    public function import_offer( $request ) {
        global $wpdb;
        $data = $this->get_params( $request );
        $user_id = $this->get_current_user_id();

        if ( empty( $data['offer'] ) ) return $this->error( 'Invalid export data' );

        $offer = $data['offer'];
        $wpdb->insert( "{$wpdb->prefix}cce_offers", array(
            'user_id'              => $user_id,
            'title'                => $offer['title'] . ' (Imported)',
            'price'                => $offer['price'],
            'type'                 => $offer['type'],
            'dream_outcome'        => $offer['dream_outcome'],
            'perceived_likelihood' => $offer['perceived_likelihood'],
            'time_delay'           => $offer['time_delay'],
            'effort_sacrifice'     => $offer['effort_sacrifice'],
            'created_at'           => current_time( 'mysql' ),
        ) );

        return $this->success( array( 'id' => $wpdb->insert_id ) );
    }

    /**
     * Duplicate offer.
     */
    public function duplicate_offer( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $offer = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_offers WHERE id = %d AND user_id = %d", $id, $user_id ) );
        if ( ! $offer ) return $this->error( 'Offer not found' );

        $wpdb->insert( "{$wpdb->prefix}cce_offers", array(
            'user_id'              => $user_id,
            'title'                => $offer->title . ' (Copy)',
            'description'          => $offer->description,
            'price'                => $offer->price,
            'type'                 => $offer->type,
            'dream_outcome'        => $offer->dream_outcome,
            'perceived_likelihood' => $offer->perceived_likelihood,
            'time_delay'           => $offer->time_delay,
            'effort_sacrifice'     => $offer->effort_sacrifice,
            'upsell_offer_id'      => $offer->upsell_offer_id,
            'order_bump_offer_id'  => $offer->order_bump_offer_id,
            'is_active'            => 1,
            'created_at'           => current_time( 'mysql' ),
        ) );

        return $this->success( array( 'id' => $wpdb->insert_id ) );
    }

    /**
     * Delete offer.
     */
    public function delete_offer( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $wpdb->update( "{$wpdb->prefix}cce_offers", array( 'is_active' => 0 ), array( 'id' => $id, 'user_id' => $user_id ) );
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
		$params = $this->get_params( $request );
		$offer_model = new CCE_Offer_Model();
        $user_id = $this->get_current_user_id();

		$data = array(
            'user_id'     => $user_id,
			'title'       => sanitize_text_field( $params['title'] ),
			'description' => sanitize_textarea_field( $params['description'] ?? '' ),
			'price'       => (float) $params['price'],
			'type'        => sanitize_text_field( $params['type'] ?? 'one-time' ),
            'created_at'  => current_time( 'mysql' ),
		);

		$result = $offer_model->create( $data );

		if ( false === $result ) {
			return $this->error( 'Failed to create offer' );
		}

		return $this->success( array( 'id' => $GLOBALS['wpdb']->insert_id ) );
	}
}
