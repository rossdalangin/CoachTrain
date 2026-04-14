<div class="wrap cce-admin-wrap">
    <h1>Automation & Workflows</h1>
    <p class="description">Automate your business so you can focus on coaching. Link events like "Lead Captured" to actions like "Send Email" or "Move CRM Stage".</p>
    <hr class="wp-header-end">

    <div class="cce-modal-tabs" style="display:flex; border-bottom:1px solid #ddd; margin-bottom:20px;">
        <button class="cce-automation-tab-link active" data-tab="rules" style="background:none; border:none; padding:10px 20px; cursor:pointer; border-bottom:2px solid #0073aa;">Automation Rules</button>
        <button class="cce-automation-tab-link" data-tab="templates" style="background:none; border:none; padding:10px 20px; cursor:pointer;">Email Templates</button>
    </div>

    <div id="tab-rules" class="cce-automation-tab-content">
        <div class="cce-card" style="margin-bottom:20px;">
            <h3>Create New Automation Rule</h3>
            <form id="cce-add-automation-form">
                <div style="display:flex; gap:20px; flex-wrap:wrap;">
                    <div>
                        <label>When this happens...</label><br>
                        <select name="trigger_event" id="rule-trigger-event" required>
                            <option value="cce_lead_created">New Lead Captured</option>
                            <option value="cce_booking_confirmed">Consultation Booked</option>
                            <option value="cce_payment_completed">Payment Received</option>
                            <option value="cce_lead_stage_changed">CRM Stage Changed</option>
                        </select>
                    </div>
                    <div>
                        <label>Do this...</label><br>
                        <select name="action_type" id="rule-action-type" required>
                            <option value="send_email">Send Email</option>
                            <option value="move_stage">Move to CRM Stage</option>
                            <option value="schedule_reminder">Schedule Reminder</option>
                            <option value="create_task">Create Task</option>
                        </select>
                    </div>
                    <div id="action-config-stage" style="display:none;">
                        <label>Target Stage</label><br>
                        <select name="config[stage_id]">
                            <?php
                            global $wpdb;
                            $user_id = get_current_user_id();
                            $stages = $wpdb->get_results($wpdb->prepare("SELECT id, name FROM {$wpdb->prefix}cce_crm_stages WHERE user_id = %d", $user_id));
                            foreach($stages as $s) echo "<option value='{$s->id}'>{$s->name}</option>";
                            ?>
                        </select>
                    </div>
                    <div id="action-config-reminder" style="display:none;">
                        <label>Delay (Hours)</label><br>
                        <input type="number" name="config[delay_hours]" value="24" class="small-text">
                    </div>
                    <div id="action-config-task" style="display:none;">
                        <label>Task Title</label><br>
                        <input type="text" name="config[task_title]" placeholder="e.g. Call lead back" class="regular-text">
                    </div>
                    <div id="action-config-email" style="display:block;">
                        <label>Select Template</label><br>
                        <select name="config[template_id]">
                            <option value="">Default Welcome Email</option>
                            <?php
                            $templates = $wpdb->get_results($wpdb->prepare("SELECT id, name FROM {$wpdb->prefix}cce_email_templates WHERE user_id = %d", $user_id));
                            foreach($templates as $t) echo "<option value='{$t->id}'>{$t->name}</option>";
                            ?>
                        </select>
                    </div>
                    <div id="trigger-config-stage" style="display:none;">
                        <label>When moved to...</label><br>
                        <select name="config[trigger_stage_id]">
                            <option value="">Any Stage</option>
                            <?php foreach($stages as $s) echo "<option value='{$s->id}'>{$s->name}</option>"; ?>
                        </select>
                    </div>
                    <div style="align-self: flex-end;">
                        <button type="submit" class="button button-primary">Create Rule</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="cce-card">
            <h3>Active Automation Rules</h3>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Trigger</th>
                        <th>Action</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="cce-rules-list">
                    <?php
                    $rules = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}cce_automation_rules WHERE user_id = %d ORDER BY created_at DESC", $user_id) );
                    if ($rules): foreach ($rules as $rule): ?>
                    <tr id="rule-row-<?php echo $rule->id; ?>"
                        data-trigger="<?php echo esc_attr($rule->trigger_event); ?>"
                        data-action="<?php echo esc_attr($rule->action_type); ?>"
                        data-config='<?php echo esc_attr($rule->config); ?>'>
                        <td><code><?php echo esc_html($rule->trigger_event); ?></code></td>
                        <td><strong><?php echo esc_html(strtoupper(str_replace('_', ' ', $rule->action_type))); ?></strong></td>
                        <td><?php echo $rule->is_active ? 'Active' : 'Inactive'; ?></td>
                        <td>
                            <button class="button button-small cce-edit-rule" data-rule-id="<?php echo $rule->id; ?>">Edit</button>
                            <button class="button button-link-delete cce-delete-rule" data-rule-id="<?php echo $rule->id; ?>" style="color:#d63638;">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="4">No automation rules found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="cce-card" style="margin-top:20px;">
            <h3>Scheduled Workflows (WordPress Cron)</h3>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr><th>Event</th><th>Target ID</th><th>Scheduled Time</th></tr>
                </thead>
                <tbody>
                    <?php
                    $cron = _get_cron_array();
                    $found = false;
                    if ($cron) {
                        foreach ($cron as $timestamp => $events) {
                            if (isset($events['cce_delayed_email_event'])) {
                                foreach ($events['cce_delayed_email_event'] as $key => $event) {
                                    $found = true;
                                    $args = $event['args'];
                                    echo "<tr>
                                        <td><code>cce_delayed_email_event</code></td>
                                        <td>ID: " . esc_html($args[0]) . "</td>
                                        <td>" . date('Y-m-d H:i:s', $timestamp) . "</td>
                                    </tr>";
                                }
                            }
                        }
                    }
                    if (!$found) echo "<tr><td colspan='3'>No pending workflows.</td></tr>";
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="tab-templates" class="cce-automation-tab-content" style="display:none;">
        <div class="cce-card" style="margin-bottom:20px;">
            <h3>Create Email Template</h3>
            <form id="cce-add-template-form">
                <p><label>Template Name</label><br><input type="text" name="name" placeholder="e.g. Welcome & Guide" class="regular-text" required></p>
                <p><label>Email Subject</label><br><input type="text" name="subject" placeholder="e.g. Your Free Coaching Guide is Here!" class="large-text" required></p>
                <p><label>Email Content</label><br>
                <small>Use <code>{{first_name}}</code>, <code>{{last_name}}</code>, or <code>{{email}}</code> for personalization.</small><br>
                <textarea name="content" rows="10" style="width:100%; border-radius:8px;" placeholder="Hi {{first_name}},&#10;&#10;Welcome to the program! Here is your access link: [Link]&#10;&#10;Best,&#10;[Your Name]" required></textarea></p>
                <button type="submit" class="button button-primary">Save Template</button>
            </form>
        </div>

        <div class="cce-card">
            <h3>Saved Templates</h3>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr><th>Name</th><th>Subject</th><th>Created At</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php
                    $templates = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}cce_email_templates WHERE user_id = %d ORDER BY created_at DESC", $user_id));
                    foreach($templates as $t) echo "<tr id='template-row-{$t->id}'
                        data-name='" . esc_attr($t->name) . "'
                        data-subject='" . esc_attr($t->subject) . "'
                        data-content='" . esc_attr($t->content) . "'>
                        <td><strong>" . esc_html($t->name) . "</strong></td>
                        <td>" . esc_html($t->subject) . "</td>
                        <td>" . esc_html($t->created_at) . "</td>
                        <td>
                            <button class='button button-small cce-edit-template' data-id='" . esc_attr($t->id) . "'>Edit</button>
                            <button class='button button-link-delete cce-delete-template' data-id='" . esc_attr($t->id) . "' style='color:#d63638;'>Delete</button>
                        </td>
                    </tr>";
                    if(!$templates) echo "<tr><td colspan='4'>No templates yet.</td></tr>";
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Rule Modal -->
    <div id="cce-edit-rule-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div style="background:#fff; margin:10% auto; padding:25px; width:500px; border-radius:12px; position:relative;">
            <span class="cce-modal-close" style="position:absolute; right:20px; top:15px; cursor:pointer; font-size:24px;">&times;</span>
            <h2>Edit Automation Rule</h2>
            <form id="cce-edit-rule-form">
                <input type="hidden" id="edit-rule-id">
                <div style="display:flex; flex-direction:column; gap:15px;">
                    <div>
                        <label>When this happens...</label><br>
                        <select id="edit-rule-trigger" class="widefat" required>
                            <option value="cce_lead_created">New Lead Captured</option>
                            <option value="cce_booking_confirmed">Consultation Booked</option>
                            <option value="cce_payment_completed">Payment Received</option>
                            <option value="cce_lead_stage_changed">CRM Stage Changed</option>
                        </select>
                    </div>
                    <div id="edit-trigger-config-stage">
                        <label>When moved to...</label><br>
                        <select id="edit-config-trigger-stage-id" class="widefat">
                            <option value="">Any Stage</option>
                            <?php foreach($stages as $s) echo "<option value='{$s->id}'>{$s->name}</option>"; ?>
                        </select>
                    </div>
                    <div>
                        <label>Do this...</label><br>
                        <select id="edit-rule-action" class="widefat" required>
                            <option value="send_email">Send Email</option>
                            <option value="move_stage">Move to CRM Stage</option>
                            <option value="schedule_reminder">Schedule Reminder</option>
                            <option value="create_task">Create Task</option>
                        </select>
                    </div>
                    <div id="edit-action-config-stage">
                        <label>Target Stage</label><br>
                        <select id="edit-config-stage-id" class="widefat">
                            <?php foreach($stages as $s) echo "<option value='{$s->id}'>{$s->name}</option>"; ?>
                        </select>
                    </div>
                    <div id="edit-action-config-reminder">
                        <label>Delay (Hours)</label><br>
                        <input type="number" id="edit-config-delay" class="widefat">
                    </div>
                    <div id="edit-action-config-task">
                        <label>Task Title</label><br>
                        <input type="text" id="edit-config-task-title" class="widefat">
                    </div>
                    <div id="edit-action-config-email">
                        <label>Select Template</label><br>
                        <select id="edit-config-template-id" class="widefat">
                            <option value="">Default Welcome Email</option>
                            <?php foreach($templates as $t) echo "<option value='{$t->id}'>{$t->name}</option>"; ?>
                        </select>
                    </div>
                    <button type="submit" class="button button-primary">Update Rule</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Template Modal -->
    <div id="cce-edit-template-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div style="background:#fff; margin:5% auto; padding:25px; width:600px; border-radius:12px; position:relative;">
            <span class="cce-modal-close" style="position:absolute; right:20px; top:15px; cursor:pointer; font-size:24px;">&times;</span>
            <h2>Edit Email Template</h2>
            <form id="cce-edit-template-form">
                <input type="hidden" id="edit-template-id">
                <p><label>Template Name</label><br><input type="text" id="edit-template-name" class="widefat" required></p>
                <p><label>Email Subject</label><br><input type="text" id="edit-template-subject" class="widefat" required></p>
                <p><label>Email Content</label><br>
                <textarea id="edit-template-content" rows="10" class="widefat" required></textarea></p>
                <button type="submit" class="button button-primary">Update Template</button>
            </form>
        </div>
    </div>
</div>
