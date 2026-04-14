<?php
/**
 * Offer data model.
 */
class CCE_Offer_Model {

	/**
	 * Create an offer.
	 */
	public function create( $data ) {
		global $wpdb;
        if ( ! isset( $data['user_id'] ) ) {
            $data['user_id'] = get_current_user_id();
        }
		$table_name = $wpdb->prefix . 'cce_offers';
		return $wpdb->insert( $table_name, $data );
	}

	/**
	 * Get all offers.
	 */
	public function get_all() {
		global $wpdb;
        $user_id = get_current_user_id();
		$table_name = $wpdb->prefix . 'cce_offers';
		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE is_active = 1 AND user_id = %d", $user_id ) );
	}

    /**
     * Get single offer.
     */
    public function get( $id ) {
        global $wpdb;
        $user_id = get_current_user_id();
        $table_name = $wpdb->prefix . 'cce_offers';
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d AND user_id = %d", $id, $user_id ) );
    }
}
