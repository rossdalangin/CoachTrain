<div class="wrap cce-admin-wrap">
    <h1>Plugin Settings</h1>

    <div class="cce-settings-nav">
        <h2 class="nav-tab-wrapper">
            <a href="#general" class="nav-tab nav-tab-active">General</a>
            <a href="#payments" class="nav-tab">Payments</a>
            <a href="#branding" class="nav-tab">Branding</a>
            <a href="#shortcodes" class="nav-tab">Shortcodes</a>
            <a href="#licensing" class="nav-tab">Licensing</a>
            <a href="#status" class="nav-tab">System Status</a>
        </h2>
    </div>

    <div id="cce-settings-sections" style="margin-top:20px;">
        <form id="cce-settings-form">
        <div id="section-general" class="cce-settings-section">
            <table class="form-table">
                <tr>
                    <th scope="row">License Key</th>
                    <td><input type="text" name="license_key" value="<?php echo esc_attr( get_user_meta( get_current_user_id(), 'cce_license_key', true ) ?: get_option('cce_license_key') ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Coach Name</th>
                    <td><input type="text" name="coach_name" value="<?php echo esc_attr( get_user_meta( get_current_user_id(), 'cce_coach_name', true ) ?: get_option('cce_coach_name') ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Default Currency</th>
                    <td>
                        <select name="default_currency">
                            <?php
                            $currencies = ['USD' => '$', 'EUR' => '€', 'GBP' => '£', 'CAD' => 'C$', 'AUD' => 'A$'];
                            $current = get_user_meta( get_current_user_id(), 'cce_currency', true ) ?: get_option('cce_currency', 'USD');
                            foreach ($currencies as $code => $symbol) {
                                printf('<option value="%s" %s>%s (%s)</option>', $code, selected($current, $code, false), $code, $symbol);
                            }
                            ?>
                        </select>
                    </td>
                </tr>
            </table>
        </div>

        <div id="section-payments" class="cce-settings-section" style="display:none;">
            <table class="form-table">
                <tr>
                    <th scope="row">Stripe Secret Key</th>
                    <td><input type="password" name="stripe_api_key" value="<?php echo esc_attr( get_user_meta( get_current_user_id(), 'cce_stripe_api_key', true ) ?: get_option('cce_stripe_api_key') ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Stripe Webhook Secret</th>
                    <td><input type="password" name="stripe_webhook_secret" value="<?php echo esc_attr( get_user_meta( get_current_user_id(), 'cce_stripe_webhook_secret', true ) ?: get_option('cce_stripe_webhook_secret') ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">PayPal Client ID</th>
                    <td><input type="text" name="paypal_client_id" value="<?php echo esc_attr( get_user_meta( get_current_user_id(), 'cce_paypal_client_id', true ) ?: get_option('cce_paypal_client_id') ); ?>" class="regular-text"></td>
                </tr>
            </table>
        </div>

        <div id="section-branding" class="cce-settings-section" style="display:none;">
            <table class="form-table">
                <tr>
                    <th scope="row">Primary Color</th>
                    <td>
                        <input type="color" id="cce-primary-color-input" name="primary_color" value="<?php echo esc_attr( get_user_meta( get_current_user_id(), 'cce_primary_color', true ) ?: get_option('cce_primary_color', '#0073aa') ); ?>">
                        <div id="cce-branding-preview" style="margin-top:10px; padding:15px; border:1px solid #ddd; border-radius:8px; display:inline-block;">
                            <span style="font-size:11px; color:#888; display:block; margin-bottom:5px;">Live Preview</span>
                            <button type="button" class="button button-primary" id="cce-preview-button">Sample Button</button>
                            <div style="margin-top:10px; width:100px; height:4px; border-radius:2px;" id="cce-preview-accent"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Test Mode</th>
                    <td>
                        <label>
                            <input type="checkbox" name="test_mode" value="1" <?php checked( get_user_meta( get_current_user_id(), 'cce_test_mode', true ) ); ?>>
                            Enable Payment Simulation (Bypasses real Stripe/PayPal for testing)
                        </label>
                    </td>
                </tr>
            </table>
        </div>

        <div id="section-shortcodes" class="cce-settings-section" style="display:none;">
            <div class="cce-card">
                <h3>Shortcode Reference</h3>
                <p class="description">Copy and paste these shortcodes onto any WordPress page or post to display the Engine's features.</p>

                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 200px;">Shortcode</th>
                            <th>Description</th>
                            <th>Example / Attributes</th>
                            <th style="width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>[cce_lead_capture]</code></td>
                            <td>Displays a lead magnet opt-in form.</td>
                            <td><code>type="inline|popup|sticky"</code>, <code>title="My Title"</code>, <code>redirect="url"</code></td>
                            <td><button type="button" class="button button-small cce-copy-shortcode" data-shortcode='[cce_lead_capture title="Get My Guide" type="inline"]'>Copy</button></td>
                        </tr>
                        <tr>
                            <td><code>[cce_booking]</code></td>
                            <td>Displays the consultation booking calendar.</td>
                            <td><code>title="Book Your Call"</code>, <code>redirect="url"</code></td>
                            <td><button type="button" class="button button-small cce-copy-shortcode" data-shortcode='[cce_booking title="Schedule Your Session"]'>Copy</button></td>
                        </tr>
                        <tr>
                            <td><code>[cce_funnel]</code></td>
                            <td>Renders a multi-step conversion funnel.</td>
                            <td><code>id="FUNNEL_ID"</code> (Find IDs in the Funnel Engine tab)</td>
                            <td><button type="button" class="button button-small cce-copy-shortcode" data-shortcode='[cce_funnel id="1"]'>Copy</button></td>
                        </tr>
                        <tr>
                            <td><code>[cce_client_portal]</code></td>
                            <td>Displays the secure private client dashboard.</td>
                            <td>No attributes required. Requires lead token cookie to view content.</td>
                            <td><button type="button" class="button button-small cce-copy-shortcode" data-shortcode='[cce_client_portal]'>Copy</button></td>
                        </tr>
                        <tr>
                            <td><code>[cce_testimonials]</code></td>
                            <td>Displays active social proof elements.</td>
                            <td><code>type="testimonial|case_study"</code></td>
                            <td><button type="button" class="button button-small cce-copy-shortcode" data-shortcode='[cce_testimonials type="testimonial"]'>Copy</button></td>
                        </tr>
                        <tr>
                            <td><code>[cce_checkout]</code></td>
                            <td>Displays a payment form for a specific offer.</td>
                            <td><code>offer_id="OFFER_ID"</code> (Find IDs in the Clients & Offers tab)</td>
                            <td><button type="button" class="button button-small cce-copy-shortcode" data-shortcode='[cce_checkout offer_id="1"]'>Copy</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="section-licensing" class="cce-settings-section" style="display:none;">
            <?php
            $license_manager = new CCE_License_Manager();
            $license_res = $license_manager->get_license_status( new WP_REST_Request() );
            $l_status = is_wp_error($license_res) ? [] : $license_res->get_data()['data'];
            ?>
            <div class="cce-card" style="margin-bottom:20px; border-left:4px solid <?php echo $l_status['is_pro'] ? '#00a32a' : '#ffb700'; ?>;">
                <h3>Current License Status</h3>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <p style="font-size:18px; font-weight:bold; margin-bottom:5px;">
                            <?php echo $l_status['is_pro'] ? '💎 PLATINUM / PRO ACTIVE' : '⚠️ STANDARD LICENSE'; ?>
                        </p>
                        <p style="font-size:12px; color:#666;">
                            <?php if($l_status['is_pro']): ?>
                                Your key: <code><?php echo $l_status['license_key']; ?></code> | Type: Perpetual Pro
                            <?php else: ?>
                                Basic features enabled. Enter a PRO key to unlock automation and analytics.
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php if(!$l_status['is_pro']): ?>
                        <a href="https://coachclientengine.com" target="_blank" class="button button-primary">Get Pro Key</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="cce-card">
                <h3>How Licensing Works</h3>
                <p>To unlock the full potential of the Coach Client Engine (PRO), you need a valid license key. License keys are tied to your account and can be managed from our official portal.</p>
                <ul style="list-style:disc; padding-left:20px;">
                    <li><strong>PRO Features:</strong> Advanced Automation, Strategic Analytics, and Priority Support.</li>
                    <li><strong>Getting a Key:</strong> Visit <a href="https://coachclientengine.com" target="_blank">coachclientengine.com</a> to purchase a license.</li>
                    <li><strong>Activation:</strong> Enter your key in the "General" tab and save settings.</li>
                </ul>
                <div class="notice notice-info inline"><p>Looking to sell this plugin? Contact us for white-label licensing options.</p></div>

                <div style="margin-top:20px; background:#f9f9f9; padding:15px; border:1px solid #ddd; border-radius:5px;">
                    <h4>🚀 Reseller & Distribution Guide (Owner Only)</h4>
                    <p>If you plan to sell the Coach Client Engine as a SaaS or a stand-alone product, consider the following:</p>
                    <ol>
                        <li><strong>Multi-Tenancy:</strong> The engine is already built to isolate data by <code>user_id</code>. Each WordPress user has their own leads, funnels, and settings.</li>
                        <li><strong>API Key Management:</strong> Encourage users to enter their own Stripe/PayPal keys in the "Payments" tab.</li>
                        <li><strong>Custom Branding:</strong> Use the "Branding" tab to let users change the primary color of their forms and portal.</li>
                        <li><strong>License Verification:</strong> Use the standalone <code>cce-license-issuer</code> tool (located in the plugin directory) to generate valid keys for your buyers.</li>
                        <li><strong>Checksum Security:</strong> The system uses a secret salt to verify keys offline. Do not share your <code>license-issuer.php</code> file with anyone.</li>
                    </ol>
                </div>
            </div>
        </div>

        </form>

        <div id="section-status" class="cce-settings-section" style="display:none;">
            <div class="cce-card" style="border-left: 4px solid #673ab7; margin-bottom: 20px;">
                <h3>🛠 Repair & Optimization</h3>
                <p>Run these tools if you encounter issues with database tables or need to force an update of the Engine's core structures.</p>
                <form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
                    <input type="hidden" name="action" value="cce_repair_db">
                    <?php wp_nonce_field('cce_repair_db_nonce'); ?>
                    <button type="submit" class="button button-secondary">Force Database Repair</button>
                </form>
            </div>

            <div class="cce-card" style="border-left: 4px solid #00a32a; margin-bottom: 20px;">
                <h3>🚀 One-Click Demo Mode</h3>
                <p>Want to see how the Coach Client Engine looks with a full pipeline of leads, bookings, and active clients? Click the button below to populate all 15+ database tables with strategically aligned sample data.</p>
                <button type="button" id="cce-generate-sample-data" class="button button-primary button-hero">Populate Sample Data</button>
                <p style="font-size:11px; color:#888; margin-top:10px;">Note: This will add new records to your database. It will not delete your existing data.</p>
            </div>

            <div class="cce-card">
                <h3>Diagnostic Check</h3>
                <table class="wp-list-table widefat fixed striped">
                    <thead><tr><th>Component</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php
                        global $wpdb;
                        $tables = ['leads', 'bookings', 'offers', 'funnels', 'payments', 'crm_stages', 'activity_log', 'tasks', 'testimonials', 'automation_rules', 'resources', 'email_templates'];
                        foreach($tables as $t) {
                            $check = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}cce_{$t}'");
                            echo "<tr><td>Database Table: <code>cce_{$t}</code></td><td>" . ($check ? '✅ OK' : '❌ Missing') . "</td></tr>";
                        }
                        ?>
                        <tr><td>Plugin Version</td><td><code><?php echo CCE_VERSION; ?></code></td></tr>
                        <tr><td>PHP Version</td><td><code><?php echo phpversion(); ?></code></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <p class="submit" id="cce-settings-submit-container">
            <button type="submit" form="cce-settings-form" class="button button-primary">Save Settings</button>
        </p>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.cce-settings-section').hide();

            var target = $(this).attr('href').substring(1);
            $('#section-' + target).show();

            if (target === 'status' || target === 'shortcodes' || target === 'licensing') {
                $('#cce-settings-submit-container').hide();
            } else {
                $('#cce-settings-submit-container').show();
            }
        });

        $('#cce-generate-sample-data').on('click', function() {
            const $btn = $(this);
            $btn.prop('disabled', true).text('Generating...');

            $.ajax({
                url: cceAdmin.restUrl + 'maintenance/sample-data',
                method: 'POST',
                beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', cceAdmin.nonce); },
                success: function(res) {
                    if (res.success) {
                        alert(res.message);
                        window.location.reload();
                    }
                },
                error: function() {
                    alert('An error occurred while generating sample data.');
                    $btn.prop('disabled', false).text('Populate Sample Data');
                }
            });
        });

        $('#cce-primary-color-input').on('input', function() {
            const color = $(this).val();
            $('#cce-preview-button').css('background-color', color);
            $('#cce-preview-accent').css('background-color', color);
        }).trigger('input');

        $('#cce-settings-form').on('submit', function(e) {
            e.preventDefault();
            const data = {};
            $(this).serializeArray().forEach(item => data[item.name] = item.value);
            data.test_mode = $('[name="test_mode"]').is(':checked') ? 1 : 0;

            $.ajax({
                url: cceAdmin.restUrl + 'settings',
                method: 'POST',
                beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', cceAdmin.nonce); },
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(res) {
                    if (res.success) alert('Settings saved successfully!');
                }
            });
        });
    });
    </script>
</div>
