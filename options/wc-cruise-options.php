<?php

class WC_CRUISE_settings {

	public function __construct() {

		// get plugin options values
		$this->options = get_option('wc_cruise_options');
		
		// initialize options the first time
		if(!$this->options) {
		
		    $this->options = array( 
		    						'wc_cruise_guests_text' => 'Guests'
		                        );
		    add_option('wc_cruise_options', $this->options);

		}

		if(is_admin()) {

			add_action('admin_menu', array($this, 'wc_cruise_add_option_page'));
			add_action('admin_init', array($this, 'wc_cruise_admin_init'));

		}

	}

	public function wc_cruise_add_option_page() {
		add_options_page('Cruise Charter Options', 'Cruise Charter Options', 'manage_options', 'wc_cruise_options', array( $this, 'wc_cruise_option_page' ));
	}

	

	public function wc_cruise_admin_init() {

		register_setting(
			'wc_cruise_options',
			'wc_cruise_options', 
			array( $this, 'sanitize_values' )
		);

		add_settings_section(
			'wc_cruise_main',
			'Text settings',
			array( $this, 'wc_cruise_section_text' ),
			'wc_cruise_options'
		);

		add_settings_field(
			'wc_cruise_guests_text',
			__('Guests Label', 'wc_cruise'),
			array( $this, 'wc_cruise_guests_text' ),
			'wc_cruise_options',
			'wc_cruise_main'
		);

		add_settings_section(
			'wc_cruise_main',
			'Money settings',
			array( $this, 'wc_cruise_section_text' ),
			'wc_cruise_options'
		);

		add_settings_field(
			'wc_cruise_tax_gratuity_amount',
			__('Fixed Tax &amp; Gratuity Per Person', 'wc_cruise'),
			array( $this, 'wc_cruise_tax_gratuity_amount' ),
			'wc_cruise_options',
			'wc_cruise_main'
		);

	}

	public function wc_cruise_option_page() {

		?><div class="wrap">
	
			<?php screen_icon('generic'); ?>

			<h2><?php _e('WooCommerce Cruise Charter options', 'wc_cruise'); ?></h2>

			<form method="post" action="options.php">

			<?php settings_fields('wc_cruise_options'); ?>
			<?php do_settings_sections('wc_cruise_options'); ?>
			 
			<?php submit_button(); ?>

			</form>

		</div>
		<?php

	}

	public function wc_cruise_section_text() {
		echo '<p>' . __('Customize this plugin to suit your needs.', 'wc_cruise') . '</p>';
	}

	public function wc_cruise_guests_text() {
		echo '<input id="plugin_text_string" name="wc_cruise_options[wc_cruise_guests_text]" size="40" type="text" value="' . $this->options['wc_cruise_guests_text'] . '" />
		<p class="description">' . __('Text displayed before the first date', 'wc_cruise') . '</p>';
	}
	public function wc_cruise_tax_gratuity_amount() {
		echo '<input id="plugin_text_string" name="wc_cruise_options[wc_cruise_tax_gratuity_amount]" size="40" type="text" value="' . $this->options['wc_cruise_tax_gratuity_amount'] . '" />
		<p class="description">' . __('Is applied to all room bookings per guest.', 'wc_cruise') . '</p>';
	}

	public function sanitize_values( $settings ) {
		
		foreach($settings as $key => $value) {
			$settings[$key] = esc_html($value);
		}

		return $settings;
	}

}

new WC_CRUISE_settings();
