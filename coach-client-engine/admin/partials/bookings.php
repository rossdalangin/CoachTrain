<div class="wrap cce-admin-wrap">
    <h1>Scheduled Consultations</h1>
    <p class="description">Manage your coaching schedule. Leads who book a session are automatically moved to the "Booked" stage in your CRM.</p>
    <hr class="wp-header-end">

    <div class="cce-card" style="margin-bottom:20px; border-left:4px solid #ffb700;">
        <h3>💡 Pro Tip: Pre-Call Qualification</h3>
        <p style="font-size:12px;">Use dynamic questions to filter leads. High-ticket sessions should only be booked by qualified prospects.</p>
        <p style="font-size:11px; color:#666;"><strong>Example:</strong> Add a question: <em>"What is your monthly budget for growth?"</em></p>
        <p style="font-size:11px; color:#666;"><strong>What's Next?</strong> Set up your availability and add qualifying questions in the <strong>Questionnaire Builder</strong> below.</p>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <form method="get" action="">
            <input type="hidden" name="page" value="cce-bookings">
            <input type="search" name="s" value="<?php echo esc_attr($_GET['s'] ?? ''); ?>" placeholder="Search bookings...">
            <button type="submit" class="button">Search</button>
        </form>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="cce_export_bookings">
            <?php wp_nonce_field('cce_export_bookings_nonce'); ?>
            <button type="submit" class="button">Export to CSV</button>
        </form>
    </div>

    <div class="cce-card" style="margin-bottom: 20px;">
        <h3>Add New Booking</h3>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="cce_save_booking">
            <?php wp_nonce_field('cce_save_booking_nonce'); ?>
            <div style="display:flex; gap:10px; flex-wrap: wrap;">
                <select name="lead_id" required>
                    <option value="">Select Lead...</option>
                    <?php
                    global $wpdb;
                    $user_id = get_current_user_id();
                    $leads_list = $wpdb->get_results($wpdb->prepare("SELECT id, first_name, last_name FROM {$wpdb->prefix}cce_leads WHERE user_id = %d", $user_id));
                    foreach($leads_list as $l) echo "<option value='{$l->id}'>{$l->first_name} {$l->last_name}</option>";
                    ?>
                </select>
                <input type="datetime-local" name="start_time" required>
                <input type="text" name="timezone" placeholder="UTC" required>
                <button type="submit" class="button button-primary">Schedule Booking</button>
            </div>
        </form>
    </div>

    <?php
    $search = $_GET['s'] ?? '';
    $query = "
        SELECT b.*, CONCAT(l.first_name, ' ', l.last_name) as lead_name
        FROM {$wpdb->prefix}cce_bookings b
        LEFT JOIN {$wpdb->prefix}cce_leads l ON b.lead_id = l.id
        WHERE b.user_id = %d
    ";
    $params = array( $user_id );

    if ( ! empty( $search ) ) {
        $query .= " AND (l.first_name LIKE %s OR l.last_name LIKE %s OR l.email LIKE %s)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    $query .= " ORDER BY b.start_time ASC";
    $bookings = $wpdb->get_results( $wpdb->prepare( $query, $params ) );
    ?>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Client</th>
                <th>Time</th>
                <th>Timezone</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($bookings): foreach ( $bookings as $booking ): ?>
                <tr>
                    <td>
                        <strong><?php echo esc_html( $booking->lead_name ); ?></strong>
                        <?php if ($booking->questionnaire_data): ?>
                            <br><a href="#" class="cce-view-questionnaire" data-data='<?php echo esc_attr($booking->questionnaire_data); ?>' style="font-size:10px;">View Questionnaire</a>
                        <?php endif; ?>
                    </td>
                    <td><?php echo esc_html( $booking->start_time ); ?></td>
                    <td><?php echo esc_html( $booking->timezone ); ?></td>
                    <td><span class="status-tag status-<?php echo esc_attr($booking->status); ?>"><?php echo esc_html( strtoupper( $booking->status ) ); ?></span></td>
                    <td>
                        <?php if ($booking->status === 'pending'): ?>
                            <button class="button button-small cce-booking-action" data-booking-id="<?php echo $booking->id; ?>" data-action="confirmed">Confirm</button>
                        <?php endif; ?>
                        <?php if ($booking->status === 'confirmed'): ?>
                            <button class="button button-small cce-booking-action" data-booking-id="<?php echo $booking->id; ?>" data-action="completed" style="background:#00a32a; color:#fff;">Mark Completed</button>
                        <?php endif; ?>
                        <?php if ($booking->status !== 'cancelled' && $booking->status !== 'completed'): ?>
                            <button class="button button-small cce-booking-action" data-booking-id="<?php echo $booking->id; ?>" data-action="cancelled">Cancel</button>
                        <?php endif; ?>
                        <button class="button button-link-delete cce-delete-booking" data-booking-id="<?php echo $booking->id; ?>" style="color:#d63638;">Delete</button>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="5">No bookings found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div id="cce-questionnaire-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div style="background:#fff; margin:10% auto; padding:25px; width:400px; border-radius:12px; position:relative;">
            <span class="cce-modal-close" style="position:absolute; right:20px; top:15px; cursor:pointer; font-size:24px;">&times;</span>
            <h2>Pre-call Questionnaire</h2>
            <div id="cce-questionnaire-content"></div>
        </div>
    </div>

    <div class="cce-card" style="margin-top:30px;">
        <h3>Questionnaire Builder</h3>
        <p>Define the questions clients must answer when booking a consultation.</p>
        <form id="cce-add-question-form">
            <div style="display:flex; gap:10px; align-items:center;">
                <input type="text" id="new-question-text" placeholder="Enter question..." class="regular-text" required>
                <select id="new-question-type">
                    <option value="text">Short Text</option>
                    <option value="textarea">Long Text</option>
                </select>
                <label><input type="checkbox" id="new-question-required" checked> Required</label>
                <button type="submit" class="button button-primary">Add Question</button>
            </div>
        </form>

        <table class="wp-list-table widefat fixed striped" style="margin-top:15px;">
            <thead><tr><th>Question</th><th>Type</th><th>Required</th><th>Action</th></tr></thead>
            <tbody id="cce-questions-body">
                <?php
                $questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}cce_questions WHERE user_id = %d ORDER BY question_order ASC", $user_id));
                if($questions): foreach($questions as $q): ?>
                <tr>
                    <td><?php echo esc_html($q->question_text); ?></td>
                    <td><?php echo esc_html($q->question_type); ?></td>
                    <td><?php echo $q->is_required ? 'Yes' : 'No'; ?></td>
                    <td><button class="button button-link-delete cce-delete-question" data-id="<?php echo $q->id; ?>" style="color:#d63638;">×</button></td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="4">No custom questions. Default "Goal" question will be used.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('#cce-add-question-form').on('submit', function(e) {
            e.preventDefault();
            const data = {
                question_text: $('#new-question-text').val(),
                question_type: $('#new-question-type').val(),
                is_required: $('#new-question-required').is(':checked') ? 1 : 0
            };
            cceApi('bookings/questions', 'POST', data, () => location.reload());
        });

        $(document).on('click', '.cce-delete-question', function() {
            if(!confirm('Delete question?')) return;
            cceApi('bookings/questions/' + $(this).data('id'), 'DELETE', {}, () => location.reload());
        });

        $('.cce-view-questionnaire').on('click', function(e) {
            e.preventDefault();
            const data = $(this).data('data');
            let html = '<ul>';
            for (const key in data) {
                const escapedKey = $('<div>').text(key).html();
                const escapedVal = $('<div>').text(data[key]).html();
                html += `<li><strong>${escapedKey}:</strong> ${escapedVal}</li>`;
            }
            html += '</ul>';
            $('#cce-questionnaire-content').html(html);
            $('#cce-questionnaire-modal').show();
        });
    });
    </script>
</div>
