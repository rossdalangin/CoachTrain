<?php
/**
 * Admin handler for Coach Client Engine.
 */
class CCE_Admin {

	/**
	 * Main menu hook.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'register_menus' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Register admin menus.
	 */
	public function register_menus() {
		add_menu_page(
			'Coach Engine',
			'Coach Engine',
			'manage_options',
			'coach-client-engine',
			array( $this, 'render_dashboard' ),
			'dashicons-performance',
			25
		);

        $pages = [
            'Leads'      => 'cce-leads',
            'Bookings'   => 'cce-bookings',
            'Clients'    => 'cce-clients',
            'Funnels'    => 'cce-funnels',
            'Automation' => 'cce-automation',
            'CRM'        => 'cce-crm',
            'Portal'     => 'cce-portal',
            'Proof'      => 'cce-proof',
            'Analytics'  => 'cce-analytics',
            'Settings'   => 'cce-settings',
        ];

        foreach ( $pages as $title => $slug ) {
            add_submenu_page(
                'coach-client-engine',
                $title,
                $title,
                'manage_options',
                $slug,
                array( $this, 'render_' . strtolower( str_replace( ' ', '_', $title ) ) )
            );
        }
	}

    /**
     * Render Dashboard.
     */
    public function render_dashboard() {
        include plugin_dir_path( __FILE__ ) . '../admin/partials/dashboard.php';
    }

    /**
     * Render Leads.
     */
    public function render_leads() {
        include plugin_dir_path( __FILE__ ) . '../admin/partials/leads.php';
    }

    /**
     * Render Settings.
     */
    public function render_settings() {
        include plugin_dir_path( __FILE__ ) . '../admin/partials/settings.php';
    }

    // Additional render methods for other pages...

    /**
     * Enqueue admin assets.
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'cce-' ) === false && strpos( $hook, 'coach-client-engine' ) === false ) {
            return;
        }
        wp_enqueue_style( 'cce-admin-classic', plugin_dir_url( __FILE__ ) . '../admin/css/cce-admin-classic.css', array(), CCE_VERSION );
    }
}
