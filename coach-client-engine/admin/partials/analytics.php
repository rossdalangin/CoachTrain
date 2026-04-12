<div class="wrap cce-admin-wrap">
    <h1>Strategic Analytics</h1>
    <hr class="wp-header-end">

    <div class="cce-dashboard-grid">
        <div class="cce-card">
            <h3>Total Visitors</h3>
            <div class="value"><?php echo (int) get_option('cce_total_visitors', 0); ?></div>
        </div>
        <div class="cce-card">
            <h3>Lead Conversion</h3>
            <div class="value">
                <?php
                global $wpdb;
                $visitors = (int) get_option('cce_total_visitors', 0);
                $leads = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_leads" );
                echo $visitors > 0 ? round( ($leads / $visitors) * 100, 1 ) : 0;
                ?>%
            </div>
        </div>
        <div class="cce-card">
            <h3>Total Revenue</h3>
            <div class="value">$<?php echo number_format( (float) $wpdb->get_var( "SELECT SUM(amount) FROM {$wpdb->prefix}cce_payments WHERE status = 'completed'" ), 2 ); ?></div>
        </div>
    </div>

    <div class="cce-card" style="border-left:4px solid #0073aa;">
        <h3>💎 Strategy Insights</h3>
        <p>Your path to scaling to $10k/month:</p>
        <ul style="list-style:disc; padding-left:20px;">
            <li>Your lead conversion is healthy. Focus on increasing <strong>top-of-funnel traffic</strong>.</li>
            <li>Optimize your <strong>consultation show-up rate</strong> by sending automated 1-hour reminders.</li>
        </ul>
    </div>
</div>
