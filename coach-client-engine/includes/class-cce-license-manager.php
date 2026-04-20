<?php
/**
 * License Manager class.
 */
class CCE_License_Manager extends CCE_REST_Controller {

    /**
     * Register routes.
     */
    public function register_routes() {
        register_rest_route( $this->namespace, '/license/activate', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'activate_license' ),
                'permission_callback' => array( $this, 'check_permission' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/license/status', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_license_status' ),
                'permission_callback' => array( $this, 'check_permission' ),
            ),
        ) );
    }

    /**
     * Activate a license key.
     */
    public function activate_license( $request ) {
        global $wpdb;
        $license_key = sanitize_text_field( $request->get_param( 'license_key' ) );
        $user_id = $this->get_current_user_id();

        if ( empty( $license_key ) ) {
            return $this->error( 'License key is required' );
        }

        // Validate checksum using secret salt (same as issuer tool)
        $secret_salt = 'cce_ultra_secret_salt_12345';
        $parts = explode('-', $license_key);
        $is_valid_format = false;
        if (count($parts) === 3 && $parts[0] === 'PRO') {
            $random_part = strtolower($parts[1]);
            $checksum = strtolower($parts[2]);
            $expected_checksum = substr(md5($random_part . $secret_salt), 0, 4);
            $is_valid_format = ($checksum === $expected_checksum);
        }

        if ( ! $is_valid_format ) {
            return $this->error( 'Invalid license key format or checksum' );
        }

        // Check if license exists and is not assigned to another user
        $existing = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cce_licenses WHERE license_key = %s",
            $license_key
        ) );

        if ( ! $existing ) {
             $wpdb->insert( "{$wpdb->prefix}cce_licenses", array(
                'license_key' => $license_key,
                'user_id'     => $user_id,
                'status'      => 'active'
            ) );
            update_option( 'cce_license_key', $license_key );
            return $this->success( array( 'message' => 'License activated successfully!' ) );
        }

        if ( $existing->user_id != 0 && $existing->user_id != $user_id ) {
            return $this->error( 'License already in use by another account' );
        }

        $wpdb->update(
            "{$wpdb->prefix}cce_licenses",
            array( 'user_id' => $user_id, 'status' => 'active' ),
            array( 'id' => $existing->id )
        );

        update_option( 'cce_license_key', $license_key );

        return $this->success( array( 'message' => 'License activated successfully!' ) );
    }

    /**
     * Get current license status for the user.
     */
    public function get_license_status( $request ) {
        global $wpdb;
        $user_id = $this->get_current_user_id();

        $license = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cce_licenses WHERE user_id = %d",
            $user_id
        ) );

        if ( ! $license ) {
            return $this->success( array(
                'is_pro' => false,
                'status' => 'none'
            ) );
        }

        return $this->success( array(
            'is_pro'      => ( $license->status === 'active' ),
            'status'      => $license->status,
            'license_key' => $this->mask_license( $license->license_key ),
            'created_at'  => $license->created_at
        ) );
    }

    private function mask_license( $key ) {
        if ( strlen( $key ) < 8 ) return '****';
        return substr( $key, 0, 4 ) . '...' . substr( $key, -4 );
    }

    /**
     * Check if a specific feature is enabled for the user.
     */
    public static function check_feature( $feature ) {
        $is_pro = self::is_user_pro();
        if ( $is_pro ) return true;

        $pro_features = ['automation_rules', 'advanced_analytics', 'broadcasts', 'webhooks', 'conditional_funnels'];
        return ! in_array( $feature, $pro_features );
    }

    /**
     * Static helper to check if current user is pro.
     */
    public static function is_user_pro( $user_id = null ) {
        if ( ! $user_id ) $user_id = get_current_user_id();
        if ( ! $user_id ) return false;

        global $wpdb;
        $status = $wpdb->get_var( $wpdb->prepare(
            "SELECT status FROM {$wpdb->prefix}cce_licenses WHERE user_id = %d",
            $user_id
        ) );

        return ( $status === 'active' );
    }
}
