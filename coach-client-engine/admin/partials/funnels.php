<div class="wrap cce-admin-wrap">
    <h1>Funnel Engine</h1>
    <hr class="wp-header-end">

    <?php
    global $wpdb;
    $funnels = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_funnels" );
    $offers = $wpdb->get_results( "SELECT id, title FROM {$wpdb->prefix}cce_offers WHERE is_active = 1" );
    ?>

    <div class="cce-card">
        <h3>Your Funnels</h3>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Shortcode</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($funnels): foreach ( $funnels as $funnel ): ?>
                    <tr>
                        <td><strong><?php echo esc_html( $funnel->title ); ?></strong></td>
                        <td><?php echo esc_html( strtoupper( $funnel->type ) ); ?></td>
                        <td><?php echo esc_html( strtoupper( $funnel->status ) ); ?></td>
                        <td>
                            <code>[cce_funnel id="<?php echo $funnel->id; ?>"]</code>
                            <button class="button button-small cce-copy-shortcode" data-shortcode='[cce_funnel id="<?php echo $funnel->id; ?>"]'>Copy</button>
                        </td>
                        <td>
                            <a href="#" class="button cce-view-steps" data-funnel-id="<?php echo $funnel->id; ?>">View Steps</a>
                            <button class="button button-link-delete cce-delete-funnel" data-funnel-id="<?php echo $funnel->id; ?>" style="color:#d63638;">Delete</button>
                        </td>
                    </tr>
                    <tr id="funnel-steps-<?php echo $funnel->id; ?>" style="display:none;">
                        <td colspan="5" style="background:#f9f9f9; padding:15px;">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <h4>Steps in this funnel:</h4>
                                <button class="button button-small cce-add-step-btn" data-funnel-id="<?php echo $funnel->id; ?>">+ Add Step</button>
                            </div>
                            <div class="steps-container-<?php echo $funnel->id; ?>" style="margin-top:10px;">
                                <em>Loading steps...</em>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="5">No funnels found. Create one from a template below!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="cce-card" style="margin-top:20px;">
        <h3>Pre-built Templates</h3>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
            <div class="cce-card" style="border:1px solid #ddd;">
                <h4>Lead Magnet Funnel</h4>
                <p>Visitor -> Opt-in -> Thank You</p>
                <button class="button button-primary cce-use-template" data-template="lead_magnet">Use Template</button>
            </div>
            <div class="cce-card" style="border:1px solid #ddd;">
                <h4>Consultation Funnel</h4>
                <p>Visitor -> Opt-in -> Booking -> Thank You</p>
                <button class="button button-primary cce-use-template" data-template="consultation">Use Template</button>
            </div>
        </div>
    </div>

    <!-- Add Step Modal -->
    <div id="cce-add-step-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div style="background:#fff; margin:10% auto; padding:25px; width:400px; border-radius:12px; position:relative;">
            <span class="cce-modal-close" style="position:absolute; right:20px; top:15px; cursor:pointer; font-size:24px;">&times;</span>
            <h2>Add Funnel Step</h2>
            <form id="cce-add-step-form">
                <input type="hidden" id="add-step-funnel-id">
                <p><label>Step Title</label><br><input type="text" id="add-step-title" class="widefat" required></p>
                <p><label>Step Type</label><br>
                    <select id="add-step-type" class="widefat">
                        <option value="optin">Opt-in Form</option>
                        <option value="booking">Booking/Scheduling</option>
                        <option value="checkout">Checkout/Payment</option>
                        <option value="thank_you">Thank You Page</option>
                    </select>
                </p>
                <button type="submit" class="button button-primary">Add Step</button>
            </form>
        </div>
    </div>

    <!-- Step Config Modal -->
    <div id="cce-step-config-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div style="background:#fff; margin:10% auto; padding:25px; width:400px; border-radius:12px; position:relative;">
            <span class="cce-modal-close" style="position:absolute; right:20px; top:15px; cursor:pointer; font-size:24px;">&times;</span>
            <h2>Configure Step</h2>
            <form id="cce-step-config-form">
                <input type="hidden" id="config-funnel-id">
                <input type="hidden" id="config-step-idx">

                <div id="config-offer-selector" style="display:none;">
                    <p><label>Link to Offer</label><br>
                    <select id="config-offer-id" class="widefat">
                        <option value="">Select Offer...</option>
                        <?php foreach($offers as $o) echo "<option value='{$o->id}'>{$o->title}</option>"; ?>
                    </select></p>
                </div>

                <div id="config-thankyou-selector" style="display:none;">
                    <p><label>Custom Success Message</label><br>
                    <textarea id="config-success-message" class="widefat" rows="3"></textarea></p>
                    <p><label>OR Redirect URL</label><br>
                    <input type="url" id="config-redirect-url" class="widefat" placeholder="https://..."></p>
                </div>

                <button type="submit" class="button button-primary">Save Config</button>
            </form>
        </div>
    </div>
</div>
