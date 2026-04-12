<div class="wrap cce-admin-wrap">
    <h1>Coach Client Engine - Dashboard</h1>

    <?php
    $analytics = new CCE_Analytics_Manager();
    $summary = $analytics->get_summary( new WP_REST_Request() )->get_data()['data'];
    ?>

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
            <li><strong>Focus on Lead Velocity:</strong> You captured <?php echo (int) $summary['leads_today']; ?> leads today. Increase this to 10+ to guarantee high-ticket bookings.</li>
            <li><strong>Conversion Performance:</strong> Total leads captured: <?php echo (int) $summary['total_leads']; ?>. Total bookings: <?php echo (int) $summary['total_bookings']; ?>.</li>
            <?php if ($summary['total_leads'] > 0): ?>
                <li><strong>Booking Rate:</strong> <?php echo round( ($summary['total_bookings'] / $summary['total_leads']) * 100, 1 ); ?>%. Target 10-15% for optimal ROI.</li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="cce-quick-actions">
        <h2>Quick Actions</h2>
        <a href="?page=cce-funnels" class="button button-primary button-hero">Create Funnel</a>
        <a href="?page=cce-clients" class="button button-hero">Add Offer</a>
    </div>
</div>
