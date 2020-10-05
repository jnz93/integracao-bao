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

})( jQuery );

/**
 * Função sendMinutaIdToBackEnd()
 * Envia o id da minuta junto ao post id para o backend
 * @param minutaId(int) = id number
 * @param postId(int) = id do post
 * @param ajaxUrl(str) = url wp admin ajax
 *
 * @since 1.0.2
 */
function sendMinutaIdToBackEnd(minutaId, postId, ajaxUrl)
{
	var minutaId = minutaId, 
		postId = postId,
		actionWp = 'save_minuta_id_on_product',
		ajaxUrl = ajaxUrl;

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
			console.log('Dados gravados com sucesso!');
		},
		error: function(err)
		{
			console.log(err);
		}
	});
}


/**
 * Função getMinutaByID(id, token)
 * Recebe dois parametros para executar a requisição que retornará um objeto js em casos de sucesso.
 * 
 * @param id(int) = id da minuta
 * @param postId(int) = id do produto/post
 * @param token(string) = token de acesso ao sistema brix
 * @param ajaxUrl(string) = url wp admin ajax
 * 
 * @doc https://brix.brudam.com.br/docs/#/Ocorr%C3%AAncias/get_tracking_ocorrencias_minuta
 * 
 * @return object
 */
function getMinutaByID(id, postId, token, ajaxUrl)
{
	var endpointBrix = "https://brix.brudam.com.br/api/v1/tracking/ocorrencias/minuta?codigo=" + id;
	jQuery.ajax({
		method: "GET",
		headers: {
			"Authorization": "Bearer " + token,
		},
		url: endpointBrix,
		success: function(data)
		{
			data.data.forEach(function(el)
			{
				console.log(el.dados);
				el.dados.forEach(function(obj)
				{
					sendMinutaDataToUpdate(obj, postId, ajaxUrl);
				});
			});
		}
	})
}


/**
 * Função sendMinutaDataToUpdate()
 * Recebe os dados da minuta(obj) faz o tratamento e envia para o update no backend
 * 
 * @param minuta(obj) = dados da minuta
 * @param postId(int) = id do post que receberá os dados
 * @param ajaxUrl(str) = url wp admin ajax
 */
function sendMinutaDataToUpdate(minuta, postId, ajaxUrl)
{
	var minuta = minuta,
		postId = postId,
		ajaxUrl = ajaxUrl,
		dataMinuta = '',
		actionWp = 'update_data_minuta_on_product';

	dataMinuta = 'cnpj_dest_*' + minuta.cnpj_destinatario + '**'
				+ 'cnpj_rem_*' + minuta.cnpj_remetente + '**'
				+ 'cte_aut_data_*' + minuta.cte_aut_data + '**'
				+ 'cte_num_*' + minuta.cte_numero + '**'
				+ 'data_env_*' + minuta.data + '**'
				+ 'desc_*' + minuta.descricao + '**'
				+ 'nf_num_*' + minuta.nf_numero + '**'
				+ 'minuta_id_*' + minuta.numero + '**'
				+ 'obs_*' + minuta.obs + '**'
				+ 'razao_dest_*' + minuta.razao_destinatario + '**'
				+ 'razao_rem_*' + minuta.razao_remetente + '**'
				+ 'servico_*' + minuta.servico + '**'
				+ 'status_env_*' + minuta.status + '**'
				+ 'tipo_*' + minuta.tipo + '**'
				+ 'usuario_*' + minuta.usuario;

	// Send data to backend
	jQuery.ajax({
		url: ajaxUrl,
		type: 'POST',
		data: {
			'action': actionWp,
			'post_id': postId,
			'data_minuta': dataMinuta
		},
		success: function(data)
		{
			console.log('Dados enviados com sucesso!');
			console.log(data);
		},
		error: function(err)
		{
			console.log(err);
		}
	});
}


/**
 * Enviar os dados do form de cotação para o backend
 * 
 * @param price
 * @param delivery_days
 * @param zip_from
 * @param zip_to
 * @param n_volumes 
 * @param weight 
 * @param value
 * @param ajaxUrl
 * 
 * @since 1.0.3
 */
function sendCotacaoDataToBackEnd(price, deliveryDays, zipFrom, zipTo, nVolumes, value, ajaxUrl)
{
	var dataToSend = price + '|' + deliveryDays + '|' + zipFrom + '|' + zipTo + '|' + nVolumes + '|' + '|' + value;
	let actionWp = 'handle_cotacao_form';
	
	// console.log(dataToSend);
	// console.log(ajaxUrl);
	jQuery.ajax({
		url: ajaxUrl,
		type: 'POST',
		data: {
			'action': actionWp,
			'dataForm': dataToSend
		},
		success: function(response)
		{
			console.log('Cotação adicionada ao carrinho com sucesso!');
			// console.log(response);
			jQuery('#messages-cart').children('.uk-alert-success').fadeIn();
		},
		error: function(err)
		{
			console.log(err);
		},
		complete: function(response)
		{
			jQuery(this).next('.uk-alert-success').fadeOut();
			// add reset form
		}
	});
}


/**
 * Enviar dados do formulário de coleta pro backend
 * 
 * @param productId(int)
 * @param ajaxUrl(url/string)
 * 
 * @since 1.0.3
 */
function sendCollectFormDataToBackEnd(btn, productId, ajaxUrl)
{
	var form 			= jQuery('#modal-coleta-'+productId);
	// Dados form
	var coll_full_name 	= jQuery('#bao_collect_fullname_' + productId).val(),
	coll_tel 			= jQuery('#bao_collect_tel_' + productId).val(),
	coll_city 			= jQuery('#bao_collect_city_' + productId).val(),
	coll_neighborhood 	= jQuery('#bao_collect_neighborhood_' + productId).val(),
	coll_address 		= jQuery('#bao_collect_address_' + productId).val(),
	coll_cep 			= jQuery('#bao_collect_cep_' + productId).val(),
	coll_number			= jQuery('#bao_collect_number_' + productId).val(),
	coll_complement 	= jQuery('#bao_collect_complement_' + productId).val();

	// Building string for collect data
	var dataForm = coll_full_name + '-|-' + coll_tel + '-|-' + coll_city + '-|-' + coll_neighborhood + '-|-' + coll_address + '-|-' + coll_cep + '-|-' + coll_number + '-|-' + coll_complement;
	
	// Teste validaçao dos inputs
	var arrInputsA = [];
	arrInputsA.push(jQuery('#bao_collect_fullname_' + productId));
	arrInputsA.push(jQuery('#bao_collect_tel_' + productId));
	arrInputsA.push(jQuery('#bao_collect_city_' + productId));
	arrInputsA.push(jQuery('#bao_collect_neighborhood_' + productId));
	arrInputsA.push(jQuery('#bao_collect_address_' + productId));
	arrInputsA.push(jQuery('#bao_collect_cep_' + productId));
	arrInputsA.push(jQuery('#bao_collect_number_' + productId));
	arrInputsA.push(jQuery('#bao_collect_complement_' + productId));


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

	var action_name = 'save_coleta_data_form';
	
	// Send to backend
	if (invalidInputs == 0 ) {
		jQuery.ajax({
			url: ajaxUrl,
			type: 'POST',
			data: {
				'action': action_name,
				'postId': productId,
				'dataForm': dataForm,
			},
			beforeSend: function(){
				UIkit.notification("<span class='uk-box-shadow-small uk-padding'>Salvando...</span>", {pos: 'bottom-center', status: 'primary'});
			},
			success: function(response)
			{
				UIkit.notification("<span class='uk-box-shadow-small uk-padding'>Sucesso!</span>", {pos: 'bottom-center', status: 'success'});
				UIkit.modal(form).hide();
			},
			error: function(response)
			{
				UIkit.notification("<span class='uk-box-shadow-small uk-padding'>Erro ao salvar.</span>", {pos: 'bottom-center', status: 'error'});
				UIkit.notification("<span class='uk-box-shadow-small uk-padding'>"+ response.error +"</span>", {pos: 'bottom-center', status: 'error'});
			},
			complete: function(response)
			{
				verifyColetaEntregaForms(cartProductsIds)
				// console.log(cartProductsIds);
			}
		});
	} else {
		console.log(invalidInputs + " Inválido(s)!");
		jQuery('#error-message').fadeIn();
	}

}


/**
 * Enviar dados do formulário de entrega pro backend
 * 
 * @param productId(int)
 * @param ajaxUrl(url/string)
 * 
 * @since 1.0.3
 */
function sendShippingFormDataToBackEnd(productId, ajaxUrl)
{
	var form 			= jQuery('#modal-entrega-'+productId);

	// Dados entrega
	var shipping_full_name 	= jQuery('#bao_shipping_fullname_' + productId).val(),
	shipping_tel 			= jQuery('#bao_shipping_tel_' + productId).val(),
	shipping_city 			= jQuery('#bao_shipping_city_' + productId).val(),
	shipping_neighborhood 	= jQuery('#bao_shipping_neighborhood_' + productId).val(),
	shipping_address 		= jQuery('#bao_shipping_address_' + productId).val(),
	shipping_cep 			= jQuery('#bao_shipping_cep_' + productId).val(),
	shipping_number 		= jQuery('#bao_shipping_number_' + productId).val(),
	shipping_complement 	= jQuery('#bao_shipping_complement_' + productId).val();

	// Build string for shipping data
	var dataForm = shipping_full_name + '-|-' + shipping_tel + '-|-' + shipping_city + '-|-' + shipping_neighborhood + '-|-' + shipping_address + '-|-' + shipping_cep + '-|-' + shipping_number + '-|-' + shipping_complement;

	// Teste validaçao dos inputs
	var arrInputsA = [];
	arrInputsA.push(jQuery('#bao_shipping_fullname_' + productId));
	arrInputsA.push(jQuery('#bao_shipping_tel_' + productId));
	arrInputsA.push(jQuery('#bao_shipping_city_' + productId));
	arrInputsA.push(jQuery('#bao_shipping_neighborhood_' + productId));
	arrInputsA.push(jQuery('#bao_shipping_address_' + productId));
	arrInputsA.push(jQuery('#bao_shipping_cep_' + productId));
	arrInputsA.push(jQuery('#bao_shipping_number_' + productId));
	arrInputsA.push(jQuery('#bao_shipping_complement_' + productId));

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
	var action_name = 'save_entrega_data_form';
	if (invalidInputs == 0 ) {
		jQuery.ajax({
			url: ajaxUrl,
			type: 'POST',
			data: {
				'action': action_name,
				'postId': productId,
				'dataForm': dataForm,
			},
			beforeSend: function(){
				UIkit.notification("<span class='uk-box-shadow-small uk-padding'>Salvando...</span>", {pos: 'bottom-center', status: 'primary'});
			},
			success: function(response)
			{
				UIkit.notification("<span class='uk-box-shadow-small uk-padding'>Sucesso!</span>", {pos: 'bottom-center', status: 'success'});
				UIkit.modal(form).hide();
			},
			error: function(response)
			{
				UIkit.notification("<span class='uk-box-shadow-small uk-padding'>Erro ao salvar.</span>", {pos: 'bottom-center', status: 'error'});
				UIkit.notification("<span class='uk-box-shadow-small uk-padding'>"+ response.error +"</span>", {pos: 'bottom-center', status: 'error'});
			},
			complete: function(response)
			{
				verifyColetaEntregaForms(cartProductsIds)
				// console.log(cartProductsIds);
			}
		});
	} else {
		console.log(invalidInputs + " Inválido(s)!");
		jQuery('#error-message').fadeIn();
	}
}


/**
 * Verifica se os formulários de coleta/entrega foram preenchidos e habilita/desabilita o botão "concluir compra" no carrinho
 * 
 * @param productIds(str)
 * 
 * @since 1.0.3
 */
function verifyColetaEntregaForms(productIds)
{
	console.log(productIds)
	var arrIds = productIds.split(',');
	console.log(arrIds);
	// Teste validaçao dos inputs
	var validate = 0;
	arrIds.forEach(function(id){
		if(id != '') {
			var arrInputsA = [];
			arrInputsA.push(jQuery('#bao_collect_fullname_' + id));
			arrInputsA.push(jQuery('#bao_collect_tel_' + id));
			arrInputsA.push(jQuery('#bao_collect_city_' + id));
			arrInputsA.push(jQuery('#bao_collect_neighborhood_' + id));
			arrInputsA.push(jQuery('#bao_collect_address_' + id));
			arrInputsA.push(jQuery('#bao_collect_cep_' + id));
			arrInputsA.push(jQuery('#bao_collect_number_' + id));
			arrInputsA.push(jQuery('#bao_collect_complement_' + id));
			arrInputsA.push(jQuery('#bao_shipping_fullname_' + id));
			arrInputsA.push(jQuery('#bao_shipping_tel_' + id));
			arrInputsA.push(jQuery('#bao_shipping_city_' + id));
			arrInputsA.push(jQuery('#bao_shipping_neighborhood_' + id));
			arrInputsA.push(jQuery('#bao_shipping_address_' + id));
			arrInputsA.push(jQuery('#bao_shipping_cep_' + id));
			arrInputsA.push(jQuery('#bao_shipping_number_' + id));
			arrInputsA.push(jQuery('#bao_shipping_complement_' + id));
	
			arrInputsA.forEach(function(el)
			{
				// console.log(el);
				if (el.val().length < 3)
				{
					el.css({'border': '1px solid red'});
					validate++;
				}
			});
			
			if (validate != 0)
			{
				jQuery('#cotacao-' + id).css({
					'background': 'red'
				});
			}
			else {
				jQuery('#cotacao-' + id).css({
					'background': 'none'
				});
			}
		}
	});
	if (validate != 0)
	{
		var cautionMessage = '<div class="uk-alert-warning" uk-alert><p>Preencha os dados de coleta e entrega das cotações em destaque.</p></div>',
			btnConcluirCompra = jQuery('.checkout-button') ;
		console.log(validate);
		console.log(jQuery('.checkout-button'));
		btnConcluirCompra.fadeOut();
		btnConcluirCompra.after(cautionMessage);
	} else {
		var cautionMessage = '<div class="uk-alert-warning" uk-alert><p>Preencha os dados de coleta e entrega das cotações em destaque.</p></div>',
			btnConcluirCompra = jQuery('.checkout-button');

		btnConcluirCompra.fadeIn();
		btnConcluirCompra.next().fadeOut();
	}	

}