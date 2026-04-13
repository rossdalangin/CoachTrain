<div class="wrap cce-admin-wrap">
    <h1>Social Proof & Testimonials</h1>
    <hr class="wp-header-end">

    <div class="cce-card" style="margin-bottom: 20px;">
        <h3>Add New Testimonial</h3>
        <form id="cce-add-testimonial-form">
            <div style="margin-bottom:15px;">
                <label>Client Name</label><br>
                <input type="text" name="client_name" class="regular-text" required>
            </div>
            <div style="margin-bottom:15px;">
                <label>Testimonial Content</label><br>
                <textarea name="content" rows="4" style="width:100%; max-width:500px;" required></textarea>
            </div>
            <div style="margin-bottom:15px;">
                <label>Rating (1-5)</label><br>
                <input type="number" name="rating" min="1" max="5" value="5" required>
            </div>
            <button type="submit" class="button button-primary">Save Testimonial</button>
        </form>
    </div>

    <div class="cce-card">
        <h3>Active Testimonials</h3>
        <div id="cce-testimonials-list" style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
            <?php
            global $wpdb;
            $testimonials = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_testimonials WHERE status = 'active' ORDER BY created_at DESC" );
            if ( $testimonials ):
                foreach ( $testimonials as $t ):
                ?>
                <div class="cce-card" style="border-top:none; background:#f9f9f9; position:relative;">
                    <button class="button button-link-delete cce-delete-testimonial" data-id="<?php echo $t->id; ?>" style="position:absolute; right:10px; top:10px; color:#d63638;">×</button>
                    <p>"<?php echo esc_html( $t->content ); ?>"</p>
                    <strong>- <?php echo esc_html( $t->client_name ); ?></strong>
                    <div style="color:#ffb700; margin-top:5px;">
                        <?php echo str_repeat('★', $t->rating); ?>
                    </div>
                </div>
                <?php
                endforeach;
            else:
                echo "<p>No testimonials found. Add your first one above!</p>";
            endif;
            ?>
        </div>
    </div>
</div>
