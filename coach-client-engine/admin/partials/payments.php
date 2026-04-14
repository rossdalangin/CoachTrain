<div class="wrap cce-admin-wrap">
    <h1>Payment History</h1>
    <hr class="wp-header-end">

    <div class="cce-card" style="margin-bottom:20px;">
        <h3>Record Manual Payment</h3>
        <form id="cce-manual-payment-form">
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <select name="lead_id" required>
                    <option value="">Select Customer...</option>
                    <?php
                    global $wpdb;
                    $leads = $wpdb->get_results("SELECT id, first_name, last_name FROM {$wpdb->prefix}cce_leads");
                    foreach($leads as $l) echo "<option value='{$l->id}'>{$l->first_name} {$l->last_name}</option>";
                    ?>
                </select>
                <select name="offer_id" required>
                    <option value="">Select Offer...</option>
                    <?php
                    $offers = $wpdb->get_results("SELECT id, title, price FROM {$wpdb->prefix}cce_offers WHERE is_active = 1");
                    foreach($offers as $o) echo "<option value='{$o->id}' data-price='{$o->price}'>{$o->title}</option>";
                    ?>
                </select>
                <input type="number" name="amount" step="0.01" placeholder="Amount" required>
                <button type="submit" class="button button-primary">Record Payment</button>
            </div>
        </form>
    </div>

    <?php
    $payments = $wpdb->get_results( "
        SELECT p.*, CONCAT(l.first_name, ' ', l.last_name) as lead_name, o.title as offer_title
        FROM {$wpdb->prefix}cce_payments p
        LEFT JOIN {$wpdb->prefix}cce_leads l ON p.lead_id = l.id
        LEFT JOIN {$wpdb->prefix}cce_offers o ON p.offer_id = o.id
        ORDER BY p.created_at DESC
    " );
    ?>

    <div style="display:flex; justify-content:flex-end; margin-bottom:20px;">
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="cce_export_payments">
            <?php wp_nonce_field('cce_export_payments_nonce'); ?>
            <button type="submit" class="button">Export to CSV</button>
        </form>
    </div>

    <div class="cce-card">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Offer</th>
                    <th>Amount</th>
                    <th>Gateway</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($payments):
                $currency_code = get_option('cce_currency', 'USD');
                $currency_symbols = ['USD' => '$', 'EUR' => '€', 'GBP' => '£', 'CAD' => 'C$', 'AUD' => 'A$'];
                $currency_symbol = $currency_symbols[$currency_code] ?? '$';
                foreach ($payments as $p): ?>
                <tr>
                    <td><strong><?php echo esc_html($p->lead_name ?: 'Unknown'); ?></strong></td>
                    <td><?php echo esc_html($p->offer_title ?: 'N/A'); ?></td>
                    <td><?php echo $currency_symbol . number_format($p->amount, 2); ?></td>
                    <td><?php echo esc_html(strtoupper($p->gateway)); ?></td>
                    <td><span class="status-tag status-<?php echo esc_attr($p->status); ?>"><?php echo esc_html(strtoupper($p->status)); ?></span></td>
                    <td><?php echo esc_html($p->created_at); ?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="6">No payments recorded yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('[name="offer_id"]').on('change', function() {
            const price = $(this).find(':selected').data('price');
            if (price) $('[name="amount"]').val(price);
        });

        $('#cce-manual-payment-form').on('submit', function(e) {
            e.preventDefault();
            const data = {
                lead_id: $(this).find('[name="lead_id"]').val(),
                offer_id: $(this).find('[name="offer_id"]').val(),
                amount: $(this).find('[name="amount"]').val()
            };
            if (typeof cceApi === 'function') {
                cceApi('payments', 'POST', JSON.stringify(data), () => location.reload());
            } else {
                $.ajax({
                    url: cceAdmin.restUrl + 'payments',
                    method: 'POST',
                    beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', cceAdmin.nonce); },
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    success: function() { location.reload(); }
                });
            }
        });
    });
    </script>
</div>
