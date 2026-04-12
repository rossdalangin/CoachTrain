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
	 * Render client portal.
	 */
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

        $current_step_index = absint( $_GET['step'] ?? 0 );
        $current_step = $steps[$current_step_index] ?? $steps[0];

		ob_start();
		?>
		<div class="cce-funnel-wrapper">
			<div class="cce-funnel-step">
                <?php
                switch ( $current_step->step_type ) {
                    case 'optin':
                        echo $this->render_lead_capture_form( array( 'title' => $current_step->title ) );
                        break;
                    case 'booking':
                        echo $this->render_booking_form( array( 'title' => $current_step->title ) );
                        break;
                    case 'checkout':
                        echo $this->render_checkout( array( 'offer_id' => 1 ) ); // offer_id logic
                        break;
                    case 'thank_you':
                        echo "<h3>" . esc_html( $current_step->title ) . "</h3><p>Success! You are all set.</p>";
                        break;
                }
                ?>
                <?php if ( isset( $steps[$current_step_index + 1] ) ): ?>
                    <div style="margin-top:20px;">
                        <a href="?step=<?php echo $current_step_index + 1; ?>" class="button">Next Step →</a>
                    </div>
                <?php endif; ?>
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
		?>
		<div class="cce-client-portal">
			<h3>Your Client Dashboard</h3>
			<div style="display:flex; gap:20px;">
				<div style="flex:1; border:1px solid #ddd; padding:20px;">
					<h4>Resources</h4>
					<ul>
						<li>Welcome Pack (PDF)</li>
						<li>High-Ticket Training (Video)</li>
					</ul>
				</div>
				<div style="flex:1; border:1px solid #ddd; padding:20px;">
					<h4>Your Progress</h4>
					<p>Onboarding: <strong>Complete</strong></p>
					<p>Next Step: <strong>Consultation Call</strong></p>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render testimonials.
	 */
	public function render_testimonials( $atts ) {
        $atts = shortcode_atts( array(
			'type' => 'testimonial', // testimonial, case_study
		), $atts );

		ob_start();
		?>
		<div class="cce-testimonials-display">
            <?php if ( 'case_study' === $atts['type'] ): ?>
                <div class="cce-case-study" style="border:1px solid #ddd; padding:20px; margin-bottom:10px; background:#fff;">
                    <h4>How Sarah Doubled Her Revenue</h4>
                    <p>Before using the Engine, Sarah was struggling to get 1 client/month. Now she gets 5 consistently.</p>
                </div>
            <?php else: ?>
                <div class="cce-testimonial-card" style="border:1px solid #ddd; padding:20px; margin-bottom:10px;">
                    <p>"The Coach Client Engine tripled my bookings in one month!"</p>
                    <strong>- Sarah Jenkins</strong>
                </div>
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
		), $atts );

		ob_start();
		$token = $_COOKIE['cce_lead_token'] ?? '';
		$lead_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}cce_leads WHERE secure_token = %s", $token ) ) ?: 0;
		?>
		<div class="cce-booking-form-wrapper">
			<h3><?php echo esc_html( $atts['title'] ); ?></h3>
			<form id="cce-public-booking-form">
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
			<div id="cce-booking-message"></div>
		</div>
		<script>
		document.getElementById('cce-public-booking-form').addEventListener('submit', function(e) {
			e.preventDefault();
			const formData = new FormData(this);
			const data = Object.fromEntries(formData.entries());
			data.end_time = data.start_time; // Simplified for this version

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
				const msg = document.getElementById('cce-booking-message');
				if (res.success) {
					msg.innerHTML = '<p style="color:green">Booking confirmed! We will contact you soon.</p>';
					this.reset();
				} else {
					msg.innerHTML = '<p style="color:red">Failed to book session. Please try again.</p>';
				}
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
		// Start session if not started
		if ( ! session_id() ) {
			session_start();
		}

		$atts = shortcode_atts( array(
			'title' => 'Get My Free Coaching Guide',
			'type'  => 'inline', // inline, popup, sticky
		), $atts );

		ob_start();
		$wrapper_class = 'cce-lead-form-wrapper cce-form-' . esc_attr( $atts['type'] );
		?>
		<div class="<?php echo esc_attr( $wrapper_class ); ?>">
			<h3><?php echo esc_html( $atts['title'] ); ?></h3>
			<form id="cce-public-lead-form">
				<input type="text" name="first_name" placeholder="First Name" required>
				<input type="email" name="email" placeholder="Email Address" required>
				<button type="submit" class="button">Send Me the Guide</button>
			</form>
			<div id="cce-form-message"></div>
		</div>
		<script>
		document.getElementById('cce-public-lead-form').addEventListener('submit', function(e) {
			e.preventDefault();
			const formData = new FormData(this);
			const data = Object.fromEntries(formData.entries());

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
				const msg = document.getElementById('cce-form-message');
				if (res.success) {
					msg.innerHTML = '<p style="color:green">Success! Check your email.</p>';
					this.reset();
					// Store secure token in session via cookie
					document.cookie = "cce_lead_token=" + res.data.secure_token + ";path=/";
				} else {
					msg.innerHTML = '<p style="color:red">Something went wrong. Please try again.</p>';
				}
			});
		});
		</script>
		<?php
		return ob_get_clean();
	}
}
