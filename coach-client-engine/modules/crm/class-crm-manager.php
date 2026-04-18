<?php
/**
 * CRM Manager class.
 */
class CCE_CRM_Manager extends CCE_REST_Controller {

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/crm/stages', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_stages' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_stage' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/crm/activities', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_all_activities' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/crm/stages/(?P<id>\d+)', array(
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_stage' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
            array(
				'methods'             => array( WP_REST_Server::EDITABLE, WP_REST_Server::CREATABLE ),
				'callback'            => array( $this, 'update_stage' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/crm/leads/(?P<id>\d+)/tasks/(?P<task_id>\d+)', array(
			array(
				'methods'             => array( WP_REST_Server::EDITABLE, WP_REST_Server::CREATABLE ),
				'callback'            => array( $this, 'update_task' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
            array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_task' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/crm/pipeline', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_pipeline' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/crm/leads/(?P<id>\d+)/notes', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'add_note' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/crm/leads/(?P<id>\d+)/stage', array(
			array(
				'methods'             => array( WP_REST_Server::EDITABLE, WP_REST_Server::CREATABLE ),
				'callback'            => array( $this, 'update_lead_stage' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/crm/leads/(?P<id>\d+)/contact', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'contact_lead' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/crm/leads/(?P<id>\d+)/tasks', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_tasks' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'add_task' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/crm/leads/(?P<id>\d+)/activities', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_activities' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/crm/leads/(?P<id>\d+)/stats', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_lead_stats' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/crm/leads/(?P<id>\d+)/milestones', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_milestones' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'add_milestone' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );

        register_rest_route( $this->namespace, '/crm/leads/(?P<id>\d+)/milestones/(?P<mid>\d+)', array(
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_milestone' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
            array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_milestone' ),
				'permission_callback' => array( $this, 'check_permission' ),
			),
		) );
	}

    /**
     * Get financial and engagement stats for a lead.
     */
    public function get_lead_stats( $request ) {
        global $wpdb;
        $lead_id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();

        $lead = $wpdb->get_row( $wpdb->prepare( "SELECT source, created_at FROM {$wpdb->prefix}cce_leads WHERE id = %d AND user_id = %d", $lead_id, $user_id ) );
        if ( ! $lead ) return $this->error( 'Lead not found' );

        $total_paid = $wpdb->get_var( $wpdb->prepare( "SELECT SUM(amount) FROM {$wpdb->prefix}cce_payments WHERE lead_id = %d AND user_id = %d AND status = 'completed'", $lead_id, $user_id ) );
        $appointments = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_bookings WHERE lead_id = %d AND user_id = %d", $lead_id, $user_id ) );
        $tasks_total = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_tasks WHERE lead_id = %d AND user_id = %d", $lead_id, $user_id ) );
        $tasks_done = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_tasks WHERE lead_id = %d AND user_id = %d AND status = 'completed'", $lead_id, $user_id ) );

        return $this->success( array(
            'source'       => $lead->source ?? 'Direct',
            'created_at'   => $lead->created_at,
            'total_paid'   => (float) ($total_paid ?: 0),
            'appointments' => (int) $appointments,
            'tasks_done'   => (int) $tasks_done,
            'tasks_total'  => (int) $tasks_total,
        ) );
    }

    /**
     * Get activities for a lead.
     */
    public function get_activities( $request ) {
        $lead_id = absint( $request['id'] );
        $activities = CCE_Activity_Logger::get_logs( $lead_id );
        return $this->success( $activities );
    }

    /**
     * Get all global activities.
     */
    public function get_all_activities( $request ) {
        global $wpdb;
        $user_id = $this->get_current_user_id();
        $activities = $wpdb->get_results( $wpdb->prepare( "
            SELECT a.*, CONCAT(l.first_name, ' ', l.last_name) as lead_name
            FROM {$wpdb->prefix}cce_activity_log a
            LEFT JOIN {$wpdb->prefix}cce_leads l ON a.lead_id = l.id
            WHERE a.user_id = %d
            ORDER BY a.created_at DESC
            LIMIT 50
        ", $user_id ) );
        return $this->success( $activities );
    }

    /**
     * Get tasks for a lead.
     */
    public function get_tasks( $request ) {
        global $wpdb;
        $lead_id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $tasks = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_tasks WHERE lead_id = %d AND user_id = %d", $lead_id, $user_id ) );
        return $this->success( $tasks );
    }

    /**
     * Add task.
     */
    public function add_task( $request ) {
        global $wpdb;
        $lead_id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $title = sanitize_text_field( $request->get_param( 'title' ) );

        $wpdb->insert( "{$wpdb->prefix}cce_tasks", array(
            'user_id' => $user_id,
            'lead_id' => $lead_id,
            'title'   => $title,
            'status'  => 'pending',
        ) );

        return $this->success( array( 'id' => $wpdb->insert_id ) );
    }

    /**
     * Delete task.
     */
    public function delete_task( $request ) {
        global $wpdb;
        $task_id = absint( $request['task_id'] );
        $user_id = $this->get_current_user_id();
        $wpdb->delete( "{$wpdb->prefix}cce_tasks", array( 'id' => $task_id, 'user_id' => $user_id ) );
        return $this->success( array( 'message' => 'Task deleted' ) );
    }

    /**
     * Update task status.
     */
    public function update_task( $request ) {
        global $wpdb;
        $task_id = absint( $request['task_id'] );
        $user_id = $this->get_current_user_id();
        $status = sanitize_text_field( $request->get_param( 'status' ) );

        $wpdb->update(
            "{$wpdb->prefix}cce_tasks",
            array( 'status' => $status ),
            array( 'id' => $task_id, 'user_id' => $user_id )
        );

        return $this->success( array( 'message' => 'Task updated' ) );
    }

    /**
     * Contact lead via email.
     */
    public function contact_lead( $request ) {
        global $wpdb;
        $lead_id = absint( $request['id'] );
        $message = sanitize_textarea_field( $request->get_param( 'message' ) );

        $lead = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_leads WHERE id = %d", $lead_id ) );
        if ( ! $lead ) {
            return $this->error( 'Lead not found' );
        }

        $mailer = new CCE_Mailer();
        $subject = 'Regarding your coaching application';
        $mailer->send( $lead->email, $subject, wpautop( $message ) );

        // Automatically move to 'Contacted' stage dynamically
        $contacted_stage_id = $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}cce_crm_stages WHERE name LIKE '%Contacted%' LIMIT 1" );
        if ( $contacted_stage_id ) {
            $wpdb->update( "{$wpdb->prefix}cce_leads", array( 'crm_stage_id' => $contacted_stage_id ), array( 'id' => $lead_id ) );
        }

        CCE_Activity_Logger::log( $lead_id, 'contacted', 'Manual email sent: ' . $message );

        return $this->success( array( 'message' => 'Email sent' ) );
    }

    /**
     * Update lead stage.
     */
    public function update_lead_stage( $request ) {
        global $wpdb;
        $lead_id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $params = $this->get_params( $request ); $stage_id = absint( $params['stage_id'] );

        $wpdb->update( "{$wpdb->prefix}cce_leads", array( 'crm_stage_id' => $stage_id ), array( 'id' => $lead_id, 'user_id' => $user_id ) );

        CCE_Activity_Logger::log( $lead_id, 'stage_change', 'Lead moved to stage ID: ' . $stage_id );

        // Trigger action for automation
        do_action( 'cce_lead_stage_changed', $lead_id, $stage_id );

        return $this->success( array( 'message' => 'Stage updated' ) );
    }

    /**
     * Add note to a lead.
     */
    public function add_note( $request ) {
        $lead_id = absint( $request['id'] );
        $note = sanitize_text_field( $request->get_param( 'note' ) );

        CCE_Activity_Logger::log( $lead_id, 'note', $note );

        return $this->success( array( 'message' => 'Note added' ) );
    }

    /**
     * Create CRM stage.
     */
    public function create_stage( $request ) {
        global $wpdb;
        $user_id = $this->get_current_user_id();
        $name = sanitize_text_field( $request->get_param( 'name' ) );
        $order = (int) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(stage_order) FROM {$wpdb->prefix}cce_crm_stages WHERE user_id = %d", $user_id ) ) + 1;

        $wpdb->insert( "{$wpdb->prefix}cce_crm_stages", array(
            'user_id'     => $user_id,
            'name'        => $name,
            'stage_order' => $order
        ) );

        return $this->success( array( 'id' => $wpdb->insert_id ) );
    }

    /**
     * Delete CRM stage.
     */
    public function delete_stage( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $wpdb->delete( "{$wpdb->prefix}cce_crm_stages", array( 'id' => $id, 'user_id' => $user_id ) );
        return $this->success( array( 'message' => 'Stage deleted' ) );
    }

    /**
     * Update CRM stage.
     */
    public function update_stage( $request ) {
        global $wpdb;
        $id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $name = sanitize_text_field( $request->get_param( 'name' ) );
        $order = absint( $request->get_param( 'stage_order' ) );

        $wpdb->update( "{$wpdb->prefix}cce_crm_stages",
            array( 'name' => $name, 'stage_order' => $order ),
            array( 'id' => $id, 'user_id' => $user_id )
        );

        return $this->success( array( 'message' => 'Stage updated' ) );
    }

	/**
	 * Get CRM stages.
	 */
	public function get_stages( $request ) {
		global $wpdb;
        $user_id = $this->get_current_user_id();
		$table_name = $wpdb->prefix . 'cce_crm_stages';

        $stages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE user_id = %d ORDER BY stage_order ASC", $user_id ) );

        if ( empty( $stages ) && $user_id > 0 ) {
            $this->seed_default_stages( $user_id );
            $stages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE user_id = %d ORDER BY stage_order ASC", $user_id ) );
        }

		return $this->success( $stages );
	}

    /**
     * Seed default stages for a user.
     */
    private function seed_default_stages( $user_id ) {
        global $wpdb;
        $stages = ['New', 'Contacted', 'Booked', 'Closed'];
        foreach ( $stages as $index => $stage ) {
            $wpdb->insert(
                "{$wpdb->prefix}cce_crm_stages",
                [
                    'user_id'     => $user_id,
                    'name'        => $stage,
                    'stage_order' => $index + 1
                ]
            );
        }
    }

    /**
     * Get milestones for a lead.
     */
    public function get_milestones( $request ) {
        global $wpdb;
        $lead_id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $milestones = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_milestones WHERE lead_id = %d AND user_id = %d ORDER BY created_at ASC", $lead_id, $user_id ) );
        return $this->success( $milestones );
    }

    /**
     * Add milestone.
     */
    public function add_milestone( $request ) {
        global $wpdb;
        $lead_id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $title = sanitize_text_field( $request->get_param( 'title' ) );

        $wpdb->insert( "{$wpdb->prefix}cce_milestones", array(
            'user_id' => $user_id,
            'lead_id' => $lead_id,
            'title'   => $title,
        ) );

        CCE_Activity_Logger::log( $lead_id, 'milestone', 'New milestone added: ' . $title );

        return $this->success( array( 'id' => $wpdb->insert_id ) );
    }

    /**
     * Update milestone.
     */
    public function update_milestone( $request ) {
        global $wpdb;
        $mid = absint( $request['mid'] );
        $lead_id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $is_completed = (bool) $request->get_param( 'is_completed' );

        $wpdb->update( "{$wpdb->prefix}cce_milestones",
            array(
                'is_completed' => $is_completed,
                'completed_at' => $is_completed ? current_time( 'mysql' ) : null
            ),
            array( 'id' => $mid, 'lead_id' => $lead_id, 'user_id' => $user_id )
        );

        if ( $is_completed ) {
             CCE_Activity_Logger::log( $lead_id, 'milestone', 'Milestone completed!' );
        }

        return $this->success();
    }

    /**
     * Delete milestone.
     */
    public function delete_milestone( $request ) {
        global $wpdb;
        $mid = absint( $request['mid'] );
        $lead_id = absint( $request['id'] );
        $user_id = $this->get_current_user_id();
        $wpdb->delete( "{$wpdb->prefix}cce_milestones", array( 'id' => $mid, 'lead_id' => $lead_id, 'user_id' => $user_id ) );
        return $this->success();
    }

    /**
     * Get Pipeline (Leads grouped by stages).
     */
    public function get_pipeline( $request ) {
        global $wpdb;
        $user_id = $this->get_current_user_id();
        $stages_table = $wpdb->prefix . 'cce_crm_stages';
        $leads_table = $wpdb->prefix . 'cce_leads';

        $stages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $stages_table WHERE user_id = %d ORDER BY stage_order ASC", $user_id ) );
        $pipeline = array();

        foreach ( $stages as $stage ) {
            $leads = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM $leads_table WHERE crm_stage_id = %d AND user_id = %d",
                $stage->id,
                $user_id
            ) );

            // Fetch activities for each lead
            foreach ( $leads as &$lead ) {
                $lead->activities = CCE_Activity_Logger::get_logs( $lead->id );
            }

            $pipeline[] = array(
                'stage' => $stage,
                'leads' => $leads
            );
        }

        return $this->success( $pipeline );
    }
}
