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
                                <a href="#" class="button button-small">View Notes</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
