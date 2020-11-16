<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       unitycode.tech
 * @since      1.0.0
 *
 * @package    Integracao_Bao
 * @subpackage Integracao_Bao/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Integracao_Bao
 * @subpackage Integracao_Bao/admin
 * @author     jnz93 <joanez@unitycode.tech>
 */
class Integracao_Bao_Admin {

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

		// Actions
		add_action('admin_menu', array($this, 'create_settings_menu'));
		add_action('admin_post_create_minutas', array($this, 'send_minutas_tms_ajax'));

		// Ajax Actions
		add_action('wp_ajax_save_minuta_id_on_product', array($this, 'save_minuta_id_on_product')); //Salvar o id da minuta no produto
		add_action('wp_ajax_nopriv_save_minuta_id_on_product', array($this, 'save_minuta_id_on_product')); //Salvar o id da minuta no produto
		
		add_action('wp_ajax_update_data_minuta_on_product', array($this, 'update_data_minuta_on_product')); // Update dos dados da minuta no produto
		add_action('wp_ajax_nopriv_update_data_minuta_on_product', array($this, 'update_data_minuta_on_product')); // Update dos dados da minuta no produto

		add_action('wp_ajax_save_coleta_data_form', array($this, 'update_coleta')); // Update dos dados da minuta no produto
		add_action('wp_ajax_nopriv_save_coleta_data_form', array($this, 'update_coleta')); // Update dos dados da minuta no produto

		add_action('wp_ajax_save_entrega_data_form', array($this, 'update_entrega')); // Update dos dados da minuta no produto
		add_action('wp_ajax_nopriv_save_entrega_data_form', array($this, 'update_entrega')); // Update dos dados da minuta no produto

		add_action('wp_ajax_save_minuta_error', array($this, 'save_minuta_error')); // Salvar erro ao gerar minuta
		add_action('wp_ajax_nopriv_save_minuta_error', array($this, 'save_minuta_error')); // Salvar erro ao gerar minuta
		
		add_action('wp_ajax_create_minutas_ajax', array($this, 'send_minutas_tms_ajax')); // Gerar minutas via ajax

		add_action('wp_ajax_bao_save_settings', array($this, 'bao_save_settings')); //Salvar o id da minuta no produto

		// Action to insert new colunm and value of minuta id on order edit
		add_action( 'woocommerce_admin_order_item_headers', array($this, 'bao_admin_order_items_headers'), 10, 1 );
		add_action( 'woocommerce_admin_order_item_values', array($this, 'bao_admin_order_item_values'), 10, 1 );

		add_action('edit_form_advanced', array($this, 'render_table_coleta_entrega_admin_edit_order'));
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/integracao-bao-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'uikit', plugin_dir_url( __FILE__ ) . 'css/uikit.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/integracao-bao-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'uikit', plugin_dir_url( __FILE__ ) . 'js/uikit.min.js', array(), $this->version, false );

	}


	/**
	 * Register menu admin of the plugin
	 * 
	 * @since 1.0.0
	 */
	public function create_settings_menu()
	{
		$page_title = 'Integração BAO';
		$menu_title = 'Integração BAO';
		$menu_slug 	= 'integracao-bao';
		$capability = 10;
		$icon_url 	= plugin_dir_url(__FILE__) . '/images/brixlogo.png';
		$position 	= 10;

		add_menu_page($page_title, $menu_title, $capability, $menu_slug, array($this, 'page_settings_plugin'), $icon_url, $position);
	}

	/**
	 * Create page of settings plugin
	 * 
	 * @since 1.0.0
	 */
	public function page_settings_plugin()
	{
		require( plugin_dir_path(__FILE__) . 'partials/integracao-bao-admin-display.php');
	}

	/**
	 * Function bao_save_settings
	 * Recebe os valores a serem salvos via ajax e executa ações de update option
	 * 
	 * @since 1.2.1
	 */
	public function bao_save_settings()
	{
		$data 	= $_POST['data'];
		$arr 	= explode('|', $data);

		foreach($arr as $item) :
			$item 	= explode(':', $item);
			$key 	= $item[0];
			$value 	= $item[1];

			update_option( $key, $value );
		endforeach;
	}

	/**
	 * Aunthentication login in brix brudam api rest
	 * 
	 * @return access_token
	 */
	public function login_brudam_api()
	{
		# SETTINGS API REST
		$brudam_api_url = 'https://brix.brudam.com.br/api/v1/acesso/auth/login';
		$brix_user 		= '94a708524b9c35be089b3069280ef1ed';
		$brix_pass 		= '420caa5cc5931a4126d4120f67a0a22464eeb8052b9c9f62f15fd70905dd5fdc';
		
		if (empty($brix_user) || empty($brix_pass)) :
			echo 'Usuário ou senha não configurados.';
			return;		
		endif;

		# REQUEST
		$api_response = wp_remote_post(
			$brudam_api_url,
			array(
				'body'		=> array(
					'usuario'	=> $brix_user,
					'senha'		=> $brix_pass
				),
			)
		);
		$response = json_decode($api_response['body']);

		if (wp_remote_retrieve_response_message($api_response) === 'OK') :
			return $response->data->access_key;
		else :
			return 'error';
		endif;
	}

	/**
	 * Registra nova minuta do pedido no sistema brix/brudam e salva o ID da minuta no respectivo produto/post
	 * 
	 * @return void
	 */
	public function send_order_to_brix_brudam()
	{
		?>
		<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
		<link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ ) . 'css/uikit.min.css'; ?>">
		<script type="text/javascript">
		function sendMinutaIdToBackEnd(minutaId, postId, ajaxUrl)
		{
			var minutaId = minutaId, 
				postId = postId,
				ajaxUrl = ajaxUrl;
			
			let actionWp = 'save_minuta_id_on_product';

			jQuery.ajax({
				url: ajaxUrl,
				type: 'POST',
				data: {
					'action': actionWp,
					'minuta_id': minutaId,
					'post_id': postId
				},
				success: function(data)
				{
					console.log('ID Minuta #'+ minutaId +' foi salvo na cotação #'+ postId +' com sucesso!');
					console.log(data);
				},
				error: function(err)
				{
					console.log(err);
				}
			});
		}
		</script>
		<?php
		$access_token 	= Integracao_Bao_Admin::login_brudam_api();
		$orders_to_send = Integracao_Bao_Admin::bao_verify_order_paid();
		$brudam_api_url = 'https://brix.brudam.com.br/api/v1/operacional/emissao/cte';
		
		if (empty($access_token)) :
			return;
		endif;

		if (!empty($orders_to_send)) :
			$orders_already = get_option('_bao_orders_already_sent_to_brix');

			$str_orders_id 	= '';
			foreach ($orders_to_send as $order) :
				$order_id 			= $order['ID'];
				$order_date 		= $order['date_created'];

				$order_obj 			= wc_get_order($order_id);
				$order_items		= $order_obj->get_items();

				if (!is_wp_error($order_items)) :
					?>
					<script>
						var product_id = '';
					</script>
					<?php
					foreach ($order_items as $item) :
						$product_id = $item->get_product_id();
						$total 		= $item->get_total();
						
						// Get collect data
						$keys_collect = array(
							'bao_product_collect_name',
							'bao_product_collect_phone',
							'bao_product_collect_doc',
							'bao_product_collect_city',
							'bao_product_collect_neighborhood',
							'bao_product_collect_address',
							'bao_product_collect_zip',
							'bao_product_collect_number',
							'bao_product_collect_complement'
						);
						
						$collect_data = array();
						foreach ($keys_collect as $data) :
							$arr = explode('_', $data);
							$collect_data[end($arr)] = get_post_meta($product_id, $data, true);
						endforeach;

						// Get shipping data
						$keys_shipping = array(
							'bao_product_shipping_name',
							'bao_product_shipping_phone',
							'bao_product_shipping_doc',
							'bao_product_shipping_city',
							'bao_product_shipping_neighborhood',
							'bao_product_shipping_address',
							'bao_product_shipping_zip',
							'bao_product_shipping_number',
							'bao_product_shipping_complement'
						);

						$shipping_data = array();
						foreach ($keys_shipping as $data) :
							$arr = explode('_', $data);
							$shipping_data[end($arr)] = get_post_meta($product_id, $data, true);
						endforeach;

						// CIDADE 			| CEP/ZIP 	|Cod. IBGE	| Cod. ICAO
						// São Paulo 		| 04348070 	| 3550308 	| SBSP
						// Porto Alegre 	| 91350240 	| 4314902 	| SBPA
						// Belo Horizonte 	| 31270700 	| 3106200 	| SBCF
						// Brasília 		| 71608900 	| 5300108 	| SBBR
						// Campinas 		| 13051154 	| 3509502 	| SBKP
						// Curitiba 		| 83040540 	| 4106902 	| SBCT
						// Florianópolis 	| 88015902 	| 4205407 	| SBFL
						// Goiânia 			| 75133320 	| 5208707 	| SBGO
						// Manaus 			| 69028140 	| 1302603 	| SBEG
						// Rio de Janeiro 	| 21020190 	| 3304557 	| SBGL

						$zip_configs = array(
							'04348-070'	=> ['3550308', 'SBSP'], #São Paulo
							'91350-240'	=> ['4314902', 'SBPA'], #Porto Alegre
							'31270-700'	=> ['3106200', 'SBCF'], #Belo Horizonte
							'71608-900'	=> ['5300108', 'SBBR'], #Brasília
							'13051-154'	=> ['3509502', 'SBKP'], #Campinas
							'83040-540'	=> ['4106902', 'SBCT'], #Curitiba
							'88015-902'	=> ['4205407', 'SBFL'], #Florianópolis
							'75133-320'	=> ['5208707', 'SBGO'], #Goiânia
							'69028-140'	=> ['1302603', 'SBEG'], #Manaus
							'21020-190'	=> ['3304557', 'SBGL']  #Rio De Janeiro
						);

						foreach($zip_configs as $zip => $data) :
							if ($zip == trim($collect_data['zip'])) :
								$ibge_orig = $data[0];
								$icao_orig = $data[1];
							endif;
							if ($zip == trim($shipping_data['zip'])) :
								$ibge_dest = $data[0];
								$icao_dest = $data[1];
							endif;
						endforeach;
						
						$weight 				= get_post_meta($product_id, 'bao_product_weight', true);
						$volumes 				= get_post_meta($product_id, 'bao_product_volumes', true);
						$days_delivery 			= get_post_meta($product_id, 'bao_product_delivery_days', true);
						$obs_merchandise 		= get_post_meta($product_id, 'bao_product_merchandise', true);

						$now 					= date('Y-m-d H:i:s'); //Padrão 2020-09-04 23:00:00
						$date					= new DateTime($now);
						$date->modify('+'. $days_delivery .' day');
						$expected_day 			= $date->format( 'Y-m-d' );
						$expected_time 			= $date->format( 'H:i:s' );
						?>
						<script type="text/javascript">
							var today 			= '<?php echo $now; ?>',
								expectedDay 	= '<?php echo $expected_day; ?>',
								expectedTime 	= '<?php echo $expected_time; ?>',
								weight 			= '<?php echo $weight; ?>',
								volumes 		= '<?php echo $volumes; ?>',
								days_delivery 	= '<?php echo $days_delivery; ?>',
								obsMerchandise 	= '<?php echo $obs_merchandise; ?>',
								total 			= '<?php echo $total; ?>';

							// Dados BAO
							var tomName 	= 'BAO Serviços',
								tomFant 	= 'BAO',
								tomDoc 		= '11255202000109',
								tomDocIE	= '513048395113',
								tomLgr 		= 'Avenida Cruzeiro',
								tomNro 		= '300',
								tomBairro 	= 'Distrito Industrial',
								tomCodMun 	= '4303103',
								tomCEP 		= '94930-615',
								tomCpl		= 'Sala 02';

							// dados remetente
							var remDoc 		= '<?php echo $collect_data['doc'] ?>',
								remName 	= '<?php echo $collect_data['name']; ?>',
								remFant 	= '<?php echo $collect_data['name']; ?>',
								remPhone	= '<?php echo $collect_data['phone']; ?>',
								remLgr 		= '<?php echo $collect_data['address']; ?>',
								remNro 		= '<?php echo $collect_data['number']; ?>',
								remBairro 	= '<?php echo $collect_data['neighborhood']; ?>',
								remCpl 		= '<?php echo $collect_data['complement']; ?>',
								remCEP 		= '<?php echo $collect_data['zip']; ?>',
								remCodIBGE 	= '<?php echo $ibge_orig; ?>',
								remCodICAO 	= '<?php echo $icao_orig; ?>';

							// dados destinatario
							var destDoc 	= '<?php echo $shipping_data['doc']; ?>',
								destName 	= '<?php echo $shipping_data['name']; ?>',
								destFant 	= '<?php echo $shipping_data['name']; ?>',
								destPhone	= '<?php echo $shipping_data['phone']; ?>',
								destLgr 	= '<?php echo $shipping_data['address']; ?>',
								destNro 	= '<?php echo $shipping_data['number']; ?>',
								destBairro 	= '<?php echo $shipping_data['neighborhood']; ?>',
								destCpl 	= '<?php echo $shipping_data['complement']; ?>',
								destCEP 	= '<?php echo $shipping_data['zip']; ?>',
								destCodIBGE = '<?php echo $ibge_dest; ?>',
								destCodICAO = '<?php echo $icao_dest; ?>';

							var	ajaxAdminUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
							product_id = '<?php echo $product_id; ?>';
							jQuery.ajax({
								method: "POST",
								headers: {
									'Authorization': '<?php echo 'Bearer ' . $access_token; ?>'
								},
								url: '<?php echo $brudam_api_url ?>',
								data: {
									"documentos" : [
										{
											"minuta" : {
												"CFOP" : "6353",
												"tpCTe" : "0",
												"toma" : "4",
												"nDocEmit" : "94001641000104",
												"dEmi" : today,
												"rSeg" : 0,
												"cSeg" : "61383493000180",
												"nAver" : "06238022000233065000",
												"cServ" : "38",
												"cTab" : "395",
												"tpEmi" : "1",
												"cAut" : "999999999999",
												"carga" : {
													"pBru" : weight,
													"pCub" : weight,
													"qVol" : volumes,
													"vTot" : total
												}
											},
											"compl" : {
												"entrega" : { 
													"dPrev" : expectedDay,
													"hPrev" : expectedTime
												},
												"cOrigCalc" : remCodICAO, // Cod Aeroporto
												"cDestCalc" : destCodICAO, // Cod Aeroporto
												"xObs" : obsMerchandise
											},
											"toma" : {
												"nDoc" : tomDoc,
												"IE" : tomDocIE,
												"xNome" : tomName,
												"xFant" : tomFant,
												"xLgr" : tomLgr,
												"nro" : tomNro,
												"xCpl" : tomCpl,
												"xBairro" : tomBairro,
												"cMun" : tomCodMun,
												"CEP" : tomCEP,
												"cPais" : "1058",
												"email" : "email@bao.com.br"
											},
											"rem" : {
												"nDoc" : remDoc,
												"IE" : "513048395113",
												"xNome" : remName,
												"xFant" : remFant,
												"nFone" : remPhone,
												"xLgr" : remLgr,
												"nro" : remNro,
												"xBairro" : remBairro,
												"xCpl" : remCpl,
												"cMun" : remCodIBGE,
												"CEP" : remCEP,
												"cPais" : "1058",
												"email" : "email_rem@domain.com"
											},
											"dest" : {
												"nDoc" : destDoc,
												"IE" : "20353243",
												"xNome" : destName,
												"xFant" : destFant,
												"nFone" : destPhone,
												"xLgr" : destLgr,
												"nro" : destNro,
												"xBairro" : destBairro,
												"cMun" : destCodIBGE,
												"CEP" : destCEP,
												"cPais" : "1058",
												"email" : "email_dest@domain.com"
											},
											"valores" : {
												"vFrete" : total,
												"comp" : [
													{
														"xItem" : "peso",
														"vItem" : weight
													},
													{
														"xItem" : "adv",
														"vItem" : "000000000000.00"
													},
													{
														"xItem" : "entrega",
														"vItem" : "000000000000.00"
													},
													{
														"xItem" : "pedagio",
														"vItem" : "000000000000.00"
													},
													{
														"xItem" : "outros",
														"vItem" : "000000000000.00"
													}
												],
												"imp" : {
													"ICMS" : {
														"CST" : "00",
														"vBC" : "000000000000.00",
														"pRedBC" : "000000000000.00",
														"pICMS" : "000000000000.00",
														"vICMS" : "000000000000.00"
													}
												}
											},
											"documentos" : [
												{
													"serie" : "1",
													"nDoc" : "000122893",
													"dEmi" : today,
													"vBC" : "00000",
													"vICMS" : "00000",
													"vBCST" : "00000",
													"vST" : "00000",
													"vProd" : total,
													"vNF" : total,
													"pBru" : weight,
													"qVol" : volumes,
													"chave" : "35191210918425000308550010001228931001141643",
													"tpDoc" : "55",
													"xEsp" : "Diversos",
													"xNat" : "Diversos"
												}
											]
										}
									]
								},
								beforeSend: function(data)
								{
									jQuery('#result-content table tbody').append('<div uk-spinner></div>');
								},
								success: function(response)
								{
									var respArr = response.data;
									product_id = '<?php echo $product_id; ?>';

									respArr.forEach(function(index)
									{
										sendMinutaIdToBackEnd(index.id, product_id, ajaxAdminUrl);
										jQuery('#result-content table tbody').append('<tr><td>'+product_id+'</td><td>'+index.id+'</td></tr>');
									});
								},
								error: function(response)
								{
									sendMinutaErrorToBackEnd(product_id, ajaxAdminUrl)
									console.log('Erro:' + response);
								}
							});
						</script>
						<?php
						$str_orders_id = $str_orders_id . ',' . $order_id;
					endforeach;
				endif;
			endforeach;

			// Update option
			$orders_already = $orders_already . '' . $str_orders_id;
			update_option('_bao_orders_already_sent_to_brix', $orders_already);

			return true;
		else :
			return false;
		endif;
	}

	/**
	 * Function send_alert_verified_orders
	 * 
	 * Envia um email quando executa a rotina de verificar pedidos para enviar ao sistema brix
	 * 
	 * @since 1.0.0
	 */
	public function send_alert_virified_orders()
	{
		Integracao_Bao_Admin::send_order_to_brix_brudam();
	}

	/**
	 * Function send_minutas_tms_ajax
	 * 
	 * @since 1.2.0
	 */
	public function send_minutas_tms_ajax()
	{
		$result = Integracao_Bao_Admin::send_order_to_brix_brudam();
		$table_result = '<div id="result-content">
							<p></p>
							<table class="uk-table uk-table-divider">
								<thead>
									<tr>
										<th>Pedido(s)</th>
										<th>Minuta(s)</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>';

		$message = '<p class="uk-text-normal uk-text-large uk-text-success uk-text-center">Todos os pedidos já foram exportados!</p>';

		if ($result == 1) :
			echo $table_result;
		else :
			echo $message;
		endif;
	}

	/**
	 * Function verify_orders_paid
	 * 
	 * Seleciona pedidos pagos e retorna um array com os ids
	 * 
	 * @return array
	 * 
	 * @since 1.0.0 
	 */

	public function bao_verify_order_paid()
	{
		$str_orders_exclude = get_option('_bao_orders_already_sent_to_brix');
		$orders_not_in = explode(',', $str_orders_exclude);

		$args = array(
			'status' 	=> 'completed',
			'exclude' 	=> $orders_not_in
		);
		$orders_data = wc_get_orders( $args );

		$orders = array();
		if(!is_wp_error($orders_data)) :
			foreach ($orders_data as $order) :
				// if (get_post_meta( $post_id:integer, $key:string, $single:boolean ))
				$orders[] = array(
					'ID' 			=> $order->id,
					'date_created' 	=> $order->date_created,
					'total' 		=> $order->total
				);
			endforeach;
		endif;
		return $orders;
	}

	/**
	 * Function save_minuta_id_on_product()
	 * 
	 * recebe dados via ajax e salva como metacampo no produto/post relacionado
	 * 
	 * @since 1.0.2
	 */
	public function save_minuta_id_on_product()
	{
		if (empty($_POST)) :
			echo 'Nenhum dado encontrado';
			return;
		endif;

		$minuta_id 	= $_POST['minuta_id'];
		$post_id 	= $_POST['post_id'];

		$old_minutas = get_post_meta( $post_id, 'old_minutas_ids', true );
		$new_list = $old_minutas . ',' . $minuta_id;
		update_post_meta( $post_id, 'old_minutas_ids', $new_list );
		update_post_meta($post_id, 'bao_minuta_id', $minuta_id);

		echo 'Minutas Gravadas: ' . get_post_meta( $post_id, 'old_minutas_ids', true );
		die();
	}


	/**
	 * Function save_minuta_error
	 * 
	 * @since 1.1.4
	 */
	public function save_minuta_error()
	{
		if (empty($_POST)) :
			echo 'Nenhum dado encontrado';
			return;
		endif;

		$post_id 	= $_POST['post_id'];
		$err 		= $_POST['err'];

		update_post_meta($post_id, 'bao_minuta_id', $err);
		die();
	}

	/**
	 * Function update_data_minuta_on_product()
	 * Recebe dados via ajax e faz o update das informações relacionadas a minuta(status, etc...)
	 * 
	 * @since 1.0.2
	 */
	public function update_data_minuta_on_product()
	{
		if (empty($_POST)) :
			echo 'Nenhum dado encontrado';
			return;
		endif;

		$post_id 	= $_POST['post_id'];
		$str_data 	= $_POST['data_minuta'];

		$arr_data 	= explode('**', $str_data);

		foreach ($arr_data as $data) :
			$result = explode('_*', $data);
			
			$meta_key = trim($result[0]);
			$meta_value = trim($result[1]);
			
			update_post_meta($post_id, $meta_key, $meta_value);
		endforeach;
		$str_status = explode('_*', $arr_data[5]);
		$status = $str_status[1];
		$result = $post_id . ':' . $status;
		die($result);
	}

	/**
	 * Action: Salvar dados de coleta no produto
	 * 
	 * @since 1.0.3
	 */
	public function update_coleta()
	{
		if(empty($_POST)) :
			die('Dados inválidos');
		endif;
		
		if(empty($_POST['postId'])) :
			die('Post id inválido');
		endif;

		if (empty($_POST['dataForm'])) :
			die('Os dados do formulário são inválidos');
		endif;
		
		$post_id					= $_POST['postId'];
		$data_form 					= $_POST['dataForm'];
		$extract_collect_data 		= explode('-|-', $data_form);

		$keys_collect_data = array(
			'bao_product_collect_name',
			'bao_product_collect_phone',
			'bao_product_collect_doc',
			'bao_product_collect_city',
			'bao_product_collect_neighborhood',
			'bao_product_collect_address',
			'bao_product_collect_zip',
			'bao_product_collect_number',
			'bao_product_collect_complement'
		);

		// Updating collect data
		if (!empty($extract_collect_data)) :
			$count = 0;
			foreach ($extract_collect_data as $data) :
				if (!empty($data)) :
					update_post_meta($post_id, $keys_collect_data[$count], trim($data));
				endif;
				$count++;
			endforeach;
		endif;
		die();
	}

	/**
	 * Action: Salvar dados de entrega no produto
	 * 
	 * @since 1.0.3
	 */
	public function update_entrega()
	{
		if(empty($_POST)) :
			die('Dados inválidos');
		endif;
		
		if(empty($_POST['postId'])) :
			die('Post id inválido');
		endif;

		if (empty($_POST['dataForm'])) :
			die('Os dados do formulário são inválidos');
		endif;
		
		$post_id					= $_POST['postId'];
		$data_form 					= $_POST['dataForm'];
		$extract_shipping_data 		= explode('-|-', $data_form);

		$keys_shipping_data = array(
			'bao_product_shipping_name',
			'bao_product_shipping_phone',
			'bao_product_shipping_doc',
			'bao_product_shipping_city',
			'bao_product_shipping_neighborhood',
			'bao_product_shipping_address',
			'bao_product_shipping_zip',
			'bao_product_shipping_number',
			'bao_product_shipping_complement'
		);

		// Updating shipping data
		if (!empty($extract_shipping_data)) :
			$count = 0;
			foreach ($extract_shipping_data as $data) :
				if (!empty($data)) :
					update_post_meta($post_id, $keys_shipping_data[$count], trim($data));
				endif;
				$count++;
			endforeach;
		endif;
		die();
	}

	/**
	 * Na edição do pedido, na tabela de produtos insere uma nova coluna destinada ao ID da minuta
	 * 
	 * @param $order(obj)
	 * 
	 * @since 1.0.3
	 */
	public function bao_admin_order_items_headers($order){
		?>
		<th class="line_customtitle sortable" data-sort="your-sort-option">Minuta ID</th>
		<th class="line_customtitle sortable" data-sort="your-sort-option">Coleta & Entrega</th>
		<?php
	}

	/**
	 * Na edição do pedido, na tabela de produtos, insere o valor da minuta id na coluna correspondente
	 * 
	 * @param $product(arr)
	 * 
	 * @since 1.0.3
	 */
	public function bao_admin_order_item_values( $product ) {
		$post_id 	= $product->id;
		$minuta_id 	= get_post_meta($post_id, 'bao_minuta_id', true)
		?>
		<td class="line_customtitle"><?php echo $minuta_id ;?></td>
		<td class="line_customtitle">
			<button uk-toggle="target: #product-<?php echo $post_id; ?>" type="button">Ver</button></td>
		</td>
		<?php
	}

	/**
	 * Na edição do pedido, na tabela de produtos, insere o popup com os dados de coleta/entrega de cada cotação no pedido
	 * 
	 * @param $order(obj)
	 * 
	 * @since 1.0.3
	 */
	public function render_table_coleta_entrega_admin_edit_order($order)
	{
		if( get_post_type(get_the_ID()) != 'shop_order') :
			return;
		endif;

		$order_obj 	= new WC_Order($order->ID);
		$products 	= $order_obj->get_items();

		do_action('show_table_coleta_entrega', $products);

		?>
		<style>
			.order_data_column:last-child{
				display: none;
			}
			#order_shipping_line_items{
				display: none;
			}
		</style>
		<?php
	}


	/**
	 * Rotina para atualizar os dados da minuta(status)
	 * 
	 * @since 1.0.3
	 */
	public function update_minutas($order_id)
	{
		$token = Integracao_Bao_Admin::login_brudam_api();
		$ajax_url = admin_url('admin-ajax.php');
		
		$args = array(
			'status' 	=> 'completed',
			'limit'		=> -1, 
		);
		$orders = wc_get_orders( $args );

		foreach ($orders as $order)
		{
			if ($order->get_id() == $order_id) :
				$items = $order->get_items();
				foreach ($items as $item) :
					$product_id = $item['product_id'];
					$minuta_id = get_post_meta($product_id, 'bao_minuta_id', true);
					if(!empty($minuta_id)) :
						?>
						<script>
							var minutaId 	= '<?php echo $minuta_id; ?>',
								postId 		= '<?php echo $product_id; ?>',
								token 		= '<?php echo $token; ?>',
								ajaxUrl 	= '<?php echo $ajax_url; ?>';

							getMinutaByID(minutaId, postId, token, ajaxUrl);
						</script>
						<?php
					endif;
				endforeach;

				$count++;
			endif;
		}
	}

}