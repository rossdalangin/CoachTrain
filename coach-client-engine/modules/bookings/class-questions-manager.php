<?php
/**
 * Questions Manager class.
 */
class CCE_Questions_Manager extends CCE_REST_Controller {

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/bookings/questions', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_questions' ),
				'permission_callback' => '__return_true', // Public for form
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_question' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/bookings/questions/(?P<id>\d+)', array(
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_question' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

    /**
     * Get questions.
     */
    public function get_questions( $request ) {
        global $wpdb;
        $questions = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_questions ORDER BY question_order ASC" );
        return $this->success( $questions );
    }

    /**
     * Create question.
     */
    public function create_question( $request ) {
        global $wpdb;
        $params = $request->get_params();
        $order = (int) $wpdb->get_var( "SELECT MAX(question_order) FROM {$wpdb->prefix}cce_questions" ) + 1;

        $wpdb->insert( "{$wpdb->prefix}cce_questions", array(
            'question_text' => sanitize_text_field( $params['question_text'] ),
            'question_type' => sanitize_text_field( $params['question_type'] ?? 'text' ),
            'is_required'   => (int) ($params['is_required'] ?? 1),
            'question_order' => $order
        ) );

        return $this->success( array( 'id' => $wpdb->insert_id ) );
    }

    /**
     * Delete question.
     */
    public function delete_question( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $wpdb->delete( "{$wpdb->prefix}cce_questions", array( 'id' => $id ) );
        return $this->success( array( 'message' => 'Question deleted' ) );
    }
}
