<div class="wrap cce-admin-wrap">
    <h1>Strategic Template Library</h1>
    <p class="description">Access pre-built frameworks based on high-performance coaching models. You can edit these templates to match your brand and offer.</p>

    <div class="cce-card" style="margin-bottom: 30px; border-top: 4px solid #0073aa;">
        <h3>💎 Masterpiece Strategy Vault</h3>
        <p>Deploy full, cross-linked business frameworks based on industry titans. These frameworks automatically create your Funnels, Offers, and Automation rules.</p>

        <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:20px; margin-top:15px;">
            <!-- Hormozi Strategy -->
            <div style="background:#fff; padding:20px; border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                <div style="background:#fef3c7; color:#92400e; font-size:10px; font-weight:700; padding:2px 8px; border-radius:10px; display:inline-block; margin-bottom:10px;">TITAN FRAMEWORK</div>
                <h4 style="margin:0 0 10px 0;">The Hormozi Launch</h4>
                <p style="font-size:12px; color:#64748b;">Built for "Offers so good they feel stupid saying no." Includes VSL Funnel + High-Ticket Offer + Value Ascension Emails.</p>
                <button type="button" class="button button-primary cce-deploy-strategy" data-strategy="hormozi" style="width:100%; margin-top:10px;">Deploy Full Strategy</button>
            </div>

            <!-- Brunson Strategy -->
            <div style="background:#fff; padding:20px; border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                <div style="background:#dcfce7; color:#166534; font-size:10px; font-weight:700; padding:2px 8px; border-radius:10px; display:inline-block; margin-bottom:10px;">TITAN FRAMEWORK</div>
                <h4 style="margin:0 0 10px 0;">The Brunson Webinar</h4>
                <p style="font-size:12px; color:#64748b;">The perfect webinar framework for group scaling. Includes Webinar Funnel + Order Bump Offer + Indoctrination Sequence.</p>
                <button type="button" class="button button-primary cce-deploy-strategy" data-strategy="brunson" style="width:100%; margin-top:10px;">Deploy Full Strategy</button>
            </div>

            <!-- Custom Business Models -->
            <div style="background:#fff; padding:20px; border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                <div style="background:#f1f5f9; color:#475569; font-size:10px; font-weight:700; padding:2px 8px; border-radius:10px; display:inline-block; margin-bottom:10px;">MODEL DEPLOYMENT</div>
                <h4 style="margin:0 0 10px 0;">Agency Builder</h4>
                <p style="font-size:12px; color:#64748b;">For DFY services. Populates the Engine with Lead Gen funnels and cold outreach automation templates.</p>
                <button type="button" class="button button-secondary cce-deploy-model" data-model="agency" style="width:100%; margin-top:10px;">Deploy Agency Model</button>
            </div>
        </div>
    </div>

    <div class="cce-template-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap:20px; margin-top:20px;">

        <!-- Funnel Templates -->
        <div class="cce-card">
            <span class="dashicons dashicons-filter" style="font-size:40px; width:40px; height:40px; color:#673ab7;"></span>
            <h3>Funnel Templates</h3>
            <p>Frameworks for VSLs, Webinars, and Challenges.</p>
            <a href="?page=cce-funnels" class="button button-primary">Manage Funnels</a>
        </div>

        <!-- Email Templates -->
        <div class="cce-card">
            <span class="dashicons dashicons-email-alt" style="font-size:40px; width:40px; height:40px; color:#0073aa;"></span>
            <h3>Email Sequences</h3>
            <p>Pre-written indoctrination and sales sequences.</p>
            <a href="?page=cce-automation#templates" class="button button-primary">Edit Sequences</a>
        </div>

        <!-- Offer Templates -->
        <div class="cce-card">
            <span class="dashicons dashicons-awards" style="font-size:40px; width:40px; height:40px; color:#ffb700;"></span>
            <h3>Grand Slam Offers</h3>
            <p>Hormozi-style offer structures for maximum value.</p>
            <a href="?page=cce-clients" class="button button-primary">View Offers</a>
        </div>
    </div>

    <div class="cce-card" style="margin-top:30px; border-left: 4px solid #d63638;">
        <h3>🧹 Data Maintenance</h3>
        <p>Use this to clear all leads, funnels, and settings for your current user. This is irreversible.</p>
        <button type="button" id="cce-clear-user-data" class="button button-link" style="color:#d63638;">Reset Engine Data</button>
    </div>

    <div class="cce-card" style="margin-top:30px; border-left: 4px solid #00a32a;">
        <h3>🛠 Customization Guide</h3>
        <p>Every template is designed to be 100% editable. To modify a template:</p>
        <ol>
            <li>Navigate to the respective module (Funnels, Automation, or Clients).</li>
            <li>Select the template you want to change.</li>
            <li>Update the content, configuration, or triggers.</li>
            <li>Save your changes to apply them to your live engine.</li>
        </ol>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.cce-deploy-strategy').on('click', function() {
        if (!confirm('This will deploy a full cross-linked strategy. Continue?')) return;
        const $btn = $(this);
        const strategy = $btn.data('strategy');
        $btn.prop('disabled', true).text('Building Strategy...');

        $.ajax({
            url: cceAdmin.restUrl + 'maintenance/sample-data',
            method: 'POST',
            data: { model: strategy, linked: true },
            beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', cceAdmin.nonce); },
            success: function(res) {
                if (res.success) {
                    alert('Strategy deployed successfully! All Funnels, Offers, and Emails are now linked.');
                    window.location.reload();
                }
            }
        });
    });

    $('.cce-deploy-model').on('click', function() {
        if (!confirm('This will add new sample data to your account. Continue?')) return;

        const $btn = $(this);
        const model = $btn.data('model');
        $btn.prop('disabled', true).text('Deploying...');

        $.ajax({
            url: cceAdmin.restUrl + 'maintenance/sample-data',
            method: 'POST',
            data: { model: model },
            beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', cceAdmin.nonce); },
            success: function(res) {
                if (res.success) {
                    alert(res.message);
                    window.location.reload();
                }
            },
            error: function() {
                alert('An error occurred.');
                $btn.prop('disabled', false).text('Deploy ' + model + ' Model');
            }
        });
    });

    $('#cce-clear-user-data').on('click', function() {
        if (!confirm('Are you absolutely sure you want to clear ALL your data? This cannot be undone.')) return;

        $.ajax({
            url: cceAdmin.restUrl + 'maintenance/clear-data',
            method: 'POST',
            beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', cceAdmin.nonce); },
            success: function(res) {
                if (res.success) {
                    alert(res.message);
                    window.location.reload();
                }
            }
        });
    });
});
</script>
