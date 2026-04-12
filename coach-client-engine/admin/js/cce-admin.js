jQuery(document).ready(function($) {
    // Shared AJAX Helper
    function cceApi(endpoint, method, data, success) {
        const ajaxSettings = {
            url: cceAdmin.restUrl + endpoint,
            method: method,
            beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', cceAdmin.nonce); },
            success: success,
            error: function(err) { console.error('CCE API Error:', err); }
        };
        if (method === 'POST' || method === 'PUT' || method === 'PATCH') {
            if (typeof data === 'string') {
                ajaxSettings.contentType = 'application/json';
                ajaxSettings.data = data;
            } else {
                ajaxSettings.data = data;
            }
        } else {
            ajaxSettings.data = data;
        }
        $.ajax(ajaxSettings);
    }

    // CRM: Tabs
    $('.cce-tab-link').on('click', function() {
        $('.cce-tab-link').removeClass('active').css('border-bottom', 'none');
        $(this).addClass('active').css('border-bottom', '2px solid #0073aa');
        $('.cce-tab-content').hide();
        $('#cce-tab-' + $(this).data('tab')).show();
    });

    // CRM: View Details (Notes/Tasks/Contact)
    $(document).on('click', '.cce-view-notes, .cce-view-tasks, .cce-contact-btn', function(e) {
        e.preventDefault();
        const leadId = $(this).data('lead-id');
        const leadName = $(this).data('lead-name');
        let initialTab = 'notes';
        if ($(this).hasClass('cce-view-tasks')) initialTab = 'tasks';
        if ($(this).hasClass('cce-contact-btn')) initialTab = 'contact';

        $('#cce-modal-title').text(leadName);
        $('.cce-lead-id-field').val(leadId);
        $(`.cce-tab-link[data-tab="${initialTab}"]`).click();
        $('#cce-leads-modal').show();

        loadNotes(leadId);
        loadTasks(leadId);
        $('#cce-contact-status').html('');
    });

    function loadNotes(leadId) {
        cceApi('crm/leads/' + leadId + '/activities', 'GET', {}, function(res) {
            if (res.success) {
                let html = '<ul class="cce-activity-list" style="padding-left:0; list-style:none;">';
                res.data.forEach(activity => {
                    const desc = $('<div>').text(activity.description).html();
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

    function loadTasks(leadId) {
        cceApi('crm/leads/' + leadId + '/tasks', 'GET', {}, function(res) {
            if (res.success) {
                let html = '<ul style="padding-left:0; list-style:none;">';
                res.data.forEach(task => {
                    const checked = task.status === 'completed' ? 'checked' : '';
                    html += `<li style="padding:8px 0; border-bottom:1px solid #f9f9f9;">
                        <label>
                            <input type="checkbox" ${checked} class="cce-toggle-task" data-task-id="${task.id}" data-lead-id="${leadId}">
                            ${task.title}
                        </label>
                    </li>`;
                });
                html += '</ul>';
                $('#cce-tasks-content').html(res.data.length ? html : '<p>No tasks found.</p>');
            }
        });
    }

    // CRM: Toggle Task
    $(document).on('change', '.cce-toggle-task', function() {
        const leadId = $(this).data('lead-id');
        const taskId = $(this).data('task-id');
        const status = $(this).is(':checked') ? 'completed' : 'pending';
        cceApi('crm/leads/' + leadId + '/tasks/' + taskId, 'POST', { status: status }, function(res) {
            // Success
        });
    });

    // CRM: Add Task
    $('#cce-add-task-form').on('submit', function(e) {
        e.preventDefault();
        const leadId = $(this).find('.cce-lead-id-field').val();
        const title = $('#cce-new-task-title').val();
        cceApi('crm/leads/' + leadId + '/tasks', 'POST', { title: title }, function(res) {
            if (res.success) {
                $('#cce-new-task-title').val('');
                loadTasks(leadId);
            }
        });
    });

    // CRM: Contact Lead
    $('#cce-contact-form').on('submit', function(e) {
        e.preventDefault();
        const leadId = $(this).find('.cce-lead-id-field').val();
        const message = $('#cce-contact-message').val();
        const $btn = $(this).find('button');
        $btn.prop('disabled', true).text('Sending...');

        cceApi('crm/leads/' + leadId + '/contact', 'POST', { message: message }, function(res) {
            if (res.success) {
                $('#cce-contact-message').val('');
                $('#cce-contact-status').html('<p style="color:green">Email sent successfully!</p>');
                loadNotes(leadId);
            }
            $btn.prop('disabled', false).text('Send Email');
        });
    });

    // CRM: Add Note
    $('#cce-add-note-form').on('submit', function(e) {
        e.preventDefault();
        const leadId = $(this).find('.cce-lead-id-field').val();
        const note = $('#cce-new-note-text').val();
        cceApi('crm/leads/' + leadId + '/notes', 'POST', { note: note }, function(res) {
            if (res.success) {
                $('#cce-new-note-text').val('');
                loadNotes(leadId);
            }
        });
    });

    // Modals: Close
    $(document).on('click', '.cce-modal-close', function() {
        $('#cce-leads-modal, #cce-edit-lead-modal, #cce-edit-offer-modal, #cce-add-step-modal').hide();
    });

    // CRM: Stage Update
    $(document).on('change', '.cce-stage-select', function() {
        const leadId = $(this).data('lead-id');
        const stageId = $(this).val();
        cceApi('crm/leads/' + leadId + '/stage', 'POST', { stage_id: stageId }, function(res) {
            if (res.success) location.reload();
        });
    });

    // CRM: Status Toggle
    $(document).on('change', '.cce-status-toggle', function() {
        const leadId = $(this).data('lead-id');
        const status = $(this).val();
        cceApi('leads/' + leadId + '/status', 'POST', { status: status }, function(res) {
            if (res.success) location.reload();
        });
    });

    // Generic Actions
    $('.cce-delete-funnel').on('click', function() {
        if(!confirm('Delete funnel?')) return;
        cceApi('funnels/' + $(this).data('funnel-id'), 'DELETE', {}, () => location.reload());
    });

    $('.cce-delete-lead').on('click', function() {
        if(!confirm('Delete lead?')) return;
        cceApi('leads/' + $(this).data('lead-id'), 'DELETE', {}, () => location.reload());
    });

    $('.cce-delete-offer').on('click', function() {
        if(!confirm('Delete offer?')) return;
        cceApi('offers/' + $(this).data('offer-id'), 'DELETE', {}, () => location.reload());
    });

    $('.cce-edit-lead').on('click', function() {
        const id = $(this).data('lead-id');
        const $row = $('#lead-row-' + id);
        $('#edit-lead-id').val(id);
        $('#edit-lead-first-name').val($row.data('first-name'));
        $('#edit-lead-last-name').val($row.data('last-name'));
        $('#edit-lead-email').val($row.data('email'));
        $('#edit-lead-status').val($row.data('status'));
        $('#cce-edit-lead-modal').show();
    });

    $('#cce-edit-lead-form').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit-lead-id').val();
        const data = {
            first_name: $('#edit-lead-first-name').val(),
            last_name: $('#edit-lead-last-name').val(),
            email: $('#edit-lead-email').val(),
            status: $('#edit-lead-status').val()
        };
        cceApi('leads/' + id, 'POST', data, function(res) {
            if(res.success) location.reload();
        });
    });

    $('.cce-edit-offer').on('click', function() {
        const id = $(this).data('offer-id');
        const $row = $('#offer-row-' + id);
        $('#edit-offer-id').val(id);
        $('#edit-offer-title').val($row.data('title'));
        $('#edit-offer-price').val($row.data('price'));
        $('#edit-offer-type').val($row.data('type'));
        $('#edit-offer-dream-outcome').val($row.data('dream-outcome'));
        $('#edit-offer-perceived-likelihood').val($row.data('perceived-likelihood'));
        $('#edit-offer-time-delay').val($row.data('time-delay'));
        $('#edit-offer-effort-sacrifice').val($row.data('effort-sacrifice'));
        $('#cce-edit-offer-modal').show();
    });

    $('#cce-edit-offer-form').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit-offer-id').val();
        const data = {
            title: $('#edit-offer-title').val(),
            price: $('#edit-offer-price').val(),
            type: $('#edit-offer-type').val(),
            dream_outcome: $('#edit-offer-dream-outcome').val(),
            perceived_likelihood: $('#edit-offer-perceived-likelihood').val(),
            time_delay: $('#edit-offer-time-delay').val(),
            effort_sacrifice: $('#edit-offer-effort-sacrifice').val()
        };
        cceApi('offers/' + id, 'POST', data, function(res) {
            if(res.success) location.reload();
        });
    });

    $('.cce-booking-action').on('click', function() {
        const id = $(this).data('booking-id');
        const action = $(this).data('action');
        cceApi('bookings/' + id + '/status', 'POST', { status: action }, () => location.reload());
    });

    $('.cce-delete-booking').on('click', function() {
        if(!confirm('Delete booking?')) return;
        cceApi('bookings/' + $(this).data('booking-id'), 'DELETE', {}, () => location.reload());
    });

    // Funnels: View Steps (enhanced for reordering)
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
        loadFunnelSteps(funnelId, $container);
    });

    function loadFunnelSteps(funnelId, $container) {
        cceApi('funnels/' + funnelId + '/steps', 'GET', {}, function(res) {
            if (res.success && res.data.length > 0) {
                let html = '<ul style="list-style:none; padding:0;">';
                res.data.forEach((step, index) => {
                    html += `<li style="background:#fff; padding:10px; margin-bottom:5px; border:1px solid #ddd; display:flex; justify-content:space-between; align-items:center;">
                        <span><strong>${step.title}</strong> (${step.step_type})</span>
                        <div>
                            <button class="button button-small cce-step-move" data-funnel-id="${funnelId}" data-idx="${index}" data-dir="up" ${index===0?'disabled':''}>↑</button>
                            <button class="button button-small cce-step-move" data-funnel-id="${funnelId}" data-idx="${index}" data-dir="down" ${index===res.data.length-1?'disabled':''}>↓</button>
                            <button class="button button-small cce-step-remove" data-funnel-id="${funnelId}" data-idx="${index}" style="color:#d63638;">×</button>
                        </div>
                    </li>`;
                });
                html += '</ul>';
                $container.html(html);
            } else {
                $container.html('<p>No steps configured.</p>');
            }
        });
    }

    $(document).on('click', '.cce-step-move, .cce-step-remove', function() {
        const funnelId = $(this).data('funnel-id');
        const idx = $(this).data('idx');
        const dir = $(this).data('dir');
        const isRemove = $(this).hasClass('cce-step-remove');

        cceApi('funnels/' + funnelId + '/steps', 'GET', {}, function(res) {
            let steps = res.data.map(s => ({ title: s.title, type: s.step_type }));

            if (isRemove) {
                steps.splice(idx, 1);
            } else {
                const targetIdx = dir === 'up' ? idx - 1 : idx + 1;
                [steps[idx], steps[targetIdx]] = [steps[targetIdx], steps[idx]];
            }

            cceApi('funnels/' + funnelId + '/steps', 'POST', JSON.stringify({ steps: steps }), function() {
                loadFunnelSteps(funnelId, $('.steps-container-' + funnelId));
            });
        });
    });
});
