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
		) );

        register_rest_route( $this->namespace, '/crm/leads/(?P<id>\d+)/tasks/(?P<task_id>\d+)', array(
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_task' ),
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
     * Get tasks for a lead.
     */
    public function get_tasks( $request ) {
        global $wpdb;
        $lead_id = absint( $request['id'] );
        $tasks = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_tasks WHERE lead_id = %d", $lead_id ) );
        return $this->success( $tasks );
    }

    /**
     * Add task.
     */
    public function add_task( $request ) {
        global $wpdb;
        $lead_id = absint( $request['id'] );
        $title = sanitize_text_field( $request->get_param( 'title' ) );

        $wpdb->insert( "{$wpdb->prefix}cce_tasks", array(
            'lead_id' => $lead_id,
            'title'   => $title,
            'status'  => 'pending',
        ) );

        return $this->success( array( 'id' => $wpdb->insert_id ) );
    }

    /**
     * Update task status.
     */
    public function update_task( $request ) {
        global $wpdb;
        $task_id = absint( $request['task_id'] );
        $status = sanitize_text_field( $request->get_param( 'status' ) );

        $wpdb->update(
            "{$wpdb->prefix}cce_tasks",
            array( 'status' => $status ),
            array( 'id' => $task_id )
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

        // Automatically move to 'Contacted' stage (ID 2)
        $wpdb->update( "{$wpdb->prefix}cce_leads", array( 'crm_stage_id' => 2 ), array( 'id' => $lead_id ) );

        CCE_Activity_Logger::log( $lead_id, 'contacted', 'Manual email sent: ' . $message );

        return $this->success( array( 'message' => 'Email sent' ) );
    }

    /**
     * Update lead stage.
     */
    public function update_lead_stage( $request ) {
        global $wpdb;
        $lead_id = absint( $request['id'] );
        $stage_id = absint( $request['stage_id'] );

        $wpdb->update( "{$wpdb->prefix}cce_leads", array( 'crm_stage_id' => $stage_id ), array( 'id' => $lead_id ) );

        CCE_Activity_Logger::log( $lead_id, 'stage_change', 'Lead moved to stage ID: ' . $stage_id );

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
	 * Get CRM stages.
	 */
	public function get_stages( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'cce_crm_stages';
		$stages = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY stage_order ASC" );
		return $this->success( $stages );
	}

    /**
     * Get Pipeline (Leads grouped by stages).
     */
    public function get_pipeline( $request ) {
        global $wpdb;
        $stages_table = $wpdb->prefix . 'cce_crm_stages';
        $leads_table = $wpdb->prefix . 'cce_leads';

        $stages = $wpdb->get_results( "SELECT * FROM $stages_table ORDER BY stage_order ASC" );
        $pipeline = array();

        foreach ( $stages as $stage ) {
            $leads = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM $leads_table WHERE crm_stage_id = %d",
                $stage->id
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
