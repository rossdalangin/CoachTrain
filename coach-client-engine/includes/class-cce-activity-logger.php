<?php
/**
 * Activity Logger.
 */
class CCE_Activity_Logger {

	/**
	 * Log an activity.
	 */
	public static function log( $lead_id, $type, $description ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'cce_activity_log';

		return $wpdb->insert( $table_name, array(
			'lead_id'       => absint( $lead_id ),
			'activity_type' => sanitize_text_field( $type ),
			'description'   => sanitize_text_field( $description ),
		) );
	}

    /**
     * Get logs for a lead.
     */
    public static function get_logs( $lead_id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cce_activity_log';
        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE lead_id = %d ORDER BY created_at DESC", $lead_id ) );
    }
}
