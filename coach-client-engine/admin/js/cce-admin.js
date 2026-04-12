jQuery(document).ready(function($) {
    // Shared AJAX Helper
    function cceApi(endpoint, method, data, success) {
        $.ajax({
            url: cceAdmin.restUrl + endpoint,
            method: method,
            beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', cceAdmin.nonce); },
            data: data,
            success: success,
            error: function(err) { console.error('CCE API Error:', err); }
        });
    }

    // CRM: Stage Update
    $(document).on('change', '.cce-stage-select', function() {
        const leadId = $(this).data('lead-id');
        const stageId = $(this).val();
        cceApi('crm/leads/' + leadId + '/stage', 'POST', { stage_id: stageId }, function(res) {
            if (res.success) location.reload();
        });
    });

    // CRM: View Activities
    $(document).on('click', '.cce-view-notes', function(e) {
        e.preventDefault();
        const leadId = $(this).data('lead-id');
        const leadName = $(this).data('lead-name');

        $('#cce-modal-title').text('Activity Log: ' + leadName);
        $('#cce-notes-content').html('<p>Loading activities...</p>');
        $('#cce-add-note-lead-id').val(leadId);
        $('#cce-notes-modal').show();

        loadNotes(leadId);
    });

    function loadNotes(leadId) {
        cceApi('crm/leads/' + leadId + '/activities', 'GET', {}, function(res) {
            if (res.success) {
                let html = '<ul class="cce-activity-list" style="padding-left:0; list-style:none;">';
                res.data.forEach(activity => {
                    const desc = $('<div>').text(activity.description).html(); // Basic Escape
                    html += `<li style="border-bottom:1px solid #eee; padding:10px 0;">
                        <div style="display:flex; justify-content:space-between;">
                            <strong>${activity.activity_type.toUpperCase()}</strong>
                            <small style="color:#888;">${activity.created_at}</small>
                        </div>
                        <div style="margin-top:5px;">${desc}</div>
                    </li>`;
                });
                html += '</ul>';
                $('#cce-notes-content').html(res.data.length ? html : '<p>No activities found.</p>');
            }
        });
    }

    // CRM: Add Note
    $('#cce-add-note-form').on('submit', function(e) {
        e.preventDefault();
        const leadId = $('#cce-add-note-lead-id').val();
        const note = $('#cce-new-note-text').val();
        const $btn = $(this).find('button');

        $btn.prop('disabled', true).text('Saving...');
        cceApi('crm/leads/' + leadId + '/notes', 'POST', { note: note }, function(res) {
            if (res.success) {
                $('#cce-new-note-text').val('');
                loadNotes(leadId);
            }
            $btn.prop('disabled', false).text('Add Note');
        });
    });

    // CRM: Close Modal
    $(document).on('click', '.cce-modal-close', function() {
        $('#cce-notes-modal').hide();
    });

    // Funnels: Use Template
    $('.cce-use-template').on('click', function() {
        const templateId = $(this).data('template');
        cceApi('funnels/create-from-template', 'POST', { template_id: templateId }, function(res) {
            if(res.success) location.reload();
        });
    });

    // Funnels: Delete
    $('.cce-delete-funnel').on('click', function() {
        if(!confirm('Are you sure you want to delete this funnel?')) return;
        const funnelId = $(this).data('funnel-id');
        cceApi('funnels/' + funnelId, 'DELETE', {}, function(res) {
            if(res.success) location.reload();
        });
    });

    // Funnels: Copy Shortcode
    $('.cce-copy-shortcode').on('click', function() {
        const text = $(this).data('shortcode');
        navigator.clipboard.writeText(text).then(() => {
            const originalText = $(this).text();
            $(this).text('Copied!');
            setTimeout(() => $(this).text(originalText), 2000);
        });
    });

    // Funnels: View Steps
    $('.cce-view-steps').on('click', function(e) {
        e.preventDefault();
        const funnelId = $(this).data('funnel-id');
        const $row = $('#funnel-steps-' + funnelId);
        const $container = $('.steps-container-' + funnelId);

        if ($row.is(':visible')) {
            $row.hide();
            return;
        }

        $row.show();
        cceApi('funnels/' + funnelId + '/steps', 'GET', {}, function(res) {
            if (res.success && res.data.length > 0) {
                let html = '<ol>';
                res.data.forEach(step => {
                    html += `<li><strong>${step.title}</strong> (${step.step_type})</li>`;
                });
                html += '</ol>';
                $container.html(html);
            } else {
                $container.html('<p>No steps configured for this funnel yet.</p>');
            }
        });
    });

    // Leads: Delete
    $('.cce-delete-lead').on('click', function() {
        if(!confirm('Are you sure you want to delete this lead?')) return;
        const leadId = $(this).data('lead-id');
        cceApi('leads/' + leadId, 'DELETE', {}, function(res) {
            if(res.success) location.reload();
        });
    });

    // Offers: Delete
    $('.cce-delete-offer').on('click', function() {
        if(!confirm('Are you sure you want to delete this offer?')) return;
        const offerId = $(this).data('offer-id');
        cceApi('offers/' + offerId, 'DELETE', {}, function(res) {
            if(res.success) location.reload();
        });
    });

    // Testimonials: Add
    $('#cce-add-testimonial-form').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        cceApi('proof/testimonials', 'POST', JSON.stringify(data), function(res) {
            if (res.success) location.reload();
        }, 'application/json');
    });
});
