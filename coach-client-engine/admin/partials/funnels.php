<div class="wrap cce-admin-wrap">
    <h1>Funnel Engine</h1>
    <hr class="wp-header-end">

    <?php
    global $wpdb;
    $funnels = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_funnels" );
    ?>

    <div class="cce-card">
        <h3>Your Funnels</h3>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $funnels as $funnel ): ?>
                    <tr>
                        <td><strong><?php echo esc_html( $funnel->title ); ?></strong></td>
                        <td><?php echo esc_html( strtoupper( $funnel->type ) ); ?></td>
                        <td><?php echo esc_html( strtoupper( $funnel->status ) ); ?></td>
                        <td><a href="#" class="button">Edit Steps</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="cce-card" style="margin-top:20px;">
        <h3>Pre-built Templates</h3>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
            <div class="cce-card" style="border:1px solid #ddd;">
                <h4>Lead Magnet Funnel</h4>
                <p>Visitor -> Opt-in -> Thank You</p>
                <button class="button button-primary">Use Template</button>
            </div>
            <div class="cce-card" style="border:1px solid #ddd;">
                <h4>Consultation Funnel</h4>
                <p>Visitor -> Opt-in -> Booking -> Thank You</p>
                <button class="button button-primary">Use Template</button>
            </div>
        </div>
    </div>
</div>
