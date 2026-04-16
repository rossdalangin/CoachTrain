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
            ['name' => 'Elon Musk', 'content' => 'Incredible ROI. The automation is efficient and reliable.']
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
    }
}
