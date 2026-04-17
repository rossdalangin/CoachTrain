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
            'Mastery Hub' => 'cce-hub',
            'Leads'      => 'cce-leads',
            'Bookings'   => 'cce-bookings',
            'Clients'    => 'cce-clients',
            'Funnels'    => 'cce-funnels',
            'Automation' => 'cce-automation',
            'CRM'        => 'cce-crm',
            'Portal'     => 'cce-portal',
            'Proof'      => 'cce-proof',
            'Analytics'  => 'cce-analytics',
            'Templates'  => 'cce-templates',
            'Payments'   => 'cce-payments',
            'Settings'   => 'cce-settings',
        ];

        $engine = new Coach_Client_Engine();
        $is_pro = $engine->is_pro();

        foreach ( $pages as $title => $slug ) {
            $menu_title = $title;
            if ( in_array($slug, ['cce-automation', 'cce-analytics']) && !$is_pro ) {
                $menu_title .= ' (PRO)';
            }

            add_submenu_page(
                'coach-client-engine',
                $title,
                $menu_title,
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
     * Render Mastery Hub.
     */
    public function render_mastery_hub() {
        include plugin_dir_path( __FILE__ ) . '../admin/partials/hub.php';
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

    /**
     * Render Automation.
     */
    public function render_automation() {
        include plugin_dir_path( __FILE__ ) . '../admin/partials/automation.php';
    }

    // Additional render methods for other pages...

    /**
     * Render Bookings.
     */
    public function render_bookings() {
        include plugin_dir_path( __FILE__ ) . '../admin/partials/bookings.php';
    }

    /**
     * Render Clients.
     */
    public function render_clients() {
        include plugin_dir_path( __FILE__ ) . '../admin/partials/clients.php';
    }

    /**
     * Render Funnels.
     */
    public function render_funnels() {
        include plugin_dir_path( __FILE__ ) . '../admin/partials/funnels.php';
    }

    /**
     * Render CRM.
     */
    public function render_crm() {
        include plugin_dir_path( __FILE__ ) . '../admin/partials/crm.php';
    }

    /**
     * Render Portal.
     */
    public function render_portal() {
        include plugin_dir_path( __FILE__ ) . '../admin/partials/portal.php';
    }

    /**
     * Render Proof.
     */
    public function render_proof() {
        include plugin_dir_path( __FILE__ ) . '../admin/partials/proof.php';
    }

    /**
     * Render Analytics.
     */
    public function render_analytics() {
        include plugin_dir_path( __FILE__ ) . '../admin/partials/analytics.php';
    }

    /**
     * Render Templates.
     */
    public function render_templates() {
        include plugin_dir_path( __FILE__ ) . '../admin/partials/templates.php';
    }

    /**
     * Render Payments.
     */
    public function render_payments() {
        include plugin_dir_path( __FILE__ ) . '../admin/partials/payments.php';
    }

    /**
     * Enqueue admin assets.
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'cce-' ) === false && strpos( $hook, 'coach-client-engine' ) === false ) {
            return;
        }
        wp_enqueue_style( 'cce-admin-classic', plugin_dir_url( __FILE__ ) . '../admin/css/cce-admin-classic.css', array(), CCE_VERSION );
        wp_enqueue_script( 'cce-admin-js', plugin_dir_url( __FILE__ ) . '../admin/js/cce-admin.js', array( 'jquery', 'jquery-ui-sortable' ), CCE_VERSION, true );

        wp_localize_script( 'cce-admin-js', 'cceAdmin', array(
            'restUrl' => esc_url_raw( rest_url( 'cce/v1/' ) ),
            'nonce'   => wp_create_nonce( 'wp_rest' ),
        ) );
    }
}
