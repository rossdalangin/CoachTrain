<div class="wrap cce-admin-wrap">
    <h1>Coach Client Engine - Dashboard</h1>

    <div class="cce-dashboard-grid">
        <div class="cce-card">
            <h3>Leads Today</h3>
            <div class="value"><?php echo (int) get_option('cce_leads_today', 0); ?></div>
        </div>
        <div class="cce-card">
            <h3>Bookings Today</h3>
            <div class="value"><?php echo (int) get_option('cce_bookings_today', 0); ?></div>
        </div>
        <div class="cce-card">
            <h3>Revenue Today</h3>
            <div class="value">$<?php echo number_format((float) get_option('cce_revenue_today', 0), 2); ?></div>
        </div>
    </div>

    <div class="cce-card strategy-insights" style="margin-bottom:40px; border-left: 4px solid #0073aa;">
        <h3>💎 Master Architect Strategy Insights</h3>
        <p>Based on your current data, here is your path to 3–5 clients this month:</p>
        <ul style="list-style:disc; padding-left:20px;">
            <li><strong>Focus on Lead Velocity:</strong> You captured <?php echo (int) get_option('cce_leads_today', 0); ?> leads today. Increase this to 10+ to guarantee high-ticket bookings.</li>
            <li><strong>Optimized Consultation:</strong> Ensure your pre-call questionnaire filters for "high-intent" leads only.</li>
        </ul>
    </div>

    <div class="cce-quick-actions">
        <h2>Quick Actions</h2>
        <a href="?page=cce-funnels" class="button button-primary button-hero">Create Funnel</a>
        <a href="?page=cce-clients" class="button button-hero">Add Offer</a>
    </div>
</div>
