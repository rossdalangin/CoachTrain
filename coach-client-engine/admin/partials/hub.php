<div class="wrap cce-admin-wrap">
    <h1>Mastery Hub</h1>
    <p class="description">Your central library for strategic frameworks, high-ticket scripts, and marketing excellence. Use these resources to scale your impact.</p>

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

    <!-- Resource Modal -->
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
            const file = $(this).data('file');
            $('#cce-hub-modal-content').html('Loading...');
            $('#cce-hub-modal').show();

            $.ajax({
                url: cceAdmin.restUrl + 'maintenance/hub-resource',
                method: 'GET',
                data: { file: file },
                beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', cceAdmin.nonce); },
                success: function(res) {
                    if (res.success) {
                        let content = res.content;
                        // Replace [ ] and [x] with checkboxes
                        content = content.replace(/\[ \]/g, '<input type="checkbox">');
                        content = content.replace(/\[x\]/g, '<input type="checkbox" checked>');

                        $('#cce-hub-modal-content').html(content);

                        // Load progress
                        const checklistId = 'cce_hub_progress_' + $(this).closest('.hub-item').find('h3').text().replace(/\s+/g, '_').toLowerCase();
                        const saved = JSON.parse(localStorage.getItem(checklistId) || '[]');
                        $('#cce-hub-modal-content input[type="checkbox"]').each(function() {
                            const text = $(this).next('label').text() || $(this).parent().text();
                            if (saved.includes(text)) $(this).prop('checked', true);
                        });
                    }
                }
            });
        });

        $('#cce-hub-modal-close').on('click', () => $('#cce-hub-modal').hide());

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
