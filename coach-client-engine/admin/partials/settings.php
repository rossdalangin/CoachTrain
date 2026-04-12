<div class="wrap cce-admin-wrap">
    <h1>Plugin Settings</h1>

    <form method="post" action="options.php">
        <?php
        settings_fields( 'cce_settings_group' );
        do_settings_sections( 'cce-settings' );
        ?>

        <h2 class="nav-tab-wrapper">
            <a href="#general" class="nav-tab nav-tab-active">General</a>
            <a href="#payments" class="nav-tab">Payments</a>
            <a href="#branding" class="nav-tab">Branding</a>
        </h2>

        <div id="cce-settings-sections">
            <table class="form-table">
                <tr>
                    <th scope="row">License Key</th>
                    <td><input type="text" name="cce_license_key" value="<?php echo esc_attr( get_option('cce_license_key') ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Coach Name</th>
                    <td><input type="text" name="cce_coach_name" value="<?php echo esc_attr( get_option('cce_coach_name') ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Stripe Secret Key</th>
                    <td><input type="password" name="cce_stripe_api_key" value="<?php echo esc_attr( get_option('cce_stripe_api_key') ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">PayPal Client ID</th>
                    <td><input type="text" name="cce_paypal_client_id" value="<?php echo esc_attr( get_option('cce_paypal_client_id') ); ?>" class="regular-text"></td>
                </tr>
            </table>
        </div>

        <?php submit_button(); ?>
    </form>
</div>
