<div class="wrap cce-admin-wrap">
    <h1>Payment History</h1>
    <hr class="wp-header-end">

    <?php
    global $wpdb;
    $payments = $wpdb->get_results( "
        SELECT p.*, CONCAT(l.first_name, ' ', l.last_name) as lead_name, o.title as offer_title
        FROM {$wpdb->prefix}cce_payments p
        LEFT JOIN {$wpdb->prefix}cce_leads l ON p.lead_id = l.id
        LEFT JOIN {$wpdb->prefix}cce_offers o ON p.offer_id = o.id
        ORDER BY p.created_at DESC
    " );
    ?>

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
                <?php if ($payments): foreach ($payments as $p): ?>
                <tr>
                    <td><strong><?php echo esc_html($p->lead_name ?: 'Unknown'); ?></strong></td>
                    <td><?php echo esc_html($p->offer_title ?: 'N/A'); ?></td>
                    <td><?php echo number_format($p->amount, 2) . ' ' . strtoupper($p->currency ?? 'USD'); ?></td>
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
</div>
