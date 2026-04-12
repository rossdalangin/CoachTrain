<div class="wrap cce-admin-wrap">
    <h1>Coach Client Engine - Dashboard</h1>

    <?php
    if ( class_exists( 'CCE_Analytics_Manager' ) ) {
        $analytics = new CCE_Analytics_Manager();
        $summary_res = $analytics->get_summary( new WP_REST_Request() );
        $summary = is_wp_error($summary_res) ? [] : $summary_res->get_data()['data'];
    } else {
        $summary = [];
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

    <div class="cce-card strategy-insights" style="margin-bottom:40px; border-left: 4px solid #0073aa;">
        <h3>💎 Master Architect Strategy Insights</h3>
        <p>Based on your current data, here is your path to 3–5 clients this month:</p>
        <ul style="list-style:disc; padding-left:20px;">
            <li><strong>Lead Velocity:</strong> Captured <?php echo (int) $summary['leads_today']; ?> leads today. Increase this to 10+ to guarantee scale.</li>
            <li><strong>Conversion Ratio:</strong> Your lead-to-client conversion is <strong><?php echo $summary['lead_to_client']; ?>%</strong>.</li>
            <?php if ($summary['lead_to_client'] < 3): ?>
                <li style="color:#d63638;"><strong>Action Required:</strong> Your conversion is below 3%. Review your Offer Builder and Questionnaire.</li>
            <?php else: ?>
                <li style="color:#00a32a;"><strong>Performing Well:</strong> Your funnel is converting efficiently. Scale traffic.</li>
            <?php endif; ?>
        </ul>
    </div>
    <?php else: ?>
        <div class="notice notice-warning"><p>No analytics data available. Start capturing leads to see insights!</p></div>
    <?php endif; ?>

    <div class="cce-quick-actions">
        <h2>Quick Actions</h2>
        <a href="?page=cce-funnels" class="button button-primary button-hero">Create Funnel</a>
        <a href="?page=cce-clients" class="button button-hero">Add Offer</a>
    </div>
</div>
