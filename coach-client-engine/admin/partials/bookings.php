<div class="wrap cce-admin-wrap">
    <h1>Scheduled Consultations</h1>
    <hr class="wp-header-end">

    <div class="cce-card" style="margin-bottom: 20px;">
        <h3>Add New Booking</h3>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="cce_save_booking">
            <?php wp_nonce_field('cce_save_booking_nonce'); ?>
            <div style="display:flex; gap:10px; flex-wrap: wrap;">
                <select name="lead_id" required>
                    <option value="">Select Lead...</option>
                    <?php
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
    global $wpdb;
    $bookings = $wpdb->get_results( "
        SELECT b.*, CONCAT(l.first_name, ' ', l.last_name) as lead_name
        FROM {$wpdb->prefix}cce_bookings b
        LEFT JOIN {$wpdb->prefix}cce_leads l ON b.lead_id = l.id
        ORDER BY b.start_time ASC
    " );
    ?>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Client</th>
                <th>Time</th>
                <th>Timezone</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $bookings as $booking ): ?>
                <tr>
                    <td><strong><?php echo esc_html( $booking->lead_name ); ?></strong></td>
                    <td><?php echo esc_html( $booking->start_time ); ?></td>
                    <td><?php echo esc_html( $booking->timezone ); ?></td>
                    <td><span class="status-tag"><?php echo esc_html( strtoupper( $booking->status ) ); ?></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
