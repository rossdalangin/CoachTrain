<div class="wrap cce-admin-wrap">
    <h1>Funnel Engine</h1>
    <hr class="wp-header-end">

    <?php
    global $wpdb;
    $funnels = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_funnels" );
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
                            <h4>Steps in this funnel:</h4>
                            <div class="steps-container-<?php echo $funnel->id; ?>">
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

    <script>
    jQuery(document).ready(function($) {
        $('.cce-use-template').on('click', function() {
            const templateId = $(this).data('template');
            $.ajax({
                url: cceAdmin.restUrl + 'funnels/create-from-template',
                method: 'POST',
                beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', cceAdmin.nonce); },
                data: { template_id: templateId },
                success: function(res) { if(res.success) location.reload(); }
            });
        });

        $('.cce-delete-funnel').on('click', function() {
            if(!confirm('Are you sure you want to delete this funnel?')) return;
            const funnelId = $(this).data('funnel-id');
            $.ajax({
                url: cceAdmin.restUrl + 'funnels/' + funnelId,
                method: 'DELETE',
                beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', cceAdmin.nonce); },
                success: function(res) { if(res.success) location.reload(); }
            });
        });

        $('.cce-copy-shortcode').on('click', function() {
            const text = $(this).data('shortcode');
            navigator.clipboard.writeText(text).then(() => {
                const originalText = $(this).text();
                $(this).text('Copied!');
                setTimeout(() => $(this).text(originalText), 2000);
            });
        });

        $('.cce-view-steps').on('click', function(e) {
            e.preventDefault();
            const funnelId = $(this).data('funnel-id');
            const $row = $('#funnel-steps-' + funnelId);
            const $container = $('.steps-container-' + funnelId);

            if ($row.is(':visible')) {
                $row.hide();
                return;
            }

            $row.show();

            $.ajax({
                url: cceAdmin.restUrl + 'funnels/' + funnelId + '/steps',
                method: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', cceAdmin.nonce);
                },
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        let html = '<ol>';
                        response.data.forEach(step => {
                            html += `<li><strong>${step.title}</strong> (${step.step_type})</li>`;
                        });
                        html += '</ol>';
                        $container.html(html);
                    } else {
                        $container.html('<p>No steps configured for this funnel yet.</p>');
                    }
                }
            });
        });
    });
    </script>
</div>
