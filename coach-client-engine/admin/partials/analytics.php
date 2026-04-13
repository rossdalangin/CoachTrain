<div class="wrap cce-admin-wrap">
    <h1>Strategic Analytics</h1>
    <hr class="wp-header-end">

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
            <h3>Total Visitors</h3>
            <div class="value"><?php echo (int) $summary['total_visitors']; ?></div>
        </div>
        <div class="cce-card">
            <h3>Lead Conversion</h3>
            <div class="value">
                <?php
                $visitors = $summary['total_visitors'];
                $leads = $summary['total_leads'];
                echo $visitors > 0 ? round( ($leads / $visitors) * 100, 1 ) : 0;
                ?>%
            </div>
        </div>
        <div class="cce-card">
            <h3>Total Revenue</h3>
            <div class="value">$<?php echo number_format( (float) $summary['revenue_today'], 2 ); // Simplified for this view ?></div>
        </div>
    </div>

    <div class="cce-card" style="margin-bottom:20px;">
        <h3>Funnel Performance (Leads)</h3>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr><th>Funnel / Source</th><th>Total Leads</th></tr>
            </thead>
            <tbody>
                <?php if (!empty($summary['funnel_stats'])): foreach($summary['funnel_stats'] as $fs): ?>
                    <tr><td><strong><?php echo esc_html($fs->funnel_name); ?></strong></td><td><?php echo (int) $fs->lead_count; ?></td></tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="2">No funnel data yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="cce-card" style="border-left:4px solid #0073aa;">
        <h3>💎 Strategy Insights</h3>
        <p>Your path to scaling to $10k/month:</p>
        <ul style="list-style:disc; padding-left:20px;">
            <li>Your lead conversion is healthy. Focus on increasing <strong>top-of-funnel traffic</strong>.</li>
            <li>Optimize your <strong>consultation show-up rate</strong> by sending automated reminders.</li>
        </ul>
    </div>
    <?php else: ?>
        <div class="notice notice-warning"><p>No analytics data available.</p></div>
    <?php endif; ?>
</div>
