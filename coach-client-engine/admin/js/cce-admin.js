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

    // CRM: Manage Stages
    $('.cce-manage-stages-btn').on('click', function() {
        $('#cce-manage-stages-modal').show();
    });

    $(document).on('click', '.cce-edit-template', function() {
        const id = $(this).data('id');
        const $row = $('#template-row-' + id);
        $('#edit-template-id').val(id);
        $('#edit-template-name').val($row.data('name'));
        $('#edit-template-subject').val($row.data('subject'));
        $('#edit-template-content').val($row.data('content'));
        $('#cce-edit-template-modal').show();
    });

    $('#cce-edit-template-form').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit-template-id').val();
        const data = {
            name: $('#edit-template-name').val(),
            subject: $('#edit-template-subject').val(),
            content: $('#edit-template-content').val()
        };
        cceApi('automation/templates/' + id, 'POST', JSON.stringify(data), function(res) {
            if(res.success) location.reload();
        });
    });

    $(document).on('click', '.cce-edit-rule', function() {
        const id = $(this).data('rule-id');
        const $row = $('#rule-row-' + id);
        const config = $row.data('config');

        $('#edit-rule-id').val(id);
        $('#edit-rule-trigger').val($row.data('trigger')).change();
        $('#edit-rule-action').val($row.data('action')).change();

        if ($row.data('action') === 'move_stage') {
            $('#edit-config-stage-id').val(config.stage_id);
        } else if ($row.data('action') === 'schedule_reminder') {
            $('#edit-config-delay').val(config.delay_hours);
        } else if ($row.data('action') === 'send_email') {
            $('#edit-config-template-id').val(config.template_id);
        } else if ($row.data('action') === 'create_task') {
            $('#edit-config-task-title').val(config.task_title);
        }

        $('#cce-edit-rule-modal').show();
    });

    $('#rule-trigger-event').on('change', function() {
        $('#trigger-config-stage').toggle($(this).val() === 'cce_lead_stage_changed');
    });

    $('#edit-rule-trigger').on('change', function() {
        $('#edit-trigger-config-stage').toggle($(this).val() === 'cce_lead_stage_changed');
    });

    $('#edit-rule-action').on('change', function() {
        const val = $(this).val();
        $('#edit-action-config-email').toggle(val === 'send_email');
        $('#edit-action-config-stage').toggle(val === 'move_stage');
        $('#edit-action-config-reminder').toggle(val === 'schedule_reminder');
        $('#edit-action-config-task').toggle(val === 'create_task');
        $('#edit-action-config-webhook').toggle(val === 'trigger_webhook');
        $('#edit-action-config-tag').toggle(val === 'add_tag' || val === 'remove_tag');
    });

    $('#cce-edit-rule-form').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit-rule-id').val();
        const action = $('#edit-rule-action').val();
        const data = {
            trigger_event: $('#edit-rule-trigger').val(),
            action_type: action,
            config: {
                trigger_stage_id: $('#edit-config-trigger-stage-id').val(),
                template_id: $('#edit-config-template-id').val(),
                stage_id: $('#edit-config-stage-id').val(),
                delay_hours: $('#edit-config-delay').val(),
                task_title: $('#edit-config-task-title').val(),
                webhook_id: $('#edit-config-webhook-id').val()
            }
        };
        // Reuse create rule endpoint if it supports ID or create a new one
        cceApi('automation/rules/' + id, 'POST', JSON.stringify(data), function(res) {
            if(res.success) location.reload();
        });
    });

    $(document).on('click', '.cce-edit-testimonial', function() {
        const id = $(this).data('id');
        const $row = $('#testimonial-row-' + id);
        $('#edit-testimonial-id').val(id);
        $('#edit-testimonial-type').val($row.data('type'));
        $('#edit-testimonial-title').val($row.data('title'));
        $('#edit-testimonial-name').val($row.data('client-name'));
        $('#edit-testimonial-content').val($row.data('content'));
        $('#edit-testimonial-rating').val($row.data('rating'));
        $('#cce-edit-testimonial-modal').show();
    });

    $('#cce-add-testimonial-form').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        cceApi('proof/testimonials', 'POST', JSON.stringify(data), function(res) {
            if(res.success) location.reload();
        });
    });

    $('#cce-edit-testimonial-form').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit-testimonial-id').val();
        const data = {
            type: $('#edit-testimonial-type').val(),
            title: $('#edit-testimonial-title').val(),
            client_name: $('#edit-testimonial-name').val(),
            content: $('#edit-testimonial-content').val(),
            rating: $('#edit-testimonial-rating').val()
        };
        cceApi('proof/testimonials/' + (id || ''), 'POST', JSON.stringify(data), function(res) {
            if(res.success) location.reload();
        });
    });

    $('#cce-add-stage-form').on('submit', function(e) {
        e.preventDefault();
        const name = $('#new-stage-name').val();
        cceApi('crm/stages', 'POST', { name: name }, () => location.reload());
    });

    $('.cce-delete-stage').on('click', function() {
        if(!confirm('Delete stage? leads in this stage will be orphaned.')) return;
        cceApi('crm/stages/' + $(this).data('id'), 'DELETE', {}, () => location.reload());
    });

    // CRM: Tabs
    $('.cce-tab-link').on('click', function() {
        $('.cce-tab-link').removeClass('active').css('border-bottom', 'none');
        $(this).addClass('active').css('border-bottom', '2px solid #0073aa');
        $('.cce-tab-content').hide();
        $('#cce-tab-' + $(this).data('tab')).show();
    });

    // CRM: View Details (Notes/Tasks/Contact/Stats)
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
        loadMilestones(leadId);
        loadStats(leadId);
        $('#cce-contact-status').html('');
    });

    function loadStats(leadId) {
        cceApi('crm/leads/' + leadId + '/stats', 'GET', {}, function(res) {
            if (res.success) {
                const s = res.data;
                let html = `
                <div style="margin-bottom:15px; font-size:12px; color:#666;">
                    <strong>Source:</strong> ${s.source} | <strong>Joined:</strong> ${s.created_at}
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div class="cce-card" style="border-top:2px solid #0073aa; padding:15px;">
                        <small>TOTAL PAID</small><div style="font-size:20px; font-weight:bold;">$${s.total_paid.toFixed(2)}</div>
                    </div>
                    <div class="cce-card" style="border-top:2px solid #ffb700; padding:15px;">
                        <small>APPOINTMENTS</small><div style="font-size:20px; font-weight:bold;">${s.appointments}</div>
                    </div>
                    <div class="cce-card" style="border-top:2px solid #00a32a; padding:15px;">
                        <small>TASKS DONE</small><div style="font-size:20px; font-weight:bold;">${s.tasks_done}/${s.tasks_total}</div>
                    </div>
                </div>`;
                $('#cce-lead-stats-content').html(html);
            }
        });
    }

    function loadNotes(leadId) {
        cceApi('crm/leads/' + leadId + '/activities', 'GET', {}, function(res) {
            if (res.success) {
                let html = '<ul class="cce-activity-list" style="padding-left:0; list-style:none;">';
                res.data.forEach(activity => {
                    const desc = $('<div>').text(activity.description).html();
                    const type = $('<div>').text(activity.activity_type.toUpperCase()).html();
                    const date = $('<div>').text(activity.created_at).html();
                    html += `<li style="border-bottom:1px solid #eee; padding:10px 0;">
                        <div style="display:flex; justify-content:space-between;">
                            <strong>${type}</strong>
                            <small style="color:#888;">${date}</small>
                        </div>
                        <div style="margin-top:5px;">${desc}</div>
                    </li>`;
                });
                html += '</ul>';
                $('#cce-notes-content').html(res.data.length ? html : '<p>No activities found.</p>');
            }
        });
    }

    function loadMilestones(leadId) {
        cceApi('crm/leads/' + leadId + '/milestones', 'GET', {}, function(res) {
            if (res.success) {
                let html = '<ul style="padding-left:0; list-style:none;">';
                res.data.forEach(m => {
                    const checked = m.is_completed == 1 ? 'checked' : '';
                    const title = $('<div>').text(m.title).html();
                    html += `<li style="padding:8px 0; border-bottom:1px solid #f9f9f9; display:flex; justify-content:space-between; align-items:center;">
                        <label>
                            <input type="checkbox" ${checked} class="cce-toggle-milestone" data-id="${m.id}" data-lead-id="${leadId}">
                            ${title}
                        </label>
                        <button class="button button-small cce-delete-milestone" data-id="${m.id}" data-lead-id="${leadId}" style="color:#d63638;">×</button>
                    </li>`;
                });
                html += '</ul>';
                $('#cce-milestones-content').html(res.data.length ? html : '<p>No milestones tracked.</p>');
            }
        });
    }

    $(document).on('change', '.cce-toggle-milestone', function() {
        const leadId = $(this).data('lead-id');
        const id = $(this).data('id');
        const completed = $(this).is(':checked') ? 1 : 0;
        cceApi('crm/leads/' + leadId + '/milestones/' + id, 'POST', { is_completed: completed }, function() {
            loadMilestones(leadId);
        });
    });

    $(document).on('click', '.cce-delete-milestone', function() {
        if(!confirm('Delete milestone?')) return;
        const leadId = $(this).data('lead-id');
        const id = $(this).data('id');
        cceApi('crm/leads/' + leadId + '/milestones/' + id, 'DELETE', {}, function() {
            loadMilestones(leadId);
        });
    });

    $('#cce-add-milestone-form').on('submit', function(e) {
        e.preventDefault();
        const leadId = $(this).find('.cce-lead-id-field').val();
        const title = $('#cce-new-milestone-title').val();
        cceApi('crm/leads/' + leadId + '/milestones', 'POST', { title: title }, function(res) {
            if (res.success) {
                $('#cce-new-milestone-title').val('');
                loadMilestones(leadId);
            }
        });
    });

    function loadTasks(leadId) {
        cceApi('crm/leads/' + leadId + '/tasks', 'GET', {}, function(res) {
            if (res.success) {
                let html = '<ul style="padding-left:0; list-style:none;">';
                res.data.forEach(task => {
                    const checked = task.status === 'completed' ? 'checked' : '';
                    const title = $('<div>').text(task.title).html();
                    html += `<li style="padding:8px 0; border-bottom:1px solid #f9f9f9; display:flex; justify-content:space-between; align-items:center;">
                        <label>
                            <input type="checkbox" ${checked} class="cce-toggle-task" data-task-id="${task.id}" data-lead-id="${leadId}">
                            ${title}
                        </label>
                        <button class="button button-small cce-delete-task" data-task-id="${task.id}" data-lead-id="${leadId}" style="color:#d63638;">×</button>
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
            loadStats(leadId);
        });
    });

    $(document).on('click', '.cce-delete-task', function() {
        if(!confirm('Delete task?')) return;
        const leadId = $(this).data('lead-id');
        const taskId = $(this).data('task-id');
        cceApi('crm/leads/' + leadId + '/tasks/' + taskId, 'DELETE', {}, function() {
            loadTasks(leadId);
            loadStats(leadId);
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
                loadStats(leadId);
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
        $('#cce-leads-modal, #cce-edit-lead-modal, #cce-edit-offer-modal, #cce-add-step-modal, #cce-step-config-modal, #cce-manage-stages-modal, #cce-questionnaire-modal, #cce-edit-template-modal, #cce-edit-rule-modal').hide();
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

    // Leads: Bulk Actions
    $('#cce-select-all-leads').on('change', function() {
        $('.cce-lead-checkbox').prop('checked', $(this).is(':checked'));
    });

    $('#cce-apply-bulk-action').on('click', function() {
        const action = $('#cce-bulk-action-selector').val();
        const ids = $('.cce-lead-checkbox:checked').map(function() { return $(this).val(); }).get();

        if (!action || ids.length === 0) {
            alert('Please select an action and at least one lead.');
            return;
        }

        if (action === 'delete' && !confirm('Are you sure you want to delete ' + ids.length + ' leads?')) return;

        cceApi('leads/bulk', 'POST', { bulk_action: action, ids: ids }, function(res) {
            if (res.success) location.reload();
        });
    });

    // Generic Actions
    $('.cce-delete-funnel').on('click', function() {
        if(!confirm('Delete funnel?')) return;
        cceApi('funnels/' + $(this).data('funnel-id'), 'DELETE', {}, () => location.reload());
    });

    $('.cce-duplicate-funnel').on('click', function() {
        if(!confirm('Duplicate this funnel?')) return;
        cceApi('funnels/' + $(this).data('funnel-id') + '/duplicate', 'POST', {}, () => location.reload());
    });

    $('.cce-delete-lead').on('click', function() {
        if(!confirm('Delete lead?')) return;
        cceApi('leads/' + $(this).data('lead-id'), 'DELETE', {}, () => location.reload());
    });

    $('.cce-delete-offer').on('click', function() {
        if(!confirm('Delete offer?')) return;
        cceApi('offers/' + $(this).data('offer-id'), 'DELETE', {}, () => location.reload());
    });

    $('.cce-duplicate-offer').on('click', function() {
        if(!confirm('Duplicate this offer?')) return;
        cceApi('offers/' + $(this).data('offer-id') + '/duplicate', 'POST', {}, () => location.reload());
    });

    $('.cce-delete-resource').on('click', function() {
        if(!confirm('Delete resource?')) return;
        cceApi('portal/resources/' + $(this).data('id'), 'DELETE', {}, () => location.reload());
    });

    $('.cce-delete-testimonial').on('click', function() {
        if(!confirm('Delete testimonial?')) return;
        cceApi('proof/testimonials/' + $(this).data('id'), 'DELETE', {}, () => location.reload());
    });

    $('.cce-delete-template').on('click', function() {
        if(!confirm('Delete template?')) return;
        cceApi('automation/templates/' + $(this).data('id'), 'DELETE', {}, () => location.reload());
    });

    $('#cce-broadcast-form').on('submit', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button');
        const originalText = $btn.text();
        $btn.prop('disabled', true).text('Sending Broadcast...');

        const data = {
            tag: $(this).find('[name="tag"]').val(),
            template_id: $(this).find('[name="template_id"]').val()
        };

        cceApi('automation/broadcast', 'POST', JSON.stringify(data), function(res) {
            $btn.prop('disabled', false).text(originalText);
            if (res.success) {
                $('#broadcast-status').html('<div class="notice notice-success inline"><p>Broadcast sent to ' + res.data.count + ' leads!</p></div>');
            }
        });
    });

    $('.cce-edit-lead').on('click', function() {
        const id = $(this).data('lead-id');
        const $row = $('#lead-row-' + id);
        $('#edit-lead-id').val(id);
        $('#edit-lead-first-name').val($row.data('first-name'));
        $('#edit-lead-last-name').val($row.data('last-name'));
        $('#edit-lead-email').val($row.data('email'));
        $('#edit-lead-phone').val($row.data('phone'));
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
            phone: $('#edit-lead-phone').val(),
            status: $('#edit-lead-status').val(),
            tags: $('#edit-lead-tags').val()
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
        $('#edit-offer-upsell-id').val($row.data('upsell-id'));
        $('#edit-offer-downsell-id').val($row.data('downsell-id'));
        $('#edit-offer-order-bump-id').val($row.data('order-bump-id'));
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
            effort_sacrifice: $('#edit-offer-effort-sacrifice').val(),
            upsell_offer_id: $('#edit-offer-upsell-id').val(),
            downsell_offer_id: $('#edit-offer-downsell-id').val(),
            order_bump_offer_id: $('#edit-offer-order-bump-id').val()
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
        loadFunnelSteps(funnelId, $container);
        loadFunnelViz(funnelId);
    });

    function loadFunnelViz(funnelId) {
        cceApi('funnels/' + funnelId + '/steps', 'GET', {}, function(res) {
            if (res.success && res.data.length > 0) {
                const $viz = $('.funnel-viz-' + funnelId);
                const $track = $('.viz-track-' + funnelId);
                $track.html('');
                $viz.show();

                res.data.forEach((step, idx) => {
                    const visits = parseInt(step.visits || 0);
                    const convs = parseInt(step.conversions || 0);
                    const rate = visits > 0 ? ((convs / visits) * 100).toFixed(1) : 0;

                    let html = `<div style="text-align:center; min-width:120px; padding:15px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                        <div style="font-size:11px; color:#64748b; margin-bottom:5px; text-transform:uppercase;">Step ${idx + 1}</div>
                        <div style="font-weight:700; margin-bottom:10px;">${step.title}</div>
                        <div style="display:flex; justify-content:space-between; font-size:12px;">
                            <span>Visits:</span><strong>${visits}</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; font-size:12px;">
                            <span>Convs:</span><strong>${convs}</strong>
                        </div>
                        <div style="margin-top:10px; padding-top:5px; border-top:1px solid #f1f5f9; font-size:14px; font-weight:800; color:#2563eb;">
                            ${rate}%
                        </div>
                    </div>`;

                    if (idx < res.data.length - 1) {
                        html += `<div style="color:#94a3b8; font-size:20px;">→</div>`;
                    }
                    $track.append(html);
                });
            }
        });
    }

    function loadFunnelSteps(funnelId, $container) {
        cceApi('funnels/' + funnelId + '/steps', 'GET', {}, function(res) {
            if (res.success && res.data.length > 0) {
                let html = '<ul style="list-style:none; padding:0;">';
                res.data.forEach((step, index) => {
                    const configStr = JSON.stringify(step.config);
                    const visits = step.visits || 0;
                    html += `<li style="background:#fff; padding:10px; margin-bottom:5px; border:1px solid #ddd; display:flex; justify-content:space-between; align-items:center;">
                        <span><strong>${step.title}</strong> (${step.step_type}) - <small>${visits} visits</small></span>
                        <div>
                            <button class="button button-small cce-step-config" data-funnel-id="${funnelId}" data-idx="${index}" data-type="${step.step_type}" data-config='${configStr}'>⚙</button>
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
            let steps = res.data.map(s => ({ title: s.title, type: s.step_type, config: s.config }));

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

    $(document).on('click', '.cce-step-config', function() {
        const funnelId = $(this).data('funnel-id');
        const idx = $(this).data('idx');
        const type = $(this).data('type');
        const config = $(this).data('config');

        $('#config-funnel-id').val(funnelId);
        $('#config-step-idx').val(idx);
        $('#config-offer-selector').toggle(type === 'checkout');
        $('#config-thankyou-selector').toggle(type === 'thank_you');

        if (type === 'checkout') {
            $('#config-offer-id').val(config.offer_id || '');
        }
        if (type === 'thank_you') {
            $('#config-success-message').val(config.success_message || '');
            $('#config-redirect-url').val(config.redirect_url || '');
        }

        $('#cce-step-config-modal').show();
    });

    $('#cce-step-config-form').on('submit', function(e) {
        e.preventDefault();
        const funnelId = $('#config-funnel-id').val();
        const idx = parseInt($('#config-step-idx').val());
        const offerId = $('#config-offer-id').val();
        const successMessage = $('#config-success-message').val();
        const redirectUrl = $('#config-redirect-url').val();

        cceApi('funnels/' + funnelId + '/steps', 'GET', {}, function(res) {
            let steps = res.data.map(s => ({ title: s.title, type: s.step_type, config: s.config }));
            steps[idx].config = {
                offer_id: offerId,
                success_message: successMessage,
                redirect_url: redirectUrl
            };

            cceApi('funnels/' + funnelId + '/steps', 'POST', JSON.stringify({ steps: steps }), function() {
                $('#cce-step-config-modal').hide();
                loadFunnelSteps(funnelId, $('.steps-container-' + funnelId));
            });
        });
    });

    // Automation UI Tabs logic...
    $('.cce-automation-tab-link').on('click', function() {
        $('.cce-automation-tab-link').removeClass('active').css('border-bottom', 'none');
        $(this).addClass('active').css('border-bottom', '2px solid #0073aa');
        $('.cce-automation-tab-content').hide();
        $('#tab-' + $(this).data('tab')).show();
    });

    // Funnel Templates
    $('.cce-use-template').on('click', function() {
        const templateId = $(this).data('template');
        if(!confirm('Create new funnel from ' + templateId + ' template?')) return;
        cceApi('funnels/create-from-template', 'POST', { template_id: templateId }, function(res) {
            if(res.success) location.reload();
        });
    });

    // Funnel Step Management
    $('.cce-add-step-btn').on('click', function() {
        $('#add-step-funnel-id').val($(this).data('funnel-id'));
        $('#cce-add-step-modal').show();
    });

    $('#cce-add-step-form').on('submit', function(e) {
        e.preventDefault();
        const funnelId = $('#add-step-funnel-id').val();
        const title = $('#add-step-title').val();
        const type = $('#add-step-type').val();

        cceApi('funnels/' + funnelId + '/steps', 'GET', {}, function(res) {
            let steps = res.data.map(s => ({ title: s.title, type: s.step_type, config: s.config }));
            steps.push({ title: title, type: type, config: {} });

            cceApi('funnels/' + funnelId + '/steps', 'POST', JSON.stringify({ steps: steps }), function() {
                $('#cce-add-step-modal').hide();
                loadFunnelSteps(funnelId, $('.steps-container-' + funnelId));
            });
        });
    });

    // Copy Shortcode
    $('.cce-copy-shortcode').on('click', function() {
        const text = $(this).data('shortcode');
        navigator.clipboard.writeText(text).then(() => {
            const original = $(this).text();
            $(this).text('Copied!');
            setTimeout(() => $(this).text(original), 2000);
        });
    });

    // Automation: Add Rule
    $('#cce-add-automation-form').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = {
            trigger_event: formData.get('trigger_event'),
            action_type: formData.get('action_type'),
            config: {
                trigger_stage_id: formData.get('config[trigger_stage_id]'),
                template_id: formData.get('config[template_id]'),
                stage_id: formData.get('config[stage_id]'),
                delay_hours: formData.get('config[delay_hours]'),
                task_title: formData.get('config[task_title]'),
                webhook_id: formData.get('config[webhook_id]')
            }
        };
        cceApi('automation/rules', 'POST', JSON.stringify(data), function(res) {
            if(res.success) location.reload();
        });
    });

    $('#rule-action-type').on('change', function() {
        const val = $(this).val();
        $('#action-config-email').toggle(val === 'send_email');
        $('#action-config-stage').toggle(val === 'move_stage');
        $('#action-config-reminder').toggle(val === 'schedule_reminder');
        $('#action-config-task').toggle(val === 'create_task');
        $('#action-config-webhook').toggle(val === 'trigger_webhook');
        $('#action-config-tag').toggle(val === 'add_tag' || val === 'remove_tag');
    });

    $('.cce-delete-rule').on('click', function() {
        if(!confirm('Delete rule?')) return;
        cceApi('automation/rules/' + $(this).data('rule-id'), 'DELETE', {}, () => location.reload());
    });

    // Automation: Add Webhook
    $('#cce-add-webhook-form').on('submit', function(e) {
        e.preventDefault();
        const data = {
            name: $(this).find('[name="name"]').val(),
            url: $(this).find('[name="url"]').val()
        };
        cceApi('automation/webhooks', 'POST', data, function(res) {
            if (res.success) location.reload();
        });
    });

    $(document).on('click', '.cce-delete-webhook', function() {
        if (!confirm('Delete this webhook?')) return;
        const id = $(this).data('id');
        cceApi('automation/webhooks/' + id, 'DELETE', {}, function(res) {
            if (res.success) $('#webhook-row-' + id).remove();
        });
    });

    // Automation: Add Template
    $('#cce-add-template-form').on('submit', function(e) {
        e.preventDefault();
        const data = {
            name: $(this).find('[name="name"]').val(),
            subject: $(this).find('[name="subject"]').val(),
            content: $(this).find('[name="content"]').val()
        };
        cceApi('automation/templates', 'POST', JSON.stringify(data), function(res) {
            if(res.success) location.reload();
        });
    });

    // Leads: Import CSV with Mapping
    let csvData = [];
    $('#cce-start-import').on('click', function() {
        const file = $('#cce-import-csv')[0].files[0];
        if (!file) { alert('Select CSV'); return; }

        const reader = new FileReader();
        reader.onload = function(e) {
            const text = e.target.result;
            const lines = text.split('\n').filter(l => l.trim() !== '');
            const headers = lines[0].split(',').map(h => h.trim());
            csvData = lines.slice(1).map(l => l.split(','));

            let mappingHtml = '';
            ['first_name', 'last_name', 'email', 'phone'].forEach(field => {
                mappingHtml += `<div style="margin-bottom:10px;">
                    <label style="display:block; font-size:11px;">${field.replace('_', ' ').toUpperCase()}</label>
                    <select class="cce-map-field" data-field="${field}" style="width:100%;">
                        <option value="">-- Skip --</option>
                        ${headers.map((h, i) => `<option value="${i}">${h}</option>`).join('')}
                    </select>
                </div>`;
            });

            $('#cce-mapping-fields').html(mappingHtml);
            $('#cce-import-mapping').show();
            $('#cce-start-import').hide();
        };
        reader.readAsText(file);
    });

    $('#cce-execute-import').on('click', function() {
        const mapping = {};
        $('.cce-map-field').each(function() {
            if ($(this).val() !== '') mapping[$(this).data('field')] = parseInt($(this).val());
        });

        const leads = csvData.map(row => {
            const lead = {};
            for (const [field, index] of Object.entries(mapping)) {
                lead[field] = row[index] ? row[index].trim() : '';
            }
            return lead;
        });

        $('#import-status').text('Importing ' + leads.length + ' leads...');
        cceApi('leads/import', 'POST', JSON.stringify({ leads: leads }), function(res) {
            if (res.success) {
                alert('Imported ' + res.data.count + ' leads!');
                location.reload();
            }
        });
    });

    // CRM: Drag and Drop
    $('.cce-kanban-column').sortable({
        connectWith: '.cce-kanban-column',
        placeholder: 'cce-kanban-placeholder',
        cursor: 'move',
        opacity: 0.7,
        update: function(event, ui) {
            if (this === ui.item.parent()[0]) {
                const leadId = ui.item.data('lead-id');
                const stageId = $(this).data('stage-id');

                cceApi('crm/leads/' + leadId + '/stage', 'POST', { stage_id: stageId }, function(res) {
                    if (res.success) {
                        console.log('Lead moved to stage:', stageId);
                    }
                });
            }
        }
    }).disableSelection();

    // CRM: Quick Task
    $(document).on('submit', '.cce-quick-task-form', function(e) {
        e.preventDefault();
        const leadId = $(this).find('[name="lead_id"]').val();
        const title = $(this).find('[name="title"]').val();
        const $input = $(this).find('[name="title"]');

        cceApi('crm/leads/' + leadId + '/tasks', 'POST', { title: title }, function(res) {
            if (res.success) {
                $input.val('');
                alert('Task added!');
            }
        });
    });
});
