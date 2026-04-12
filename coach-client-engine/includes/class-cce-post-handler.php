<?php
/**
 * Processes form submissions for the admin area.
 */
class CCE_Post_Handler {

	public function init() {
		add_action( 'admin_post_cce_save_offer', array( $this, 'save_offer' ) );
	}

	/**
	 * Save coaching offer.
	 */
	public function save_offer() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Unauthorized' );
		}

		check_admin_referer( 'cce_save_offer_nonce' );

		global $wpdb;
		$data = array(
			'title' => sanitize_text_field( $_POST['title'] ),
			'price' => (float) $_POST['price'],
			'type'  => sanitize_text_field( $_POST['type'] ),
		);

		$wpdb->insert( "{$wpdb->prefix}cce_offers", $data );

		wp_redirect( admin_url( 'admin.php?page=cce-clients&message=1' ) );
		exit;
	}
}
