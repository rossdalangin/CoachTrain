<?php
/**
 * Hub Manager class.
 */
class CCE_Hub_Manager extends CCE_REST_Controller {

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/hub/resources', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_resources' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_resource' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/hub/resources/(?P<id>\d+)', array(
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_resource' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
            array(
				'methods'             => array( WP_REST_Server::EDITABLE, WP_REST_Server::CREATABLE ),
				'callback'            => array( $this, 'update_resource' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/hub/resources/(?P<id>\d+)/delete', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'delete_resource' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

    /**
     * Get hub resources.
     */
    public function get_resources( $request ) {
        global $wpdb;
        $user_id = $this->get_current_user_id();
        $resources = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_resources WHERE user_id = %d AND visibility = 'internal' ORDER BY created_at DESC", $user_id ) );
        return $this->success( $resources );
    }

    /**
     * Create hub resource.
     */
    public function create_resource( $request ) {
        global $wpdb;
        $user_id = $this->get_current_user_id();
        $params = $this->get_params( $request );

        $wpdb->insert( "{$wpdb->prefix}cce_resources", array(
            'user_id'    => $user_id,
            'title'      => sanitize_text_field( $params['title'] ?? '' ),
            'category'   => sanitize_text_field( $params['category'] ?? '' ),
            'type'       => sanitize_text_field( $params['type'] ?? '' ),
            'url'        => esc_url_raw( $params['url'] ?? '' ),
            'visibility' => 'internal',
            'created_at' => current_time( 'mysql' ),
        ) );

        return $this->success( array( 'id' => $wpdb->insert_id ) );
    }

    /**
     * Update hub resource.
     */
    public function update_resource( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $params = $this->get_params( $request );

        $wpdb->update( "{$wpdb->prefix}cce_resources", array(
            'title'    => sanitize_text_field( $params['title'] ?? '' ),
            'category' => sanitize_text_field( $params['category'] ?? '' ),
            'type'     => sanitize_text_field( $params['type'] ?? '' ),
            'url'      => esc_url_raw( $params['url'] ?? '' ),
        ), array( 'id' => $id, 'user_id' => $user_id ) );

        return $this->success( array( 'message' => 'Resource updated' ) );
    }

    /**
     * Delete hub resource.
     */
    public function delete_resource( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $wpdb->delete( "{$wpdb->prefix}cce_resources", array( 'id' => $id, 'user_id' => $user_id ) );
        return $this->success( array( 'message' => 'Resource deleted' ) );
    }
}
