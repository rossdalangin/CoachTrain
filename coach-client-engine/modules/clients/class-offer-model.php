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
		$table_name = $wpdb->prefix . 'cce_offers';
		return $wpdb->insert( $table_name, $data );
	}

	/**
	 * Get all offers.
	 */
	public function get_all() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'cce_offers';
		return $wpdb->get_results( "SELECT * FROM $table_name WHERE is_active = 1" );
	}

    /**
     * Get single offer.
     */
    public function get( $id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cce_offers';
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ) );
    }
}
