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
        add_action( 'cce_lead_stage_changed', array( $this, 'trigger_automation' ), 10, 2 );
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
            array(
				'methods'             => array( WP_REST_Server::EDITABLE, WP_REST_Server::CREATABLE ),
				'callback'            => array( $this, 'update_rule' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/automation/templates', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_templates' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_template' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/automation/templates/(?P<id>\d+)', array(
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_template' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
            array(
				'methods'             => array( WP_REST_Server::EDITABLE, WP_REST_Server::CREATABLE ),
				'callback'            => array( $this, 'update_template' ),
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
     * Update email template.
     */
    public function update_template( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $params = $request->get_params();

        $wpdb->update( "{$wpdb->prefix}cce_email_templates", array(
            'name'    => sanitize_text_field( $params['name'] ),
            'subject' => sanitize_text_field( $params['subject'] ),
            'content' => wp_kses_post( $params['content'] ),
        ), array( 'id' => $id ) );

        return $this->success( array( 'message' => 'Template updated' ) );
    }

    /**
     * Delete email template.
     */
    public function delete_template( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $wpdb->delete( "{$wpdb->prefix}cce_email_templates", array( 'id' => $id ) );
        return $this->success( array( 'message' => 'Template deleted' ) );
    }

    /**
     * Update automation rule.
     */
    public function update_rule( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $params = $request->get_params();

        $wpdb->update( "{$wpdb->prefix}cce_automation_rules", array(
            'trigger_event' => sanitize_text_field( $params['trigger_event'] ),
            'action_type'   => sanitize_text_field( $params['action_type'] ),
            'config'        => json_encode( $params['config'] ?? array() ),
        ), array( 'id' => $id ) );

        return $this->success( array( 'message' => 'Rule updated' ) );
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
     * Get email templates.
     */
    public function get_templates( $request ) {
        global $wpdb;
        $templates = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_email_templates ORDER BY created_at DESC" );
        return $this->success( $templates );
    }

    /**
     * Create email template.
     */
    public function create_template( $request ) {
        global $wpdb;
        $params = $request->get_params();

        $wpdb->insert( "{$wpdb->prefix}cce_email_templates", array(
            'name'    => sanitize_text_field( $params['name'] ),
            'subject' => sanitize_text_field( $params['subject'] ),
            'content' => wp_kses_post( $params['content'] ),
        ) );

        return $this->success( array( 'id' => $wpdb->insert_id ) );
    }

	/**
	 * Trigger automation.
	 */
	public function trigger_automation( $id, $arg2 = null ) {
        global $wpdb;
        $hook = current_action();

        $rules = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cce_automation_rules WHERE trigger_event = %s AND is_active = 1",
            $hook
        ) );

        foreach ( $rules as $rule ) {
            $this->execute_rule( $rule, $id, $hook, $arg2 );
        }

        // Keep legacy defaults if no rules found for simple setup
        if ( empty( $rules ) ) {
            if ( 'cce_lead_created' === $hook ) {
                $mailer = new CCE_Mailer();
                $mailer->send_welcome_email( $id );
            }

            // Default stage movement if no specific rule for booking/payment
            if ( 'cce_booking_confirmed' === $hook ) {
                $lead_id = $wpdb->get_var( $wpdb->prepare( "SELECT lead_id FROM {$wpdb->prefix}cce_bookings WHERE id = %d", $id ) );
                if ( $lead_id ) {
                    $booked_stage_id = $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}cce_crm_stages WHERE name LIKE '%Booked%' LIMIT 1" );
                    if ( $booked_stage_id ) {
                        $wpdb->update( "{$wpdb->prefix}cce_leads", array( 'crm_stage_id' => $booked_stage_id ), array( 'id' => $lead_id ) );
                    }
                }
            }

            if ( 'cce_payment_completed' === $hook ) {
                $lead_id = $id; // For payment, ID is lead_id
                $closed_stage_id = $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}cce_crm_stages WHERE name LIKE '%Closed%' OR name LIKE '%Won%' LIMIT 1" );
                if ( $closed_stage_id ) {
                    $wpdb->update( "{$wpdb->prefix}cce_leads", array( 'crm_stage_id' => $closed_stage_id ), array( 'id' => $lead_id ) );
                }
            }
        }
	}

    /**
     * Execute specific rule.
     */
    private function execute_rule( $rule, $source_id, $hook, $arg2 = null ) {
        global $wpdb;
        $config = json_decode( $rule->config, true );

        // Resolve lead_id based on hook
        $lead_id = 0;
        if ( 'cce_lead_created' === $hook || 'cce_payment_completed' === $hook || 'cce_lead_stage_changed' === $hook ) {
            $lead_id = $source_id;
        } elseif ( 'cce_booking_confirmed' === $hook ) {
            $lead_id = $wpdb->get_var( $wpdb->prepare( "SELECT lead_id FROM {$wpdb->prefix}cce_bookings WHERE id = %d", $source_id ) );
        }

        // For stage changed, check if target stage matches config
        if ( 'cce_lead_stage_changed' === $hook ) {
            $target_stage_id = absint( $config['trigger_stage_id'] ?? 0 );
            if ( $target_stage_id && absint( $arg2 ) !== $target_stage_id ) {
                return;
            }
        }

        if ( ! $lead_id ) return;

        switch ( $rule->action_type ) {
            case 'send_email':
                $template_id = absint( $config['template_id'] ?? 0 );
                $mailer = new CCE_Mailer();
                if ( $template_id ) {
                    $mailer->send_template( $template_id, $lead_id );
                } else {
                    $mailer->send_welcome_email( $lead_id );
                }
                break;
            case 'move_stage':
                $stage_id = absint( $config['stage_id'] ?? 0 );
                if ( $stage_id ) {
                    $wpdb->update( "{$wpdb->prefix}cce_leads", array( 'crm_stage_id' => $stage_id ), array( 'id' => $lead_id ) );
                    CCE_Activity_Logger::log( $lead_id, 'stage_change', 'Lead automatically moved by automation rule: ' . $rule->id );
                }
                break;
            case 'schedule_reminder':
                $delay = absint( $config['delay_hours'] ?? 24 ) * HOUR_IN_SECONDS;
                wp_schedule_single_event( time() + $delay, 'cce_delayed_email_event', array( $source_id, 'reminder' ) );
                CCE_Activity_Logger::log( $lead_id, 'automation', 'Reminder scheduled via rule: ' . $rule->id );
                break;
            case 'create_task':
                $title = sanitize_text_field( $config['task_title'] ?? 'Follow up' );
                $wpdb->insert( "{$wpdb->prefix}cce_tasks", array(
                    'lead_id' => $lead_id,
                    'title'   => $title,
                    'status'  => 'pending',
                ) );
                CCE_Activity_Logger::log( $lead_id, 'automation', 'Task created via automation rule: ' . $title );
                break;
        }
    }

    /**
     * Send delayed email.
     */
    public function send_delayed_email( $id, $type ) {
        error_log( "Sending delayed email ($type) for ID: $id" );
        if ( 'reminder' === $type ) {
            $mailer = new CCE_Mailer();
            $mailer->send_reminder( $id );
        }
    }
}
