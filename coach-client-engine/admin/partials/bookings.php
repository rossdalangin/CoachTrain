<div class="wrap cce-admin-wrap">
    <h1>Scheduled Consultations</h1>
    <hr class="wp-header-end">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <form method="get" action="">
            <input type="hidden" name="page" value="cce-bookings">
            <input type="search" name="s" value="<?php echo esc_attr($_GET['s'] ?? ''); ?>" placeholder="Search bookings...">
            <button type="submit" class="button">Search</button>
        </form>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="cce_export_bookings">
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
                    $leads_list = $wpdb->get_results("SELECT id, first_name, last_name FROM {$wpdb->prefix}cce_leads");
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
    ";
    if ( ! empty( $search ) ) {
        $query .= $wpdb->prepare( " WHERE l.first_name LIKE %s OR l.last_name LIKE %s OR l.email LIKE %s", "%$search%", "%$search%", "%$search%" );
    }
    $query .= " ORDER BY b.start_time ASC";
    $bookings = $wpdb->get_results( $query );
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
                        <?php if ($booking->status !== 'cancelled'): ?>
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

    <script>
    jQuery(document).ready(function($) {
        $('.cce-view-questionnaire').on('click', function(e) {
            e.preventDefault();
            const data = $(this).data('data');
            let html = '<ul>';
            for (const key in data) {
                html += `<li><strong>${key}:</strong> ${data[key]}</li>`;
            }
            html += '</ul>';
            $('#cce-questionnaire-content').html(html);
            $('#cce-questionnaire-modal').show();
        });
    });
    </script>
</div>
