<div class="wrap cce-admin-wrap">
    <h1>CRM & Pipeline</h1>
    <p class="description">Your Sales Command Center. Move leads from left to right as they progress through your sales process.</p>
    <hr class="wp-header-end">

    <div class="cce-card" style="margin-bottom:20px; border-left:4px solid #ffb700;">
        <h3>💡 Pro Tip: Lead Heat</h3>
        <p style="font-size:12px;">The card border color indicates lead engagement: <span style="color:#d63638;">Red = HOT</span> (Active), <span style="color:#ffb700;">Yellow = WARM</span> (Engaging), <span style="color:#0073aa;">Blue = COLD</span> (New).</p>
        <p style="font-size:11px; color:#666;"><strong>What's Next?</strong> Drag a card to the next column to update their stage and trigger automated nurture emails.</p>
    </div>

    <div class="cce-modal-tabs" style="display:flex; border-bottom:1px solid #ddd; margin-bottom:20px;">
        <button class="cce-crm-tab-link active" data-tab="kanban" style="background:none; border:none; padding:10px 20px; cursor:pointer; border-bottom:2px solid #0073aa;">Pipeline</button>
        <button class="cce-crm-tab-link" data-tab="log" style="background:none; border:none; padding:10px 20px; cursor:pointer;">Activity Log</button>
    </div>

    <div id="crm-tab-kanban" class="cce-crm-tab-content">
        <div style="margin-bottom:20px; display:flex; gap:15px; align-items:center;">
            <input type="text" id="cce-crm-search" placeholder="Search leads by name, email or tag..." style="flex:1; padding:10px; border-radius:8px;">
            <select id="cce-crm-filter-status" style="padding:10px; border-radius:8px;">
                <option value="">All Statuses</option>
                <option value="cold">Cold</option>
                <option value="warm">Warm</option>
                <option value="hot">Hot</option>
            </select>
        </div>

        <div class="cce-card" style="margin-bottom: 30px; border-bottom: 4px solid #673ab7;">
            <h3>📊 Sales Pipeline Visibility</h3>
            <div style="display:flex; justify-content:space-between; align-items:flex-end; height:100px; gap:5px; padding-top:20px;">
                <?php
                global $wpdb;
                $user_id = get_current_user_id();
                $stages_data = $wpdb->get_results( $wpdb->prepare( "SELECT id, name FROM {$wpdb->prefix}cce_crm_stages WHERE user_id = %d ORDER BY stage_order ASC", $user_id ) );
                $counts = [];
                foreach($stages_data as $sd) {
                    $counts[] = [
                        'name' => $sd->name,
                        'count' => (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}cce_leads WHERE crm_stage_id = %d AND user_id = %d", $sd->id, $user_id))
                    ];
                }
                $total_leads_crm = array_sum(array_column($counts, 'count')) ?: 1;
                foreach($counts as $c):
                    $height = ($c['count'] / $total_leads_crm) * 100;
                ?>
                    <div style="flex:1; display:flex; flex-direction:column; align-items:center;">
                        <div style="width:80%; background:#673ab7; height:<?php echo $height; ?>%; border-radius:4px 4px 0 0; min-height:2px; opacity:<?php echo 0.3 + ($height/200); ?>;"></div>
                        <small style="font-size:10px; margin-top:5px; font-weight:bold;"><?php echo $c['count']; ?></small>
                        <small style="font-size:9px; color:#888; text-transform:uppercase;"><?php echo esc_html($c['name']); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <div style="background:#fff9e6; border-left:4px solid #ffb700; padding:10px; font-size:12px; color:#856404;">
                <strong>Pro Tip:</strong> Leads are most likely to convert within the first 5 minutes of opting in. Check your "New" column frequently!
            </div>
            <button class="button cce-manage-stages-btn">Manage Stages</button>
        </div>

        <?php
        global $wpdb;
        $user_id = get_current_user_id();
        $stages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_crm_stages WHERE user_id = %d ORDER BY stage_order ASC", $user_id ) );
        ?>

        <div class="cce-kanban-wrapper" id="cce-kanban-stages-sortable" style="display:flex; gap:20px; overflow-x:auto; padding-bottom:30px;">
            <?php
            $analytics = new CCE_Analytics_Manager();
            $projections = $analytics->get_projections();
            $aov = $projections['aov'] ?: 5000;

            // Define weights for pipeline value
            $weights = [
                'New' => 0.05,
                'Contacted' => 0.15,
                'Booking Scheduled' => 0.40,
                'Booked' => 0.40,
                'Consultation Done' => 0.75,
                'Closed - Won' => 1.0,
                'Closed' => 1.0
            ];

            foreach ( $stages as $stage ):
                $stage_leads = $wpdb->get_results( $wpdb->prepare( "SELECT id, first_name, last_name, tags, status FROM {$wpdb->prefix}cce_leads WHERE crm_stage_id = %d AND user_id = %d", $stage->id, $user_id ) );
                $count = count($stage_leads);
                $weight = 0.1; // Default
                foreach($weights as $key => $val) {
                    if (stripos($stage->name, $key) !== false) { $weight = $val; break; }
                }
                $pipeline_val = $count * $aov * $weight;
            ?>
                <div class="kanban-column cce-kanban-stage" data-id="<?php echo $stage->id; ?>" style="min-width:280px; background:#e2e8f0; border-radius:10px; padding:15px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        <h3 style="margin:0; color:#4a5568; cursor:grab;"><?php echo esc_html( $stage->name ); ?></h3>
                        <span style="font-size:10px; background:#cbd5e0; padding:2px 6px; border-radius:10px; font-weight:bold;"><?php echo $count; ?></span>
                    </div>
                    <div style="font-size:11px; color:#718096; margin-bottom:15px; border-bottom:1px solid #cbd5e0; padding-bottom:5px;">
                        Est. Value: <strong><?php echo $currency_symbol . number_format($pipeline_val, 0); ?></strong>
                    </div>

                    <div class="kanban-cards cce-kanban-column" data-stage-id="<?php echo $stage->id; ?>" style="min-height:100px;">
                        <?php
                        if ($stage_leads): foreach ( $stage_leads as $lead ):
                            $engagement_score = $analytics->calculate_engagement_score( $lead->id );
                            $heat_color = $engagement_score > 50 ? '#d63638' : ($engagement_score > 20 ? '#ffb700' : '#0073aa');

                            $last_activity = $wpdb->get_var($wpdb->prepare("SELECT created_at FROM {$wpdb->prefix}cce_activity_log WHERE lead_id = %d ORDER BY created_at DESC LIMIT 1", $lead->id));
                            $days_ago = $last_activity ? round((time() - strtotime($last_activity)) / DAY_IN_SECONDS) : '∞';
                        ?>
                            <div class="cce-card cce-kanban-card"
                                 data-lead-id="<?php echo $lead->id; ?>"
                                 data-search-text="<?php echo esc_attr(strtolower($lead->first_name . ' ' . $lead->last_name . ' ' . $lead->tags)); ?>"
                                 data-status="<?php echo esc_attr($lead->status); ?>"
                                 style="margin-bottom:10px; border-top:none; border-left:4px solid <?php echo $heat_color; ?>; padding:15px; cursor:move; background:#fff; position:relative;">

                                <span title="Days since last activity" style="position:absolute; right:10px; bottom:10px; font-size:9px; color:<?php echo $days_ago > 3 ? '#d63638' : '#888'; ?>;"><?php echo $days_ago; ?>d ago</span>

                                <div style="display:flex; justify-content:space-between; align-items:start;">
                                    <div>
                                        <strong><?php echo esc_html( $lead->first_name . ' ' . $lead->last_name ); ?></strong>
                                        <div style="font-size:10px; color:#666;">Engagement: <span style="color:<?php echo $heat_color; ?>; font-weight:bold;"><?php echo $engagement_score; ?></span></div>
                                        <?php if($lead->tags): ?>
                                            <div style="margin-top:5px; display:flex; gap:3px; flex-wrap:wrap;">
                                                <?php foreach(explode(',', $lead->tags) as $tag): ?>
                                                    <span style="background:#f1f5f9; color:#64748b; font-size:8px; padding:2px 5px; border-radius:3px;"><?php echo esc_html(trim($tag)); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <select class="cce-status-toggle" data-lead-id="<?php echo $lead->id; ?>" style="font-size:9px; height:auto; padding:2px;">
                                        <option value="cold" <?php selected($lead->status, 'cold'); ?>>COLD</option>
                                        <option value="warm" <?php selected($lead->status, 'warm'); ?>>WARM</option>
                                        <option value="hot" <?php selected($lead->status, 'hot'); ?>>HOT</option>
                                    </select>
                                </div>

                                <div style="margin-top:10px;">
                                    <select class="cce-stage-select" data-lead-id="<?php echo $lead->id; ?>" style="font-size:11px; width:100%;">
                                        <?php foreach ( $stages as $s ): ?>
                                            <option value="<?php echo $s->id; ?>" <?php selected( $lead->crm_stage_id, $s->id ); ?>>
                                                Move to: <?php echo esc_html( $s->name ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div style="margin-top:10px; padding-top:10px; border-top:1px solid #eee;">
                                    <form class="cce-quick-task-form" style="display:flex; gap:5px;">
                                        <input type="hidden" name="lead_id" value="<?php echo $lead->id; ?>">
                                        <input type="text" name="title" placeholder="Quick Task..." style="font-size:10px; height:24px; flex:1;" required>
                                        <button type="submit" class="button button-small" style="height:24px; padding:0 8px; line-height:22px;">+</button>
                                    </form>
                                </div>

                                <div style="margin-top:10px; display:flex; gap:5px; flex-wrap:wrap;">
                                    <a href="#" class="button button-small cce-view-notes"
                                    data-lead-id="<?php echo $lead->id; ?>"
                                    data-lead-name="<?php echo esc_attr( $lead->first_name . ' ' . $lead->last_name ); ?>">Activity</a>
                                    <a href="#" class="button button-small cce-view-tasks"
                                    data-lead-id="<?php echo $lead->id; ?>"
                                    data-lead-name="<?php echo esc_attr( $lead->first_name . ' ' . $lead->last_name ); ?>">Tasks</a>
                                    <a href="#" class="button button-small cce-contact-btn"
                                    data-lead-id="<?php echo $lead->id; ?>"
                                    data-lead-name="<?php echo esc_attr( $lead->first_name . ' ' . $lead->last_name ); ?>">Contact</a>
                                </div>
                            </div>
                        <?php endforeach; else: ?>
                            <div style="font-style:italic; color:#718096; font-size:12px; text-align:center;">Empty</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="crm-tab-log" class="cce-crm-tab-content" style="display:none;">
        <div class="cce-card">
            <h3>Global Activity Log</h3>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr><th>Lead</th><th>Activity</th><th>Time</th></tr>
                </thead>
                <tbody id="cce-global-activity-body">
                    <?php
                    $activities = $wpdb->get_results( $wpdb->prepare( "
                        SELECT a.*, CONCAT(l.first_name, ' ', l.last_name) as lead_name
                        FROM {$wpdb->prefix}cce_activity_log a
                        LEFT JOIN {$wpdb->prefix}cce_leads l ON a.lead_id = l.id
                        WHERE a.user_id = %d
                        ORDER BY a.created_at DESC LIMIT 50
                    ", $user_id ) );
                    if($activities): foreach($activities as $a): ?>
                    <tr>
                        <td><strong><?php echo esc_html($a->lead_name ?: 'System'); ?></strong></td>
                        <td><?php echo esc_html($a->description); ?></td>
                        <td><?php echo esc_html($a->created_at); ?></td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="3">No activities logged.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Leads Modal (Shared) -->
    <div id="cce-leads-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div style="background:#fff; margin:5% auto; padding:25px; width:500px; border-radius:12px; position:relative; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
            <span class="cce-modal-close" style="position:absolute; right:20px; top:15px; cursor:pointer; font-size:24px; color:#888;">&times;</span>
            <h2 id="cce-modal-title" style="margin-top:0;">Lead Details</h2>

            <div class="cce-modal-tabs" style="display:flex; border-bottom:1px solid #eee; margin-bottom:20px;">
                <button class="cce-tab-link active" data-tab="notes" style="background:none; border:none; padding:10px 15px; cursor:pointer; border-bottom:2px solid #0073aa;">Activity</button>
                <button class="cce-tab-link" data-tab="tasks" style="background:none; border:none; padding:10px 15px; cursor:pointer;">Tasks</button>
                <button class="cce-tab-link" data-tab="contact" style="background:none; border:none; padding:10px 15px; cursor:pointer;">Contact</button>
                <button class="cce-tab-link" data-tab="milestones" style="background:none; border:none; padding:10px 15px; cursor:pointer;">Milestones</button>
                <button class="cce-tab-link" data-tab="stats" style="background:none; border:none; padding:10px 15px; cursor:pointer;">Stats</button>
            </div>

            <div id="cce-tab-notes" class="cce-tab-content">
                <div id="cce-notes-content" style="max-height:250px; overflow-y:auto; margin-bottom:20px; border:1px solid #eee; padding:15px; border-radius:8px; background:#fcfcfc;"></div>
                <div class="cce-add-note-section">
                    <form id="cce-add-note-form">
                        <input type="hidden" class="cce-lead-id-field">
                        <textarea id="cce-new-note-text" placeholder="Add a note..." style="width:100%; border-radius:8px; margin-bottom:10px;" rows="2" required></textarea>
                        <button type="submit" class="button button-primary">Add Note</button>
                    </form>
                </div>
            </div>

            <div id="cce-tab-tasks" class="cce-tab-content" style="display:none;">
                <div id="cce-tasks-content" style="max-height:250px; overflow-y:auto; margin-bottom:20px; border:1px solid #eee; padding:15px; border-radius:8px; background:#fcfcfc;"></div>
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

            <div id="cce-tab-milestones" class="cce-tab-content" style="display:none;">
                <div id="cce-milestones-content" style="max-height:250px; overflow-y:auto; margin-bottom:20px; border:1px solid #eee; padding:15px; border-radius:8px; background:#fcfcfc;"></div>
                <div class="cce-add-milestone-section">
                    <form id="cce-add-milestone-form">
                        <input type="hidden" class="cce-lead-id-field">
                        <div style="display:flex; gap:10px;">
                            <input type="text" id="cce-new-milestone-title" placeholder="New milestone (e.g. First $1k day)..." style="flex:1; border-radius:8px;" required>
                            <button type="submit" class="button button-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="cce-tab-contact" class="cce-tab-content" style="display:none;">
                <p>Send a direct email to the lead.</p>
                <form id="cce-contact-form">
                    <input type="hidden" class="cce-lead-id-field">
                    <textarea id="cce-contact-message" placeholder="Type your message here..." style="width:100%; border-radius:8px; margin-bottom:10px;" rows="5" required></textarea>
                    <button type="submit" class="button button-primary">Send Email</button>
                </form>
                <div id="cce-contact-status" style="margin-top:10px;"></div>
            </div>

            <div id="cce-tab-stats" class="cce-tab-content" style="display:none;">
                <div id="cce-lead-stats-content"></div>
            </div>
        </div>
    </div>

    <!-- Manage Stages Modal -->
    <div id="cce-manage-stages-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div style="background:#fff; margin:10% auto; padding:25px; width:400px; border-radius:12px; position:relative;">
            <span class="cce-modal-close" style="position:absolute; right:20px; top:15px; cursor:pointer; font-size:24px;">&times;</span>
            <h2>Manage CRM Stages</h2>
            <ul style="list-style:none; padding:0;">
                <?php foreach($stages as $s): ?>
                    <li style="display:flex; justify-content:space-between; padding:10px; border-bottom:1px solid #eee;">
                        <span><?php echo esc_html($s->name); ?></span>
                        <button class="button button-small cce-delete-stage" data-id="<?php echo $s->id; ?>" style="color:#d63638;">×</button>
                    </li>
                <?php endforeach; ?>
            </ul>
            <form id="cce-add-stage-form" style="margin-top:20px;">
                <input type="text" id="new-stage-name" placeholder="New Stage Name" class="widefat" required>
                <button type="submit" class="button button-primary" style="margin-top:10px;">Add Stage</button>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.cce-crm-tab-link').on('click', function() {
        $('.cce-crm-tab-link').removeClass('active').css('border-bottom', 'none');
        $(this).addClass('active').css('border-bottom', '2px solid #0073aa');
        $('.cce-crm-tab-content').hide();
        $('#crm-tab-' + $(this).data('tab')).show();
    });

    // CRM Real-time Filtering
    $('#cce-crm-search, #cce-crm-filter-status').on('input change', function() {
        const term = $('#cce-crm-search').val().toLowerCase();
        const status = $('#cce-crm-filter-status').val();

        $('.cce-kanban-card').each(function() {
            const text = $(this).data('search-text');
            const leadStatus = $(this).data('status');
            const matchesSearch = text.includes(term);
            const matchesStatus = !status || leadStatus === status;

            $(this).toggle(matchesSearch && matchesStatus);
        });
    });

    // Stage Reordering
    $('#cce-kanban-stages-sortable').sortable({
        items: '.cce-kanban-stage',
        handle: 'h3',
        update: function() {
            const stageIds = $(this).sortable('toArray', { attribute: 'data-id' });
            cceApi('crm/stages/reorder', 'POST', { stage_ids: stageIds }, function(res) {
                if(res.success) console.log('Stages reordered');
            });
        }
    });
});
</script>
