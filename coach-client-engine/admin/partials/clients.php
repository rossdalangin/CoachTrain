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
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($offers): foreach ( $offers as $offer ): ?>
                <tr id="offer-row-<?php echo $offer->id; ?>"
                    data-title="<?php echo esc_attr($offer->title); ?>"
                    data-price="<?php echo esc_attr($offer->price); ?>"
                    data-type="<?php echo esc_attr($offer->type); ?>">
                    <td><strong><?php echo esc_html( $offer->title ); ?></strong></td>
                    <td>$<?php echo number_format( $offer->price, 2 ); ?></td>
                    <td><?php echo esc_html( strtoupper( $offer->type ) ); ?></td>
                    <td>
                        <button class="button button-small cce-edit-offer" data-offer-id="<?php echo $offer->id; ?>">Edit</button>
                        <button class="button button-link-delete cce-delete-offer" data-offer-id="<?php echo $offer->id; ?>" style="color:#d63638;">Delete</button>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="4">No active offers.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Edit Offer Modal -->
    <div id="cce-edit-offer-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div style="background:#fff; margin:10% auto; padding:25px; width:400px; border-radius:12px; position:relative;">
            <span class="cce-modal-close" style="position:absolute; right:20px; top:15px; cursor:pointer; font-size:24px;">&times;</span>
            <h2>Edit Offer</h2>
            <form id="cce-edit-offer-form">
                <input type="hidden" name="id" id="edit-offer-id">
                <p><label>Offer Title</label><br><input type="text" name="title" id="edit-offer-title" class="widefat" required></p>
                <p><label>Price ($)</label><br><input type="number" name="price" id="edit-offer-price" class="widefat" required></p>
                <p><label>Type</label><br>
                    <select name="type" id="edit-offer-type" class="widefat">
                        <option value="one-time">One-Time</option>
                        <option value="subscription">Subscription</option>
                    </select>
                </p>
                <button type="submit" class="button button-primary">Update Offer</button>
            </form>
        </div>
    </div>
</div>
