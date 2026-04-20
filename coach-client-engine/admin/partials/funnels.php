<div class="wrap cce-admin-wrap">
    <h1>Funnel Engine</h1>
    <p class="description">Build and manage your conversion funnels using the Hormozi/Brunson methodology.</p>
    <hr class="wp-header-end">

    <?php
    global $wpdb;
    $funnels = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_funnels" );
    ?>

    <div class="cce-card cce-card-primary" style="margin-top:20px;">
        <h3>Your Active Funnels</h3>
        <table class="wp-list-table widefat fixed striped" style="margin-top:15px; border:none; box-shadow:none;">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty($funnels) ): ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding: 40px 0;">No funnels found. Use a template below to get started.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ( $funnels as $funnel ): ?>
                        <tr>
                            <td><strong><?php echo esc_html( $funnel->title ); ?></strong></td>
                            <td><span class="status-tag"><?php echo esc_html( strtoupper( $funnel->type ) ); ?></span></td>
                            <td><?php echo esc_html( strtoupper( $funnel->status ) ); ?></td>
                            <td style="text-align:right;"><a href="#" class="button cce-btn-primary">Edit Steps</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top:40px;">
        <h3>Strategic Pre-built Templates</h3>
        <p class="description">One-click deployment for proven coaching funnel architectures.</p>

        <div class="cce-dashboard-grid" style="margin-top:20px;">
            <div class="cce-card">
                <div style="font-size: 2rem; margin-bottom: 15px;">🧲</div>
                <h4>Lead Magnet Funnel</h4>
                <p style="color:var(--cce-text-muted); font-size: 0.9rem; margin-bottom: 20px;">Ideal for building your email list. Visitor -> Opt-in -> Thank You Page.</p>
                <button class="button cce-btn-primary" style="width:100%;">Use Template</button>
            </div>
            <div class="cce-card">
                <div style="font-size: 2rem; margin-bottom: 15px;">📅</div>
                <h4>Consultation Funnel</h4>
                <p style="color:var(--cce-text-muted); font-size: 0.9rem; margin-bottom: 20px;">High-ticket booking flow. Visitor -> Opt-in -> Booking -> Thank You.</p>
                <button class="button cce-btn-primary" style="width:100%;">Use Template</button>
            </div>
            <div class="cce-card">
                <div style="font-size: 2rem; margin-bottom: 15px;">🎥</div>
                <h4>VSL Funnel</h4>
                <p style="color:var(--cce-text-muted); font-size: 0.9rem; margin-bottom: 20px;">Video Sales Letter flow. Visitor -> VSL -> Booking -> Success.</p>
                <button class="button cce-btn-primary" style="width:100%;">Use Template</button>
            </div>
        </div>
    </div>
</div>
