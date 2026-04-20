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
				user_id bigint(20) UNSIGNED DEFAULT 0,
				first_name varchar(100),
				last_name varchar(100),
				email varchar(100),
				phone varchar(20),
				secure_token varchar(100),
				source varchar(100),
				status varchar(50) DEFAULT 'cold',
				crm_stage_id bigint(20),
				onboarding_progress longtext,
				tags text,
				utm_source varchar(100),
				utm_medium varchar(100),
				utm_campaign varchar(100),
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY  (id),
				UNIQUE KEY user_email (user_id, email)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_bookings (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id bigint(20) UNSIGNED DEFAULT 0,
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
				user_id bigint(20) UNSIGNED DEFAULT 0,
				title varchar(255),
				description text,
				price decimal(10, 2),
				currency varchar(3) DEFAULT 'USD',
				type varchar(50) DEFAULT 'one-time',
				dream_outcome text,
				perceived_likelihood text,
				time_delay text,
				effort_sacrifice text,
				upsell_offer_id bigint(20) DEFAULT 0,
				downsell_offer_id bigint(20) DEFAULT 0,
				order_bump_offer_id bigint(20) DEFAULT 0,
				is_active tinyint(1) DEFAULT 1,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_funnels (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id bigint(20) UNSIGNED DEFAULT 0,
				title varchar(255),
				type varchar(50),
				status varchar(50) DEFAULT 'draft',
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_funnel_steps (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id bigint(20) UNSIGNED DEFAULT 0,
				funnel_id bigint(20),
				title varchar(255),
				step_order int,
				step_type varchar(50),
				config longtext,
				tracking_scripts text,
				logic text,
				visits bigint(20) DEFAULT 0,
				conversions bigint(20) DEFAULT 0,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_payments (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id bigint(20) UNSIGNED DEFAULT 0,
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
				user_id bigint(20) UNSIGNED DEFAULT 0,
				name varchar(100),
				stage_order int,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_activity_log (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id bigint(20) UNSIGNED DEFAULT 0,
				lead_id bigint(20),
				activity_type varchar(100),
				description text,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_tasks (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id bigint(20) UNSIGNED DEFAULT 0,
				lead_id bigint(20),
				title varchar(255),
				due_date datetime,
				status varchar(50) DEFAULT 'pending',
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_testimonials (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id bigint(20) UNSIGNED DEFAULT 0,
				type varchar(50) DEFAULT 'testimonial',
				title varchar(255),
				client_name varchar(255),
				content text,
				rating int DEFAULT 5,
				status varchar(50) DEFAULT 'active',
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_automation_rules (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id bigint(20) UNSIGNED DEFAULT 0,
				trigger_event varchar(100),
				action_type varchar(100),
				config longtext,
				is_active tinyint(1) DEFAULT 1,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_email_templates (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id bigint(20) UNSIGNED DEFAULT 0,
				name varchar(255),
				subject varchar(255),
				content longtext,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_resources (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id bigint(20) UNSIGNED DEFAULT 0,
				title varchar(255),
				category varchar(100) DEFAULT 'Uncategorized',
				type varchar(50),
				url varchar(255),
				visibility varchar(50) DEFAULT 'public',
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_questions (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id bigint(20) UNSIGNED DEFAULT 0,
				question_text text,
				question_type varchar(50) DEFAULT 'text',
				is_required tinyint(1) DEFAULT 1,
				question_order int,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_onboarding_tasks (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id bigint(20) UNSIGNED DEFAULT 0,
				task_name varchar(255),
				description text,
				task_order int,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_licenses (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				license_key varchar(255) UNIQUE,
				user_id bigint(20) UNSIGNED DEFAULT 0,
				status varchar(50) DEFAULT 'active',
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_webhooks (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id bigint(20) UNSIGNED DEFAULT 0,
				name varchar(255),
				url varchar(255),
				events text,
				is_active tinyint(1) DEFAULT 1,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_resource_access (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id bigint(20) UNSIGNED DEFAULT 0,
				lead_id bigint(20) UNSIGNED DEFAULT 0,
				resource_id bigint(20) UNSIGNED DEFAULT 0,
				accessed_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_milestones (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id bigint(20) UNSIGNED DEFAULT 0,
				lead_id bigint(20) UNSIGNED DEFAULT 0,
				title varchar(255),
				description text,
				is_completed tinyint(1) DEFAULT 0,
				completed_at datetime,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;",

			"CREATE TABLE {$wpdb->prefix}cce_strategies (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id bigint(20) UNSIGNED DEFAULT 0,
				title varchar(255),
				description text,
				config longtext,
				is_public tinyint(1) DEFAULT 0,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;"
		];

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		foreach ( $tables as $sql ) {
			dbDelta( $sql );
		}

        // Default seeding is now handled dynamically per user in CCE_CRM_Manager and individual modules to support multi-tenancy.
	}
}
