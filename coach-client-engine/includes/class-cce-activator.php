<?php
/**
 * Fired during plugin activation.
 */
class CCE_Activator {

	/**
	 * Create custom tables.
	 */
	public static function activate() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$tables = [
			"CREATE TABLE {$wpdb->prefix}cce_leads (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				first_name varchar(100),
				last_name varchar(100),
				email varchar(100) UNIQUE,
				phone varchar(20),
				secure_token varchar(100),
				source varchar(100),
				status varchar(50) DEFAULT 'cold',
				crm_stage_id bigint(20),
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_bookings (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				lead_id bigint(20),
				start_time datetime,
				end_time datetime,
				timezone varchar(50),
				status varchar(50) DEFAULT 'pending',
				questionnaire_data longtext,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_offers (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				title varchar(255),
				description text,
				price decimal(10, 2),
				currency varchar(3) DEFAULT 'USD',
				type varchar(50) DEFAULT 'one-time',
				is_active tinyint(1) DEFAULT 1,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_funnels (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				title varchar(255),
				type varchar(50),
				status varchar(50) DEFAULT 'draft',
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_funnel_steps (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				funnel_id bigint(20),
				title varchar(255),
				step_order int,
				step_type varchar(50),
				config longtext,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_payments (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				lead_id bigint(20),
				offer_id bigint(20),
				transaction_id varchar(255),
				gateway varchar(50),
				amount decimal(10, 2),
				currency varchar(3),
				status varchar(50),
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_crm_stages (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				name varchar(100),
				stage_order int,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_activity_log (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				lead_id bigint(20),
				activity_type varchar(100),
				description text,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_tasks (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				lead_id bigint(20),
				title varchar(255),
				due_date datetime,
				status varchar(50) DEFAULT 'pending',
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_testimonials (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				client_name varchar(255),
				content text,
				rating int DEFAULT 5,
				status varchar(50) DEFAULT 'active',
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_automation_rules (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				trigger_event varchar(100),
				action_type varchar(100),
				config longtext,
				is_active tinyint(1) DEFAULT 1,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;"
		];

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		foreach ( $tables as $sql ) {
			dbDelta( $sql );
		}

        // Seed default CRM stages if empty
        $count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_crm_stages" );
        if ( 0 == $count ) {
            $stages = ['New', 'Contacted', 'Booked', 'Closed'];
            foreach ( $stages as $index => $stage ) {
                $wpdb->insert(
                    "{$wpdb->prefix}cce_crm_stages",
                    [
                        'name' => $stage,
                        'stage_order' => $index + 1
                    ]
                );
            }
        }

        // Seed default Funnel Templates if empty
        $funnel_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_funnels" );
        if ( 0 == $funnel_count ) {
            $templates = [
                ['title' => 'Lead Magnet Funnel', 'type' => 'lead_magnet'],
                ['title' => 'Consultation Funnel', 'type' => 'consultation'],
                ['title' => 'Webinar Funnel', 'type' => 'webinar'],
            ];
            foreach ( $templates as $template ) {
                $wpdb->insert(
                    "{$wpdb->prefix}cce_funnels",
                    [
                        'title' => $template['title'],
                        'type' => $template['type'],
                        'status' => 'draft'
                    ]
                );
            }
        }
	}
}
