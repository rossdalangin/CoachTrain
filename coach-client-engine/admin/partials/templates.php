<div class="wrap cce-admin-wrap">
    <h1>Strategic Template Library</h1>
    <p class="description">Access pre-built frameworks based on high-performance coaching models. You can edit these templates to match your brand and offer.</p>

    <div class="cce-card" style="margin-bottom: 30px; border-top: 4px solid #0073aa;">
        <h3>🚀 One-Click Business Model Deployment</h3>
        <p>Choose your coaching business model below. Deploying a model will populate your Engine with tailored Funnels, Offers, and Email Sequences.</p>

        <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:20px; margin-top:15px;">
            <div style="background:#f9f9f9; padding:15px; border-radius:8px; border:1px solid #ddd;">
                <h4>Agency Model</h4>
                <p style="font-size:12px;">For Done-For-You services. Includes Lead Gen VSLs and Outreach Strategy funnels.</p>
                <button type="button" class="button button-secondary cce-deploy-model" data-model="agency">Deploy Agency Model</button>
            </div>
            <div style="background:#f9f9f9; padding:15px; border-radius:8px; border:1px solid #ddd;">
                <h4>Mastery Coach</h4>
                <p style="font-size:12px;">For high-ticket 1-on-1 coaching. Includes VSL, Webinar, and Challenge frameworks.</p>
                <button type="button" class="button button-secondary cce-deploy-model" data-model="standard">Deploy Mastery Model</button>
            </div>
            <div style="background:#f9f9f9; padding:15px; border-radius:8px; border:1px solid #ddd;">
                <h4>Membership / Group</h4>
                <p style="font-size:12px;">For low-ticket recurring or group programs. Includes Value Ladder and Sales funnels.</p>
                <button type="button" class="button button-secondary cce-deploy-model" data-model="membership">Deploy Membership Model</button>
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
