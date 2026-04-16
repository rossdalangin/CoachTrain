<div class="wrap cce-admin-wrap">
    <h1>Leads Management</h1>
    <p class="description">Your leads are your greatest asset. Manage, tag, and import your contacts here. Use the "Status" tags to prioritize your daily outreach.</p>
    <hr class="wp-header-end">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
        <form method="get" action="">
            <input type="hidden" name="page" value="cce-leads">
            <input type="search" name="s" value="<?php echo esc_attr($_GET['s'] ?? ''); ?>" placeholder="Search leads...">
            <button type="submit" class="button">Search</button>
        </form>

        <div style="display:flex; gap:10px;">
            <select id="cce-bulk-action-selector">
                <option value="">Bulk Actions</option>
                <option value="delete">Delete Selected</option>
                <option value="cold">Mark as COLD</option>
                <option value="warm">Mark as WARM</option>
                <option value="hot">Mark as HOT</option>
            </select>
            <button class="button" id="cce-apply-bulk-action">Apply</button>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="cce_export_leads">
                <?php wp_nonce_field('cce_export_leads_nonce'); ?>
                <button type="submit" class="button">Export to CSV</button>
            </form>
        </div>
    </div>

    <div class="cce-card" style="margin-bottom: 20px; display:flex; gap:20px;">
        <div style="flex:1;">
            <h3>Add New Lead</h3>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="cce_save_lead">
            <?php wp_nonce_field('cce_save_lead_nonce'); ?>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="phone" placeholder="Phone">
                <button type="submit" class="button button-primary">Add Lead</button>
            </div>
        </form>
        </div>
        <div style="flex:1; border-left:1px solid #eee; padding-left:20px;">
            <h3>Import Leads (CSV)</h3>
            <p style="font-size:12px; color:#666;">Moving from another CRM? Upload your list here. <br>Format: <code>first_name,last_name,email</code></p>
            <input type="file" id="cce-import-csv" accept=".csv" style="display:block; margin-bottom:10px;">
            <button class="button" id="cce-start-import">Start Import</button>
            <div id="import-status" style="margin-top:10px;"></div>
        </div>
    </div>

    <?php
    global $wpdb;
    $user_id = get_current_user_id();
    $search = $_GET['s'] ?? '';

    $query = "SELECT * FROM {$wpdb->prefix}cce_leads WHERE user_id = %d";
    $params = array( $user_id );

    if ( ! empty( $search ) ) {
        $query .= " AND (first_name LIKE %s OR last_name LIKE %s OR email LIKE %s)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $query .= " ORDER BY created_at DESC";
    $leads = $wpdb->get_results( $wpdb->prepare( $query, $params ) );
    ?>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width:30px;"><input type="checkbox" id="cce-select-all-leads"></th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Tags</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($leads): foreach ( $leads as $lead ): ?>
                <tr id="lead-row-<?php echo $lead->id; ?>"
                    data-first-name="<?php echo esc_attr($lead->first_name); ?>"
                    data-last-name="<?php echo esc_attr($lead->last_name); ?>"
                    data-email="<?php echo esc_attr($lead->email); ?>"
                    data-phone="<?php echo esc_attr($lead->phone); ?>"
                    data-status="<?php echo esc_attr($lead->status); ?>"
                    data-tags="<?php echo esc_attr($lead->tags); ?>">
                    <td><input type="checkbox" class="cce-lead-checkbox" value="<?php echo $lead->id; ?>"></td>
                    <td><strong><?php echo esc_html( $lead->first_name . ' ' . $lead->last_name ); ?></strong></td>
                    <td><?php echo esc_html( $lead->email ); ?></td>
                    <td><span class="status-tag"><?php echo esc_html( strtoupper( $lead->status ) ); ?></span></td>
                    <td><small style="color:#666;"><?php echo esc_html($lead->tags ?: '-'); ?></small></td>
                    <td><?php echo esc_html( $lead->created_at ); ?></td>
                    <td>
                        <button class="button button-small cce-edit-lead" data-lead-id="<?php echo $lead->id; ?>">Edit</button>
                        <button class="button button-link-delete cce-delete-lead" data-lead-id="<?php echo $lead->id; ?>" style="color:#d63638;">Delete</button>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="6">No leads found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Edit Lead Modal -->
    <div id="cce-edit-lead-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div style="background:#fff; margin:10% auto; padding:25px; width:400px; border-radius:12px; position:relative;">
            <span class="cce-modal-close" style="position:absolute; right:20px; top:15px; cursor:pointer; font-size:24px;">&times;</span>
            <h2>Edit Lead</h2>
            <form id="cce-edit-lead-form">
                <input type="hidden" name="id" id="edit-lead-id">
                <p><label>First Name</label><br><input type="text" name="first_name" id="edit-lead-first-name" class="widefat" required></p>
                <p><label>Last Name</label><br><input type="text" name="last_name" id="edit-lead-last-name" class="widefat" required></p>
                <p><label>Email</label><br><input type="email" name="email" id="edit-lead-email" class="widefat" required></p>
                <p><label>Phone</label><br><input type="text" name="phone" id="edit-lead-phone" class="widefat"></p>
                <p><label>Status</label><br>
                    <select name="status" id="edit-lead-status" class="widefat">
                        <option value="cold">COLD</option>
                        <option value="warm">WARM</option>
                        <option value="hot">HOT</option>
                    </select>
                </p>
                <p><label>Tags (comma separated)</label><br><input type="text" name="tags" id="edit-lead-tags" class="widefat" placeholder="Consultant, Agency, High-Ticket"></p>
                <button type="submit" class="button button-primary">Update Lead</button>
            </form>
        </div>
    </div>
</div>
