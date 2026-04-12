<div class="wrap cce-admin-wrap">
    <h1>Automation & Workflows</h1>
    <hr class="wp-header-end">

    <div class="cce-card" style="margin-bottom:20px;">
        <h3>Create New Automation Rule</h3>
        <form id="cce-add-automation-form">
            <div style="display:flex; gap:20px; flex-wrap:wrap;">
                <div>
                    <label>When this happens...</label><br>
                    <select name="trigger_event" required>
                        <option value="cce_lead_created">New Lead Captured</option>
                        <option value="cce_booking_confirmed">Consultation Booked</option>
                        <option value="cce_payment_completed">Payment Received</option>
                    </select>
                </div>
                <div>
                    <label>Do this...</label><br>
                    <select name="action_type" required>
                        <option value="send_email">Send Welcome/Guide Email</option>
                        <option value="move_stage">Move to CRM Stage</option>
                    </select>
                </div>
                <div id="action-config-stage" style="display:none;">
                    <label>Target Stage</label><br>
                    <select name="config[stage_id]">
                        <?php
                        global $wpdb;
                        $stages = $wpdb->get_results("SELECT id, name FROM {$wpdb->prefix}cce_crm_stages");
                        foreach($stages as $s) echo "<option value='{$s->id}'>{$s->name}</option>";
                        ?>
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
                $rules = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_automation_rules ORDER BY created_at DESC" );
                if ($rules): foreach ($rules as $rule): ?>
                <tr>
                    <td><code><?php echo esc_html($rule->trigger_event); ?></code></td>
                    <td><strong><?php echo esc_html(strtoupper(str_replace('_', ' ', $rule->action_type))); ?></strong></td>
                    <td><?php echo $rule->is_active ? 'Active' : 'Inactive'; ?></td>
                    <td>
                        <button class="button button-link-delete cce-delete-rule" data-rule-id="<?php echo $rule->id; ?>" style="color:#d63638;">Delete</button>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="4">No automation rules found. Create your first one above!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
