<div class="wrap cce-admin-wrap">
    <h1>Leads Management</h1>
    <hr class="wp-header-end">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="cce_export_leads">
            <button type="submit" class="button">Export to CSV</button>
        </form>
    </div>

    <div class="cce-card" style="margin-bottom: 20px;">
        <h3>Add New Lead</h3>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="cce_save_lead">
            <?php wp_nonce_field('cce_save_lead_nonce'); ?>
            <div style="display:flex; gap:10px;">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <button type="submit" class="button button-primary">Add Lead</button>
            </div>
        </form>
    </div>

    <?php
    global $wpdb;
    $leads = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_leads ORDER BY created_at DESC" );
    ?>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $leads as $lead ): ?>
                <tr>
                    <td><strong><?php echo esc_html( $lead->first_name . ' ' . $lead->last_name ); ?></strong></td>
                    <td><?php echo esc_html( $lead->email ); ?></td>
                    <td><span class="status-tag"><?php echo esc_html( strtoupper( $lead->status ) ); ?></span></td>
                    <td><?php echo esc_html( $lead->created_at ); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
