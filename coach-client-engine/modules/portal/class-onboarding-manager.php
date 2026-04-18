<?php
/**
 * Onboarding Manager class.
 */
class CCE_Onboarding_Manager extends CCE_REST_Controller {

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/portal/onboarding-tasks', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_tasks' ),
				'permission_callback' => '__return_true', // Public for portal
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_task' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/portal/onboarding-tasks/(?P<id>\d+)', array(
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_task' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

    /**
     * Get onboarding tasks.
     */
    public function get_tasks( $request ) {
        global $wpdb;
        $user_id = 0;
        $token = $_COOKIE['cce_lead_token'] ?? '';
        if ( $token ) {
            $user_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->prefix}cce_leads WHERE secure_token = %s", $token ) );
        }
        if ( ! $user_id && current_user_can( 'manage_options' ) ) {
            $user_id = get_current_user_id();
        }

        $tasks = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_onboarding_tasks WHERE user_id = %d ORDER BY task_order ASC", $user_id ) );
        return $this->success( $tasks );
    }

    /**
     * Create onboarding task.
     */
    public function create_task( $request ) {
        global $wpdb;
        $user_id = $this->get_current_user_id();
        $params = $this->get_params( $request );
        $order = (int) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(task_order) FROM {$wpdb->prefix}cce_onboarding_tasks WHERE user_id = %d", $user_id ) ) + 1;

        $result = $wpdb->insert( "{$wpdb->prefix}cce_onboarding_tasks", array(
            'user_id'     => $user_id,
            'task_name'   => sanitize_text_field( $params['task_name'] ),
            'description' => sanitize_textarea_field( $params['description'] ?? '' ),
            'task_order'  => $order,
            'created_at'  => current_time( 'mysql' ),
        ) );

        if ( false === $result ) return $this->error( 'Failed to create task' );

        return $this->success( array( 'id' => $wpdb->insert_id ) );
    }

    /**
     * Delete onboarding task.
     */
    public function delete_task( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $wpdb->delete( "{$wpdb->prefix}cce_onboarding_tasks", array( 'id' => $id, 'user_id' => $user_id ) );
        return $this->success( array( 'message' => 'Onboarding task deleted' ) );
    }
}
