<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://olivas.digital
 * @since      1.0.0
 *
 * @package    Od_Woo_Sms
 * @subpackage Od_Woo_Sms/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Od_Woo_Sms
 * @subpackage Od_Woo_Sms/includes
 * @author     Olivas Digital <contato@olivasdigital.com.br>
 */
class Od_Woo_Sms_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'od-woo-sms',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
