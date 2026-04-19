<?php
/**
 * Leads Manager class.
 */
class CCE_Leads_Manager extends CCE_REST_Controller {

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/leads', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_leads' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_lead' ),
				'permission_callback' => '__return_true', // Public lead capture
			),
		) );

        register_rest_route( $this->namespace, '/leads/bulk', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'handle_bulk_action' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/leads/(?P<id>\d+)/tags', array(
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'update_lead_tags' ),
                'permission_callback' => array( $this, 'check_permission' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/leads/(?P<id>\d+)/status', array(
			array(
				'methods'             => array( WP_REST_Server::EDITABLE, WP_REST_Server::CREATABLE ),
				'callback'            => array( $this, 'update_lead_status' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/leads/(?P<id>\d+)', array(
			array(
				'methods'             => array( WP_REST_Server::DELETABLE, WP_REST_Server::CREATABLE ),
				'callback'            => array( $this, 'delete_lead' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
			array(
				'methods'             => array( WP_REST_Server::EDITABLE, WP_REST_Server::CREATABLE ),
				'callback'            => array( $this, 'update_lead' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/leads/import', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'import_leads' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

    /**
     * Import leads from CSV data.
     */
    public function import_leads( $request ) {
        global $wpdb;
        $leads = $request->get_param( 'leads' );
        if ( ! is_array( $leads ) ) return $this->error( 'Invalid data' );

        $count = 0;
        $user_id = $this->get_current_user_id();
        foreach ( $leads as $lead ) {
            $email = sanitize_email( $lead['email'] ?? '' );
            if ( ! $email ) continue;

            // Check if exists for this user
            $exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cce_leads WHERE email = %s AND user_id = %d", $email, $user_id ) );
            if ( $exists ) continue;

            $default_stage_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cce_crm_stages WHERE user_id = %d ORDER BY stage_order ASC LIMIT 1", $user_id ) ) ?: 1;

            $wpdb->insert( "{$wpdb->prefix}cce_leads", array(
                'user_id'      => $this->get_current_user_id(),
                'first_name'   => sanitize_text_field( $lead['first_name'] ?? '' ),
                'last_name'    => sanitize_text_field( $lead['last_name'] ?? '' ),
                'email'        => $email,
                'status'       => 'cold',
                'crm_stage_id' => $default_stage_id,
                'secure_token' => wp_generate_password( 64, false ),
                'source'       => 'Imported',
                'created_at'   => current_time( 'mysql' ),
            ) );
            $count++;
        }

        return $this->success( array( 'count' => $count ) );
    }

    /**
     * Update lead.
     */
    public function update_lead( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $params = $this->get_params( $request );

        $data = array(
            'first_name' => sanitize_text_field( $params['first_name'] ?? '' ),
            'last_name'  => sanitize_text_field( $params['last_name'] ?? '' ),
            'email'       => sanitize_email( $params['email'] ?? '' ),
            'phone'       => sanitize_text_field( $params['phone'] ?? '' ),
            'status'      => sanitize_text_field( $params['status'] ?? 'cold' ),
            'tags'        => sanitize_text_field( $params['tags'] ?? '' ),
        );

        $wpdb->update( "{$wpdb->prefix}cce_leads", $data, array( 'id' => $id, 'user_id' => $user_id ) );
        CCE_Activity_Logger::log( $id, 'update', 'Lead information updated.' );

        return $this->success( array( 'message' => 'Lead updated' ) );
    }

    /**
     * Handle bulk actions.
     */
    public function handle_bulk_action( $request ) {
        global $wpdb;
        $user_id = $this->get_current_user_id();
        $params = $this->get_params( $request );
        $ids = $params['ids'] ?? [];
        $action = $params['bulk_action'] ?? '';

        if ( empty( $ids ) ) return $this->error( 'No IDs provided' );

        $ids_string = implode( ',', array_map( 'absint', $ids ) );

        if ( 'delete' === $action ) {
            $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}cce_leads WHERE id IN ($ids_string) AND user_id = %d", $user_id ) );
        } elseif ( in_array( $action, ['cold', 'warm', 'hot'] ) ) {
            $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}cce_leads SET status = %s WHERE id IN ($ids_string) AND user_id = %d", $action, $user_id ) );
        }

        return $this->success( array( 'message' => 'Bulk action completed' ) );
    }

    /**
     * Delete lead.
     */
    public function delete_lead( $request ) {
        global $wpdb;
        $lead_id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();

        if ( ! $lead_id ) return $this->error( 'Invalid ID' );

        $wpdb->delete( "{$wpdb->prefix}cce_leads", array( 'id' => $lead_id, 'user_id' => $user_id ) );
        return $this->success( array( 'message' => 'Lead deleted' ) );
    }

    /**
     * Update lead tags.
     */
    public function update_lead_tags( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $params = $this->get_params( $request );
        $tags = sanitize_text_field( $params['tags'] ?? '' );
        $user_id = $this->get_current_user_id();

        $wpdb->update(
            "{$wpdb->prefix}cce_leads",
            array( 'tags' => $tags ),
            array( 'id' => $id, 'user_id' => $user_id )
        );

        return $this->success();
    }

    /**
     * Update lead status (tag).
     */
    public function update_lead_status( $request ) {
        global $wpdb;
        $lead_id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $status = sanitize_text_field( $request->get_param( 'status' ) );

        $wpdb->update( "{$wpdb->prefix}cce_leads", array( 'status' => $status ), array( 'id' => $lead_id, 'user_id' => $user_id ) );

        CCE_Activity_Logger::log( $lead_id, 'status_change', 'Lead tag updated to: ' . $status );

        return $this->success( array( 'message' => 'Status updated' ) );
    }

	/**
	 * Get leads.
	 */
	public function get_leads( $request ) {
		global $wpdb;
        $user_id = $this->get_current_user_id();
		$table_name = $wpdb->prefix . 'cce_leads';
		$leads = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE user_id = %d ORDER BY created_at DESC", $user_id ) );
		return $this->success( $leads );
	}

	/**
	 * Create lead.
	 */
	public function create_lead( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'cce_leads';

		$params = $this->get_params( $request );
        $user_id = absint( $params['user_id'] ?? 0 );

        $source = $_COOKIE['cce_funnel_source'] ?? 'Direct';
        if ( is_numeric( $source ) ) {
            $funnel = $wpdb->get_row( $wpdb->prepare( "SELECT title, user_id FROM {$wpdb->prefix}cce_funnels WHERE id = %d", $source ) );
            if ( $funnel ) {
                $source = $funnel->title;
                if ( ! $user_id ) $user_id = $funnel->user_id;
            } else {
                $source = 'Direct';
            }
        }

        $token = wp_generate_password( 64, false );
        $default_stage_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cce_crm_stages WHERE user_id = %d ORDER BY stage_order ASC LIMIT 1", $user_id ) ) ?: 1;

		$data = array(
            'user_id'      => $user_id,
			'first_name'   => sanitize_text_field( $params['first_name'] ?? '' ),
			'last_name'    => sanitize_text_field( $params['last_name'] ?? '' ),
			'email'        => sanitize_email( $params['email'] ?? '' ),
			'phone'        => sanitize_text_field( $params['phone'] ?? '' ),
            'secure_token' => $token,
            'source'       => $source,
			'status'       => 'cold',
            'crm_stage_id' => $default_stage_id,
            'utm_source'   => $_COOKIE['cce_utm_source'] ?? '',
            'utm_medium'   => $_COOKIE['cce_utm_medium'] ?? '',
            'utm_campaign' => $_COOKIE['cce_utm_campaign'] ?? '',
            'created_at'   => current_time( 'mysql' ),
		);

		$result = $wpdb->insert( $table_name, $data );

		if ( false === $result ) {
			return $this->error( 'Failed to create lead' );
		}

		$lead_id = $wpdb->insert_id;

        // Track Conversion if in funnel
        $step_id = absint( $params['step_id'] ?? $_COOKIE['cce_active_funnel_step'] ?? 0 );
        if ( $step_id ) {
            $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}cce_funnel_steps SET conversions = conversions + 1 WHERE id = %d", $step_id ) );
        }
		$data['id'] = $lead_id;

        // Log activity
        CCE_Activity_Logger::log( $lead_id, 'optin', 'Lead captured from ' . ( $_SERVER['HTTP_REFERER'] ?? 'unknown' ) );

        // Trigger action for automation
        do_action( 'cce_lead_created', $lead_id );

		return $this->success( $data );
	}
}
