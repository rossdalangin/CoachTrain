<?php
/**
 * Funnels Manager class.
 */
class CCE_Funnels_Manager extends CCE_REST_Controller {

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/funnels', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_funnels' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_funnel' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/funnels/templates', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_templates' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/funnels/(?P<id>\d+)/steps', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_funnel_steps' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'save_funnel_steps' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

    /**
     * Get funnel steps.
     */
    public function get_funnel_steps( $request ) {
        global $wpdb;
        $funnel_id = absint( $request['id'] );
        $table_name = $wpdb->prefix . 'cce_funnel_steps';
        $steps = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE funnel_id = %d ORDER BY step_order ASC", $funnel_id ) );
        return $this->success( $steps );
    }

    /**
     * Save funnel steps.
     */
    public function save_funnel_steps( $request ) {
        global $wpdb;
        $funnel_id = absint( $request['id'] );
        $steps = $request->get_param( 'steps' );
        $table_name = $wpdb->prefix . 'cce_funnel_steps';

        // Simplified: Delete and re-insert for this version
        $wpdb->delete( $table_name, array( 'funnel_id' => $funnel_id ) );

        foreach ( $steps as $index => $step ) {
            $wpdb->insert( $table_name, array(
                'funnel_id'  => $funnel_id,
                'title'      => sanitize_text_field( $step['title'] ),
                'step_order' => $index + 1,
                'step_type'  => sanitize_text_field( $step['type'] ),
                'config'     => json_encode( $step['config'] ?? array() ),
            ) );
        }

        return $this->success( array( 'message' => 'Steps saved' ) );
    }

    /**
     * Get funnel templates.
     */
    public function get_templates( $request ) {
        $templates = array(
            array( 'id' => 'lead_magnet', 'title' => 'Lead Magnet Funnel', 'description' => 'Perfect for building your email list.' ),
            array( 'id' => 'consultation', 'title' => 'Consultation Funnel', 'description' => 'Ideal for high-ticket coaching bookings.' ),
            array( 'id' => 'webinar', 'title' => 'Webinar Funnel', 'description' => 'Best for automated sales presentations.' ),
        );
        return $this->success( $templates );
    }

	/**
	 * Get funnels.
	 */
	public function get_funnels( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'cce_funnels';
		$funnels = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC" );
		return $this->success( $funnels );
	}

	/**
	 * Create funnel.
	 */
	public function create_funnel( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'cce_funnels';

		$params = $request->get_params();

		$data = array(
			'title'  => sanitize_text_field( $params['title'] ),
			'type'   => sanitize_text_field( $params['type'] ),
			'status' => 'draft',
		);

		$result = $wpdb->insert( $table_name, $data );

		if ( false === $result ) {
			return $this->error( 'Failed to create funnel' );
		}

		$data['id'] = $wpdb->insert_id;
		return $this->success( $data );
	}
}
