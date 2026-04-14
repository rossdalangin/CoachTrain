<div class="wrap cce-admin-wrap">
    <h1>Coach Client Engine - Dashboard</h1>
    <p class="description">Welcome back, fellow consultant. This dashboard is your "Mission Control" for acquiring high-ticket clients. Use these metrics to identify bottlenecks in your funnel and scale your impact.</p>

    <?php
    if ( class_exists( 'CCE_Analytics_Manager' ) ) {
        $analytics = new CCE_Analytics_Manager();
        $summary_res = $analytics->get_summary( new WP_REST_Request() );
        $summary = is_wp_error($summary_res) ? [] : $summary_res->get_data()['data'];

        $activity_res = $analytics->get_recent_activity( new WP_REST_Request() );
        $activities = is_wp_error($activity_res) ? [] : $activity_res->get_data()['data'];
    } else {
        $summary = [];
        $activities = [];
    }
    ?>

    <?php if ($summary): ?>
    <div class="cce-dashboard-grid">
        <div class="cce-card">
            <h3>Leads Today</h3>
            <div class="value"><?php echo (int) $summary['leads_today']; ?></div>
        </div>
        <div class="cce-card">
            <h3>Bookings Today</h3>
            <div class="value"><?php echo (int) $summary['bookings_today']; ?></div>
        </div>
        <div class="cce-card">
            <h3>Revenue Today</h3>
            <div class="value">$<?php echo number_format((float) $summary['revenue_today'], 2); ?></div>
        </div>
    </div>

    <div class="cce-card" style="margin-bottom:20px; border-left: 4px solid #00a32a;">
        <h3>🚀 Quick Setup Guide</h3>
        <p style="font-size:12px; color:#666;">Complete these steps to activate your client acquisition machine.</p>
        <div style="display:flex; gap:30px; margin-top:10px;">
            <?php
            global $wpdb;
            $setup_steps = [
                'License Key' => [
                    'check' => get_option('cce_license_key'),
                    'desc'  => 'Unlock Pro features'
                ],
                'Stripe Connected' => [
                    'check' => get_option('cce_stripe_api_key'),
                    'desc'  => 'Accept high-ticket payments'
                ],
                'Lead Magnet' => [
                    'check' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}cce_funnels"),
                    'desc'  => 'Create your first opt-in page'
                ],
                'Coaching Offer' => [
                    'check' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}cce_offers"),
                    'desc'  => 'Define your Grand Slam Offer'
                ],
            ];
            foreach($setup_steps as $label => $data): ?>
                <div style="display:flex; flex-direction:column; gap:4px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="font-size:18px;"><?php echo $data['check'] ? '✅' : '❌'; ?></span>
                        <span style="font-size:13px; font-weight:bold; color:#333;"><?php echo $label; ?></span>
                    </div>
                    <small style="font-size:10px; color:#888; padding-left:26px;"><?php echo $data['desc']; ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
        <div class="cce-card strategy-insights" style="border-left: 4px solid #0073aa;">
            <h3>💎 Master Architect Strategy Insights</h3>
            <p>Based on your current data, here is your path to 3–5 clients this month:</p>
            <ul style="list-style:disc; padding-left:20px;">
                <li><strong>Lead Velocity:</strong> Captured <?php echo (int) $summary['leads_today']; ?> leads today. Increase this to 10+ to guarantee scale.</li>
                <li><strong>Conversion Ratio:</strong> Your lead-to-client conversion is <strong><?php echo $summary['lead_to_client']; ?>%</strong>.</li>
                <?php if ($summary['lead_to_client'] < 3): ?>
                    <li style="color:#d63638;"><strong>Action Required:</strong> Your conversion is below 3%. Review your Offer Builder.</li>
                <?php else: ?>
                    <li style="color:#00a32a;"><strong>Performing Well:</strong> Your funnel is converting efficiently. Scale traffic.</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="cce-card" style="border-left: 4px solid #ffb700;">
            <h3>⚡ Recent Activity</h3>
            <div style="max-height:200px; overflow-y:auto;">
                <?php if ($activities): ?>
                    <ul style="list-style:none; padding:0; margin:0;">
                        <?php foreach($activities as $a): ?>
                            <li style="font-size:12px; padding:8px 0; border-bottom:1px solid #eee;">
                                <strong><?php echo esc_html($a['lead_name'] ?: 'System'); ?>:</strong> <?php echo esc_html($a['description']); ?>
                                <br><small style="color:#888;"><?php echo esc_html($a['created_at']); ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No activity yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="cce-card" style="margin-top:20px; border-left: 4px solid #e91e63;">
            <h3>📌 Pending Tasks</h3>
            <?php if (!empty($summary['pending_tasks'])): ?>
                <ul style="list-style:none; padding:0;">
                    <?php foreach($summary['pending_tasks'] as $task): ?>
                        <li style="font-size:12px; padding:8px 0; border-bottom:1px solid #eee; display:flex; justify-content:space-between;">
                            <span><strong><?php echo esc_html($task->lead_name); ?>:</strong> <?php echo esc_html($task->title); ?></span>
                            <a href="?page=cce-crm" class="button button-small">View CRM</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No pending tasks! You're all caught up.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
        <div class="notice notice-warning"><p>No analytics data available. Start capturing leads to see insights!</p></div>
    <?php endif; ?>

    <div class="cce-card" style="margin-top:20px; border-top: 4px solid #673ab7;">
        <h3>📊 Conversion Pipeline</h3>
        <p style="font-size:12px; color:#666;">This visualization shows the "leakage" in your sales process. Aim for a 20%+ conversion between each stage for maximum profitability.</p>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:15px; position:relative; padding:20px 0;">
            <?php foreach($summary['pipeline'] as $idx => $p): ?>
                <div style="text-align:center; flex:1; position:relative; z-index:2;">
                    <div style="font-weight:bold; color:#673ab7; font-size:18px;"><?php echo $p['value']; ?></div>
                    <div style="font-size:11px; color:#666; text-transform:uppercase;"><?php echo $p['label']; ?></div>
                </div>
                <?php if($idx < count($summary['pipeline'])-1): ?>
                    <div style="flex:0.5; height:2px; background:#e0e0e0; margin-top:-15px;"></div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="cce-quick-actions" style="margin-top:30px; display:flex; gap:15px; justify-content:center;">
        <a href="?page=cce-leads" class="button button-hero">Manage Leads</a>
        <a href="?page=cce-bookings" class="button button-hero">View Schedule</a>
        <a href="?page=cce-funnels" class="button button-primary button-hero">Create Funnel</a>
        <a href="?page=cce-clients" class="button button-hero">Add Offer</a>
    </div>
</div>
