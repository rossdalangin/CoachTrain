<?php
/**
 * Leads Manager class.
 */
class CCE_Leads_Manager extends CCE_REST_Controller {

	/**
	 * Check if the user has permission to perform the request.
	 */
	public function check_permission( $request ) {
		// Allow public lead creation, restrict reading to admins
		if ( WP_REST_Server::CREATABLE === $request->get_method() ) {
			return true;
		}
		return current_user_can( 'manage_options' );
	}

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
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_lead' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_lead' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

    /**
     * Update lead.
     */
    public function update_lead( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $params = $request->get_params();

        $data = array(
            'first_name' => sanitize_text_field( $params['first_name'] ),
            'last_name'  => sanitize_text_field( $params['last_name'] ),
            'email'       => sanitize_email( $params['email'] ),
            'phone'       => sanitize_text_field( $params['phone'] ?? '' ),
            'status'      => sanitize_text_field( $params['status'] ?? 'cold' ),
        );

        $wpdb->update( "{$wpdb->prefix}cce_leads", $data, array( 'id' => $id ) );
        CCE_Activity_Logger::log( $id, 'update', 'Lead information updated.' );

        return $this->success( array( 'message' => 'Lead updated' ) );
    }

    /**
     * Delete lead.
     */
    public function delete_lead( $request ) {
        global $wpdb;
        $lead_id = absint( $request['id'] );
        $wpdb->delete( "{$wpdb->prefix}cce_leads", array( 'id' => $lead_id ) );
        return $this->success( array( 'message' => 'Lead deleted' ) );
    }

    /**
     * Update lead status (tag).
     */
    public function update_lead_status( $request ) {
        global $wpdb;
        $lead_id = absint( $request['id'] );
        $status = sanitize_text_field( $request->get_param( 'status' ) );

        $wpdb->update( "{$wpdb->prefix}cce_leads", array( 'status' => $status ), array( 'id' => $lead_id ) );

        CCE_Activity_Logger::log( $lead_id, 'status_change', 'Lead tag updated to: ' . $status );

        return $this->success( array( 'message' => 'Status updated' ) );
    }

	/**
	 * Get leads.
	 */
	public function get_leads( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'cce_leads';
		$leads = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC" );
		return $this->success( $leads );
	}

	/**
	 * Create lead.
	 */
	public function create_lead( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'cce_leads';

		$params = $request->get_params();
        $source = $_COOKIE['cce_funnel_source'] ?? 'Direct';
        if ( is_numeric( $source ) ) {
            $source = $wpdb->get_var( $wpdb->prepare( "SELECT title FROM {$wpdb->prefix}cce_funnels WHERE id = %d", $source ) ) ?: 'Direct';
        }

        $token = bin2hex( random_bytes( 32 ) );
		$data = array(
			'first_name'   => sanitize_text_field( $params['first_name'] ),
			'last_name'    => sanitize_text_field( $params['last_name'] ),
			'email'        => sanitize_email( $params['email'] ),
			'phone'        => sanitize_text_field( $params['phone'] ),
            'secure_token' => $token,
            'source'       => $source,
			'status'       => 'cold',
            'crm_stage_id' => 1, // Default to 'New' stage
		);

		$result = $wpdb->insert( $table_name, $data );

		if ( false === $result ) {
			return $this->error( 'Failed to create lead' );
		}

		$lead_id = $wpdb->insert_id;
		$data['id'] = $lead_id;

        // Log activity
        CCE_Activity_Logger::log( $lead_id, 'optin', 'Lead captured from ' . ( $_SERVER['HTTP_REFERER'] ?? 'unknown' ) );

        // Trigger action for automation
        do_action( 'cce_lead_created', $lead_id );

		return $this->success( $data );
	}
}
