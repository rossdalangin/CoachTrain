<div class="wrap cce-admin-wrap">
    <h1>Leads Management</h1>
    <hr class="wp-header-end">

    <?php
    global $wpdb;
    $leads = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_leads ORDER BY created_at DESC" );
    ?>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $leads as $lead ): ?>
                <tr>
                    <td><strong><?php echo esc_html( $lead->first_name . ' ' . $lead->last_name ); ?></strong></td>
                    <td><?php echo esc_html( $lead->email ); ?></td>
                    <td><span class="status-tag"><?php echo esc_html( strtoupper( $lead->status ) ); ?></span></td>
                    <td><?php echo esc_html( $lead->created_at ); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
