<div class="wrap cce-admin-wrap">
    <h1>Social Proof & Testimonials</h1>
    <hr class="wp-header-end">

    <div class="cce-card" style="margin-bottom: 20px;">
        <h3>Add New Testimonial / Case Study</h3>
        <form id="cce-add-testimonial-form">
            <div style="display:flex; gap:20px; flex-wrap:wrap; margin-bottom:15px;">
                <div>
                    <label>Type</label><br>
                    <select name="type" id="new-testimonial-type">
                        <option value="testimonial">Testimonial</option>
                        <option value="case_study">Case Study</option>
                    </select>
                </div>
                <div>
                    <label>Title (for Case Studies)</label><br>
                    <input type="text" name="title" class="regular-text" placeholder="e.g. Scaling to $10k/mo">
                </div>
                <div>
                    <label>Client Name</label><br>
                    <input type="text" name="client_name" class="regular-text" required>
                </div>
            </div>
            <div style="margin-bottom:15px;">
                <label>Content</label><br>
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
                <div class="cce-card" id="testimonial-row-<?php echo $t->id; ?>"
                    style="border-top:none; background:#f9f9f9; position:relative;"
                    data-type="<?php echo esc_attr($t->type); ?>"
                    data-title="<?php echo esc_attr($t->title); ?>"
                    data-client-name="<?php echo esc_attr($t->client_name); ?>"
                    data-content="<?php echo esc_attr($t->content); ?>"
                    data-rating="<?php echo $t->rating; ?>">
                    <div style="position:absolute; right:10px; top:10px;">
                        <button class="button button-small cce-edit-testimonial" data-id="<?php echo $t->id; ?>">Edit</button>
                        <button class="button button-link-delete cce-delete-testimonial" data-id="<?php echo $t->id; ?>" style="color:#d63638;">×</button>
                    </div>
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

    <!-- Edit Testimonial Modal -->
    <div id="cce-edit-testimonial-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div style="background:#fff; margin:10% auto; padding:25px; width:400px; border-radius:12px; position:relative;">
            <span class="cce-modal-close" style="position:absolute; right:20px; top:15px; cursor:pointer; font-size:24px;">&times;</span>
            <h2>Edit Social Proof</h2>
            <form id="cce-edit-testimonial-form">
                <input type="hidden" id="edit-testimonial-id">
                <p><label>Type</label><br>
                    <select id="edit-testimonial-type" class="widefat">
                        <option value="testimonial">Testimonial</option>
                        <option value="case_study">Case Study</option>
                    </select>
                </p>
                <p><label>Title</label><br><input type="text" id="edit-testimonial-title" class="widefat"></p>
                <p><label>Client Name</label><br><input type="text" id="edit-testimonial-name" class="widefat" required></p>
                <p><label>Content</label><br><textarea id="edit-testimonial-content" class="widefat" rows="4" required></textarea></p>
                <p><label>Rating (1-5)</label><br><input type="number" id="edit-testimonial-rating" min="1" max="5" required></p>
                <button type="submit" class="button button-primary">Update Social Proof</button>
            </form>
        </div>
    </div>
</div>
