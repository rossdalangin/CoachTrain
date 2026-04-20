<div class="wrap cce-admin-wrap">
    <h1>Client Portal Management</h1>
    <p class="description">The Client Portal is where your clients access their coaching materials. Organize your resources by category and define a clear roadmap for their success.</p>
    <hr class="wp-header-end">

    <div class="cce-card" style="margin-bottom:20px;">
        <h3>Add New Resource</h3>
        <form id="cce-add-resource-form">
            <div style="display:flex; gap:15px; flex-wrap:wrap;">
                <div style="flex:1;">
                    <label>Title</label><br>
                    <input type="text" name="title" placeholder="e.g. Week 1: Mindset Shift" class="widefat" required>
                </div>
                <div style="flex:1;">
                    <label>Category</label><br>
                    <input type="text" name="category" placeholder="e.g. Core Training" class="widefat">
                </div>
                <div style="width:150px;">
                    <label>Type</label><br>
                    <select name="type" class="widefat">
                        <option value="PDF">PDF Document</option>
                        <option value="Video">Video Link</option>
                        <option value="Link">External Link</option>
                    </select>
                </div>
                <div style="flex:1;">
                    <label>URL</label><br>
                    <input type="url" name="url" class="widefat" placeholder="https://..." required>
                </div>
                <div style="width:150px;">
                    <label>Visibility</label><br>
                    <select name="visibility" class="widefat">
                        <option value="public">Public</option>
                        <option value="clients_only">Active Clients Only</option>
                    </select>
                </div>
                <div style="align-self:flex-end;">
                    <button type="submit" class="button button-primary">Save Resource</button>
                </div>
            </div>
        </form>
    </div>

    <div class="cce-card" style="margin-bottom:20px; border-left: 4px solid #00a32a;">
        <h3>Onboarding Task Builder</h3>
        <p style="font-size:12px; color:#666;">These tasks appear as a checklist for new clients. <strong>Recommended:</strong> 'Watch Welcome Video', 'Join Facebook Group', 'Complete Onboarding Survey'.</p>
        <form id="cce-add-onboarding-task-form">
            <div style="display:flex; gap:15px; align-items:center;">
                <input type="text" id="new-onboarding-task-name" placeholder="Task name (e.g. Join Community)" class="regular-text" required>
                <button type="submit" class="button button-primary">Add Onboarding Task</button>
            </div>
        </form>
        <table class="wp-list-table widefat fixed striped" style="margin-top:15px;">
            <thead><tr><th>Task Name</th><th>Action</th></tr></thead>
            <tbody>
                <?php
                global $wpdb;
                $user_id = get_current_user_id();
                $onboarding_tasks = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}cce_onboarding_tasks WHERE user_id = %d ORDER BY task_order ASC", $user_id));
                if($onboarding_tasks): foreach($onboarding_tasks as $ot): ?>
                <tr>
                    <td><?php echo esc_html($ot->task_name); ?></td>
                    <td><button class="button button-link-delete cce-delete-onboarding-task" data-id="<?php echo $ot->id; ?>" style="color:#d63638;">×</button></td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="2">No custom tasks. Default tasks will be used.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="cce-card">
        <h3>Portal Resources</h3>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Visibility</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="cce-resources-list">
                <?php
                global $wpdb;
                $resources = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cce_resources WHERE user_id = %d ORDER BY created_at DESC", $user_id) );
                if ($resources): foreach ($resources as $r): ?>
                <tr>
                    <td><strong><?php echo esc_html($r->title); ?></strong></td>
                    <td><?php echo esc_html($r->category); ?></td>
                    <td><?php echo esc_html($r->type); ?></td>
                    <td><?php echo $r->visibility === 'public' ? 'Public' : 'Clients Only'; ?></td>
                    <td>
                        <button class="button button-link-delete cce-delete-resource" data-id="<?php echo $r->id; ?>" style="color:#d63638;">Delete</button>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="4">No resources found. Add your first one above!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('#cce-add-onboarding-task-form').on('submit', function(e) {
            e.preventDefault();
            const data = { task_name: $('#new-onboarding-task-name').val() };
            cceApi('portal/onboarding-tasks', 'POST', data, () => location.reload());
        });

        $(document).on('click', '.cce-delete-onboarding-task', function() {
            if(!confirm('Delete onboarding task?')) return;
            cceApi('portal/onboarding-tasks/' + $(this).attr('data-id') + '/delete', 'POST', {}, () => location.reload());
        });

        $('#cce-add-resource-form').on('submit', function(e) {
            e.preventDefault();
            const data = {};
            $(this).serializeArray().forEach(item => data[item.name] = item.value);
            cceApi('portal/resources', 'POST', data, () => location.reload());
        });

        $(document).on('click', '.cce-delete-resource', function() {
            if(!confirm('Delete this resource?')) return;
            cceApi('portal/resources/' + $(this).data('id'), 'DELETE', {}, () => location.reload());
        });
    });
    </script>
</div>
