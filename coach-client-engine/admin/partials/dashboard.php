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

    <div class="cce-quick-actions">
        <h2>Quick Actions</h2>
        <a href="?page=cce-funnels" class="button button-primary button-hero">Create Funnel</a>
        <a href="?page=cce-clients" class="button button-hero">Add Offer</a>
    </div>
</div>
