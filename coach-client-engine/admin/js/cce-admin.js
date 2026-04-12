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

    // Leads: Edit
    $(document).on('click', '.cce-edit-lead', function() {
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

    // Offers: Edit
    $(document).on('click', '.cce-edit-offer', function() {
        const id = $(this).data('offer-id');
        const $row = $('#offer-row-' + id);
        $('#edit-offer-id').val(id);
        $('#edit-offer-title').val($row.data('title'));
        $('#edit-offer-price').val($row.data('price'));
        $('#edit-offer-type').val($row.data('type'));
        $('#cce-edit-offer-modal').show();
    });

    $('#cce-edit-offer-form').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit-offer-id').val();
        const data = {
            title: $('#edit-offer-title').val(),
            price: $('#edit-offer-price').val(),
            type: $('#edit-offer-type').val()
        };
        cceApi('offers/' + id, 'POST', data, function(res) {
            if(res.success) location.reload();
        });
    });

    // CRM: Tabs
    $('.cce-tab-link').on('click', function() {
        $('.cce-tab-link').removeClass('active').css('border-bottom', 'none');
        $(this).addClass('active').css('border-bottom', '2px solid #0073aa');
        $('.cce-tab-content').hide();
        $('#cce-tab-' + $(this).data('tab')).show();
    });

    // CRM: View Details (Notes/Tasks)
    $(document).on('click', '.cce-view-notes, .cce-view-tasks', function(e) {
        e.preventDefault();
        const leadId = $(this).data('lead-id');
        const leadName = $(this).data('lead-name');
        const initialTab = $(this).hasClass('cce-view-tasks') ? 'tasks' : 'notes';

        $('#cce-modal-title').text(leadName);
        $('.cce-lead-id-field').val(leadId);
        $(`.cce-tab-link[data-tab="${initialTab}"]`).click();
        $('#cce-leads-modal').show();

        loadNotes(leadId);
        loadTasks(leadId);
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

    // CRM: Close Modals
    $(document).on('click', '.cce-modal-close', function() {
        $('.cce-edit-lead-modal, #cce-leads-modal, #cce-edit-lead-modal, #cce-edit-offer-modal').hide();
    });

    // CRM: Stage Update
    $(document).on('change', '.cce-stage-select', function() {
        const leadId = $(this).data('lead-id');
        const stageId = $(this).val();
        cceApi('crm/leads/' + leadId + '/stage', 'POST', { stage_id: stageId }, function(res) {
            if (res.success) location.reload();
        });
    });

    // Funnels, Leads, Offers delete logic
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
});
