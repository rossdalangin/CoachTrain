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
	}

    /**
     * Delete testimonial.
     */
    public function delete_testimonial( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $wpdb->delete( "{$wpdb->prefix}cce_testimonials", array( 'id' => $id ) );
        return $this->success( array( 'message' => 'Testimonial deleted' ) );
    }

	/**
	 * Get testimonials.
	 */
	public function get_testimonials( $request ) {
        global $wpdb;
        $testimonials = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_testimonials WHERE status = 'active' ORDER BY created_at DESC" );
		return $this->success( $testimonials );
	}

    /**
     * Create testimonial.
     */
    public function create_testimonial( $request ) {
        global $wpdb;
        $params = $request->get_params();

        $data = array(
            'client_name' => sanitize_text_field( $params['client_name'] ),
            'content'     => sanitize_textarea_field( $params['content'] ),
            'rating'      => absint( $params['rating'] ?? 5 ),
            'status'      => 'active',
        );

        $result = $wpdb->insert( "{$wpdb->prefix}cce_testimonials", $data );

        if ( false === $result ) {
            return $this->error( 'Failed to create testimonial' );
        }

        return $this->success( array( 'id' => $wpdb->insert_id ) );
    }
}
