<?php
/**
 * Social Proof Manager class.
 */
class CCE_Proof_Manager extends CCE_REST_Controller {

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/proof/testimonials', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_testimonials' ),
				'permission_callback' => '__return_true',
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_testimonial' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/proof/testimonials/(?P<id>\d+)', array(
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_testimonial' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/proof/testimonials/(?P<id>\d+)/delete', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'delete_testimonial' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
            array(
				'methods'             => array( WP_REST_Server::EDITABLE, WP_REST_Server::CREATABLE ),
				'callback'            => array( $this, 'update_testimonial' ),
				'permission_callback' => array( $this, 'check_permission' ),
			);
	}

    /**
     * Delete testimonial.
     */
    public function delete_testimonial( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $wpdb->delete( "{$wpdb->prefix}cce_testimonials", array( 'id' => $id, 'user_id' => $user_id ) );
        return $this->success( array( 'message' => 'Testimonial deleted' ) );
    }

    /**
     * Update testimonial.
     */
    public function update_testimonial( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $params = $this->get_params( $request );

        $wpdb->update( "{$wpdb->prefix}cce_testimonials", array(
            'type'        => sanitize_text_field( $params['type'] ?? 'testimonial' ),
            'title'       => sanitize_text_field( $params['title'] ?? '' ),
            'client_name' => sanitize_text_field( $params['client_name'] ?? '' ),
            'content'     => sanitize_textarea_field( $params['content'] ?? '' ),
            'rating'      => absint( $params['rating'] ?? 5 ),
        ), array( 'id' => $id, 'user_id' => $user_id ) );

        return $this->success( array( 'message' => 'Testimonial updated' ) );
    }

	/**
	 * Get testimonials.
	 */
	public function get_testimonials( $request ) {
        global $wpdb;
        $user_id = $this->get_current_user_id();

        // If it's a public request, user_id might be 0, we should use a default or filtered by current page owner in class-cce-public.php
        if ( ! $user_id && current_user_can( 'manage_options' ) ) {
            $user_id = get_current_user_id();
        }

        $query = "SELECT * FROM {$wpdb->prefix}cce_testimonials WHERE status = 'active'";
        $params = array();

        if ( $user_id ) {
            $query .= " AND user_id = %d";
            $params[] = $user_id;
        }

        $query .= " ORDER BY created_at DESC";
        $testimonials = $wpdb->get_results( $wpdb->prepare( $query, $params ) );
		return $this->success( $testimonials );
	}

    /**
     * Create testimonial.
     */
    public function create_testimonial( $request ) {
        global $wpdb;
        $user_id = $this->get_current_user_id();
        $params = $this->get_params( $request );

        $data = array(
            'user_id'     => $user_id,
            'type'        => sanitize_text_field( $params['type'] ?? 'testimonial' ),
            'title'       => sanitize_text_field( $params['title'] ?? '' ),
            'client_name' => sanitize_text_field( $params['client_name'] ),
            'content'     => sanitize_textarea_field( $params['content'] ),
            'rating'      => absint( $params['rating'] ?? 5 ),
            'status'      => 'active',
            'created_at'  => current_time( 'mysql' ),
        );

        $result = $wpdb->insert( "{$wpdb->prefix}cce_testimonials", $data );

        if ( false === $result ) {
            return $this->error( 'Failed to create testimonial' );
        }

        return $this->success( array( 'id' => $wpdb->insert_id ) );
    }
}
