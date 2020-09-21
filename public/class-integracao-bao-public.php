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

		add_action('wp_ajax_bao_update_product_freight', array($this, 'bao_update_product_freight'));
		add_action('wp_ajax_nopriv_bao_update_product_freight', array($this, 'bao_update_product_freight'));

		// Popup delivery data		
		add_action('bao_popups_delivery_data', array($this, 'popups_collect_delivery_data'));
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
		wp_enqueue_style( 'uikit', plugin_dir_url( __FILE__ ) . 'css/uikit.min.css', array(), $this->version, 'all' );
		
		if(is_checkout()){
			wp_enqueue_style('bao-checkout-custom', plugin_dir_url( __FILE__ ) . 'css/integracao-bao-checkout.css', array(), $this->version, 'all');
		}

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
		wp_enqueue_script( 'uikit', plugin_dir_url( __FILE__ ) . 'js/uikit.min.js', array(), $this->version, false );
		wp_enqueue_script( 'jquery-mask', plugin_dir_url( __FILE__ ) . 'js/jquery.mask.js', array(), '1.14.16', true);

		// Insert styles on head
		add_action('wp_head', array($this, 'insert_style_loader'));

		// insert scripts on footer
		if (is_checkout() || is_cart()) :
			add_action('wp_footer', array($this, 'bao_ajax_requests'));
		endif;

	}

	/**
	 * Function for the form of cotação
	 * 
	 * @since 1.0.0
	 */
	public function form_bao_cotacao($atts)
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
	public function handle_data_form($atts) 
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

			foreach ($list_of_cities as $zip => $city) :
				if ($zip == $zip_from) :
					$city_origin = $city;
				elseif ($zip == $zip_to) :
					$city_destiny = $city;
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
			$post_id = $body->id;

			// Updates post meta itens cotaçao
			update_post_meta($post_id, 'bao_product_zip_origin', $zip_from);
			update_post_meta($post_id, 'bao_product_zip_destiny', $zip_to);
			update_post_meta($post_id, 'bao_product_volumes', $n_volumes);
			update_post_meta($post_id, 'bao_product_weight', $weight);
			update_post_meta($post_id, 'bao_product_value', $value);
			update_post_meta($post_id, 'bao_product_delivery_days', $delivery_days);

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

	/**
	 * Insert forms of origin x destiny address for each item on cart
	 * mostrar um form de coleta e entrega para cada item no carrinho
	 */
	public function popups_collect_delivery_data($atts)
	{
		// Campos coleta e entrega no form
		$curr_cart = WC()->cart->get_cart_contents();

		if ( !empty($curr_cart) ) :
			foreach ($curr_cart as $product) :
				$post_id 	= $product['product_id'];
				Integracao_Bao_Public::tpl_address_form($post_id);
			endforeach;
		endif;
		return false;
	}

	/**
	 * Template form for delivery data for each item on cart
	 * 
	 * @since 1.0.0
	 */
	public function tpl_address_form($product_id)
	{
		// Get current data of collect
		$collect_fullname 		= get_post_meta($product_id, 'bao_product_collect_name', true);
		$collect_tel 			= get_post_meta($product_id, 'bao_product_collect_phone', true);
		$collect_city 			= get_post_meta($product_id, 'bao_product_collect_city', true);
		$collect_neighborhood 	= get_post_meta($product_id, 'bao_product_collect_neighborhood', true);
		$collect_address 		= get_post_meta($product_id, 'bao_product_collect_address', true);
		$collect_cep 			= get_post_meta($product_id, 'bao_product_collect_zip', true);
		$collect_number 		= get_post_meta($product_id, 'bao_product_collect_number', true);
		$collect_complement 	= get_post_meta($product_id, 'bao_product_collect_complement', true);

		// Get current data of shipping
		$shipping_fullname 		= get_post_meta($product_id, 'bao_product_shipping_name', true);
		$shipping_tel 			= get_post_meta($product_id, 'bao_product_shipping_phone', true);
		$shipping_city 			= get_post_meta($product_id, 'bao_product_shipping_city', true);
		$shipping_neighborhood 	= get_post_meta($product_id, 'bao_product_shipping_neighborhood', true);
		$shipping_address 		= get_post_meta($product_id, 'bao_product_shipping_address', true);
		$shipping_cep 			= get_post_meta($product_id, 'bao_product_shipping_zip', true);
		$shipping_number 		= get_post_meta($product_id, 'bao_product_shipping_number', true);
		$shipping_complement 	= get_post_meta($product_id, 'bao_product_shipping_complement', true);
		?>
		<!-- This is the modal -->
		<div id="modal-<?php echo $product_id; ?>" class="" uk-modal>
			<div class="uk-modal-dialog uk-modal-body">
				<h3><?php echo "#" . $product_id; ?> - Preencha com dados de coleta e entrega</h3>
				<div class="uk-column-1-1 bao_wrapper_form">
					<!-- Form coleta -->
					<div class="">
						<h4>Dados coleta</h4>
						<div class="uk-column-1-2">
							<div class="form-group">
								<label for="bao_collect_fullname">Nome completo</label>
								<input type="text" class="form-control" id="bao_collect_fullname_<?php echo $product_id; ?>" aria-describedby="name_help" value="<?php echo !empty($collect_fullname) ? $collect_fullname : '' ?>" required>
								<small id="name_help" class="form-text text-muted"></small>
							</div>
							<div class="form-group">
								<label for="bao_collect_tel">Telefone/Whatsapp</label>
								<input type="tel" class="form-control" id="bao_collect_tel_<?php echo $product_id; ?>" aria-describedby="tel_help" value="<?php echo !empty($collect_tel) ? $collect_tel : '' ?>" required>
								<small id="tel_help" class="form-text text-muted"></small>
							</div>
							<div class="form-group">
								<label for="bao_collect_city">Cidade</label>
								<input type="tel" class="form-control" id="bao_collect_city_<?php echo $product_id; ?>" aria-describedby="city_help" value="<?php echo !empty($collect_city) ? $collect_city : '' ?>" required>
								<small id="city_help" class="form-text text-muted"></small>
							</div>
							<div class="form-group">
								<label for="bao_collect_neighborhood">Bairro</label>
								<input type="text" class="form-control" id="bao_collect_neighborhood_<?php echo $product_id; ?>" aria-describedby="neighborhood_help" value="<?php echo !empty($collect_neighborhood) ? $collect_neighborhood : '' ?>" required>
								<small id="neighborhood_help" class="form-text text-muted"></small>
							</div>
							<div class="form-group">
								<label for="bao_collect_address">Endereço</label>
								<input type="text" class="form-control" id="bao_collect_address_<?php echo $product_id; ?>" aria-describedby="address_help" value="<?php echo !empty($collect_address) ? $collect_address : '' ?>" required>
								<small id="address_help" class="form-text text-muted"></small>
							</div>
							<div class="form-group">
								<label for="bao_collect_cep">CEP</label>
								<input type="text" class="form-control" id="bao_collect_cep_<?php echo $product_id; ?>" aria-describedby="cep_help" value="<?php echo !empty($collect_cep) ? $collect_cep : '' ?>" required>
								<small id="cep_help" class="form-text text-muted"></small>
							</div>
							<div class="form-group">
								<label for="bao_collect_number">Número</label>
								<input type="number" class="form-control" id="bao_collect_number_<?php echo $product_id; ?>" aria-describedby="number_help" value="<?php echo !empty($collect_number) ? $collect_number : '' ?>" required>
								<small id="number_help" class="form-text text-muted"></small>
							</div>
							<div class="form-group">
								<label for="bao_collect_complement">Complemento</label>
								<input type="text" class="form-control" id="bao_collect_complement_<?php echo $product_id; ?>" aria-describedby="complement_help" value="<?php echo !empty($collect_complement) ? $collect_complement : '' ?>">
								<small id="complement_help" class="form-text text-muted"></small>
							</div>
						</div>
					</div>

					<!-- Form Entrega -->
					<div class="">
						<h4>Dados entrega</h4>
						<div class="uk-column-1-2">
							<div class="form-group ">
								<label for="bao_shipping_fullname">Nome completo</label>
								<input type="text" class="form-control" id="bao_shipping_fullname_<?php echo $product_id; ?>" aria-describedby="name_help" value="<?php echo !empty($shipping_fullname) ? $shipping_fullname : '' ?>" required>
								<small id="name_help" class="form-text text-muted"></small>
							</div>
							<div class="form-group ">
								<label for="bao_shipping_tel">Telefone/Whatsapp</label>
								<input type="tel" class="form-control" id="bao_shipping_tel_<?php echo $product_id; ?>" aria-describedby="tel_help" value="<?php echo !empty($shipping_tel) ? $shipping_tel : '' ?>" required>
								<small id="tel_help" class="form-text text-muted"></small>
							</div>
							<div class="form-group ">
								<label for="bao_shipping_city">Cidade</label>
								<input type="tel" class="form-control" id="bao_shipping_city_<?php echo $product_id; ?>" aria-describedby="city_help" value="<?php echo !empty($shipping_city) ? $shipping_city : '' ?>" required>
								<small id="city_help" class="form-text text-muted"></small>
							</div>
							<div class="form-group ">
								<label for="bao_shipping_neighborhood">Bairro</label>
								<input type="text" class="form-control" id="bao_shipping_neighborhood_<?php echo $product_id; ?>" aria-describedby="neighborhood_help" value="<?php echo !empty($shipping_neighborhood) ? $shipping_neighborhood : '' ?>" required>
								<small id="neighborhood_help" class="form-text text-muted"></small>
							</div>
							<div class="form-group ">
								<label for="bao_shipping_address">Endereço</label>
								<input type="text" class="form-control" id="bao_shipping_address_<?php echo $product_id; ?>" aria-describedby="address_help" value="<?php echo !empty($shipping_address) ? $shipping_address : '' ?>" required>
								<small id="address_help" class="form-text text-muted"></small>
							</div>
							<div class="form-group ">
								<label for="bao_shipping_cep">CEP</label>
								<input type="text" class="form-control" id="bao_shipping_cep_<?php echo $product_id; ?>" aria-describedby="cep_help" value="<?php echo !empty($shipping_cep) ? $shipping_cep : '' ?>" required>
								<small id="cep_help" class="form-text text-muted"></small>
							</div>
							<div class="form-group col-2">
								<label for="bao_shipping_number">Número</label>
								<input type="number" class="form-control" id="bao_shipping_number_<?php echo $product_id; ?>" aria-describedby="number_help" value="<?php echo !empty($shipping_number) ? $shipping_number : '' ?>" required>
								<small id="number_help" class="form-text text-muted"></small>
							</div>
							<div class="form-group col-10">
								<label for="bao_shipping_complement">Complemento</label>
								<input type="text" class="form-control" id="bao_shipping_complement_<?php echo $product_id; ?>" aria-describedby="complement_help" value="<?php echo !empty($shipping_complement) ? $shipping_complement : '' ?>">
								<small id="complement_help" class="form-text text-muted"></small>
							</div>
						</div>
					</div>
					
				</div>
				<button type="submit" class="btn btn-primary" onclick="send_form_data(jQuery(this), <?php echo $product_id; ?>)">Salvar pedido</button>
				
				<!-- Messages -->
				<div class="form-messages">
					<div id="error-message" class="uk-alert-warning" style="display: none;" uk-alert>	
						<p>Por favor, preencha corretamente os campos em vermelho para salvar.</p>
					</div>

					<div id="success-message" class="uk-alert-success" style="display: none;" uk-alert>
						<p>Os dados de coleta e entrega foram salvos com sucesso!</p>
					</div>
				</div>
			</div>
			<!-- <button class="uk-modal-close" type="button">Fechar</button> -->
		</div>

		<?php
	}


	/**
	 * Insert ajax script on pages
	 * 
	 * @since 1.0.0
	 */
	public function bao_ajax_requests()
	{
		?>
		<script>
			function send_form_data(el, product_id)
			{
				var self = el;

				// Dados coleta
				var coll_full_name = jQuery('#bao_collect_fullname_' + product_id).val(),
					coll_tel = jQuery('#bao_collect_tel_' + product_id).val(),
					coll_city = jQuery('#bao_collect_city_' + product_id).val(),
					coll_neighborhood = jQuery('#bao_collect_neighborhood_' + product_id).val(),
					coll_address = jQuery('#bao_collect_address_' + product_id).val(),
					coll_cep = jQuery('#bao_collect_cep_' + product_id).val(),
					coll_number	= jQuery('#bao_collect_number_' + product_id).val(),
					coll_complement = jQuery('#bao_collect_complement_' + product_id).val();

				// Building string for collect data
				var collectDataSend = coll_full_name + '-|-' + coll_tel + '-|-' + coll_city + '-|-' + coll_neighborhood + '-|-' + coll_address + '-|-' + coll_cep + '-|-' + coll_number + '-|-' + coll_complement;
				var name = jQuery('#bao_collect_fullname_' + product_id).val();

				// Dados entrega
				var shipping_full_name = jQuery('#bao_shipping_fullname_' + product_id).val(),
					shipping_tel = jQuery('#bao_shipping_tel_' + product_id).val(),
					shipping_city = jQuery('#bao_shipping_city_' + product_id).val(),
					shipping_neighborhood = jQuery('#bao_shipping_neighborhood_' + product_id).val(),
					shipping_address = jQuery('#bao_shipping_address_' + product_id).val(),
					shipping_cep = jQuery('#bao_shipping_cep_' + product_id).val(),
					shipping_number = jQuery('#bao_shipping_number_' + product_id).val(),
					shipping_complement = jQuery('#bao_shipping_complement_' + product_id).val();

				// Build string for shipping data
				var shippingDataSend = shipping_full_name + '-|-' + shipping_tel + '-|-' + shipping_city + '-|-' + shipping_neighborhood + '-|-' + shipping_address + '-|-' + shipping_cep + '-|-' + shipping_number + '-|-' + shipping_complement;
				
				// Another data
				var action_name = 'bao_update_product_freight';


				// Teste validaçao dos inputs
				var arrInputsA = [];
				arrInputsA.push(jQuery('#bao_collect_fullname_' + product_id));
				arrInputsA.push(jQuery('#bao_collect_tel_' + product_id));
				arrInputsA.push(jQuery('#bao_collect_city_' + product_id));
				arrInputsA.push(jQuery('#bao_collect_neighborhood_' + product_id));
				arrInputsA.push(jQuery('#bao_collect_address_' + product_id));
				arrInputsA.push(jQuery('#bao_collect_cep_' + product_id));
				arrInputsA.push(jQuery('#bao_collect_number_' + product_id));
				arrInputsA.push(jQuery('#bao_collect_complement_' + product_id));
				arrInputsA.push(jQuery('#bao_shipping_fullname_' + product_id));
				arrInputsA.push(jQuery('#bao_shipping_tel_' + product_id));
				arrInputsA.push(jQuery('#bao_shipping_city_' + product_id));
				arrInputsA.push(jQuery('#bao_shipping_neighborhood_' + product_id));
				arrInputsA.push(jQuery('#bao_shipping_address_' + product_id));
				arrInputsA.push(jQuery('#bao_shipping_cep_' + product_id));
				arrInputsA.push(jQuery('#bao_shipping_number_' + product_id));
				arrInputsA.push(jQuery('#bao_shipping_complement_' + product_id));

				var invalidInputs = 0;
				arrInputsA.forEach(function(el)
				{
					if (el.val().length < 3)
					{
						el.css({'border': '1px solid red'});
						invalidInputs++;
					}
					else
					{
						console.log(el.attr('id') + " Válido!");
						el.css({'border': '1px solid green'});
					}
				});


				
				// Send to backend
				if (invalidInputs == 0 ) {
					jQuery.ajax({
						url: '<?php echo admin_url('admin-ajax.php'); ?>',
						type: 'POST',
						data: {
							'action': action_name,
							'post_id': product_id,
							'dataCollect': collectDataSend, // String
							'dataShipping': shippingDataSend, // String
						},
						beforeSend: function(){
							console.log('Enviando...');
						},
						success: function(data)
						{
							self.siblings('h3').fadeOut();4
							self.siblings('.bao_wrapper_form').fadeOut();
							self.siblings('.form-messages').find('#success-message').fadeIn();
							self.fadeOut();
						},
						error: function(data)
						{
							console.log('Erro! ' + data.erro);
						},
						complete: function(data)
						{
							console.log('Requisição efeituada com sucesso!');
						}
					});
				} else {
					console.log(invalidInputs + " Inválido(s)!");
					jQuery('#error-message').fadeIn();
				}
			}

			function checkRequiredFields(val)
			{
				if (val.length > 1) {
					// console.log('Válido: ' + val);
				}
				else
				{
					// console.log('Invalido: ' + val);
				}
			}
		</script>
		<?php
	}


	/**
	 * Update product meta's for delivery
	 * 
	 * @since 1.0.0
	 */
	public function bao_update_product_freight()
	{
		if(!empty($_POST)) :
			$post_id = $_POST['post_id'];
			$data_collect = $_POST['dataCollect'];
			$data_shipping = $_POST['dataShipping'];

			$extract_collect_data = explode('-|-', $data_collect);
			$extract_shipping_data = explode('-|-', $data_shipping);

			$keys_collect_data = array(
				'bao_product_collect_name',
				'bao_product_collect_phone',
				'bao_product_collect_city',
				'bao_product_collect_neighborhood',
				'bao_product_collect_address',
				'bao_product_collect_zip',
				'bao_product_collect_number',
				'bao_product_collect_complement'
			);

			$keys_shipping_data = array(
				'bao_product_shipping_name',
				'bao_product_shipping_phone',
				'bao_product_shipping_city',
				'bao_product_shipping_neighborhood',
				'bao_product_shipping_address',
				'bao_product_shipping_zip',
				'bao_product_shipping_number',
				'bao_product_shipping_complement'
			);

			// Updating collect data
			if (!empty($extract_collect_data)) :
				$count = 0;
				foreach ($extract_collect_data as $data) :
					if (!empty($data)) :
						update_post_meta($post_id, $keys_collect_data[$count], trim($data));
						// echo ' - ' . get_post_meta($post_id, $keys_collect_data[$count], true);
					endif;
					$count++;
				endforeach;
			endif;

			// Updating shipping data
			if (!empty($extract_shipping_data)) :
				$count = 0;
				foreach ($extract_shipping_data as $data) :
					if (!empty($data)) :
						update_post_meta($post_id, $keys_shipping_data[$count], trim($data));
						// echo ' - ' . get_post_meta($post_id, $keys_shipping_data[$count], true);
					endif;
					$count++;
				endforeach;
			endif;

		endif;

		die();
	}
}
