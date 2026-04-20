<div class="wrap cce-admin-wrap">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="margin:0;">CRM & Pipeline</h1>
            <p class="description">Visualize your sales process and move leads through your high-ticket funnel.</p>
        </div>
        <div class="status-tag" style="background: var(--cce-sidebar); color: #fff;">Kanban View</div>
    </div>

    <?php
    global $wpdb;
    $stages = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_crm_stages ORDER BY stage_order ASC" );
    ?>

    <div class="cce-kanban-wrapper" style="display:flex; gap:20px; overflow-x:auto; padding-bottom:30px; margin: 0 -10px;">
        <?php foreach ( $stages as $stage ): ?>
            <div class="kanban-column" style="min-width:300px; background: #f1f5f9; border-radius:12px; padding:20px; border: 1px solid var(--cce-border);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h3 style="margin:0; font-size:1rem; color:var(--cce-text-main); font-weight:700;"><?php echo esc_html( $stage->name ); ?></h3>
                    <?php
                    $lead_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_leads WHERE crm_stage_id = %d", $stage->id ) );
                    ?>
                    <span style="background:var(--cce-border); color:var(--cce-text-muted); padding:2px 8px; border-radius:10px; font-size:0.75rem; font-weight:600;"><?php echo (int)$lead_count; ?></span>
                </div>

                <div class="kanban-cards">
                    <?php
                    $leads = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_leads WHERE crm_stage_id = %d", $stage->id ) );
                    foreach ( $leads as $lead ):
                    ?>
                        <div class="cce-card" style="margin-bottom:15px; padding:15px; border-left:4px solid var(--cce-primary); transition: none;">
                            <div style="font-weight:700; color:var(--cce-text-main); margin-bottom:5px;">
                                <?php echo esc_html( $lead->first_name . ' ' . $lead->last_name ); ?>
                            </div>
                            <div style="font-size:0.75rem; color:var(--cce-text-muted); margin-bottom:10px;">
                                <?php echo esc_html( $lead->email ); ?>
                            </div>
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <span class="status-tag" style="font-size:0.65rem; padding:2px 8px;"><?php echo esc_html( strtoupper($lead->status) ); ?></span>
                                <a href="#" style="text-decoration:none; color:var(--cce-primary); font-size:0.75rem; font-weight:600;">Details →</a>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($leads)): ?>
                        <div style="text-align:center; padding:30px 0; color:var(--cce-text-muted); font-size:0.875rem; border:2px dashed var(--cce-border); border-radius:8px;">
                            No leads here
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
