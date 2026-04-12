<?php
/**
 * Automation Manager class.
 */
class CCE_Automation_Manager extends CCE_REST_Controller {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'cce_lead_created', array( $this, 'trigger_automation' ) );
        add_action( 'cce_booking_confirmed', array( $this, 'trigger_automation' ) );
        add_action( 'cce_payment_completed', array( $this, 'trigger_automation' ) );
        add_action( 'cce_delayed_email_event', array( $this, 'send_delayed_email' ), 10, 2 );
    }

    /**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/automation/rules', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_rules' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_rule' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/automation/rules/(?P<id>\d+)', array(
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_rule' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

    /**
     * Get automation rules.
     */
    public function get_rules( $request ) {
        global $wpdb;
        $rules = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_automation_rules ORDER BY created_at DESC" );
        return $this->success( $rules );
    }

    /**
     * Create automation rule.
     */
    public function create_rule( $request ) {
        global $wpdb;
        $params = $request->get_params();

        $wpdb->insert( "{$wpdb->prefix}cce_automation_rules", array(
            'trigger_event' => sanitize_text_field( $params['trigger_event'] ),
            'action_type'   => sanitize_text_field( $params['action_type'] ),
            'config'        => json_encode( $params['config'] ?? array() ),
            'is_active'     => 1,
        ) );

        return $this->success( array( 'id' => $wpdb->insert_id ) );
    }

    /**
     * Delete automation rule.
     */
    public function delete_rule( $request ) {
        global $wpdb;
        $wpdb->delete( "{$wpdb->prefix}cce_automation_rules", array( 'id' => absint( $request['id'] ) ) );
        return $this->success( array( 'message' => 'Rule deleted' ) );
    }

	/**
	 * Trigger automation.
	 */
	public function trigger_automation( $id ) {
        global $wpdb;
        $hook = current_action();

        $rules = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cce_automation_rules WHERE trigger_event = %s AND is_active = 1",
            $hook
        ) );

        foreach ( $rules as $rule ) {
            $this->execute_rule( $rule, $id, $hook );
        }

        // Keep legacy defaults if no rules found for simple setup
        if ( empty( $rules ) ) {
            if ( 'cce_lead_created' === $hook ) {
                $mailer = new CCE_Mailer();
                $mailer->send_welcome_email( $id );
            }
        }
	}

    /**
     * Execute specific rule.
     */
    private function execute_rule( $rule, $source_id, $hook ) {
        global $wpdb;
        $config = json_decode( $rule->config, true );

        // Resolve lead_id based on hook
        $lead_id = 0;
        if ( 'cce_lead_created' === $hook ) {
            $lead_id = $source_id;
        } elseif ( 'cce_booking_confirmed' === $hook ) {
            $lead_id = $wpdb->get_var( $wpdb->prepare( "SELECT lead_id FROM {$wpdb->prefix}cce_bookings WHERE id = %d", $source_id ) );
        } elseif ( 'cce_payment_completed' === $hook ) {
            $lead_id = $source_id; // Payment completed action already passes lead_id
        }

        if ( ! $lead_id ) return;

        switch ( $rule->action_type ) {
            case 'send_email':
                $mailer = new CCE_Mailer();
                $mailer->send_welcome_email( $lead_id );
                break;
            case 'move_stage':
                $stage_id = absint( $config['stage_id'] ?? 0 );
                if ( $stage_id ) {
                    $wpdb->update( "{$wpdb->prefix}cce_leads", array( 'crm_stage_id' => $stage_id ), array( 'id' => $lead_id ) );
                    CCE_Activity_Logger::log( $lead_id, 'stage_change', 'Lead automatically moved by automation rule: ' . $rule->id );
                }
                break;
        }
    }

    /**
     * Send delayed email.
     */
    public function send_delayed_email( $id, $type ) {
        error_log( "Sending delayed email ($type) for ID: $id" );
    }
}
