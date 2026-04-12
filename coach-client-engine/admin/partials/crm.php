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
                    foreach ( $leads as $lead ):
                    ?>
                        <div class="cce-card" style="margin-bottom:10px; border-top:none; border-left:4px solid #0073aa;">
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

                            <div style="margin-top:10px;">
                                <a href="#" class="button button-small cce-view-notes"
                                   data-lead-id="<?php echo $lead->id; ?>"
                                   data-lead-name="<?php echo esc_attr( $lead->first_name . ' ' . $lead->last_name ); ?>">View Activity</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Notes Modal -->
    <div id="cce-notes-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div style="background:#fff; margin:10% auto; padding:20px; width:50%; border-radius:10px; position:relative;">
            <span class="cce-modal-close" style="position:absolute; right:20px; top:10px; cursor:pointer; font-size:24px;">&times;</span>
            <h2 id="cce-modal-title">Lead Notes</h2>
            <div id="cce-notes-content" style="max-height:400px; overflow-y:auto;"></div>
        </div>
    </div>
</div>
