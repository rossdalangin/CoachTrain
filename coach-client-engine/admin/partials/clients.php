<div class="wrap cce-admin-wrap">
    <h1>Clients & Offers</h1>
    <hr class="wp-header-end">

    <div class="cce-card" style="margin-bottom:30px; border-left: 4px solid #ff4136;">
        <h3>💎 Hormozi "Grand Slam" Offer Builder</h3>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="cce_save_offer">
            <?php wp_nonce_field('cce_save_offer_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Offer Title</th>
                    <td><input type="text" name="title" placeholder="e.g. 90-Day High-Ticket Program" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row">Price ($)</th>
                    <td><input type="number" name="price" placeholder="5000" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row">Type</th>
                    <td>
                        <select name="type">
                            <option value="one-time">One-Time</option>
                            <option value="subscription">Subscription</option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button('Create Grand Slam Offer'); ?>
        </form>
    </div>

    <?php
    global $wpdb;
    $offers = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_offers WHERE is_active = 1" );
    ?>

    <h3>Active Coaching Offers</h3>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Title</th>
                <th>Price</th>
                <th>Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $offers as $offer ): ?>
                <tr>
                    <td><strong><?php echo esc_html( $offer->title ); ?></strong></td>
                    <td>$<?php echo number_format( $offer->price, 2 ); ?></td>
                    <td><?php echo esc_html( strtoupper( $offer->type ) ); ?></td>
                    <td>Active</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
