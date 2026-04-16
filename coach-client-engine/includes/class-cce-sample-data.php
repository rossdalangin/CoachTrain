<?php
/**
 * Sample Data Generator for Coach Client Engine.
 */
class CCE_Sample_Data {

    public static function generate() {
        global $wpdb;
        $user_id = get_current_user_id();

        // 1. Seed CRM Stages
        $stages = ['New Lead', 'Contacted', 'Booking Scheduled', 'Consultation Done', 'Closed - Won', 'Closed - Lost'];
        foreach ( $stages as $idx => $name ) {
            $wpdb->insert( "{$wpdb->prefix}cce_crm_stages", array(
                'user_id'     => $user_id,
                'name'        => $name,
                'stage_order' => $idx
            ) );
        }
        $new_stage_id = $wpdb->insert_id - 5; // Approximate first stage ID

        // 2. Seed Leads
        $leads = [
            ['first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john@example.com', 'status' => 'hot'],
            ['first_name' => 'Jane', 'last_name' => 'Smith', 'email' => 'jane@example.com', 'status' => 'warm'],
            ['first_name' => 'Alice', 'last_name' => 'Johnson', 'email' => 'alice@example.com', 'status' => 'cold'],
        ];
        foreach ( $leads as $l ) {
            $wpdb->insert( "{$wpdb->prefix}cce_leads", array(
                'user_id'      => $user_id,
                'first_name'   => $l['first_name'],
                'last_name'    => $l['last_name'],
                'email'        => $l['email'],
                'status'       => $l['status'],
                'crm_stage_id' => $new_stage_id,
                'secure_token' => bin2hex( random_bytes( 16 ) ),
                'created_at'   => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'))
            ) );
        }

        // 3. Seed Offers
        $offers = [
            [
                'title' => 'Elite Business Coaching',
                'price' => 5000,
                'outcome' => 'Scale to $10k/mo in 90 days',
                'likelihood' => '90% success rate with verified case studies'
            ],
            [
                'title' => 'Grand Slam Offer Workshop',
                'price' => 497,
                'outcome' => 'Create your irresistible offer in 4 hours',
                'likelihood' => 'Step-by-step proven frameworks'
            ]
        ];
        foreach ( $offers as $o ) {
            $wpdb->insert( "{$wpdb->prefix}cce_offers", array(
                'user_id'              => $user_id,
                'title'                => $o['title'],
                'price'                => $o['price'],
                'dream_outcome'        => $o['outcome'],
                'perceived_likelihood' => $o['likelihood'],
                'type'                 => 'one-time'
            ) );
        }

        // 4. Seed Funnels
        $wpdb->insert( "{$wpdb->prefix}cce_funnels", array(
            'user_id' => $user_id,
            'title'   => 'High-Ticket VSL Funnel',
            'type'    => 'Consultation',
            'status'  => 'active'
        ) );
        $funnel_id = $wpdb->insert_id;

        $steps = ['Opt-in Page', 'VSL Video', 'Booking Page', 'Thank You'];
        foreach ( $steps as $idx => $s ) {
            $wpdb->insert( "{$wpdb->prefix}cce_funnel_steps", array(
                'user_id'    => $user_id,
                'funnel_id'  => $funnel_id,
                'title'      => $s,
                'step_order' => $idx,
                'visits'     => rand(100, 500),
                'conversions'=> rand(10, 50)
            ) );
        }

        // 5. Seed Analytics Data (Visitors)
        update_user_meta( $user_id, 'cce_total_visitors', rand(1000, 5000) );

        // 6. Seed Testimonials
        $testimonials = [
            ['name' => 'Steve Jobs', 'content' => 'The Coach Client Engine transformed how we think about our sales funnel.'],
            ['name' => 'Elon Musk', 'content' => 'Incredible ROI. The automation is efficient and reliable.'],
            ['name' => 'Richard Branson', 'content' => 'Brilliant simplicity for high-ticket client acquisition.']
        ];
        foreach ( $testimonials as $t ) {
            $wpdb->insert( "{$wpdb->prefix}cce_testimonials", array(
                'user_id'     => $user_id,
                'client_name' => $t['name'],
                'content'     => $t['content'],
                'rating'      => 5,
                'status'      => 'active'
            ) );
        }

        // 7. Seed Bookings
        $lead_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cce_leads WHERE user_id = %d LIMIT 3", $user_id ) );
        foreach ( $lead_ids as $idx => $lid ) {
            $wpdb->insert( "{$wpdb->prefix}cce_bookings", array(
                'user_id'    => $user_id,
                'lead_id'    => $lid,
                'start_time' => date('Y-m-d H:i:s', strtotime('+' . ($idx + 1) . ' days')),
                'timezone'   => 'America/New_York',
                'status'     => 'confirmed'
            ) );
        }

        // 8. Seed Payments
        $offer_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cce_offers WHERE user_id = %d LIMIT 1", $user_id ) );
        if ( $offer_id && !empty($lead_ids) ) {
            $wpdb->insert( "{$wpdb->prefix}cce_payments", array(
                'user_id'        => $user_id,
                'lead_id'        => $lead_ids[0],
                'offer_id'       => $offer_id,
                'amount'         => 5000,
                'currency'       => 'USD',
                'status'         => 'completed',
                'transaction_id' => 'ch_sample_' . uniqid(),
                'gateway'        => 'stripe'
            ) );
        }

        // 9. Seed Activity Log
        if ( !empty($lead_ids) ) {
            $wpdb->insert( "{$wpdb->prefix}cce_activity_log", array(
                'user_id'       => $user_id,
                'lead_id'       => $lead_ids[0],
                'activity_type' => 'payment_received',
                'description'   => 'Client paid $5000 for Elite Business Coaching.'
            ) );
        }

        // 10. Seed Tasks
        if ( !empty($lead_ids) ) {
            $wpdb->insert( "{$wpdb->prefix}cce_tasks", array(
                'user_id'  => $user_id,
                'lead_id'  => $lead_ids[0],
                'title'    => 'Onboarding Call',
                'due_date' => date('Y-m-d H:i:s', strtotime('+2 days')),
                'status'   => 'pending'
            ) );
        }

        // 11. Seed Automation Rules
        $wpdb->insert( "{$wpdb->prefix}cce_automation_rules", array(
            'user_id'       => $user_id,
            'trigger_event' => 'cce_lead_created',
            'action_type'   => 'send_email',
            'config'        => json_encode(['template_id' => 1]),
            'is_active'     => 1
        ) );

        // 12. Seed Email Templates
        $templates = [
            [
                'name'    => 'Hormozi Value Sequence #1',
                'subject' => 'The truth about {{dream_outcome}}...',
                'content' => '<h1>Hey {{first_name}}!</h1><p>Most people fail at {{dream_outcome}} because they focus on the wrong things. Here is the secret to increasing your likelihood of success...</p>'
            ],
            [
                'name'    => 'Brunson Epiphany Bridge',
                'subject' => 'How I finally cracked the code',
                'content' => '<h1>I was stuck, {{first_name}}...</h1><p>I tried everything until I realized that the secret wasn\'t more work, it was a better system. Here is the story of how I built the Coach Client Engine.</p>'
            ],
            [
                'name'    => 'Direct Response Booking Invite',
                'subject' => 'Ready to scale?',
                'content' => '<h1>{{first_name}}, let\'s get serious.</h1><p>If you want to achieve {{dream_outcome}} in the next 90 days, we need to talk. Book your strategy session here.</p>'
            ]
        ];
        foreach ($templates as $t) {
            $wpdb->insert( "{$wpdb->prefix}cce_email_templates", array(
                'user_id' => $user_id,
                'name'    => $t['name'],
                'subject' => $t['subject'],
                'content' => $t['content']
            ) );
        }

        // 13. Seed Resources
        $wpdb->insert( "{$wpdb->prefix}cce_resources", array(
            'user_id'  => $user_id,
            'title'    => 'Million Dollar Offer Guide',
            'category' => 'Strategy',
            'type'     => 'PDF',
            'url'      => '#'
        ) );

        // 14. Seed Questions
        $wpdb->insert( "{$wpdb->prefix}cce_questions", array(
            'user_id'        => $user_id,
            'question_text'  => 'What is your current monthly revenue?',
            'question_type'  => 'select',
            'question_order' => 1
        ) );

        // 15. Seed Onboarding Tasks
        $wpdb->insert( "{$wpdb->prefix}cce_onboarding_tasks", array(
            'user_id'    => $user_id,
            'task_name'  => 'Watch the Welcome Video',
            'task_order' => 1
        ) );
    }
}
