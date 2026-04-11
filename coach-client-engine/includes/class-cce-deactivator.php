<?php
/**
 * Fired during plugin deactivation.
 */
class CCE_Deactivator {

	/**
	 * Logic to run on deactivation.
	 */
	public static function deactivate() {
        // Typically we don't drop tables on deactivation to preserve data.
	}
}
