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
		// add_action('update_option', array($this, 'save_plugin_options'));
		add_action('admin_init', array($this, 'register_options_settings'));

		if ( ! wp_next_scheduled( 'bao_task_hourly' ) ) {
			wp_schedule_event( time(), 'hourly', 'bao_task_hourly' );
		}
		add_action( 'bao_task_hourly', array($this, 'send_alert_virified_orders')); // 'wpdocs_task_hook` is registered when the event is scheduled
		 
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


	public function register_options_settings()
	{
		$option_group = 'bao_settings_plugin';
		$option_name = 'bao_settings';

		register_setting($option_group, $option_name);
	}
	/**
	 * Function save_plugin_options
	 * 
	 * Fires function when action update_uption has called
	 * 
	 * @since 1.0.0
	 */
	public function save_plugin_options()
	{
		if(isset($_POST['brix_token'])){
			if(get_option('brix_token')){
				update_option('brix_token', trim($_POST['brix_token']));
			}else{
				add_option('brix_token', trim($_POST['brix_token']));
			}      
		}
		
		  
		if(isset($_POST['brix-cotacao-servico'])){
			if(get_option('brix_cotacao_servico')){
				update_option('brix_cotacao_servico', trim($_POST['brix-cotacao-servico']));
			}else{
				add_option('brix_cotacao_servico', trim($_POST['brix-cotacao-servico']));
			}  
		}
	
		if(isset($_POST['brix_cliente'])){
			if(get_option('brix_cliente')){
				update_option('brix_cliente', trim($_POST['brix_cliente']));
			}else{
				add_option('brix_cliente', trim($_POST['brix_cliente']));
			}    
		}
		// update_option('brix-woocomerce-public-key', '34342423');
		// if(!empty($_POST['brix-woocomerce-public-key'])){
		// 	if(get_option('brix-woocomerce-public-key')){
		// 		update_option('brix-woocomerce-public-key', trim($_POST['brix-woocomerce-public-key']));
		// 	}else{
		// 		add_option('brix-woocomerce-public-key', trim($_POST['brix-woocomerce-public-key']));
		// 	}    
		// }
		  
		// if(isset($_POST['brix-woocomerce-secret-key'])){
		// 	if(get_option('brix-woocomerce-secret-key')){
		// 		update_option('brix-woocomerce-secret-key', $_POST['brix-woocomerce-secret-key']);
		// 	}else{
		// 		add_option('brix-woocomerce-secret-key', $_POST['brix-woocomerce-secret-key']);
		// 	}    
		// }
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
	 * Add minuta from brudam api rest
	 * 
	 * @return void
	 */
	public function send_order_to_brix_brudam()
	{
		$access_token 	= Integracao_Bao_Admin::login_brudam_api();
		$brudam_api_url = 'https://brix.brudam.com.br/api/v1/operacional/emissao/cte';
		
		if (empty($access_token)) :
			return;
		endif;
		$today = date("Y-m-d");
		?>
		<script>
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
							"toma" : "0",
							"nDocEmit" : "94001641000104",
							"dEmi" : "2020-06-08",
							"rSeg" : 0,
							"cSeg" : "61383493000180",
							"nAver" : "06238022000233065000",
							"cServ" : "38",
							"cTab" : "395",
							"tpEmi" : "1",
							"cAut" : "999999999999",
							"carga" : {
								"pBru" : "14.33",
								"pCub" : ".03",
								"qVol" : 1,
								"vTot" : "000000013300.52"
							}
						},
						"compl" : {
							"entrega" : {
								"dPrev" : "2020-06-08",
								"hPrev" : "00:00:00"
							},
							"cOrigCalc" : "3536505",
							"cDestCalc" : "2611606",
							"xObs" : "n/"
						},
						"toma" : {
							"nDoc" : "10918425000308",
							"IE" : "513048395113",
							"xNome" : "VOLVO CAR BRASIL IMPORTACAO E COMERCIO D",
							"xFant" : "VOLVO CAR BRASIL IMPORTACAO E COMERCIO D",
							"xLgr" : "Avenida Viena",
							"nro" : "419",
							"xCpl" : "GALPÃO 3.2 SALA 1",
							"xBairro" : "cascata",
							"cMun" : "3536505",
							"CEP" : "13146055",
							"cPais" : "1058",
							"email" : "joao@hotmail.com"
						},
						"rem" : {
							"nDoc" : "10918425000308",
							"IE" : "513048395113",
							"xNome" : "VOLVO CAR BRASIL IMPORTACAO E COMERCIO D",
							"xFant" : "VOLVO CAR BRASIL IMPORTACAO E COMERCIO D",
							"xLgr" : "Avenida Viena",
							"nro" : "419",
							"xBairro" : "cascata",
							"xCpl" : "GALPÃO 3.2 SALA 1",
							"cMun" : "3536505",
							"CEP" : "13146055",
							"cPais" : "1058",
							"email" : "joao2@hotmail.com"
						},
						"dest" : {
							"nDoc" : "18592005000116",
							"IE" : "20353243",
							"xNome" : "BRUDAM DESENVOLVIMENTOS WEB",
							"xFant" : "BRUDAM DESENVOLVIMENTOS WEB",
							"xLgr" : "Itajuba",
							"nro" : "311",
							"xBairro" : "Residencial",
							"cMun" : "4306767",
							"CEP" : "92990000",
							"cPais" : "1058",
							"email" : "joao3@hotmail.com"
						},
						"valores" : {
							"vFrete" : "000000000119.45",
							"comp" : [
							{
								"xItem" : "peso",
								"vItem" : "000000000091.14"
							},
							{
								"xItem" : "adv",
								"vItem" : "000000000013.30"
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
								"vItem" : "000000000008.36"
							}
							],
							"imp" : {
								"ICMS" : {
									"CST" : "00",
									"vBC" : "000000000119.45",
									"pRedBC" : "000000000000.00",
									"pICMS" : "000000000007.00",
									"vICMS" : "000000000119.45"
								}
							}
						},
						"documentos" : [
						{
							"serie" : "1",
							"nDoc" : "000122893",
							"dEmi" : "2019-12-17",
							"vBC" : "00000",
							"vICMS" : "00000",
							"vBCST" : "00000",
							"vST" : "00000",
							"vProd" : "000000000112.88",
							"vNF" : "000000000112.88",
							"pBru" : "00014.330000",
							"qVol" : 1,
							"chave" : "35191210918425000308550010001228931001141643",
							"tpDoc" : "55",
							"xEsp" : "Diversos",
							"xNat" : "Diversos"
						},
						{
							"serie" : "1",
							"nDoc" : "000122894",
							"dEmi" : "2019-12-17",
							"vBC" : "00000",
							"vICMS" : "00000",
							"vBCST" : "00000",
							"vST" : "00000",
							"vProd" : "000000000190.76",
							"vNF" : "000000000190.76",
							"pBru" : "00000.000000",
							"qVol": 1 ,
							"chave" : "35191210918425000308550010001228941001141659",
							"tpDoc" : "55",
							"xEsp" : "Diversos",
							"xNat" : "Diversos"
						},
						{
							"serie" : "1",
							"nDoc" : "000122895",
							"dEmi" : "2019-12-17",
							"vBC" : "00000",
							"vICMS" : "00000",
							"vBCST" : "00000",
							"vST" : "00000",
							"vProd" : "000000000113.05",
							"vNF" : "000000000113.05",
							"pBru" : "00000.000000",
							"qVol": 1 ,
							"chave" : "35191210918425000308550010001228951001141664",
							"tpDoc" : "55",
							"xEsp" : "Diversos",
							"xNat" : "Diversos"
						},
						{
							"serie" : "1",
							"nDoc" : "000122896",
							"dEmi" : "2019-12-17",
							"vBC" : "00000",
							"vICMS" : "00000",
							"vBCST" : "00000",
							"vST" : "00000",
							"vProd" : "000000012883.83",
							"vNF" : "000000012883.83",
							"pBru" : "00000.000000",
							"qVol": 1 ,
							"chave" : "35191210918425000308550010001228961001141670",
							"tpDoc" : "55",
							"xEsp" : "Diversos",
							"xNat" : "Diversos"
						}
						]
					}
				]
			},
			success: function(data)
			{
				// jQuery('footer').append(data);
				console.log('Sucesso!')
				console.log(data);
			},
			error: function(data)
			{
				console.log('Erro:')
				console.log(data);
			}
		})
		</script>
		<?php

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
		$to = 'logs@unitycode.tech';
		$subject = 'Rotina - Pedidos bao/brix';
		$message = 'Teste de rotina - ' . time() . ' .';
		wp_mail($to, $subject, $message);
	}

}
