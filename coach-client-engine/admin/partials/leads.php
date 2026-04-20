<div class="wrap cce-admin-wrap">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="margin:0;">Leads Management</h1>
            <p class="description">Track and manage your potential clients from acquisition to booking.</p>
        </div>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="cce_export_leads">
            <button type="submit" class="button" style="height:40px; border-radius:8px; font-weight:600;">Export to CSV</button>
        </form>
    </div>

    <div class="cce-card cce-card-primary" style="margin-bottom: 40px;">
        <h3>Quick Add Lead</h3>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="margin-top:20px;">
            <input type="hidden" name="action" value="cce_save_lead">
            <?php wp_nonce_field('cce_save_lead_nonce'); ?>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:15px; align-items: end;">
                <div class="cce-form-group">
                    <label style="display:block; font-weight:600; margin-bottom:5px; font-size:0.8rem;">First Name</label>
                    <input type="text" name="first_name" placeholder="John" style="width:100%; padding:10px; border-radius:6px; border:1px solid var(--cce-border);" required>
                </div>
                <div class="cce-form-group">
                    <label style="display:block; font-weight:600; margin-bottom:5px; font-size:0.8rem;">Last Name</label>
                    <input type="text" name="last_name" placeholder="Doe" style="width:100%; padding:10px; border-radius:6px; border:1px solid var(--cce-border);" required>
                </div>
                <div class="cce-form-group">
                    <label style="display:block; font-weight:600; margin-bottom:5px; font-size:0.8rem;">Email Address</label>
                    <input type="email" name="email" placeholder="john@example.com" style="width:100%; padding:10px; border-radius:6px; border:1px solid var(--cce-border);" required>
                </div>
                <button type="submit" class="button cce-btn-primary">Add Lead</button>
            </div>
        </form>
    </div>

    <?php
    global $wpdb;
    $leads = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_leads ORDER BY created_at DESC" );
    ?>

    <div class="cce-card">
        <h3>Master Lead List</h3>
        <table class="wp-list-table widefat fixed striped" style="margin-top:20px; border:none; box-shadow:none;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th style="text-align:right;">Acquisition Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leads)): ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding: 40px 0;">No leads found yet. Time to launch a funnel!</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ( $leads as $lead ): ?>
                        <tr>
                            <td><strong><?php echo esc_html( $lead->first_name . ' ' . $lead->last_name ); ?></strong></td>
                            <td><?php echo esc_html( $lead->email ); ?></td>
                            <td><span class="status-tag"><?php echo esc_html( strtoupper( $lead->status ) ); ?></span></td>
                            <td style="text-align:right; color:var(--cce-text-muted);"><?php echo date('M d, Y', strtotime($lead->created_at)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
