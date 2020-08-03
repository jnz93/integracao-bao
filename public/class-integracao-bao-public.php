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
		add_shortcode('handle_data_form', array($this, 'handle_data_form'));

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


	/**
	 * Shortcode de tratamento dos dados de cotação e criação do produto
	 * 
	 * @since 1.0.0
	 */
	public function handle_data_form() 
	{
		if (!empty($_POST)) :
			$zip_from             	= $_POST['cotacao-cepremetente'];
			$zip_to          		= $_POST['cotacao-cepdestinatario'];
			$n_volumes            	= $_POST['cotacao-volumes'];
			$weight              	= $_POST['cotacao-peso'];
			$price         			= $_POST['delivery_price'];
			$delivery_days       	= $_POST['delivery_time'];
			$value               	= $_POST['cotacao-valor'];
			// $value               	= "25.00";
			
			setlocale(LC_MONETARY, 'pt_BR');
			return $this->ideiadig_create_order_by_freight($price, $delivery_days, $zip_from, $zip_to, $n_volumes, $weight, $value);
		endif;
		
	}

	/**
	 * Create product with rest api
	 * @param $price, $delivery_days, $zip_from, $zip_to, $n_volumes, $weight, $value
	 * 
	 * @since 1.0.0
	 */
	public function ideiadig_create_order_by_freight($price, $delivery_days, $zip_from, $zip_to, $n_volumes, $weight, $value)
	{

		// $wc_public_key 	= get_option('brix-woocomerce-public-key');
		// $wc_secret_key 	= get_option('brix-woocomerce-secret-key');
		$wc_public_key 	= 'ck_215461795ee6e77d32701a5ddce6a21c8035e6cf';
		$wc_secret_key 	= 'cs_ff11f522bc9207f20651dc762372c86e9357fab9';
		
		// echo $wc_public_key . ' : ' . $wc_secret_key . '</br>';

		// echo 'Preço: ' . $price . ' - Delivery days: ' . $delivery_days . ' - Zip From: ' . $zip_from . ' - Zip to: ' . $zip_to . ' - N Vol: ' . $n_volumes . ' - Weight: ' . $weight . ' - Value: ' . $value . '</br>';
		if (empty($wc_public_key) || empty($wc_secret_key)) :
			echo 'Erro ao conectar com a API rest do Woocomerce. Por favor, configure as chaves publica e privada no plugin BRIX.';
		endif;

		# SETUP PRODUCT
		$title_propduct 		= 'FRETE: Origem ' . $zip_from . ' - Destino ' . $zip_to;
		$description_product  	= 'Cep origem: ' . $zip_from . '; <br> Cep destino: ' . $zip_to . '; <br> Volumes: ' . $n_volumes . '; <br> Peso: ' . $weight . '; <br> Valor: ' . $value . ';';
		$api_response = wp_remote_post(
			'https://unitycode.tech/bao/wp-json/wc/v2/products',
			array(
				'headers' 	=> array(
					'Authorization' => 'Basic ' . base64_encode($wc_public_key . ':' . $wc_secret_key)
				),
				'body' 		=> array(
					'name'			=> $title_propduct,
					'price'			=> $price,
					'regular_price'	=> $price,
					'weight'		=> $weight,
					'description'	=> $description_product,
					'status'		=> 'publish',					
				),
			)
		);

		$body = json_decode($api_response['body']);
		// print_r($body);

		if ( wp_remote_retrieve_response_message($api_response) === 'Created' ) :

			// echo $body->name . ' cadastrada com sucesso!';
			$this->add_product_to_cart($body->id);
    
		else :
			// echo $body->name;
			echo wp_remote_retrieve_response_message($api_response);

		endif;

	}

	/**
	 * Add produto criado ao carrinho
	 * @param $product_id do produto
	 * @return void
	*/
	public function add_product_to_cart($product_id)
	{
		if ( $product_id != '') :

			WC()->cart->add_to_cart( $product_id );

		endif;
	}

}
