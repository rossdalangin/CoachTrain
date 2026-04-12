jQuery(document).ready(function($) {
    // Stage Update
    $(document).on('change', '.cce-stage-select', function() {
        const leadId = $(this).data('lead-id');
        const stageId = $(this).val();

        $.ajax({
            url: cceAdmin.restUrl + 'crm/leads/' + leadId + '/stage',
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', cceAdmin.nonce);
            },
            data: {
                stage_id: stageId
            },
            success: function(response) {
                if (response.success) {
                    // Optionally reload or move card in UI
                    location.reload();
                }
            }
        });
    });

    // View Notes
    $(document).on('click', '.cce-view-notes', function(e) {
        e.preventDefault();
        const leadId = $(this).data('lead-id');
        const leadName = $(this).data('lead-name');

        $('#cce-modal-title').text('Notes for ' + leadName);
        $('#cce-notes-content').html('<p>Loading notes...</p>');
        $('#cce-notes-modal').show();

        $.ajax({
            url: cceAdmin.restUrl + 'crm/pipeline', // Pipeline returns activities
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', cceAdmin.nonce);
            },
            success: function(response) {
                if (response.success) {
                    let lead = null;
                    response.data.forEach(stage => {
                        const found = stage.leads.find(l => l.id == leadId);
                        if (found) lead = found;
                    });

                    if (lead && lead.activities) {
                        let html = '<ul>';
                        lead.activities.forEach(activity => {
                            html += `<li><strong>${activity.activity_type}:</strong> ${activity.description} <br><small>${activity.created_at}</small></li>`;
                        });
                        html += '</ul>';
                        $('#cce-notes-content').html(html);
                    } else {
                        $('#cce-notes-content').html('<p>No activities found.</p>');
                    }
                }
            }
        });
    });

    // Close Modal
    $(document).on('click', '.cce-modal-close', function() {
        $('#cce-notes-modal').hide();
    });
});
