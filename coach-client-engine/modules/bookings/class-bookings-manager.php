<?php
/**
 * Bookings Manager class.
 */
class CCE_Bookings_Manager extends CCE_REST_Controller {

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/bookings', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_bookings' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_booking' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

	/**
	 * Get bookings.
	 */
	public function get_bookings( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'cce_bookings';
		$bookings = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY start_time ASC" );
		return $this->success( $bookings );
	}

	/**
	 * Create booking.
	 */
	public function create_booking( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'cce_bookings';

		$params = $request->get_params();

		$data = array(
			'lead_id'            => absint( $params['lead_id'] ),
			'start_time'         => sanitize_text_field( $params['start_time'] ),
			'end_time'           => sanitize_text_field( $params['end_time'] ),
			'timezone'           => sanitize_text_field( $params['timezone'] ),
			'status'             => 'pending',
			'questionnaire_data' => json_encode( $params['questionnaire'] ?? array() ),
		);

		$result = $wpdb->insert( $table_name, $data );

		if ( false === $result ) {
			return $this->error( 'Failed to create booking' );
		}

		$data['id'] = $wpdb->insert_id;
		return $this->success( $data );
	}
}
