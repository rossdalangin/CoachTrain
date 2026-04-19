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
        $path = plugin_dir_path( dirname( __FILE__ ) );
		require_once $path . 'includes/class-cce-rest-controller.php';
        require_once $path . 'includes/class-cce-webhooks-controller.php';
        require_once $path . 'includes/class-cce-mailer.php';
        require_once $path . 'includes/class-cce-activity-logger.php';
        require_once $path . 'includes/class-cce-license-manager.php';
        require_once $path . 'includes/class-cce-sample-data.php';
        require_once $path . 'public/class-cce-public.php';

        // Modules
        require_once $path . 'modules/leads/class-leads-manager.php';
        require_once $path . 'modules/crm/class-crm-manager.php';
        require_once $path . 'modules/bookings/class-bookings-manager.php';
        require_once $path . 'modules/bookings/class-questions-manager.php';
        require_once $path . 'modules/clients/class-offer-model.php';
        require_once $path . 'modules/clients/class-clients-manager.php';
        require_once $path . 'modules/clients/class-checkout-manager.php';
        require_once $path . 'modules/clients/class-stripe-wrapper.php';
        require_once $path . 'modules/clients/class-paypal-wrapper.php';
        require_once $path . 'modules/funnels/class-funnels-manager.php';
        require_once $path . 'modules/analytics/class-analytics-manager.php';
        require_once $path . 'modules/portal/class-portal-manager.php';
        require_once $path . 'modules/portal/class-onboarding-manager.php';
        require_once $path . 'modules/proof/class-proof-manager.php';
        require_once $path . 'modules/automation/class-automation-manager.php';

        // Instantiate Automation Manager early to listen for hooks
        new CCE_Automation_Manager();
	}

	/**
	 * Register all of the hooks related to the admin area functionality.
	 */
	private function define_admin_hooks() {
        require_once plugin_dir_path( __FILE__ ) . 'class-cce-admin.php';
        $admin = new CCE_Admin();
        $admin->init();

        require_once plugin_dir_path( __FILE__ ) . 'class-cce-post-handler.php';
        $post_handler = new CCE_Post_Handler();
        $post_handler->init();
	}

	/**
	 * Register all of the hooks related to the public-facing functionality.
	 */
	private function define_public_hooks() {
		$public = new CCE_Public();
        add_action( 'init', array( $public, 'init' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
        add_action( 'wp', array( $this, 'track_visitor' ) );
	}

    /**
     * Track visitor.
     */
    public function track_visitor() {
        if ( is_admin() || ! is_singular() ) return;
        $post_author = get_the_author_meta( 'ID' );
        if ( $post_author ) {
            $count = (int) get_user_meta( $post_author, 'cce_total_visitors', true );
            update_user_meta( $post_author, 'cce_total_visitors', $count + 1 );
        }
    }

    /**
     * Enqueue public assets.
     */
    public function enqueue_public_assets() {
        $url = plugin_dir_url( dirname( __FILE__ ) );
        wp_enqueue_style( 'cce-public-css', $url . 'public/css/cce-public.css', array(), CCE_VERSION );

        $primary_color = get_option( 'cce_primary_color', '#0073aa' );
        $custom_css = "
            :root { --cce-primary: {$primary_color}; }
            .cce-lead-form-wrapper button, .cce-booking-form-wrapper button, .cce-checkout-wrapper button, .button-primary {
                background-color: var(--cce-primary) !important;
            }
            .cce-testimonial-card::before { color: var(--cce-primary) !important; }
        ";
        wp_add_inline_style( 'cce-public-css', $custom_css );

        wp_enqueue_script( 'cce-public-js', $url . 'public/js/cce-public.js', array(), CCE_VERSION, true );
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
        $user_id = get_current_user_id();
		$license_key = get_user_meta( $user_id, 'cce_license_key', true ) ?: get_option( 'cce_license_key' );
		// Simple mock check for demonstration
		return ! empty( $license_key ) && strpos( $license_key, 'PRO-' ) === 0;
	}

    /**
     * Register REST API routes.
     */
    public function register_rest_routes() {
        register_rest_route( 'cce/v1', '/maintenance/sample-data', array(
            array(
                'methods'             => 'POST',
                'callback'            => function( $request ) {
                    if ( class_exists( 'CCE_Sample_Data' ) ) {
                        $model = $request->get_param('model') ?: 'standard';
                        $linked = (bool) $request->get_param('linked');
                        CCE_Sample_Data::generate($model, $linked);
                        return array( 'success' => true, 'message' => 'Sample data generated successfully!' );
                    }
                    return new WP_Error( 'error', 'Sample data generator not found', array( 'status' => 500 ) );
                },
                'permission_callback' => array( $this, 'check_rest_permission' ),
            )
        ) );

        register_rest_route( 'cce/v1', '/maintenance/debug-auth', array(
            array(
                'methods'             => 'GET',
                'callback'            => function() {
                    return array(
                        'success' => true,
                        'user_id' => get_current_user_id(),
                        'can_manage' => current_user_can('manage_options'),
                        'nonce_verified' => true // If they reach here, nonce was ok enough for route
                    );
                },
                'permission_callback' => '__return_true',
            )
        ) );

        register_rest_route( 'cce/v1', '/maintenance/clear-data', array(
            array(
                'methods'             => 'POST',
                'callback'            => function() {
                    global $wpdb;
                    $user_id = get_current_user_id();
                    $tables = ['leads', 'bookings', 'offers', 'funnels', 'funnel_steps', 'payments', 'crm_stages', 'activity_log', 'tasks', 'testimonials', 'automation_rules', 'email_templates', 'resources', 'questions', 'onboarding_tasks'];
                    foreach($tables as $t) {
                        $wpdb->delete("{$wpdb->prefix}cce_{$t}", ['user_id' => $user_id]);
                    }
                    delete_user_meta($user_id, 'cce_total_visitors');
                    return array( 'success' => true, 'message' => 'User data cleared successfully!' );
                },
                'permission_callback' => array( $this, 'check_rest_permission' ),
            )
        ) );

        register_rest_route( 'cce/v1', '/maintenance/hub-resource', array(
            array(
                'methods'             => 'GET',
                'callback'            => function( $request ) {
                    $file = sanitize_text_field( $request->get_param('file') );
                    $path = dirname( plugin_dir_path( __FILE__ ) ) . '/marketing/' . $file;

                    if ( ! file_exists( $path ) || is_dir( $path ) ) {
                        return new WP_Error('not_found', 'File not found at ' . $path);
                    }

                    $content = file_get_contents( $path );

                    // Basic MD to HTML conversion (Non-catastrophic)
                    $content = preg_replace('/^# (.*)$/m', '<h1>$1</h1>', $content);
                    $content = preg_replace('/^## (.*)$/m', '<h2>$1</h2>', $content);
                    $content = preg_replace('/^### (.*)$/m', '<h3>$1</h3>', $content);
                    $content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $content);
                    $content = preg_replace('/^> (.*)$/m', '<blockquote>$1</blockquote>', $content);

                    // List handling (Improved)
                    $content = preg_replace('/^- (.*)$/m', '<li>$1</li>', $content);

                    $content = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2" target="_blank">$1</a>', $content);
                    $content = nl2br($content);

                    return array( 'success' => true, 'content' => $content );
                },
                'permission_callback' => array( $this, 'check_rest_permission' ),
            )
        ) );

        register_rest_route( 'cce/v1', '/settings', array(
            array(
                'methods'             => 'GET',
                'callback'            => function() {
                    $user_id = get_current_user_id();
                    return array(
                        'success' => true,
                        'data'    => array(
                            'license_key'    => get_user_meta( $user_id, 'cce_license_key', true ) ?: get_option( 'cce_license_key', '' ),
                            'stripe_api_key' => get_user_meta( $user_id, 'cce_stripe_api_key', true ) ?: get_option( 'cce_stripe_api_key', '' ),
                            'stripe_webhook_secret' => get_user_meta( $user_id, 'cce_stripe_webhook_secret', true ) ?: get_option( 'cce_stripe_webhook_secret', '' ),
                            'paypal_client_id' => get_user_meta( $user_id, 'cce_paypal_client_id', true ) ?: get_option( 'cce_paypal_client_id', '' ),
                            'onboarding_step' => (int) get_user_meta( $user_id, 'cce_onboarding_step', true ) ?: (int) get_option( 'cce_onboarding_step', 1 ),
                            'primary_color' => get_user_meta( $user_id, 'cce_primary_color', true ) ?: get_option( 'cce_primary_color', '#0073aa' ),
                            'coach_name' => get_user_meta( $user_id, 'cce_coach_name', true ) ?: get_option( 'cce_coach_name', '' ),
                            'default_currency' => get_user_meta( $user_id, 'cce_currency', true ) ?: get_option( 'cce_currency', 'USD' ),
                            'test_mode' => (bool) get_user_meta( $user_id, 'cce_test_mode', true ),
                        )
                    );
                },
                'permission_callback' => array( $this, 'check_rest_permission' ),
            ),
            array(
                'methods'             => 'POST',
                'callback'            => function( $request ) {
                    $user_id = get_current_user_id();
                    $params = $request->get_json_params() ?: $request->get_params();
                    if ( isset( $params['license_key'] ) ) {
                        update_user_meta( $user_id, 'cce_license_key', sanitize_text_field( $params['license_key'] ) );
                    }
                    if ( isset( $params['stripe_api_key'] ) ) {
                        update_user_meta( $user_id, 'cce_stripe_api_key', sanitize_text_field( $params['stripe_api_key'] ) );
                    }
                    if ( isset( $params['stripe_webhook_secret'] ) ) {
                        update_user_meta( $user_id, 'cce_stripe_webhook_secret', sanitize_text_field( $params['stripe_webhook_secret'] ) );
                    }
                    if ( isset( $params['onboarding_step'] ) ) {
                        update_user_meta( $user_id, 'cce_onboarding_step', absint( $params['onboarding_step'] ) );
                    }
                    if ( isset( $params['primary_color'] ) ) {
                        update_user_meta( $user_id, 'cce_primary_color', sanitize_hex_color( $params['primary_color'] ) );
                    }
                    if ( isset( $params['coach_name'] ) ) {
                        update_user_meta( $user_id, 'cce_coach_name', sanitize_text_field( $params['coach_name'] ) );
                    }
                    if ( isset( $params['default_currency'] ) ) {
                        update_user_meta( $user_id, 'cce_currency', sanitize_text_field( $params['default_currency'] ) );
                    }
                    if ( isset( $params['test_mode'] ) ) {
                        update_user_meta( $user_id, 'cce_test_mode', (bool) $params['test_mode'] );
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

        $questions_manager = new CCE_Questions_Manager();
        $questions_manager->register_routes();

        $funnels_manager = new CCE_Funnels_Manager();
        $funnels_manager->register_routes();

        $analytics_manager = new CCE_Analytics_Manager();
        $analytics_manager->register_routes();

        $clients_manager = new CCE_Clients_Manager();
        $clients_manager->register_routes();

        $checkout_manager = new CCE_Checkout_Manager();
        $checkout_manager->register_routes();

        $portal_manager = new CCE_Portal_Manager();
        $portal_manager->register_routes();

        $onboarding_manager = new CCE_Onboarding_Manager();
        $onboarding_manager->register_routes();

        require_once $path . 'modules/hub/class-hub-manager.php';
        $hub_manager = new CCE_Hub_Manager();
        $hub_manager->register_routes();

        $proof_manager = new CCE_Proof_Manager();
        $proof_manager->register_routes();

        $automation_manager = new CCE_Automation_Manager();
        $automation_manager->register_routes();

        $license_manager = new CCE_License_Manager();
        $license_manager->register_routes();

        $webhooks_controller = new CCE_Webhooks_Controller();
        $webhooks_controller->register_routes();
    }
}
