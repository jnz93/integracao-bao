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

		add_action('wp_head', array($this, 'insert_style_loader'));

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
			setlocale(LC_MONETARY, 'pt_BR');
			
			$zip_from             	= $_POST['select-origin'];
			$zip_to          		= $_POST['select-destiny'];
			$n_volumes            	= $_POST['cotacao-volumes'];
			$weight              	= $_POST['cotacao-peso'];
			$price         			= $_POST['delivery_price'];
			$delivery_days       	= $_POST['delivery_time'];
			$value               	= $_POST['cotacao-valor'];
			
			$list_of_cities = array(
				'31270-700' => 'Belo Horizonte - MG',
				'71608-900' => 'Brasília - DF',
				'13051-154' => 'Campinas - SP',
				'83040-540' => 'Curitiba - PR',
				'88015-902' => 'Florianópolis - SC',
				'75133-320' => 'Goiânia - GO',
				'69028-140' => 'Manaus - AM',
				'91350-240' => 'Porto Alegre - RS',
				'21020-190' => 'Rio de Janeiro - RJ',
				'04348-070' => 'São Paulo - SP',
			);

			foreach ($list_of_cities as $city => $value) :
				if ($city == $zip_from) :
					$city_origin = $value;
				elseif ($city == $zip_to) :
					$city_destiny = $value;
				endif;
			endforeach;

			return $this->bao_create_order_from_form($price, $delivery_days, $city_origin, $city_destiny, $zip_from, $zip_to, $n_volumes, $weight, $value);
		endif;
		
	}

	/**
	 * Create product with rest api
	 * @param $price, $delivery_days, $zip_from, $zip_to, $n_volumes, $weight, $value
	 * 
	 * @since 1.0.0
	 */
	public function bao_create_order_from_form($price, $delivery_days, $city_origin, $city_destiny, $zip_from, $zip_to, $n_volumes, $weight, $value)
	{

		// $wc_public_key 	= get_option('brix-woocomerce-public-key');
		// $wc_secret_key 	= get_option('brix-woocomerce-secret-key');
		
		$wc_public_key 	= 'ck_9fb5a8f1e1643f764dc8068b1f8c643a38434a9d';
		$wc_secret_key 	= 'cs_3d38eb415627a1357cfd834d11a7c34c7557a6c9';
		
		// echo $wc_public_key . ' : ' . $wc_secret_key . '</br>';

		// echo 'Preço: ' . $price . ' - Delivery days: ' . $delivery_days . ' - Zip From: ' . $zip_from . ' - Zip to: ' . $zip_to . ' - N Vol: ' . $n_volumes . ' - Weight: ' . $weight . ' - Value: ' . $value . '</br>';
		if (empty($wc_public_key) || empty($wc_secret_key)) :
			echo 'Erro ao conectar com a API rest do Woocomerce. Por favor, configure as chaves publica e privada no plugin BRIX.';
		endif;

		# SETUP PRODUCT
		$title_propduct 		= 'FRETE: Origem ' . $city_origin . ' - Destino ' . $city_destiny;
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

		if ( wp_remote_retrieve_response_message($api_response) === 'Created' ) :
			
			// Prevent duplicated item on cart
			$curr_cart = WC()->cart->get_cart();
			if ( !empty($curr_cart) ) :	
				foreach ($curr_cart as $item) :
					if ($item['line_total'] == $body->regular_price) :
						return;
					endif;
				endforeach;
			endif;

			$this->add_product_to_cart($body->id);
    
		else :

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

	/**
	 * Insert css styles for loader into header
	 * 
	 * @since 1.0.0
	 */
	public function insert_style_loader()
	{
		?>
		<style>
		.lds-grid {
			display: inline-block;
			position: relative;
			width: 80px;
			height: 80px;
			margin-left: calc(100% / 2 - 40px);
			display: none;
		}
		.lds-grid div {
			position: absolute;
			width: 16px;
			height: 16px;
			border-radius: 50%;
			background: #e4b040;
			animation: lds-grid 1.2s linear infinite;
		}
		.lds-grid div:nth-child(1) {
			top: 8px;
			left: 8px;
			animation-delay: 0s;
		}
		.lds-grid div:nth-child(2) {
			top: 8px;
			left: 32px;
			animation-delay: -0.4s;
		}
		.lds-grid div:nth-child(3) {
			top: 8px;
			left: 56px;
			animation-delay: -0.8s;
		}
		.lds-grid div:nth-child(4) {
			top: 32px;
			left: 8px;
			animation-delay: -0.4s;
		}
		.lds-grid div:nth-child(5) {
			top: 32px;
			left: 32px;
			animation-delay: -0.8s;
		}
		.lds-grid div:nth-child(6) {
			top: 32px;
			left: 56px;
			animation-delay: -1.2s;
		}
		.lds-grid div:nth-child(7) {
			top: 56px;
			left: 8px;
			animation-delay: -0.8s;
		}
		.lds-grid div:nth-child(8) {
			top: 56px;
			left: 32px;
			animation-delay: -1.2s;
		}
		.lds-grid div:nth-child(9) {
			top: 56px;
			left: 56px;
			animation-delay: -1.6s;
		}
		@keyframes lds-grid {
			0%, 100% {
				opacity: 1;
			}
			50% {
				opacity: 0.5;
			}
		}
		</style>
		<?php
	}

}
