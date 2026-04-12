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
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($leads): foreach ( $leads as $lead ): ?>
                <tr>
                    <td><strong><?php echo esc_html( $lead->first_name . ' ' . $lead->last_name ); ?></strong></td>
                    <td><?php echo esc_html( $lead->email ); ?></td>
                    <td><span class="status-tag"><?php echo esc_html( strtoupper( $lead->status ) ); ?></span></td>
                    <td><?php echo esc_html( $lead->created_at ); ?></td>
                    <td>
                        <button class="button button-link-delete cce-delete-lead" data-lead-id="<?php echo $lead->id; ?>" style="color:#d63638;">Delete</button>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="5">No leads found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
    jQuery(document).ready(function($) {
        $('.cce-delete-lead').on('click', function() {
            if(!confirm('Are you sure you want to delete this lead?')) return;
            const leadId = $(this).data('lead-id');
            $.ajax({
                url: cceAdmin.restUrl + 'leads/' + leadId,
                method: 'DELETE',
                beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', cceAdmin.nonce); },
                success: function(res) { if(res.success) location.reload(); }
            });
        });
    });
    </script>
</div>
