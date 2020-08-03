<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       unitycode.tech
 * @since      1.0.0
 *
 * @package    Integracao_Bao
 * @subpackage Integracao_Bao/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Integracao_Bao
 * @subpackage Integracao_Bao/public
 * @author     jnz93 <joanez@unitycode.tech>
 */
class Integracao_Bao_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;


		// Shortcodes
		add_shortcode('bao_cotacao', array($this, 'form_bao_cotacao'));

		// Ajax Actions
		add_action('wp_ajax_send_cotacao_data', array($this, 'send_cotacao_data'));
		add_action('wp_ajax_nopriv_send_cotacao_data', array($this, 'send_cotacao_data'));
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Integracao_Bao_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Integracao_Bao_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/integracao-bao-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Integracao_Bao_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Integracao_Bao_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/integracao-bao-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Function for the form of cotação
	 * 
	 * @since 1.0.0
	 */
	public function form_bao_cotacao()
	{
		require( plugin_dir_path(__FILE__) . 'partials/integracao-bao-public-display.php');
	}

	/**
	 * Function for send data from form and sanitize
	 * 
	 * @since 1.0.0
	 */
	public function send_cotacao_data()
	{
		$data_from_form = array(
			'cepremetente'      =>  trim($_POST['cepremetente']), 
			'cepdestinatario'   =>  trim($_POST['cepdestinatario']),
			'cepdestinatario'   =>  trim($_POST['cepdestinatario']), 
			'cepdestinatario'   =>  trim($_POST['cepdestinatario']),
			'volumes'           =>  trim($_POST['volumes']), 
			'peso'              =>  trim($_POST['peso']), 
			'valor'             =>  trim($_POST['valor']), 
		);   
		$response = $this->soap_request($data_from_form);
		die($response);
	}

	/**
	 * Soap request from brix cotacao
	 * 
	 * @since 1.0.0
	 */
	public function soap_request($args)
	{
		if(!is_array($args) 
			|| !isset($args['cepremetente'])
			|| !isset($args['cepdestinatario'])
			|| !isset($args['volumes']) 
			|| !isset($args['valor'])         
		){
			$result = array(
				'status'=>0,
				'erro'=> 'Requisição não realizada, revise entrada de dados.'
			);
			return json_encode($result);
		}

		$args = array(
			'cepremetente'      =>  isset($args['cepremetente'])       ?   $args['cepremetente']      : NULL, 
			'cepdestinatario'   =>  isset($args['cepdestinatario'])    ?   $args['cepdestinatario']   : NULL, 
			'volumes'           =>  isset($args['volumes'])            ?   $args['volumes']           : NULL, 
			'peso'              =>  isset($args['peso'])               ?   $args['peso']              : NULL, 
			'valor'             =>  isset($args['valor'])              ?   $args['valor']             : NULL, 
		);

		$client = new SoapClient("https://brix.ws.brudam.com.br/cotacao/frete?wsdl"); 
		
		
		$args['chave']     =   strval(get_option('brix_token'));
		$args['cliente']   =   strval(get_option('brix_cliente'));
		$args['servico']   =   intval(get_option('brix-cotacao-servico'));
		$result = $client->CalculoFrete($args);
		return json_encode($result);
		die();
	}
}
