<?php
/**
 * The core plugin class.
 */
class Coach_Client_Engine {

	/**
	 * The loader that's responsible for maintaining and registering all hooks.
	 */
	protected $loader;

	/**
	 * Define the core functionality of the plugin.
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cce-rest-controller.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cce-webhooks-controller.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cce-mailer.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cce-public.php';

        // Modules
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/leads/class-leads-manager.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/crm/class-crm-manager.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/bookings/class-bookings-manager.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/clients/class-offer-model.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/clients/class-checkout-manager.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/funnels/class-funnels-manager.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/analytics/class-analytics-manager.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/portal/class-portal-manager.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/proof/class-proof-manager.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'modules/automation/class-automation-manager.php';
	}

	/**
	 * Register all of the hooks related to the admin area functionality.
	 */
	private function define_admin_hooks() {
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality.
	 */
	private function define_public_hooks() {
		$public = new CCE_Public();
        add_action( 'init', array( $public, 'init' ) );
	}

	/**
	 * Add admin menu.
	 */
	public function add_plugin_admin_menu() {
		add_menu_page(
			'Coach Client Engine',
			'Coach Engine',
			'manage_options',
			'coach-client-engine',
			array( $this, 'display_plugin_admin_page' ),
			'dashicons-performance',
			25
		);
	}

	/**
	 * Display the admin page.
	 */
	public function display_plugin_admin_page() {
		echo '<div id="cce-admin-app"></div>';
	}

    /**
     * Enqueue admin assets.
     */
    public function enqueue_admin_assets( $hook ) {
        if ( 'toplevel_page_coach-client-engine' !== $hook ) {
            return;
        }

        $asset_path = plugin_dir_path( dirname( __FILE__ ) ) . 'build/index.asset.php';
        if ( ! file_exists( $asset_path ) ) {
            return;
        }

        $asset_file = include( $asset_path );

        wp_enqueue_script(
            'cce-admin-js',
            plugin_dir_url( dirname( __FILE__ ) ) . 'build/index.js',
            $asset_file['dependencies'],
            $asset_file['version'],
            true
        );

        wp_enqueue_style(
            'cce-admin-css',
            plugin_dir_url( dirname( __FILE__ ) ) . 'admin/css/cce-admin.css',
            array(),
            CCE_VERSION
        );

        wp_localize_script( 'cce-admin-js', 'cceData', array(
            'root'  => esc_url_raw( rest_url() ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
            'isPro' => $this->is_pro(),
        ) );
    }

	/**
	 * Check REST permission.
	 */
	public function check_rest_permission() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		// Initialization logic
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	/**
	 * Check if the plugin has a valid pro license.
	 */
	public function is_pro() {
		$license_key = get_option( 'cce_license_key' );
		// Simple mock check for demonstration
		return ! empty( $license_key ) && strpos( $license_key, 'PRO-' ) === 0;
	}

    /**
     * Register REST API routes.
     */
    public function register_rest_routes() {
        register_rest_route( 'cce/v1', '/settings', array(
            array(
                'methods'             => 'GET',
                'callback'            => function() {
                    return array(
                        'success' => true,
                        'data'    => array(
                            'license_key'    => get_option( 'cce_license_key', '' ),
                            'stripe_api_key' => get_option( 'cce_stripe_api_key', '' ),
                            'paypal_client_id' => get_option( 'cce_paypal_client_id', '' ),
                            'onboarding_step' => (int) get_option( 'cce_onboarding_step', 1 ),
                        )
                    );
                },
                'permission_callback' => array( $this, 'check_rest_permission' ),
            ),
            array(
                'methods'             => 'POST',
                'callback'            => function( $request ) {
                    $params = $request->get_params();
                    if ( isset( $params['license_key'] ) ) {
                        update_option( 'cce_license_key', sanitize_text_field( $params['license_key'] ) );
                    }
                    if ( isset( $params['stripe_api_key'] ) ) {
                        update_option( 'cce_stripe_api_key', sanitize_text_field( $params['stripe_api_key'] ) );
                    }
                    if ( isset( $params['onboarding_step'] ) ) {
                        update_option( 'cce_onboarding_step', absint( $params['onboarding_step'] ) );
                    }
                    return array( 'success' => true );
                },
                'permission_callback' => array( $this, 'check_rest_permission' ),
            )
        ) );

        $leads_manager = new CCE_Leads_Manager();
        $leads_manager->register_routes();

        $crm_manager = new CCE_CRM_Manager();
        $crm_manager->register_routes();

        $bookings_manager = new CCE_Bookings_Manager();
        $bookings_manager->register_routes();

        $funnels_manager = new CCE_Funnels_Manager();
        $funnels_manager->register_routes();

        $analytics_manager = new CCE_Analytics_Manager();
        $analytics_manager->register_routes();

        $checkout_manager = new CCE_Checkout_Manager();
        $checkout_manager->register_routes();

        $portal_manager = new CCE_Portal_Manager();
        $portal_manager->register_routes();

        $proof_manager = new CCE_Proof_Manager();
        $proof_manager->register_routes();

        $automation_manager = new CCE_Automation_Manager();
        $automation_manager->register_routes();

        $webhooks_controller = new CCE_Webhooks_Controller();
        $webhooks_controller->register_routes();
    }
}
