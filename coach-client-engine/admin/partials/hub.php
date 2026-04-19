<div class="wrap cce-admin-wrap">
    <h1>Mastery Hub</h1>
    <p class="description">Your central library for strategic frameworks, high-ticket scripts, and marketing excellence. Use these resources to scale your impact.</p>

    <div class="cce-modal-tabs" style="display:flex; border-bottom:1px solid #ddd; margin-bottom:20px;">
        <button class="cce-hub-tab-link active" data-tab="vault" style="background:none; border:none; padding:10px 20px; cursor:pointer; border-bottom:2px solid #0073aa;">Strategy Vault</button>
        <button class="cce-hub-tab-link" data-tab="custom" style="background:none; border:none; padding:10px 20px; cursor:pointer;">My Custom Resources</button>
    </div>

    <div id="hub-tab-vault" class="cce-hub-tab-content">
    <div style="margin-bottom:20px;">
        <input type="search" id="cce-hub-search" placeholder="Search resources (e.g. Hormozi, Sales Script)..." class="widefat" style="padding:12px; font-size:16px; border-radius:8px;">
    </div>

    <div id="cce-hub-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap:20px;">

        <!-- Strategy Guides -->
        <div class="cce-card hub-item" data-tags="strategy,hormozi,brunson,checklist">
            <span class="dashicons dashicons-awards" style="font-size:30px; width:30px; height:30px; color:#ffb700;"></span>
            <h3>Grand Slam Strategy Checklist</h3>
            <p>A step-by-step verification process for your offers and funnels based on Hormozi and Brunson frameworks.</p>
            <button type="button" class="button button-secondary cce-open-hub-resource" data-file="strategy-checklist.md">Open Guide</button>
        </div>

        <!-- Sales Scripts -->
        <div class="cce-card hub-item" data-tags="sales,script,outreach,scripts">
            <span class="dashicons dashicons-megaphone" style="font-size:30px; width:30px; height:30px; color:#0073aa;"></span>
            <h3>High-Ticket Sales Playbook</h3>
            <p>Word-for-word scripts for LinkedIn outreach, cold emails, and closing calls.</p>
            <button type="button" class="button button-secondary cce-open-hub-resource" data-file="sales-playbook.md">Open Playbook</button>
        </div>

        <!-- Webinar Assets -->
        <div class="cce-card hub-item" data-tags="webinar,script,presentation">
            <span class="dashicons dashicons-video-alt3" style="font-size:30px; width:30px; height:30px; color:#673ab7;"></span>
            <h3>Perfect Webinar Script</h3>
            <p>The 45-minute high-ticket webinar framework designed to book discovery calls.</p>
            <button type="button" class="button button-secondary cce-open-hub-resource" data-file="webinar-and-case-study.md">Open Script</button>
        </div>

        <!-- Social Media -->
        <div class="cce-card hub-item" data-tags="social,marketing,content,calendar">
            <span class="dashicons dashicons-share" style="font-size:30px; width:30px; height:30px; color:#00a32a;"></span>
            <h3>30-Day Content Calendar</h3>
            <p>A full month of high-engagement social media posts designed to attract high-ticket leads.</p>
            <button type="button" class="button button-secondary cce-open-hub-resource" data-file="social-media-30-day.md">Open Calendar</button>
        </div>

        <!-- Landing Page -->
        <div class="cce-card hub-item" data-tags="landing,funnel,design,html">
            <span class="dashicons dashicons-layout" style="font-size:30px; width:30px; height:30px; color:#d63638;"></span>
            <h3>Premium Landing Page HTML</h3>
            <p>The high-conversion raw HTML/CSS for our 'Consultant Core' landing page design.</p>
            <a href="<?php echo plugin_dir_url(__FILE__) . '../../marketing/landing-page.html'; ?>" target="_blank" class="button button-secondary">View Design</a>
        </div>

        <!-- Email Sequences -->
        <div class="cce-card hub-item" data-tags="email,automation,copywriting,indoctrination">
            <span class="dashicons dashicons-email-alt" style="font-size:30px; width:30px; height:30px; color:#0073aa;"></span>
            <h3>Indoctrination Email Sequences</h3>
            <p>Proven 7-day sequences to build trust and authority immediately after opt-in.</p>
            <button type="button" class="button button-secondary cce-open-hub-resource" data-file="email-sequences.md">Open Sequence</button>
        </div>

        <!-- Video Assets -->
        <div class="cce-card hub-item" data-tags="video,vsl,content,production">
            <span class="dashicons dashicons-video-alt2" style="font-size:30px; width:30px; height:30px; color:#d63638;"></span>
            <h3>High-Converting VSL Framework</h3>
            <p>The slide-by-slide breakdown for your Video Sales Letter and landing page videos.</p>
            <button type="button" class="button button-secondary cce-open-hub-resource" data-file="video-assets.md">Open Framework</button>
        </div>

        <!-- Retention SOP -->
        <div class="cce-card hub-item" data-tags="retention,sop,onboarding,checklist,sales">
            <span class="dashicons dashicons-admin-users" style="font-size:30px; width:30px; height:30px; color:#00a32a;"></span>
            <h3>Sales & Retention Mastery</h3>
            <p>Checklists for high-ticket sales calls and SOPs for client onboarding and retention.</p>
            <button type="button" class="button button-secondary cce-open-hub-resource" data-file="sales-and-retention.md">Open Guide</button>
        </div>

        <!-- Scaling & Ops -->
        <div class="cce-card hub-item" data-tags="scaling,ops,hiring,team,roadmap">
            <span class="dashicons dashicons-chart-line" style="font-size:30px; width:30px; height:30px; color:#673ab7;"></span>
            <h3>Scaling & Operations</h3>
            <p>The 7-figure roadmap and guides for hiring high-performance setters and closers.</p>
            <button type="button" class="button button-secondary cce-open-hub-resource" data-file="scaling-and-ops.md">Open Guide</button>
        </div>

    </div>
    </div>

    <div id="hub-tab-custom" class="cce-hub-tab-content" style="display:none;">
        <div class="cce-card" style="margin-bottom:20px;">
            <h3>Add Custom Strategic Resource</h3>
            <p class="description">Add links to your own training videos, SOPs, or external documents for quick access.</p>
            <form id="cce-add-hub-resource-form">
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <input type="text" name="title" placeholder="Resource Title" required class="regular-text">
                    <input type="text" name="category" placeholder="Category (e.g. Sales)" class="regular-text">
                    <input type="text" name="type" placeholder="Type (e.g. Video, PDF)" class="small-text">
                    <input type="url" name="url" placeholder="Resource URL" required class="regular-text" style="flex:1;">
                    <button type="submit" class="button button-primary">Save Resource</button>
                </div>
            </form>
        </div>

        <div class="cce-card">
            <h3>My Saved Resources</h3>
            <table class="wp-list-table widefat fixed striped">
                <thead><tr><th>Title</th><th>Category</th><th>Type</th><th>Actions</th></tr></thead>
                <tbody id="cce-hub-custom-list">
                    <?php
                    global $wpdb;
                    $custom_res = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}cce_resources WHERE user_id = %d AND visibility = 'internal' ORDER BY created_at DESC", get_current_user_id()));
                    if($custom_res): foreach($custom_res as $r): ?>
                        <tr id="resource-row-<?php echo $r->id; ?>"
                            data-title="<?php echo esc_attr($r->title); ?>"
                            data-category="<?php echo esc_attr($r->category); ?>"
                            data-type="<?php echo esc_attr($r->type); ?>"
                            data-url="<?php echo esc_attr($r->url); ?>">
                            <td><strong><?php echo esc_html($r->title); ?></strong></td>
                            <td><?php echo esc_html($r->category); ?></td>
                            <td><?php echo esc_html($r->type); ?></td>
                            <td>
                                <a href="<?php echo esc_url($r->url); ?>" target="_blank" class="button button-small">View</a>
                                <button class="button button-small cce-edit-hub-resource" data-id="<?php echo $r->id; ?>">Edit</button>
                                <button class="button button-link-delete cce-delete-hub-resource" data-id="<?php echo $r->id; ?>" style="color:#d63638;">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="4">No custom resources added yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Resource Modal -->
    <!-- Edit Resource Modal -->
    <div id="cce-edit-hub-resource-modal" style="display:none; position:fixed; z-index:99999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div style="background:#fff; margin:10% auto; padding:25px; width:500px; border-radius:12px; position:relative;">
            <span class="cce-modal-close" style="position:absolute; right:20px; top:15px; cursor:pointer; font-size:24px;">&times;</span>
            <h2>Edit Resource</h2>
            <form id="cce-edit-hub-resource-form">
                <input type="hidden" id="edit-resource-id">
                <p><label>Title</label><br><input type="text" id="edit-resource-title" class="widefat" required></p>
                <p><label>Category</label><br><input type="text" id="edit-resource-category" class="widefat"></p>
                <p><label>Type</label><br><input type="text" id="edit-resource-type" class="widefat"></p>
                <p><label>URL</label><br><input type="url" id="edit-resource-url" class="widefat" required></p>
                <button type="submit" class="button button-primary">Update Resource</button>
            </form>
        </div>
    </div>

    <div id="cce-hub-modal" style="display:none; position:fixed; z-index:99999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.8); overflow-y:auto;">
        <div style="background:#fff; margin:2% auto; padding:40px; width:800px; max-width:90%; border-radius:12px; position:relative;">
            <span id="cce-hub-modal-close" style="position:absolute; right:25px; top:20px; cursor:pointer; font-size:30px; color:#888;">&times;</span>
            <div id="cce-hub-modal-content" class="cce-md-content">
                Loading...
            </div>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $(document).on('change', '.cce-md-content input[type="checkbox"]', function() {
            const checklistId = 'cce_hub_progress_' + $(this).closest('.cce-md-content').find('h1').text().replace(/\s+/g, '_').toLowerCase();
            const checked = [];
            $(this).closest('.cce-md-content').find('input[type="checkbox"]:checked').each(function() {
                checked.push($(this).next('label').text() || $(this).parent().text());
            });
            localStorage.setItem(checklistId, JSON.stringify(checked));
        });

        $('.cce-open-hub-resource').on('click', function() {
            const $btn = $(this);
            const file = $btn.attr('data-file');
            $('#cce-hub-modal-content').html('Loading...');
            $('#cce-hub-modal').show();

            cceApi('maintenance/hub-resource', 'GET', { file: file }, function(res) {
                if (res.success) {
                    let content = res.content;
                    // Replace [ ] and [x] with checkboxes
                    content = content.replace(/\[ \]/g, '<input type="checkbox">');
                    content = content.replace(/\[x\]/g, '<input type="checkbox" checked>');

                    $('#cce-hub-modal-content').html(content);

                    // Load progress
                    const checklistId = 'cce_hub_progress_' + $btn.closest('.hub-item').find('h3').text().replace(/\s+/g, '_').toLowerCase();
                    const saved = JSON.parse(localStorage.getItem(checklistId) || '[]');
                    $('#cce-hub-modal-content input[type="checkbox"]').each(function() {
                        const text = $(this).next('label').text() || $(this).parent().text();
                        if (saved.includes(text)) $(this).prop('checked', true);
                    });
                }
            });
        });

        $('#cce-hub-modal-close').on('click', () => $('#cce-hub-modal').hide());

        $('.cce-hub-tab-link').on('click', function() {
            $('.cce-hub-tab-link').removeClass('active').css('border-bottom', 'none');
            $(this).addClass('active').css('border-bottom', '2px solid #0073aa');
            $('.cce-hub-tab-content').hide();
            $('#hub-tab-' + $(this).data('tab')).show();
        });

        $('#cce-add-hub-resource-form').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            cceApi('hub/resources', 'POST', JSON.stringify(data), function(res) {
                if (res.success) location.reload();
            });
        });

        $('.cce-edit-hub-resource').on('click', function() {
            const id = $(this).data('id');
            const $row = $('#resource-row-' + id);
            $('#edit-resource-id').val(id);
            $('#edit-resource-title').val($row.data('title'));
            $('#edit-resource-category').val($row.data('category'));
            $('#edit-resource-type').val($row.data('type'));
            $('#edit-resource-url').val($row.data('url'));
            $('#cce-edit-hub-resource-modal').show();
        });

        $('#cce-edit-hub-resource-form').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit-resource-id').val();
            const data = {
                title: $('#edit-resource-title').val(),
                category: $('#edit-resource-category').val(),
                type: $('#edit-resource-type').val(),
                url: $('#edit-resource-url').val()
            };
            cceApi('hub/resources/' + id, 'POST', JSON.stringify(data), function(res) {
                if (res.success) location.reload();
            });
        });

        $('.cce-delete-hub-resource').on('click', function() {
            if (!confirm('Delete this resource?')) return;
            const id = $(this).data('id');
            cceApi('hub/resources/' + id, 'DELETE', {}, function() {
                location.reload();
            });
        });

        $('#cce-hub-search').on('input', function() {
            const term = $(this).val().toLowerCase();
            $('.hub-item').each(function() {
                const text = $(this).text().toLowerCase();
                const tags = $(this).data('tags').toLowerCase();
                $(this).toggle(text.includes(term) || tags.includes(term));
            });
        });
    });
    </script>
</div>
