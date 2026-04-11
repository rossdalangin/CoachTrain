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
	}

	/**
	 * Render the lead capture form.
	 */
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
