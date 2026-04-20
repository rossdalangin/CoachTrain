<div class="wrap cce-admin-wrap">
    <h1>Clients & Offers</h1>
    <p class="description">Craft your High-Ticket "Grand Slam" offers and manage your client portfolio.</p>
    <hr class="wp-header-end">

    <div class="cce-card cce-card-accent" style="margin-top:20px;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
            <span style="font-size: 1.5rem;">💎</span>
            <h3 style="margin:0; color:var(--cce-accent);">Hormozi "Grand Slam" Offer Builder</h3>
        </div>

        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="cce_save_offer">
            <?php wp_nonce_field('cce_save_offer_nonce'); ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                <div class="cce-form-group">
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Offer Title</label>
                    <input type="text" name="title" placeholder="e.g. 90-Day High-Ticket Accelerator" style="width:100%; padding:10px; border-radius:6px; border:1px solid var(--cce-border);" required>
                </div>
                <div class="cce-form-group">
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Price ($)</label>
                    <input type="number" name="price" placeholder="5000" style="width:100%; padding:10px; border-radius:6px; border:1px solid var(--cce-border);" required>
                </div>
                <div class="cce-form-group">
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Payment Type</label>
                    <select name="type" style="width:100%; padding:10px; border-radius:6px; border:1px solid var(--cce-border);">
                        <option value="one-time">One-Time Payment</option>
                        <option value="subscription">Monthly Subscription</option>
                    </select>
                </div>
            </div>
            <div style="margin-top:30px;">
                <button type="submit" class="button cce-btn-primary" style="background: var(--cce-accent) !important; border-color: var(--cce-accent) !important;">Deploy Grand Slam Offer</button>
            </div>
        </form>
    </div>

    <?php
    global $wpdb;
    $offers = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_offers WHERE is_active = 1" );
    ?>

    <div class="cce-card" style="margin-top:40px;">
        <h3>Your Strategic Coaching Portfolio</h3>
        <table class="wp-list-table widefat fixed striped" style="margin-top:15px; border:none; box-shadow:none;">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Type</th>
                    <th style="text-align:right;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($offers)): ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding: 40px 0;">No active offers. Use the builder above to create your first Grand Slam offer.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ( $offers as $offer ): ?>
                        <tr>
                            <td><strong><?php echo esc_html( $offer->title ); ?></strong></td>
                            <td><span style="font-weight:700; color:var(--cce-text-main);">$<?php echo number_format( $offer->price, 2 ); ?></span></td>
                            <td><span class="status-tag"><?php echo esc_html( strtoupper( $offer->type ) ); ?></span></td>
                            <td style="text-align:right;"><span style="color:#10b981; font-weight:600;">● Active</span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
