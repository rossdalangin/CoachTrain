<div class="wrap cce-admin-wrap">
    <h1>CRM & Pipeline</h1>
    <hr class="wp-header-end">

    <?php
    global $wpdb;
    $stages = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_crm_stages ORDER BY stage_order ASC" );
    ?>

    <div class="cce-kanban-wrapper" style="display:flex; gap:20px; overflow-x:auto; padding-bottom:30px;">
        <?php foreach ( $stages as $stage ): ?>
            <div class="kanban-column" style="min-width:280px; background:#e2e8f0; border-radius:10px; padding:15px;">
                <h3 style="margin-top:0; color:#4a5568;"><?php echo esc_html( $stage->name ); ?></h3>
                <div class="kanban-cards">
                    <?php
                    $leads = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_leads WHERE crm_stage_id = %d", $stage->id ) );
                    if ($leads): foreach ( $leads as $lead ):
                    ?>
                        <div class="cce-card" style="margin-bottom:10px; border-top:none; border-left:4px solid #0073aa; padding:15px;">
                            <strong><?php echo esc_html( $lead->first_name . ' ' . $lead->last_name ); ?></strong>
                            <div style="font-size:11px; color:#718096; margin-top:5px;"><?php echo esc_html( strtoupper($lead->status) ); ?></div>

                            <div style="margin-top:10px;">
                                <select class="cce-stage-select" data-lead-id="<?php echo $lead->id; ?>" style="font-size:11px; width:100%;">
                                    <?php foreach ( $stages as $s ): ?>
                                        <option value="<?php echo $s->id; ?>" <?php selected( $lead->crm_stage_id, $s->id ); ?>>
                                            Move to: <?php echo esc_html( $s->name ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div style="margin-top:10px; display:flex; gap:5px;">
                                <a href="#" class="button button-small cce-view-notes"
                                   data-lead-id="<?php echo $lead->id; ?>"
                                   data-lead-name="<?php echo esc_attr( $lead->first_name . ' ' . $lead->last_name ); ?>">Activity</a>
                                <a href="#" class="button button-small cce-view-tasks"
                                   data-lead-id="<?php echo $lead->id; ?>"
                                   data-lead-name="<?php echo esc_attr( $lead->first_name . ' ' . $lead->last_name ); ?>">Tasks</a>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                        <div style="font-style:italic; color:#718096; font-size:12px; text-align:center;">Empty</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Leads Modal (Shared for Notes and Tasks) -->
    <div id="cce-leads-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div style="background:#fff; margin:5% auto; padding:25px; width:50%; border-radius:12px; position:relative; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
            <span class="cce-modal-close" style="position:absolute; right:20px; top:15px; cursor:pointer; font-size:24px; color:#888;">&times;</span>
            <h2 id="cce-modal-title" style="margin-top:0;">Lead Details</h2>

            <div class="cce-modal-tabs" style="display:flex; border-bottom:1px solid #eee; margin-bottom:20px;">
                <button class="cce-tab-link active" data-tab="notes" style="background:none; border:none; padding:10px 20px; cursor:pointer; border-bottom:2px solid #0073aa;">Activity</button>
                <button class="cce-tab-link" data-tab="tasks" style="background:none; border:none; padding:10px 20px; cursor:pointer;">Tasks</button>
            </div>

            <div id="cce-tab-notes" class="cce-tab-content">
                <div id="cce-notes-content" style="max-height:300px; overflow-y:auto; margin-bottom:20px; border:1px solid #eee; padding:15px; border-radius:8px; background:#fcfcfc;"></div>
                <div class="cce-add-note-section">
                    <form id="cce-add-note-form">
                        <input type="hidden" class="cce-lead-id-field">
                        <textarea id="cce-new-note-text" placeholder="Add a note..." style="width:100%; border-radius:8px; margin-bottom:10px;" rows="2" required></textarea>
                        <button type="submit" class="button button-primary">Add Note</button>
                    </form>
                </div>
            </div>

            <div id="cce-tab-tasks" class="cce-tab-content" style="display:none;">
                <div id="cce-tasks-content" style="max-height:300px; overflow-y:auto; margin-bottom:20px; border:1px solid #eee; padding:15px; border-radius:8px; background:#fcfcfc;"></div>
                <div class="cce-add-task-section">
                    <form id="cce-add-task-form">
                        <input type="hidden" class="cce-lead-id-field">
                        <div style="display:flex; gap:10px;">
                            <input type="text" id="cce-new-task-title" placeholder="New task title..." style="flex:1; border-radius:8px;" required>
                            <button type="submit" class="button button-primary">Add Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
