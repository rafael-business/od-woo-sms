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
 * The SMS's functionality of the plugin.
 *
 * Defines SMS's sending.
 *
 * @package    Od_Woo_Sms
 * @subpackage Od_Woo_Sms/admin
 * @author     Olivas Digital <contato@olivasdigital.com.br>
 *
 * TODO: Verificação de dados
 * 
 */
class Od_Woo_Sms_Send {

	/**
	 * Account.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $account
	 */
	private $account;

	/**
	 * Password.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $password
	 */
	private $password;

	/**
	 * Web Service URL.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $ws
	 */
	private $ws;

	/**
	 * From.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $from
	 */
	private $from;

	/**
	 * To.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $to
	 */
	private $to;

	/**
	 * Message.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $msg
	 */
	private $msg;

	/**
	 * Unique id.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $id
	 */
	private $id;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $email_id
	 * @param      string    $order_id
	 */
	public function __construct( $email_id, $order_id ) {

		$this->account 	= get_option('od_woo_sms_account');
		$this->password = get_option('od_woo_sms_password');
		$this->ws 		= 'https://api-rest.zenvia.com';

		$order = wc_get_order( $order_id );

		$this->from = get_option('od_woo_sms_from');

		$to = get_option('od_woo_sms_to');
		switch ($to) {
			case 'billing_phone':
				$to = $order->get_billing_phone();
				break;

			case 'billing_cellphone':
				$to = $order->get_billing_cellphone();
				break;
			
			default:
				$to = get_post_meta( $order_id, get_option('od_woo_sms_to'), true );
				break;
		}

		$to = str_replace('(', '', $to);
		$to = str_replace(')', '', $to);
		$to = str_replace('-', '', $to);
		$to = str_replace(' ', '', $to);
		$to = '55' . $to;
		$this->to = $to;

		$this->msg = get_option($email_id . '_od_woo_sms_msg');

		$this->id = $order_id . '_' . $email_id . '_' . rand(100, 999);

	}

	/**
	 * Send SMS.
	 *
	 * @since    1.0.0
	 */
	public function od_woo_sms_send() {

		if ( $this->msg ) {

			$smsFacade = new SmsFacade($this->account, $this->password, $this->ws);

			$sms = new Sms();
			$sms->setFrom($this->from);
			$sms->setTo($this->to);
			$sms->setMsg($this->msg);
			$sms->setId($this->id);
			$sms->setCallbackOption(Sms::CALLBACK_NONE);

			$date = new DateTime();
			$date->setTimeZone(new DateTimeZone('America/Sao_Paulo'));
			$schedule = $date->format("Y-m-d\TH:i:s");

			$sms->setSchedule($schedule);

			$logger = wc_get_logger();
			$source = array( 'source' => 'od-woo-sms' );
			$log 	= array();

			try{

			    $response = $smsFacade->send($sms);

			    $log['stat'] = "Status: ".$response->getStatusCode() . " - " . $response->getStatusDescription();
			    $log['deta'] = "Detalhe: ".$response->getDetailCode() . " - " . $response->getDetailDescription();
			    $log['erro'] = "Mensagem não pôde ser enviada.";

			    $logger->info( $log['stat'], $source );
			    $logger->info( $log['deta'], $source );

			    if($response->getStatusCode()!="00"){
			       $logger->error( $log['erro'], $source );
			    }

			}
			catch(Exception $ex){

			    $log['exce'] = "Falha ao fazer o envio da mensagem. Exceção: ".$ex->getMessage()." - ".$ex->getTraceAsString();
			    $logger->error( $log['exce'], $source );
			}
		}

	}

}
