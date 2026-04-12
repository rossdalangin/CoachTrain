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
		), $atts );

		ob_start();
		$token = $_COOKIE['cce_lead_token'] ?? '';
		$lead_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cce_leads WHERE secure_token = %s", $token ) ) ?: 0;
		?>
		<div class="cce-checkout-wrapper">
			<h3>Complete Your Purchase</h3>
			<form id="cce-public-checkout-form">
				<input type="hidden" name="offer_id" value="<?php echo esc_attr( $atts['offer_id'] ); ?>">
				<input type="hidden" name="lead_id" value="<?php echo esc_attr( $lead_id ); ?>">
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
				if (res.success) {
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
        $steps = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cce_funnel_steps WHERE funnel_id = %d ORDER BY step_order ASC", $funnel_id ) );

        if ( ! $steps ) {
            return '<p>Funnel not found or has no steps.</p>';
        }

        $current_step_index = absint( $_GET['step_idx'] ?? 0 );
        $current_step = $steps[$current_step_index] ?? $steps[0];
        $next_step_url = isset($steps[$current_step_index + 1]) ? add_query_arg('step_idx', $current_step_index + 1) : '';

		ob_start();
		?>
		<div class="cce-funnel-wrapper" data-funnel-id="<?php echo $funnel_id; ?>" data-step-index="<?php echo $current_step_index; ?>">
			<div class="cce-funnel-step">
                <?php
                switch ( $current_step->step_type ) {
                    case 'optin':
                        echo $this->render_lead_capture_form( array( 'title' => $current_step->title, 'redirect' => $next_step_url ) );
                        break;
                    case 'booking':
                        echo $this->render_booking_form( array( 'title' => $current_step->title, 'redirect' => $next_step_url ) );
                        break;
                    case 'checkout':
                        echo $this->render_checkout( array( 'offer_id' => 1 ) );
                        break;
                    case 'thank_you':
                        echo "<h3>" . esc_html( $current_step->title ) . "</h3><p>Success! You are all set.</p>";
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
		?>
		<div class="cce-client-portal">
			<h3>Welcome, <?php echo esc_html( $lead->first_name ); ?></h3>
			<div style="display:flex; gap:20px;">
				<div style="flex:1; border:1px solid #ddd; padding:20px;">
					<h4>Your Coaching Roadmap</h4>
                    <div id="cce-onboarding-tasks">
                        <?php
                        $tasks = ['Welcome Training', 'Community Access'];
                        foreach ($tasks as $t):
                            $is_done = in_array($t, $completed);
                        ?>
                        <p style="<?php echo $is_done ? 'text-decoration:line-through' : ''; ?>">
                            <input type="checkbox" class="cce-portal-complete" data-step="<?php echo esc_attr($t); ?>" <?php checked($is_done); ?> <?php disabled($is_done); ?>>
                            <?php echo esc_html($t); ?>
                        </p>
                        <?php endforeach; ?>
                    </div>
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
		), $atts );

		ob_start();
        $testimonials = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cce_testimonials WHERE status = 'active' ORDER BY RAND() LIMIT 3" );
		?>
		<div class="cce-testimonials-display">
            <?php if ( 'case_study' === $atts['type'] ): ?>
                <div class="cce-case-study-grid" style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                    <div class="cce-case-study" style="border:1px solid #eee; padding:20px; border-radius:10px;">
                        <h4>The $50k Month Strategy</h4>
                        <p>How we helped a fitness coach scale using the Engine.</p>
                        <a href="#" class="button button-small">Read More</a>
                    </div>
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
            'redirect' => ''
		), $atts );

		ob_start();
		$token = $_COOKIE['cce_lead_token'] ?? '';
		$lead_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cce_leads WHERE secure_token = %s", $token ) ) ?: 0;
		?>
		<div class="cce-booking-form-wrapper">
			<h3><?php echo esc_html( $atts['title'] ); ?></h3>
			<form class="cce-public-booking-form" data-redirect="<?php echo esc_url($atts['redirect']); ?>">
				<input type="hidden" name="lead_id" value="<?php echo esc_attr( $lead_id ); ?>">
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
                <div style="margin-bottom:15px;">
                    <label>What is your #1 goal right now?</label>
                    <textarea name="questionnaire[goal]" rows="3" required></textarea>
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
            'redirect' => ''
		), $atts );

		ob_start();
		$wrapper_class = 'cce-lead-form-wrapper cce-form-' . esc_attr( $atts['type'] );
		?>
		<div class="<?php echo esc_attr( $wrapper_class ); ?>">
			<h3><?php echo esc_html( $atts['title'] ); ?></h3>
			<form class="cce-public-lead-form" data-redirect="<?php echo esc_url($atts['redirect']); ?>">
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
