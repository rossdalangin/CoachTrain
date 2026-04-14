<?php
/**
 * Bookings Manager class.
 */
class CCE_Bookings_Manager extends CCE_REST_Controller {

	/**
	 * Check if the user has permission to perform the request.
	 */
	public function check_permission( $request ) {
		// Allow public booking creation, restrict reading to admins
		if ( WP_REST_Server::CREATABLE === $request->get_method() ) {
			return true;
		}
		return current_user_can( 'manage_options' );
	}

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

        register_rest_route( $this->namespace, '/bookings/(?P<id>\d+)', array(
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_booking' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/bookings/(?P<id>\d+)/status', array(
			array(
				'methods'             => array( WP_REST_Server::EDITABLE, WP_REST_Server::CREATABLE ),
				'callback'            => array( $this, 'update_booking_status' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

    /**
     * Delete booking.
     */
    public function delete_booking( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $wpdb->delete( "{$wpdb->prefix}cce_bookings", array( 'id' => $id, 'user_id' => $user_id ) );
        return $this->success( array( 'message' => 'Booking deleted' ) );
    }

    /**
     * Update booking status.
     */
    public function update_booking_status( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $status = sanitize_text_field( $request->get_param( 'status' ) );

        $wpdb->update( "{$wpdb->prefix}cce_bookings", array( 'status' => $status ), array( 'id' => $id, 'user_id' => $user_id ) );

        $booking = $wpdb->get_row( $wpdb->prepare( "SELECT lead_id FROM {$wpdb->prefix}cce_bookings WHERE id = %d AND user_id = %d", $id, $user_id ) );
        if ( $booking ) {
            CCE_Activity_Logger::log( $booking->lead_id, 'booking_status', 'Booking status updated to: ' . strtoupper($status) );
        }

        return $this->success( array( 'message' => 'Status updated' ) );
    }

	/**
	 * Get bookings.
	 */
	public function get_bookings( $request ) {
		global $wpdb;
        $user_id = $this->get_current_user_id();
		$bookings_table = $wpdb->prefix . 'cce_bookings';
        $leads_table = $wpdb->prefix . 'cce_leads';

		$bookings = $wpdb->get_results( $wpdb->prepare( "
            SELECT b.*, CONCAT(l.first_name, ' ', l.last_name) as lead_name
            FROM $bookings_table b
            LEFT JOIN $leads_table l ON b.lead_id = l.id
            WHERE b.user_id = %d
            ORDER BY b.start_time ASC
        ", $user_id ) );
		return $this->success( $bookings );
	}

	/**
	 * Create booking.
	 */
	public function create_booking( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'cce_bookings';

		$params = $request->get_params();
        $user_id = absint( $params['user_id'] ?? 0 );

        if ( ! $user_id ) {
            $lead_id = absint( $params['lead_id'] );
            $user_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->prefix}cce_leads WHERE id = %d", $lead_id ) );
        }

		$data = array(
            'user_id'            => $user_id,
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

		$booking_id = $wpdb->insert_id;
		$data['id'] = $booking_id;

        // Log activity
        CCE_Activity_Logger::log( $data['lead_id'], 'booking', 'New consultation booked for ' . $data['start_time'] );

        // Trigger action
        do_action( 'cce_booking_confirmed', $booking_id );

		return $this->success( $data );
	}
}
