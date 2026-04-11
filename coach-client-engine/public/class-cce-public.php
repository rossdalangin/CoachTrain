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
	}

	/**
	 * Render the lead capture form.
	 */
    /**
	 * Render the booking form.
	 */
    /**
	 * Render testimonials.
	 */
    /**
	 * Render checkout form.
	 */
	public function render_checkout( $atts ) {
		$atts = shortcode_atts( array(
			'offer_id' => 1,
		), $atts );

		ob_start();
		?>
		<div class="cce-checkout-wrapper">
			<h3>Complete Your Purchase</h3>
			<form id="cce-public-checkout-form">
                <input type="hidden" name="offer_id" value="<?php echo esc_attr( $atts['offer_id'] ); ?>">
                <input type="hidden" name="lead_id" value="1">
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

	public function render_testimonials( $atts ) {
		ob_start();
		?>
		<div class="cce-testimonials-display">
			<div class="cce-testimonial-card" style="border:1px solid #ddd; padding:20px; margin-bottom:10px;">
				<p>"The Coach Client Engine tripled my bookings in one month!"</p>
				<strong>- Sarah Jenkins</strong>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_booking_form( $atts ) {
		$atts = shortcode_atts( array(
			'title' => 'Schedule Your Free Consultation',
		), $atts );

		ob_start();
		?>
		<div class="cce-booking-form-wrapper">
			<h3><?php echo esc_html( $atts['title'] ); ?></h3>
			<form id="cce-public-booking-form">
                <input type="hidden" name="lead_id" value="1"> <!-- Example lead ID -->
				<input type="datetime-local" name="start_time" required>
				<select name="timezone" required>
                    <option value="UTC">UTC</option>
                    <option value="America/New_York">EST</option>
                    <option value="America/Los_Angeles">PST</option>
                </select>
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

	public function render_lead_capture_form( $atts ) {
		$atts = shortcode_atts( array(
			'title' => 'Get My Free Coaching Guide',
		), $atts );

		ob_start();
		?>
		<div class="cce-lead-form-wrapper">
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
