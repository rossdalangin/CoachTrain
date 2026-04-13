<div class="wrap cce-admin-wrap">
    <h1>Plugin Settings</h1>

    <div class="cce-settings-nav">
        <h2 class="nav-tab-wrapper">
            <a href="#general" class="nav-tab nav-tab-active">General</a>
            <a href="#payments" class="nav-tab">Payments</a>
            <a href="#branding" class="nav-tab">Branding</a>
            <a href="#status" class="nav-tab">System Status</a>
        </h2>
    </div>

    <form id="cce-settings-form" style="margin-top:20px;">
        <div id="section-general" class="cce-settings-section">
            <table class="form-table">
                <tr>
                    <th scope="row">License Key</th>
                    <td><input type="text" name="license_key" value="<?php echo esc_attr( get_option('cce_license_key') ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Coach Name</th>
                    <td><input type="text" name="coach_name" value="<?php echo esc_attr( get_option('cce_coach_name') ); ?>" class="regular-text"></td>
                </tr>
            </table>
        </div>

        <div id="section-payments" class="cce-settings-section" style="display:none;">
            <table class="form-table">
                <tr>
                    <th scope="row">Stripe Secret Key</th>
                    <td><input type="password" name="stripe_api_key" value="<?php echo esc_attr( get_option('cce_stripe_api_key') ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Stripe Webhook Secret</th>
                    <td><input type="password" name="stripe_webhook_secret" value="<?php echo esc_attr( get_option('cce_stripe_webhook_secret') ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">PayPal Client ID</th>
                    <td><input type="text" name="paypal_client_id" value="<?php echo esc_attr( get_option('cce_paypal_client_id') ); ?>" class="regular-text"></td>
                </tr>
            </table>
        </div>

        <div id="section-branding" class="cce-settings-section" style="display:none;">
            <table class="form-table">
                <tr>
                    <th scope="row">Primary Color</th>
                    <td><input type="color" name="primary_color" value="<?php echo esc_attr( get_option('cce_primary_color', '#0073aa') ); ?>"></td>
                </tr>
            </table>
        </div>

        <div id="section-status" class="cce-settings-section" style="display:none;">
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

        <p class="submit">
            <button type="submit" class="button button-primary">Save Settings</button>
        </p>
    </form>

    <script>
    jQuery(document).ready(function($) {
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.cce-settings-section').hide();
            $('#section-' + $(this).attr('href').substring(1)).show();
        });

        $('#cce-settings-form').on('submit', function(e) {
            e.preventDefault();
            const data = {};
            $(this).serializeArray().forEach(item => data[item.name] = item.value);

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
