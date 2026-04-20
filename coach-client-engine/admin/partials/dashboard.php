<div class="wrap cce-admin-wrap">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="margin:0;">Master Dashboard</h1>
            <p class="description">Command center for your coaching empire.</p>
        </div>
        <div class="status-tag" style="padding: 8px 16px;">System Online</div>
    </div>

    <div class="cce-dashboard-grid">
        <div class="cce-card cce-card-primary">
            <h3>Leads Today</h3>
            <div class="value"><?php echo (int) get_option('cce_leads_today', 0); ?></div>
        </div>
        <div class="cce-card cce-card-primary">
            <h3>Bookings Today</h3>
            <div class="value"><?php echo (int) get_option('cce_bookings_today', 0); ?></div>
        </div>
        <div class="cce-card cce-card-primary">
            <h3>Revenue Today</h3>
            <div class="value">$<?php echo number_format((float) get_option('cce_revenue_today', 0), 2); ?></div>
        </div>
    </div>

    <div class="cce-card strategy-insights" style="margin-bottom:40px; border-left: 4px solid var(--cce-primary); background: #f0f7ff;">
        <h3 style="color: var(--cce-primary); display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.2rem;">💎</span> Master Architect Strategy Insights
        </h3>
        <p style="margin: 15px 0; font-weight: 500;">Based on your current data, here is your path to 3–5 clients this month:</p>
        <ul style="list-style:none; padding:0; margin:0;">
            <li style="margin-bottom: 12px; display: flex; gap: 10px;">
                <span style="color: var(--cce-primary);">✓</span>
                <span><strong>Focus on Lead Velocity:</strong> You captured <?php echo (int) get_option('cce_leads_today', 0); ?> leads today. Increase this to 10+ to guarantee high-ticket bookings.</span>
            </li>
            <li style="display: flex; gap: 10px;">
                <span style="color: var(--cce-primary);">✓</span>
                <span><strong>Optimized Consultation:</strong> Ensure your pre-call questionnaire filters for "high-intent" leads only.</span>
            </li>
        </ul>
    </div>

    <div class="cce-quick-actions">
        <h3 style="margin-bottom: 25px; text-transform: none; color: var(--cce-text-main); font-size: 1.25rem;">Scale Your Operations</h3>
        <div style="display: flex; justify-content: center; gap: 15px;">
            <a href="?page=cce-funnels" class="button cce-btn-primary">Build New Funnel</a>
            <a href="?page=cce-clients" class="button" style="height:40px; line-height:38px; border-radius:8px; padding: 0 24px; font-weight:600;">Create Grand Slam Offer</a>
        </div>
    </div>
</div>
