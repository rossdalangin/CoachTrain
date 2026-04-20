<div class="wrap cce-admin-wrap">
    <h1>Clients & Offers</h1>
    <p class="description">Define your high-ticket coaching programs and products. Use Hormozi's Value Equation to increase your price and perceived value.</p>
    <hr class="wp-header-end">

    <div class="cce-card" style="margin-bottom:20px; border-left:4px solid #0073aa;">
        <h3>💡 Pro Tip: The Value Equation</h3>
        <p style="font-size:12px;">To charge more, increase the <strong>Dream Outcome</strong> and <strong>Likelihood</strong>, while decreasing <strong>Time Delay</strong> and <strong>Effort</strong>. <br>Example: <em>"Scale to $10k/mo (Outcome) in 90 days (Time) with our 1-click templates (Effort)."</em></p>
        <p style="font-size:11px; color:#666;"><strong>What's Next?</strong> Create your "Grand Slam" offer below, then link it to a Funnel step or the Client Portal.</p>
    </div>

    <div class="cce-card" style="margin-bottom:30px; border-left: 4px solid #ff4136;">
        <h3>💎 Hormozi "Grand Slam" Offer Builder</h3>
        <p style="font-size:12px; color:#666;">Craft an offer so good people feel stupid saying no. Use the Value Equation below to maximize the perceived value of your coaching.</p>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="cce_save_offer">
            <?php
            global $wpdb;
            $user_id = get_current_user_id();
            $offers = $wpdb->get_results( $wpdb->prepare( "SELECT id, title FROM {$wpdb->prefix}cce_offers WHERE is_active = 1 AND user_id = %d", $user_id ) );
            wp_nonce_field('cce_save_offer_nonce');
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Offer Title</th>
                    <td>
                        <input type="text" name="title" placeholder="e.g. 90-Day High-Ticket Program" class="regular-text" required>
                        <p class="description">Recommended: Use a results-oriented name (e.g., "The Client Acquisition Machine").</p>
                    </td>
                </tr>
                <?php
                $currency_code = get_option('cce_currency', 'USD');
                $currency_symbols = ['USD' => '$', 'EUR' => '€', 'GBP' => '£', 'CAD' => 'C$', 'AUD' => 'A$'];
                $currency_symbol = $currency_symbols[$currency_code] ?? '$';
                ?>
                <tr>
                    <th scope="row">Price (<?php echo $currency_symbol; ?>)</th>
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
                <tr><th colspan="2" style="padding-bottom:0;"><strong>The Value Equation</strong></th></tr>
                <tr>
                    <th scope="row">Dream Outcome</th>
                    <td>
                        <textarea name="dream_outcome" placeholder="e.g. Add $10k/mo to your coaching business in 90 days." class="large-text" rows="2"></textarea>
                        <p class="description">What is the #1 goal your client wants to achieve? Be specific.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Perceived Likelihood</th>
                    <td>
                        <textarea name="perceived_likelihood" placeholder="e.g. Step-by-step scripts and weekly 1-on-1 accountability." class="large-text" rows="2"></textarea>
                        <p class="description">Why will they believe they can actually achieve the outcome with you?</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Time Delay</th>
                    <td>
                        <textarea name="time_delay" placeholder="e.g. Your first lead in 48 hours, first client in 14 days." class="large-text" rows="2"></textarea>
                        <p class="description">How fast will they see a "Small Win"? Shorter is better.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Effort & Sacrifice</th>
                    <td>
                        <textarea name="effort_sacrifice" placeholder="e.g. No tech skills required, no cold calling, no complex ads." class="large-text" rows="2"></textarea>
                        <p class="description">What "pain" are you removing? What do they NOT have to do anymore?</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Upsell Offer</th>
                    <td>
                        <select name="upsell_offer_id" class="regular-text">
                            <option value="0">None</option>
                            <?php
                            if ($offers) {
                                foreach($offers as $o) echo "<option value='{$o->id}'>" . esc_html($o->title) . "</option>";
                            }
                            ?>
                        </select>
                        <p class="description">Select an offer to show as an upsell after the main purchase.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Downsell Offer</th>
                    <td>
                        <select name="downsell_offer_id" class="regular-text">
                            <option value="0">None</option>
                            <?php
                            if ($offers) {
                                foreach($offers as $o) echo "<option value='{$o->id}'>" . esc_html($o->title) . "</option>";
                            }
                            ?>
                        </select>
                        <p class="description">Select an offer to show if the user declines the upsell.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Order Bump</th>
                    <td>
                        <select name="order_bump_offer_id" class="regular-text">
                            <option value="0">None</option>
                            <?php
                            if ($offers) {
                                foreach($offers as $o) echo "<option value='{$o->id}'>" . esc_html($o->title) . "</option>";
                            }
                            ?>
                        </select>
                        <p class="description">Select a small add-on offer to show on the checkout page.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Create Grand Slam Offer'); ?>
        </form>
    </div>


    <?php
    $active_offers = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_offers WHERE is_active = 1 AND user_id = %d", $user_id ) );
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
            <?php if ($active_offers): foreach ( $active_offers as $offer ): ?>
                <tr id="offer-row-<?php echo $offer->id; ?>"
                    data-title="<?php echo esc_attr($offer->title); ?>"
                    data-price="<?php echo esc_attr($offer->price); ?>"
                    data-type="<?php echo esc_attr($offer->type); ?>"
                    data-dream-outcome="<?php echo esc_attr($offer->dream_outcome); ?>"
                    data-perceived-likelihood="<?php echo esc_attr($offer->perceived_likelihood); ?>"
                    data-time-delay="<?php echo esc_attr($offer->time_delay); ?>"
                    data-effort-sacrifice="<?php echo esc_attr($offer->effort_sacrifice); ?>"
                    data-upsell-id="<?php echo $offer->upsell_offer_id; ?>"
                    data-downsell-id="<?php echo $offer->downsell_offer_id; ?>"
                    data-order-bump-id="<?php echo $offer->order_bump_offer_id; ?>">
                    <?php
                    $currency_code = get_option('cce_currency', 'USD');
                    $currency_symbols = ['USD' => '$', 'EUR' => '€', 'GBP' => '£', 'CAD' => 'C$', 'AUD' => 'A$'];
                    $currency_symbol = $currency_symbols[$currency_code] ?? '$';
                    ?>
                    <td><strong><?php echo esc_html( $offer->title ); ?></strong></td>
                    <td><?php echo $currency_symbol . number_format( $offer->price, 2 ); ?></td>
                    <td><?php echo esc_html( strtoupper( $offer->type ) ); ?></td>
                    <td>
                        <button class="button button-small cce-edit-offer" data-offer-id="<?php echo $offer->id; ?>">Edit</button>
                        <button class="button button-small cce-duplicate-offer" data-offer-id="<?php echo $offer->id; ?>">Duplicate</button>
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
        <div style="background:#fff; margin:5% auto; padding:25px; width:500px; border-radius:12px; position:relative; max-height:80vh; overflow-y:auto;">
            <span class="cce-modal-close" style="position:absolute; right:20px; top:15px; cursor:pointer; font-size:24px;">&times;</span>
            <h2>Edit Offer</h2>
            <form id="cce-edit-offer-form">
                <input type="hidden" name="id" id="edit-offer-id">
                <p><label>Offer Title</label><br><input type="text" id="edit-offer-title" class="widefat" required></p>
                <p><label>Price (<?php echo $currency_symbol; ?>)</label><br><input type="number" id="edit-offer-price" class="widefat" required></p>
                <p><label>Type</label><br>
                    <select id="edit-offer-type" class="widefat">
                        <option value="one-time">One-Time</option>
                        <option value="subscription">Subscription</option>
                    </select>
                </p>
                <p><label>Dream Outcome</label><br><textarea id="edit-offer-dream-outcome" class="widefat"></textarea></p>
                <p><label>Perceived Likelihood</label><br><textarea id="edit-offer-perceived-likelihood" class="widefat"></textarea></p>
                <p><label>Time Delay</label><br><textarea id="edit-offer-time-delay" class="widefat"></textarea></p>
                <p><label>Effort & Sacrifice</label><br><textarea id="edit-offer-effort-sacrifice" class="widefat"></textarea></p>
                <p><label>Upsell Offer</label><br>
                    <select id="edit-offer-upsell-id" class="widefat">
                        <option value="0">None</option>
                        <?php foreach($active_offers as $o) echo "<option value='{$o->id}'>{$o->title}</option>"; ?>
                    </select>
                </p>
                <p><label>Downsell Offer</label><br>
                    <select id="edit-offer-downsell-id" class="widefat">
                        <option value="0">None</option>
                        <?php foreach($active_offers as $o) echo "<option value='{$o->id}'>{$o->title}</option>"; ?>
                    </select>
                </p>
                <p><label>Order Bump Offer</label><br>
                    <select id="edit-offer-order-bump-id" class="widefat">
                        <option value="0">None</option>
                        <?php foreach($active_offers as $o) echo "<option value='{$o->id}'>{$o->title}</option>"; ?>
                    </select>
                </p>
                <button type="submit" class="button button-primary">Update Offer</button>
            </form>
        </div>
    </div>
</div>
