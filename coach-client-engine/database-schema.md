# Coach Client Engine - Database Schema

## Table: `cce_leads`
Stores lead information.
- `id`: BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `first_name`: VARCHAR(100)
- `last_name`: VARCHAR(100)
- `email`: VARCHAR(100) UNIQUE
- `phone`: VARCHAR(20)
- `source`: VARCHAR(100) (e.g., Lead Magnet Funnel)
- `status`: VARCHAR(50) (cold, warm, hot)
- `crm_stage_id`: BIGINT(20)
- `created_at`: DATETIME DEFAULT CURRENT_TIMESTAMP
- `updated_at`: DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

## Table: `cce_bookings`
Stores appointment details.
- `id`: BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `lead_id`: BIGINT(20)
- `start_time`: DATETIME
- `end_time`: DATETIME
- `timezone`: VARCHAR(50)
- `status`: VARCHAR(50) (pending, confirmed, cancelled, completed)
- `questionnaire_data`: LONGTEXT (JSON)
- `created_at`: DATETIME DEFAULT CURRENT_TIMESTAMP

## Table: `cce_offers`
Stores coaching packages/offers.
- `id`: BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `title`: VARCHAR(255)
- `description`: TEXT
- `price`: DECIMAL(10, 2)
- `currency`: VARCHAR(3) DEFAULT 'USD'
- `type`: VARCHAR(50) (one-time, subscription)
- `is_active`: TINYINT(1) DEFAULT 1
- `created_at`: DATETIME DEFAULT CURRENT_TIMESTAMP

## Table: `cce_funnels`
Stores funnel configurations.
- `id`: BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `title`: VARCHAR(255)
- `type`: VARCHAR(50) (lead_magnet, consultation, webinar)
- `status`: VARCHAR(50) (draft, active)
- `created_at`: DATETIME DEFAULT CURRENT_TIMESTAMP

## Table: `cce_funnel_steps`
Stores individual steps of a funnel.
- `id`: BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `funnel_id`: BIGINT(20)
- `title`: VARCHAR(255)
- `step_order`: INT
- `step_type`: VARCHAR(50) (optin, checkout, thank_you)
- `config`: LONGTEXT (JSON)

## Table: `cce_payments`
Stores transaction records.
- `id`: BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `lead_id`: BIGINT(20)
- `offer_id`: BIGINT(20)
- `transaction_id`: VARCHAR(255)
- `gateway`: VARCHAR(50) (stripe, paypal)
- `amount`: DECIMAL(10, 2)
- `currency`: VARCHAR(3)
- `status`: VARCHAR(50)
- `created_at`: DATETIME DEFAULT CURRENT_TIMESTAMP

## Table: `cce_testimonials`
Stores client testimonials and social proof.
- `id`: BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `client_name`: VARCHAR(255)
- `content`: TEXT
- `rating`: INT DEFAULT 5
- `status`: VARCHAR(50) DEFAULT 'active'
- `created_at`: DATETIME DEFAULT CURRENT_TIMESTAMP

## Table: `cce_automation_rules`
Stores customizable automation workflows.
- `id`: BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `trigger_event`: VARCHAR(100) (e.g., cce_lead_created, cce_booking_confirmed)
- `action_type`: VARCHAR(100) (e.g., send_email, add_tag, move_stage)
- `config`: LONGTEXT (JSON)
- `is_active`: TINYINT(1) DEFAULT 1
- `created_at`: DATETIME DEFAULT CURRENT_TIMESTAMP

## Table: `cce_crm_stages`
Stores CRM pipeline stages.
- `id`: BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `name`: VARCHAR(100)
- `stage_order`: INT
- `created_at`: DATETIME DEFAULT CURRENT_TIMESTAMP

## Table: `cce_activity_log`
Stores lead/client activities.
- `id`: BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `lead_id`: BIGINT(20)
- `activity_type`: VARCHAR(100) (optin, booking, payment, email_open)
- `description`: TEXT
- `created_at`: DATETIME DEFAULT CURRENT_TIMESTAMP
