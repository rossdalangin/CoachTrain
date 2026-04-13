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
        $tasks = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_onboarding_tasks ORDER BY task_order ASC" );
        return $this->success( $tasks );
    }

    /**
     * Create onboarding task.
     */
    public function create_task( $request ) {
        global $wpdb;
        $params = $request->get_params();
        $order = (int) $wpdb->get_var( "SELECT MAX(task_order) FROM {$wpdb->prefix}cce_onboarding_tasks" ) + 1;

        $wpdb->insert( "{$wpdb->prefix}cce_onboarding_tasks", array(
            'task_name'  => sanitize_text_field( $params['task_name'] ),
            'description' => sanitize_textarea_field( $params['description'] ?? '' ),
            'task_order' => $order
        ) );

        return $this->success( array( 'id' => $wpdb->insert_id ) );
    }

    /**
     * Delete onboarding task.
     */
    public function delete_task( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $wpdb->delete( "{$wpdb->prefix}cce_onboarding_tasks", array( 'id' => $id ) );
        return $this->success( array( 'message' => 'Onboarding task deleted' ) );
    }
}
