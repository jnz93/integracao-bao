(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	function request_cotacao()
	{
		var wpAdminAjaxUrl = window.location.hostname + '/wp-admin/admin-ajax.php';
		console.log('clicou '+ wpAdminAjaxUrl);

		// jQuery.ajax({
		// 	url: 'https://unitycode.tech/bao/wp-admin/admin-ajax.php',
		// 	type: 'POST',
		// 	data: {
		// 		// 'brix_cotacao_nonce': js_global.brix_cotacao_nonce,
		// 		'action': 'brix_cotacao',
		// 		'cepremetente': jQuery('#cotacao-cepremetente').val(),
		// 		'cepdestinatario': jQuery('#cotacao-cepdestinatario').val(),
		// 		'volumes': jQuery('#cotacao-volumes').val(),
		// 		'peso':   jQuery('#cotacao-peso').val(),
		// 		'valor': jQuery('#cotacao-valor').val()
		// 	},
		// 	// dataType: 'JSON',
		// 	success: function (response) {
		// 		if(response.status == 1){
		// 			console.log('Valor Frete: '+response.servicos.item.valorFrete);
		// 			console.log('Prazo de entraga: '+response.servicos.item.prazoEntrega);
		// 			console.log('Código cidade: '+response.servicos.item.codigoCidade);
		// 			jQuery('#price').text(response.servicos.item.valorFrete);
		// 			jQuery('#deliveryTime').text(response.servicos.item.prazoEntrega);
		// 			jQuery('#bao-cotacao-result').show();

		// 			jQuery('#delivery_price').val(response.servicos.item.valorFrete);
		// 			jQuery('#delivery_time').val(response.servicos.item.prazoEntrega);
		// 			jQuery('#zip_code').val(response.servicos.item.codigoCidade);
		// 		}else{

		// 		}
				
		// 	}
		// });
	}

})( jQuery );
