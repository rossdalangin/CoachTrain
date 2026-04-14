<?php
/**
 * Public-facing functionality.
 */
class CCE_Public {

	/**
	 * Define the shortcode for lead capture.
	 */
	public function init() {
		add_shortcode( 'cce_lead_capture', array( $this, 'render_lead_capture_form' ) );
		add_shortcode( 'cce_booking', array( $this, 'render_booking_form' ) );
		add_shortcode( 'cce_testimonials', array( $this, 'render_testimonials' ) );
		add_shortcode( 'cce_checkout', array( $this, 'render_checkout' ) );
		add_shortcode( 'cce_client_portal', array( $this, 'render_client_portal' ) );
        add_shortcode( 'cce_funnel', array( $this, 'render_funnel' ) );
	}

	/**
	 * Render checkout form.
	 */
	public function render_checkout( $atts ) {
		global $wpdb;
		$atts = shortcode_atts( array(
			'offer_id' => 1,
            'user_id'  => 0,
		), $atts );

        $offer_id = absint( $atts['offer_id'] );
        $offer = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_offers WHERE id = %d", $offer_id ) );
        $user_id = $atts['user_id'] ?: ( $offer ? $offer->user_id : get_the_author_meta( 'ID' ) );

        $currency_code = get_option('cce_currency', 'USD');
        $currency_symbols = ['USD' => '$', 'EUR' => '€', 'GBP' => '£', 'CAD' => 'C$', 'AUD' => 'A$'];
        $currency_symbol = $currency_symbols[$currency_code] ?? '$';

		ob_start();
		$token = $_COOKIE['cce_lead_token'] ?? '';
		$lead_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cce_leads WHERE secure_token = %s", $token ) ) ?: 0;
		?>
		<div class="cce-checkout-wrapper">
			<?php if ( $offer ): ?>
                <h3>Enroll in <?php echo esc_html( $offer->title ); ?></h3>
                <div class="cce-offer-summary" style="margin-bottom:20px; padding:15px; background:#f9f9f9; border-radius:8px;">
                    <p style="font-size:20px; font-weight:bold; color:#0073aa;">Price: <?php echo $currency_symbol . number_format($offer->price, 2); ?></p>
                    <?php if ($offer->dream_outcome): ?>
                        <p><strong>Your Outcome:</strong> <?php echo esc_html($offer->dream_outcome); ?></p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <h3>Complete Your Purchase</h3>
            <?php endif; ?>

			<form id="cce-public-checkout-form">
				<input type="hidden" name="offer_id" value="<?php echo esc_attr( $offer_id ); ?>">
				<input type="hidden" name="lead_id" value="<?php echo esc_attr( $lead_id ); ?>">
                <input type="hidden" name="user_id" value="<?php echo esc_attr( $user_id ); ?>">
				<select name="gateway" required>
					<option value="stripe">Stripe</option>
					<option value="paypal">PayPal</option>
				</select>
				<button type="submit" class="button">Pay Now</button>
			</form>
			<div id="cce-checkout-message"></div>
		</div>
		<script>
		document.getElementById('cce-public-checkout-form').addEventListener('submit', function(e) {
			e.preventDefault();
			const formData = new FormData(this);
			const data = Object.fromEntries(formData.entries());

			fetch('<?php echo esc_url_raw( rest_url( 'cce/v1/checkout/process' ) ); ?>', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': '<?php echo wp_create_nonce( 'wp_rest' ); ?>'
				},
				body: JSON.stringify(data)
			})
			.then(res => res.json())
			.then(res => {
				const msg = document.getElementById('cce-checkout-message');
				if (res.success && res.data.redirect_url) {
					msg.innerHTML = '<p style="color:green">' + res.data.message + '</p>';
                    window.location.href = res.data.redirect_url;
				} else if (res.success) {
                    msg.innerHTML = '<p style="color:green">Payment successful! Welcome aboard.</p>';
                } else {
					msg.innerHTML = '<p style="color:red">Payment failed. Please try again.</p>';
				}
			});
		});
		</script>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render funnel journey.
	 */
	public function render_funnel( $atts ) {
		global $wpdb;
		$atts = shortcode_atts( array(
			'id' => 1,
		), $atts );

        $funnel_id = absint( $atts['id'] );
        $funnel = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_funnels WHERE id = %d", $funnel_id ) );

        if ( ! $funnel ) {
            return '<p>Funnel not found.</p>';
        }

        $owner_id = $funnel->user_id;
        $steps = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_funnel_steps WHERE funnel_id = %d ORDER BY step_order ASC", $funnel_id ) );

        if ( ! $steps ) {
            return '<p>Funnel has no steps.</p>';
        }

        $current_step_index = absint( $_GET['step_idx'] ?? 0 );
        $current_step = $steps[$current_step_index] ?? $steps[0];

        // Track visit
        if ( ! is_admin() ) {
            $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}cce_funnel_steps SET visits = visits + 1 WHERE id = %d", $current_step->id ) );
            // Store funnel title in cookie for lead source
            setcookie('cce_funnel_source', $funnel_id, time() + HOUR_IN_SECONDS, '/');
            setcookie('cce_active_funnel_step', $current_step->id, time() + HOUR_IN_SECONDS, '/');
        }

        $next_step_url = isset($steps[$current_step_index + 1]) ? add_query_arg('step_idx', $current_step_index + 1) : '';

		ob_start();
		?>
		<div class="cce-funnel-wrapper" data-funnel-id="<?php echo $funnel_id; ?>" data-step-index="<?php echo $current_step_index; ?>">
			<div class="cce-funnel-step">
                <?php
                switch ( $current_step->step_type ) {
                    case 'optin':
                        echo $this->render_lead_capture_form( array( 'title' => $current_step->title, 'redirect' => $next_step_url, 'user_id' => $owner_id ) );
                        break;
                    case 'booking':
                        echo $this->render_booking_form( array( 'title' => $current_step->title, 'redirect' => $next_step_url, 'user_id' => $owner_id ) );
                        break;
                    case 'checkout':
                        $config = json_decode( $current_step->config, true );
                        $offer_id = absint( $config['offer_id'] ?? 1 );
                        echo $this->render_checkout( array( 'offer_id' => $offer_id ) );
                        break;
                    case 'thank_you':
                        $config = json_decode( $current_step->config, true );
                        if ( ! empty( $config['redirect_url'] ) ) {
                            echo "<script>window.location.href='" . esc_url($config['redirect_url']) . "';</script>";
                        } else {
                            $msg = $config['success_message'] ?: 'Success! You are all set.';
                            echo "<h3>" . esc_html( $current_step->title ) . "</h3><p>" . wp_kses_post($msg) . "</p>";
                        }
                        break;
                }
                ?>
            </div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_client_portal( $atts ) {
		global $wpdb;
		ob_start();
		$token = $_COOKIE['cce_lead_token'] ?? '';

		$lead = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_leads WHERE secure_token = %s", $token ) );

		if ( ! $lead ) {
			return '<p>Please log in or capture your lead info first.</p>';
		}

        $completed = json_decode( $lead->onboarding_progress ?: '[]', true );

        $portal_manager = new CCE_Portal_Manager();
        $resources_res = $portal_manager->get_resources( new WP_REST_Request() );
        $resources = is_wp_error($resources_res) ? [] : $resources_res->get_data();
		?>
		<div class="cce-client-portal">
			<h3>Welcome, <?php echo esc_html( $lead->first_name ); ?></h3>
			<div style="display:flex; gap:20px;">
				<div style="flex:1; border:1px solid #ddd; padding:20px;">
					<h4>Your Coaching Roadmap</h4>
                    <div id="cce-onboarding-tasks">
                        <?php
                        $tasks_db = $wpdb->get_results("SELECT task_name FROM {$wpdb->prefix}cce_onboarding_tasks ORDER BY task_order ASC");
                        $tasks = !empty($tasks_db) ? array_column($tasks_db, 'task_name') : ['Welcome Training', 'Community Access'];
                        foreach ($tasks as $t):
                            $is_done = in_array($t, $completed);
                        ?>
                        <p style="<?php echo $is_done ? 'text-decoration:line-through' : ''; ?>">
                            <input type="checkbox" class="cce-portal-complete" data-step="<?php echo esc_attr($t); ?>" <?php checked($is_done); ?> <?php disabled($is_done); ?>>
                            <?php echo esc_html($t); ?>
                        </p>
                        <?php endforeach; ?>
                    </div>

                    <h4 style="margin-top:30px;">Resources</h4>
                    <?php
                    if ($resources):
                        $categorized = [];
                        foreach ($resources as $r) {
                            $cat = $r->category ?: 'General';
                            $categorized[$cat][] = $r;
                        }
                        foreach ($categorized as $cat => $items):
                        ?>
                            <h5 style="margin:15px 0 5px; color:#666; text-transform:uppercase; font-size:11px;"><?php echo esc_html($cat); ?></h5>
                            <ul style="list-style:none; padding:0;">
                                <?php foreach ($items as $r): ?>
                                    <li style="margin-bottom:8px; padding:10px; background:#f9f9f9; border-radius:5px;">
                                        <div style="display:flex; justify-content:space-between; align-items:center;">
                                            <span><strong>[<?php echo esc_html($r->type); ?>]</strong> <?php echo esc_html($r->title); ?></span>
                                            <a href="<?php echo esc_url($r->url); ?>" class="button button-small" target="_blank">Access</a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No resources available at your current level.</p>
                    <?php endif; ?>
				</div>
				<div style="flex:1; border:1px solid #ddd; padding:20px;">
					<h4>Your Progress</h4>
					<?php
                        $has_booking = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}cce_bookings WHERE lead_id = %d AND status != 'cancelled'", $lead->id ) );
                        if ( ! $has_booking ) {
                            echo '<p>Next Step: <strong>Schedule Consultation</strong></p>';
                            echo $this->render_booking_form( array( 'title' => '' ) );
                        } else {
                            echo '<p>Next Step: <strong>Attend Your Call</strong></p>';
                            $booking = $wpdb->get_row( $wpdb->prepare( "SELECT start_time FROM {$wpdb->prefix}cce_bookings WHERE lead_id = %d ORDER BY created_at DESC LIMIT 1", $lead->id ) );
                            if ($booking) echo '<p>Scheduled for: ' . esc_html( $booking->start_time ) . '</p>';
                        }
                    ?>
				</div>
			</div>
		</div>
        <script>
        document.querySelectorAll('.cce-portal-complete').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    const stepName = this.getAttribute('data-step');
                    fetch('<?php echo esc_url_raw( rest_url( 'cce/v1/portal/onboarding/complete' ) ); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': '<?php echo wp_create_nonce( 'wp_rest' ); ?>'
                        },
                        body: JSON.stringify({ step_name: stepName })
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            this.parentElement.style.textDecoration = 'line-through';
                            this.disabled = true;
                        }
                    });
                }
            });
        });
        </script>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render testimonials.
	 */
	public function render_testimonials( $atts ) {
        global $wpdb;
        $atts = shortcode_atts( array(
			'type' => 'testimonial',
            'user_id' => 0,
		), $atts );

        $type = sanitize_text_field( $atts['type'] );
        $user_id = absint( $atts['user_id'] ) ?: get_the_author_meta( 'ID' );
		ob_start();

        $query = "SELECT * FROM {$wpdb->prefix}cce_testimonials WHERE status = 'active' AND type = %s";
        $params = array( $type );
        if ( $user_id ) {
            $query .= " AND user_id = %d";
            $params[] = $user_id;
        }
        $query .= " ORDER BY RAND() LIMIT 3";

        $testimonials = $wpdb->get_results( $wpdb->prepare( $query, $params ) );
		?>
		<div class="cce-testimonials-display">
            <?php if ( 'case_study' === $atts['type'] ): ?>
                <div class="cce-case-study-grid" style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                    <?php if ($testimonials): foreach($testimonials as $cs): ?>
                        <div class="cce-case-study" style="border:1px solid #eee; padding:20px; border-radius:10px;">
                            <h4><?php echo esc_html($cs->title ?: 'Success Story'); ?></h4>
                            <p><?php echo wp_trim_words(esc_html($cs->content), 20); ?></p>
                            <strong>- <?php echo esc_html($cs->client_name); ?></strong>
                        </div>
                    <?php endforeach; else: ?>
                        <div class="cce-case-study" style="border:1px solid #eee; padding:20px; border-radius:10px;">
                            <h4>The $50k Month Strategy</h4>
                            <p>How we helped a fitness coach scale using the Engine.</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php if ( ! empty( $testimonials ) ): ?>
                    <?php foreach ( $testimonials as $t ): ?>
                        <div class="cce-testimonial-card" style="border:1px solid #ddd; padding:20px; margin-bottom:10px;">
                            <p>"<?php echo esc_html( $t->content ); ?>"</p>
                            <strong>- <?php echo esc_html( $t->client_name ); ?></strong>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="cce-testimonial-card" style="border:1px solid #ddd; padding:20px; margin-bottom:10px;">
                        <p>"The Coach Client Engine tripled my bookings in one month!"</p>
                        <strong>- Sarah Jenkins</strong>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render the booking form.
	 */
	public function render_booking_form( $atts ) {
		global $wpdb;
		$atts = shortcode_atts( array(
			'title' => 'Schedule Your Free Consultation',
            'redirect' => '',
            'user_id'  => 0,
		), $atts );

		ob_start();
        $user_id = absint( $atts['user_id'] ) ?: get_the_author_meta( 'ID' );
		$token = $_COOKIE['cce_lead_token'] ?? '';
		$lead_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cce_leads WHERE secure_token = %s", $token ) ) ?: 0;
		?>
		<div class="cce-booking-form-wrapper">
			<h3><?php echo esc_html( $atts['title'] ); ?></h3>
			<form class="cce-public-booking-form" data-redirect="<?php echo esc_url($atts['redirect']); ?>">
				<input type="hidden" name="lead_id" value="<?php echo esc_attr( $lead_id ); ?>">
                <input type="hidden" name="user_id" value="<?php echo esc_attr( $user_id ); ?>">
                <div style="margin-bottom:15px;">
                    <label>Preferred Date & Time</label>
				    <input type="datetime-local" name="start_time" required>
                </div>
                <div style="margin-bottom:15px;">
                    <label>Your Timezone</label>
				    <select name="timezone" required>
					<option value="UTC">UTC</option>
					<option value="America/New_York">EST</option>
					<option value="America/Los_Angeles">PST</option>
				</select>
                </div>
                <div class="cce-dynamic-questions">
                    <?php
                    $q_query = "SELECT * FROM {$wpdb->prefix}cce_questions";
                    if ( $user_id ) {
                        $questions = $wpdb->get_results($wpdb->prepare($q_query . " WHERE user_id = %d ORDER BY question_order ASC", $user_id));
                    } else {
                        $questions = $wpdb->get_results($q_query . " ORDER BY question_order ASC");
                    }
                    if ($questions): foreach ($questions as $q):
                        $req = $q->is_required ? 'required' : '';
                        $name = "questionnaire[" . esc_attr($q->question_text) . "]";
                    ?>
                        <div style="margin-bottom:15px;">
                            <label><?php echo esc_html($q->question_text); ?></label>
                            <?php if ($q->question_type === 'textarea'): ?>
                                <textarea name="<?php echo $name; ?>" rows="3" <?php echo $req; ?>></textarea>
                            <?php else: ?>
                                <input type="text" name="<?php echo $name; ?>" <?php echo $req; ?>>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; else: ?>
                        <div style="margin-bottom:15px;">
                            <label>What is your #1 goal right now?</label>
                            <textarea name="questionnaire[goal]" rows="3" required></textarea>
                        </div>
                    <?php endif; ?>
                </div>
				<button type="submit" class="button">Book My Session</button>
			</form>
			<div class="cce-booking-message"></div>
		</div>
		<script>
		document.querySelectorAll('.cce-public-booking-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());
                const redirect = this.getAttribute('data-redirect');
                data.end_time = data.start_time;

                const $btn = this.querySelector('button');
                const originalText = $btn.innerText;
                $btn.disabled = true;
                $btn.innerText = 'Processing...';

                fetch('<?php echo esc_url_raw( rest_url( 'cce/v1/bookings' ) ); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': '<?php echo wp_create_nonce( 'wp_rest' ); ?>'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(res => {
                    $btn.disabled = false;
                    $btn.innerText = originalText;
                    if (res.success) {
                        if(redirect) {
                            window.location.href = redirect;
                        } else {
                            this.nextElementSibling.innerHTML = '<p style="color:green">Booking confirmed!</p>';
                        }
                    }
                });
            });
        });
		</script>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render the lead capture form.
	 */
	public function render_lead_capture_form( $atts ) {
		$atts = shortcode_atts( array(
			'title' => 'Get My Free Coaching Guide',
			'type'  => 'inline',
            'redirect' => '',
            'user_id'  => 0,
		), $atts );

		ob_start();
        $user_id = absint( $atts['user_id'] ) ?: get_the_author_meta( 'ID' );
		$wrapper_class = 'cce-lead-form-wrapper cce-form-' . esc_attr( $atts['type'] );
		?>
		<div class="<?php echo esc_attr( $wrapper_class ); ?>">
			<h3><?php echo esc_html( $atts['title'] ); ?></h3>
			<form class="cce-public-lead-form" data-redirect="<?php echo esc_url($atts['redirect']); ?>">
                <input type="hidden" name="user_id" value="<?php echo esc_attr( $user_id ); ?>">
				<input type="text" name="first_name" placeholder="First Name" required>
				<input type="email" name="email" placeholder="Email Address" required>
				<button type="submit" class="button">Send Me the Guide</button>
			</form>
			<div class="cce-form-message"></div>
		</div>
		<script>
		document.querySelectorAll('.cce-public-lead-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());
                const redirect = this.getAttribute('data-redirect');

                const $btn = this.querySelector('button');
                const originalText = $btn.innerText;
                $btn.disabled = true;
                $btn.innerText = 'Processing...';

                fetch('<?php echo esc_url_raw( rest_url( 'cce/v1/leads' ) ); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': '<?php echo wp_create_nonce( 'wp_rest' ); ?>'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(res => {
                    $btn.disabled = false;
                    $btn.innerText = originalText;
                    if (res.success) {
                        document.cookie = "cce_lead_token=" + res.data.secure_token + ";path=/";
                        if(redirect) {
                            window.location.href = redirect;
                        } else {
                            this.nextElementSibling.innerHTML = '<p style="color:green">Success!</p>';
                        }
                    }
                });
            });
        });
		</script>
		<?php
		return ob_get_clean();
	}
}
