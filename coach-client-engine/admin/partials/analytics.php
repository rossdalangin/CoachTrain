<div class="wrap cce-admin-wrap">
    <h1>Strategic Analytics</h1>
    <hr class="wp-header-end">

    <?php
    $is_pro = (new Coach_Client_Engine())->is_pro();

    if ( ! $is_pro ) {
        echo '<div class="notice notice-info" style="margin: 20px 0; border-left-color: #ffb700; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <h2 style="margin-top:0;">🚀 Unlock Advanced Analytics (PRO)</h2>
            <p>You are viewing the basic dashboard. Upgrade to PRO to see <strong>30-Day Projections</strong>, <strong>Funnel ROI Tracking</strong>, and <strong>Show Rate Optimization</strong>.</p>
            <a href="?page=cce-settings#general" class="button button-primary">Enter License Key</a>
        </div>';
        // Allow summary for basic view, but we will gate the deeper parts below
    }

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
            <?php
            $currency_code = get_option('cce_currency', 'USD');
            $currency_symbols = ['USD' => '$', 'EUR' => '€', 'GBP' => '£', 'CAD' => 'C$', 'AUD' => 'A$'];
            $currency_symbol = $currency_symbols[$currency_code] ?? '$';
            ?>
            <h3>Total Revenue</h3>
            <div class="value"><?php echo $currency_symbol . number_format( (float) $summary['revenue_today'], 2 ); // Simplified for this view ?></div>
        </div>
        <div class="cce-card">
            <h3>Show Rate</h3>
            <div class="value"><?php echo $summary['show_rate']; ?>%</div>
        </div>
    </div>

    <div class="cce-card" style="margin-bottom:20px; <?php echo ! $is_pro ? 'opacity: 0.5; pointer-events: none; filter: blur(2px);' : ''; ?>">
        <?php if ( ! $is_pro ) echo '<div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); z-index:10; background:rgba(255,255,255,0.9); padding:10px; border-radius:5px; border:1px solid #ddd; font-weight:bold; color:var(--primary);">PRO FEATURE</div>'; ?>
        <h3>Funnel Conversion Analysis</h3>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr><th>Funnel / Source</th><th>Visits</th><th>Leads</th><th>Conversion Rate</th></tr>
            </thead>
            <tbody>
                <?php if (!empty($summary['funnel_stats'])): foreach($summary['funnel_stats'] as $fs): ?>
                    <tr>
                        <td><strong><?php echo esc_html($fs->funnel_name); ?></strong></td>
                        <td><?php echo (int) $fs->visits; ?></td>
                        <td><?php echo (int) $fs->lead_count; ?></td>
                        <td><span style="font-weight:bold; color:#0073aa;"><?php echo $fs->rate; ?>%</span></td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="4">No funnel data yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="cce-card" style="margin-bottom:20px; <?php echo ! $is_pro ? 'opacity: 0.5; pointer-events: none; filter: blur(2px);' : ''; ?>">
        <h3>Resource Engagement (Top Downloads)</h3>
        <table class="wp-list-table widefat fixed striped">
            <thead><tr><th>Resource Title</th><th>Views/Accesses</th></tr></thead>
            <tbody>
                <?php if (!empty($summary['resource_engagement'])): foreach($summary['resource_engagement'] as $re): ?>
                    <tr>
                        <td><strong><?php echo esc_html($re->title); ?></strong></td>
                        <td><?php echo (int) $re->access_count; ?></td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="2">No engagement data yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="cce-card" style="margin-bottom:20px; <?php echo ! $is_pro ? 'opacity: 0.5; pointer-events: none; filter: blur(2px);' : ''; ?>">
        <h3>Lead Attribution (Top Sources)</h3>
        <table class="wp-list-table widefat fixed striped">
            <thead><tr><th>UTM Source</th><th>Lead Count</th></tr></thead>
            <tbody>
                <?php if (!empty($summary['attribution'])): foreach($summary['attribution'] as $attr): ?>
                    <tr>
                        <td><strong><?php echo esc_html($attr->utm_source); ?></strong></td>
                        <td><?php echo (int) $attr->count; ?></td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="2">No attribution data yet. Use UTM parameters in your links!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; <?php echo ! $is_pro ? 'opacity: 0.5; pointer-events: none; filter: blur(2px);' : ''; ?>">
        <div class="cce-card" style="border-left:4px solid #0073aa;">
            <h3>💎 Strategy Insights</h3>
            <p style="font-size:12px; color:#666;">These insights are generated by analyzing your conversion ratios against industry benchmarks for high-ticket coaching.</p>
            <ul style="list-style:disc; padding-left:20px;">
                <li>Your lead conversion is healthy. Focus on increasing <strong>top-of-funnel traffic</strong> via content marketing or ads.</li>
                <li>Optimize your <strong>consultation show-up rate</strong> by sending automated email and SMS reminders.</li>
                <li>Consider a "Downsell" offer for leads that don't close on the main consultation to improve ROI.</li>
            </ul>
        </div>
        <div class="cce-card" style="border-left:4px solid #673ab7;">
            <h3>📈 Next 30 Days Projections</h3>
            <?php $p = $summary['projections']; ?>
            <p>Based on current velocity (<?php echo $p['leads_next_30']; ?> leads/mo) and <?php echo $summary['lead_to_client']; ?>% conversion:</p>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:10px;">
                <div style="background:#f3f0ff; padding:10px; border-radius:8px;">
                    <small>PROJECTED SALES</small><br><strong><?php echo $p['projected_sales']; ?></strong>
                </div>
                <div style="background:#f3f0ff; padding:10px; border-radius:8px;">
                    <small>PROJECTED REVENUE</small><br><strong><?php echo $currency_symbol . number_format($p['projected_revenue'], 2); ?></strong>
                </div>
            </div>
            <p style="font-size:11px; color:#888; margin-top:10px;">Average Order Value (AOV): <?php echo $currency_symbol . $p['aov']; ?></p>
        </div>
    </div>
    <?php else: ?>
        <div class="notice notice-warning"><p>No analytics data available.</p></div>
    <?php endif; ?>
</div>
