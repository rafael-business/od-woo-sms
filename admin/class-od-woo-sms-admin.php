<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://olivas.digital
 * @since      1.0.0
 *
 * @package    Od_Woo_Sms
 * @subpackage Od_Woo_Sms/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks.
 *
 * @package    Od_Woo_Sms
 * @subpackage Od_Woo_Sms/admin
 * @author     Olivas Digital <contato@olivasdigital.com.br>
 *
 * TODO: Contador de caracteres
 * 
 */
class Od_Woo_Sms_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/od-woo-sms-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/od-woo-sms-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Create a config page for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function od_woo_sms_config_page() {

		// Add a config page in Woo menu
		add_submenu_page(
			'woocommerce',
	        __( 'Transactional SMS\'s', 'od-woo-sms' ),
	        __( 'Sending SMS\'s', 'od-woo-sms' ),
	        'manage_woocommerce',
	        'od-woo-sms/admin/pages/od-woo-sms-configs.php',
	        '',
	        1
	    );

	}

	/**
	 * Register configs.
	 *
	 * @since    1.0.0
	 */
	public function od_woo_sms_register_configs() {

		register_setting( 'od-woo-sms-configs', 'od_woo_sms_account' );
		register_setting( 'od-woo-sms-configs', 'od_woo_sms_password' );
		register_setting( 'od-woo-sms-configs', 'od_woo_sms_from' );
		register_setting( 'od-woo-sms-configs', 'od_woo_sms_to' );
		register_setting( 'od-woo-sms-configs', 'od_woo_sms_submit_trigger', array( 'type' => 'array' ) );

		$wc_emails = WC_Emails::instance();
        $emails    = $wc_emails->get_emails();
        if (!empty( $emails )) {
        	
	        foreach ($emails as $email) {
	        	
	        	$id = $email->title === 'Admin Delivery Reminder' ? 'admin_' . $email->id : $email->id;
	        	register_setting( 'od-woo-sms-configs', $id. '_od_woo_sms_msg' );
	        }
    	}

	}

	/**
	 * Add extra info in woo email headers.
	 *
	 * @since    1.0.0
	 */
	public function od_woo_sms_custom_email_headers( $headers, $email_id, $order ) {

	    $logger = wc_get_logger();
		$source = array( 'source' => 'od-woo-sms' );

	    if ( isset($email_id) && isset($order) && is_object($order) ) {		

		    $headers = array( $headers );
		    $headers['email_id'] = 'X-Email-Id: ' . $email_id;
		    $headers['order_id'] = 'X-Order-Id: ' . $order->get_id();
	    } else {

	    	$logger->info( 'Nesta versão do plugin, SMS\'s só podem estar atrelados a e-mails de pedido.', $source );
	    }

	    return $headers;
	}

	/**
	 * Send SMS after order email send.
	 *
	 * @since    1.0.0
	 */
	public function od_woo_sms_after_order_email_send( $mail ) {

		$logger = wc_get_logger();
		$source = array( 'source' => 'od-woo-sms' );

		if ( isset($mail['headers']['email_id']) && isset($mail['headers']['order_id']) ) {

			$email_id = $mail['headers']['email_id'];
			$email_id = str_replace('X-Email-Id: ', '', $email_id);

			$order_id = $mail['headers']['order_id'];
			$order_id = str_replace('X-Order-Id: ', '', $order_id);

			$msg = get_option($email_id . '_od_woo_sms_msg');

			if ( $msg ) {

				$sms_send = new Od_Woo_Sms_Send( $email_id, $order_id );

				try {
				    
				    $sms_send->od_woo_sms_send();
				    $logger->debug( print_r($mail['headers'],true), $source );

				} catch (Exception $e) {

					$logger->error( $e->getMessage(), $source );
				}
			}
		}

		return $mail;
	}

}
