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

        register_rest_route( $this->namespace, '/funnels/create-from-template', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_from_template' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/funnels/(?P<id>\d+)', array(
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_funnel' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/funnels/(?P<id>\d+)/duplicate', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'duplicate_funnel' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/funnels/(?P<id>\d+)/export', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'export_funnel' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/funnels/import', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'import_funnel' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

    /**
     * Export funnel as JSON.
     */
    public function export_funnel( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $funnel = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_funnels WHERE id = %d AND user_id = %d", $id, $user_id ) );
        if ( ! $funnel ) return $this->error( 'Funnel not found' );

        $steps = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_funnel_steps WHERE funnel_id = %d", $id ) );

        return $this->success( array(
            'funnel' => $funnel,
            'steps'  => $steps,
            'v'      => '1.0'
        ) );
    }

    /**
     * Import funnel from JSON.
     */
    public function import_funnel( $request ) {
        global $wpdb;
        $data = $this->get_params( $request );
        $user_id = $this->get_current_user_id();

        if ( empty( $data['funnel'] ) || empty( $data['steps'] ) ) {
            return $this->error( 'Invalid export data' );
        }

        $wpdb->insert( "{$wpdb->prefix}cce_funnels", array(
            'user_id'    => $user_id,
            'title'      => $data['funnel']['title'] . ' (Imported)',
            'type'       => $data['funnel']['type'],
            'status'     => 'draft',
            'created_at' => current_time( 'mysql' ),
        ) );

        $funnel_id = $wpdb->insert_id;

        if ( ! isset( $data['steps'] ) || ! is_array( $data['steps'] ) ) return $this->success( array( 'id' => $funnel_id ) );

        foreach ( $data['steps'] as $step ) {
            $wpdb->insert( "{$wpdb->prefix}cce_funnel_steps", array(
                'user_id'    => $user_id,
                'funnel_id'  => $funnel_id,
                'title'      => $step['title'],
                'step_order' => $step['step_order'],
                'step_type'  => $step['step_type'],
                'config'     => $step['config'],
                'tracking_scripts' => $step['tracking_scripts'] ?? '',
            ) );
        }

        return $this->success( array( 'id' => $funnel_id ) );
    }

    /**
     * Duplicate funnel.
     */
    public function duplicate_funnel( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $funnel = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_funnels WHERE id = %d AND user_id = %d", $id, $user_id ) );
        if ( ! $funnel ) return $this->error( 'Funnel not found' );

        $wpdb->insert( "{$wpdb->prefix}cce_funnels", array(
            'user_id'    => $user_id,
            'title'      => $funnel->title . ' (Copy)',
            'type'       => $funnel->type,
            'status'     => 'draft',
            'created_at' => current_time( 'mysql' ),
        ) );

        $new_id = $wpdb->insert_id;
        $steps = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_funnel_steps WHERE funnel_id = %d", $id ) );

        foreach ( $steps as $step ) {
            $wpdb->insert( "{$wpdb->prefix}cce_funnel_steps", array(
                'user_id'    => $user_id,
                'funnel_id'  => $new_id,
                'title'      => $step->title,
                'step_order' => $step->step_order,
                'step_type'  => $step->step_type,
                'config'     => $step->config,
            ) );
        }

        return $this->success( array( 'id' => $new_id ) );
    }

    /**
     * Delete funnel.
     */
    public function delete_funnel( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $wpdb->delete( "{$wpdb->prefix}cce_funnels", array( 'id' => $id, 'user_id' => $user_id ) );
        $wpdb->delete( "{$wpdb->prefix}cce_funnel_steps", array( 'funnel_id' => $id, 'user_id' => $user_id ) );
        return $this->success( array( 'message' => 'Funnel deleted' ) );
    }

    /**
     * Create from template.
     */
    public function create_from_template( $request ) {
        global $wpdb;
        $user_id = $this->get_current_user_id();
        $template_id = sanitize_text_field( $request->get_param( 'template_id' ) );

        $title = 'New ' . ucwords( str_replace( '_', ' ', $template_id ) );
        $wpdb->insert( "{$wpdb->prefix}cce_funnels", array(
            'user_id'    => $user_id,
            'title'      => $title,
            'type'       => $template_id,
            'status'     => 'active',
            'created_at' => current_time( 'mysql' ),
        ) );

        $funnel_id = $wpdb->insert_id;

        // Seed default steps
        if ( 'lead_magnet' === $template_id ) {
            $steps = [
                ['title' => 'Opt-in Page', 'type' => 'optin'],
                ['title' => 'Thank You', 'type' => 'thank_you']
            ];
        } elseif ( 'consultation' === $template_id ) {
            $steps = [
                ['title' => 'Application', 'type' => 'optin'],
                ['title' => 'Schedule Call', 'type' => 'booking'],
                ['title' => 'Confirmation', 'type' => 'thank_you']
            ];
        } elseif ( 'appointment_machine' === $template_id ) {
            $steps = [
                ['title' => 'Discovery Form', 'type' => 'optin'],
                ['title' => 'Setter Triage Call', 'type' => 'booking'],
                ['title' => 'Closer Strategy Session', 'type' => 'booking'],
                ['title' => 'Success', 'type' => 'thank_you']
            ];
        } elseif ( 'hybrid_closer' === $template_id ) {
            $steps = [
                ['title' => 'Opt-in', 'type' => 'optin'],
                ['title' => 'VSL Presentation', 'type' => 'thank_you'],
                ['title' => 'Calendar', 'type' => 'booking'],
                ['title' => 'Enrollment', 'type' => 'checkout'],
                ['title' => 'Success', 'type' => 'thank_you']
            ];
        } elseif ( 'webinar' === $template_id ) {
            $steps = [
                ['title' => 'Registration', 'type' => 'optin'],
                ['title' => 'Watch Workshop', 'type' => 'thank_you'],
                ['title' => 'Book Strategy Session', 'type' => 'booking'],
                ['title' => 'Enroll', 'type' => 'checkout']
            ];
        } elseif ( 'vsl' === $template_id ) {
            $steps = [
                ['title' => 'Opt-in Page', 'type' => 'optin'],
                ['title' => 'VSL Video', 'type' => 'thank_you'],
                ['title' => 'Book Session', 'type' => 'booking'],
                ['title' => 'Success', 'type' => 'thank_you']
            ];
        } elseif ( 'tripwire' === $template_id ) {
            $steps = [
                ['title' => 'Sales Page', 'type' => 'optin'],
                ['title' => 'Checkout', 'type' => 'checkout'],
                ['title' => 'Upsell', 'type' => 'checkout'],
                ['title' => 'Success', 'type' => 'thank_you']
            ];
        }

        if ( ! empty( $steps ) ) {
            foreach ( $steps as $index => $step ) {
                $wpdb->insert( "{$wpdb->prefix}cce_funnel_steps", array(
                    'user_id'    => $user_id,
                    'funnel_id'  => $funnel_id,
                    'title'      => $step['title'],
                    'step_order' => $index + 1,
                    'step_type'  => $step['type'] ?? 'thank_you',
                    'config'     => '{}'
                ) );
            }
        }

        return $this->success( array( 'id' => $funnel_id ) );
    }

    /**
     * Get funnel steps.
     */
    public function get_funnel_steps( $request ) {
        global $wpdb;
        $funnel_id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $table_name = $wpdb->prefix . 'cce_funnel_steps';
        $steps = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE funnel_id = %d AND user_id = %d ORDER BY step_order ASC", $funnel_id, $user_id ) );

        if ( ! empty( $steps ) ) {
            foreach ( $steps as &$step ) {
                $step->config = json_decode( $step->config );
            }
        }

        return $this->success( $steps );
    }

    /**
     * Save funnel steps.
     */
    public function save_funnel_steps( $request ) {
        global $wpdb;
        $funnel_id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $params = $this->get_params( $request ); $steps = $params['steps'] ?? array();
        $table_name = $wpdb->prefix . 'cce_funnel_steps';

        // Simplified: Delete and re-insert for this version
        $wpdb->delete( $table_name, array( 'funnel_id' => $funnel_id, 'user_id' => $user_id ) );

        if ( is_array( $steps ) ) {
            foreach ( $steps as $index => $step ) {
                $wpdb->insert( $table_name, array(
                    'user_id'    => $user_id,
                    'funnel_id'  => $funnel_id,
                    'title'      => sanitize_text_field( $step['title'] ?? '' ),
                    'step_order' => $index + 1,
                    'step_type'  => sanitize_text_field( $step['type'] ?? 'thank_you' ),
                    'config'     => json_encode( $step['config'] ?? array() ),
                    'tracking_scripts' => $step['tracking_scripts'] ?? '',
                    'logic'      => $step['logic'] ?? '',
                ) );
            }
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
            array( 'id' => 'appointment_machine', 'title' => 'The Appointment Machine', 'description' => 'A dual-booking system for setters and closers.' ),
            array( 'id' => 'hybrid_closer', 'title' => 'The Hybrid Closer', 'description' => 'The ultimate VSL-to-Checkout conversion system.' ),
            array( 'id' => 'webinar', 'title' => 'Webinar Funnel', 'description' => 'Best for automated sales presentations.' ),
        );
        return $this->success( $templates );
    }

	/**
	 * Get funnels.
	 */
	public function get_funnels( $request ) {
		global $wpdb;
        $user_id = $this->get_current_user_id();
		$table_name = $wpdb->prefix . 'cce_funnels';
		$funnels = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE user_id = %d ORDER BY created_at DESC", $user_id ) );
		return $this->success( $funnels );
	}

	/**
	 * Create funnel.
	 */
	public function create_funnel( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'cce_funnels';

		$params = $this->get_params( $request );
        $user_id = $this->get_current_user_id();

		$data = array(
            'user_id'    => $user_id,
			'title'      => sanitize_text_field( $params['title'] ),
			'type'       => sanitize_text_field( $params['type'] ),
			'status'     => 'draft',
            'created_at' => current_time( 'mysql' ),
		);

		$result = $wpdb->insert( $table_name, $data );

		if ( false === $result ) {
			return $this->error( 'Failed to create funnel' );
		}

		$data['id'] = $wpdb->insert_id;
		return $this->success( $data );
	}
}
