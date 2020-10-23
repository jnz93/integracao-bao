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
		},
		error: function(err)
		{
			console.log(err);
		}
	});
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
		dataMinuta = '';

	let	actionWp = 'update_data_minuta_on_product';

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
function sendCotacaoDataToBackEnd(price, deliveryDays, zipFrom, zipTo, nVolumes, weight, value, ajaxUrl)
{
	var dataToSend = price + '|' + deliveryDays + '|' + zipFrom + '|' + zipTo + '|' + nVolumes + '|' + weight + '|' + value;
	let actionWp = 'handle_cotacao_form';
	
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
			UIkit.notification("<span class='uk-box-shadow-small uk-padding'>Cotação adicionada ao carrinho!</span>", {pos: 'bottom-center', status: 'success'});
			jQuery('#bao-cotacao-result').fadeOut();
			jQuery('#cotacao-peso').attr('disabled', false);
			jQuery('#formCotacao')[0].reset();
			sumAndUpdateCartItems();
		},
		error: function(err)
		{
			UIkit.notification("<span class='uk-box-shadow-small uk-padding'>Erro ao salvar: "+ response.error +"</span>", {pos: 'bottom-center', status: 'error'});
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
	coll_doc 			= jQuery('#bao_collect_doc_' + productId).val(),
	coll_city 			= jQuery('#bao_collect_city_' + productId).val(),
	coll_neighborhood 	= jQuery('#bao_collect_neighborhood_' + productId).val(),
	coll_address 		= jQuery('#bao_collect_address_' + productId).val(),
	coll_cep 			= jQuery('#bao_collect_cep_' + productId).val(),
	coll_number			= jQuery('#bao_collect_number_' + productId).val(),
	coll_complement 	= jQuery('#bao_collect_complement_' + productId).val();

	// Building string for collect data
	var dataForm = coll_full_name + '-|-' + coll_tel + '-|-' + coll_doc +  '-|-' + coll_city + '-|-' + coll_neighborhood + '-|-' + coll_address + '-|-' + coll_cep + '-|-' + coll_number + '-|-' + coll_complement;
	
	// Teste validaçao dos inputs
	var arrInputsA = [];
	arrInputsA.push(jQuery('#bao_collect_fullname_' + productId));
	arrInputsA.push(jQuery('#bao_collect_tel_' + productId));
	arrInputsA.push(jQuery('#bao_collect_doc_' + productId));
	arrInputsA.push(jQuery('#bao_collect_city_' + productId));
	arrInputsA.push(jQuery('#bao_collect_neighborhood_' + productId));
	arrInputsA.push(jQuery('#bao_collect_address_' + productId));
	arrInputsA.push(jQuery('#bao_collect_cep_' + productId));
	arrInputsA.push(jQuery('#bao_collect_number_' + productId));
	arrInputsA.push(jQuery('#bao_collect_complement_' + productId));


	var invalidInputs = 0;
	arrInputsA.forEach(function(el)
	{
		if (el.val().length < 2)
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
	shipping_doc 			= jQuery('#bao_shipping_doc_' + productId).val(),
	shipping_city 			= jQuery('#bao_shipping_city_' + productId).val(),
	shipping_neighborhood 	= jQuery('#bao_shipping_neighborhood_' + productId).val(),
	shipping_address 		= jQuery('#bao_shipping_address_' + productId).val(),
	shipping_cep 			= jQuery('#bao_shipping_cep_' + productId).val(),
	shipping_number 		= jQuery('#bao_shipping_number_' + productId).val(),
	shipping_complement 	= jQuery('#bao_shipping_complement_' + productId).val();

	// Build string for shipping data
	var dataForm = shipping_full_name + '-|-' + shipping_tel + '-|-' + shipping_doc + '-|-' + shipping_city + '-|-' + shipping_neighborhood + '-|-' + shipping_address + '-|-' + shipping_cep + '-|-' + shipping_number + '-|-' + shipping_complement;

	// Teste validaçao dos inputs
	var arrInputsA = [];
	arrInputsA.push(jQuery('#bao_shipping_fullname_' + productId));
	arrInputsA.push(jQuery('#bao_shipping_tel_' + productId));
	arrInputsA.push(jQuery('#bao_shipping_doc_' + productId));
	arrInputsA.push(jQuery('#bao_shipping_city_' + productId));
	arrInputsA.push(jQuery('#bao_shipping_neighborhood_' + productId));
	arrInputsA.push(jQuery('#bao_shipping_address_' + productId));
	arrInputsA.push(jQuery('#bao_shipping_cep_' + productId));
	arrInputsA.push(jQuery('#bao_shipping_number_' + productId));
	arrInputsA.push(jQuery('#bao_shipping_complement_' + productId));

	var invalidInputs = 0;
	arrInputsA.forEach(function(el)
	{
		if (el.val().length < 2)
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
	var arrIds = productIds.split(',');
	var validate = 0;

	// Teste validaçao dos inputs
	arrIds.forEach(function(id){
		if(id != '') {
			var arrInputsA = [];
			arrInputsA.push(jQuery('#bao_collect_fullname_' + id));
			arrInputsA.push(jQuery('#bao_collect_tel_' + id));
			arrInputsA.push(jQuery('#bao_collect_doc_' + id));
			arrInputsA.push(jQuery('#bao_collect_city_' + id));
			arrInputsA.push(jQuery('#bao_collect_neighborhood_' + id));
			arrInputsA.push(jQuery('#bao_collect_address_' + id));
			arrInputsA.push(jQuery('#bao_collect_cep_' + id));
			arrInputsA.push(jQuery('#bao_collect_number_' + id));
			arrInputsA.push(jQuery('#bao_collect_complement_' + id));
			arrInputsA.push(jQuery('#bao_shipping_fullname_' + id));
			arrInputsA.push(jQuery('#bao_shipping_tel_' + id));
			arrInputsA.push(jQuery('#bao_shipping_doc_' + id));
			arrInputsA.push(jQuery('#bao_shipping_city_' + id));
			arrInputsA.push(jQuery('#bao_shipping_neighborhood_' + id));
			arrInputsA.push(jQuery('#bao_shipping_address_' + id));
			arrInputsA.push(jQuery('#bao_shipping_cep_' + id));
			arrInputsA.push(jQuery('#bao_shipping_number_' + id));
			arrInputsA.push(jQuery('#bao_shipping_complement_' + id));
	
			arrInputsA.forEach(function(el)
			{
				// console.log(el);
				if (el.val().length < 2)
				{
					el.css({'border': '1px solid red'});
					validate++;
				}
			});
			
			if (validate != 0)
			{
				jQuery('#cotacao-' + id).children('td.product-delivery-data').css({
					'background': '#F04F4F'
				});
			}
			else {
				jQuery('#cotacao-' + id).children('td.product-delivery-data').css({
					'background': 'none'
				});
			}
		}
	});
	
	var cautionMessage = '<div class="uk-alert-warning" uk-alert><p>Preencha os dados de coleta e entrega das cotações em destaque.</p></div>',
		btnConcluirCompra = jQuery('.checkout-button');

	if (validate != 0)
	{
		jQuery('.wc-proceed-to-checkout').children('div.uk-alert-warning').remove();
		btnConcluirCompra.fadeOut();
		btnConcluirCompra.after(cautionMessage);
	}
	else 
	{
		btnConcluirCompra.fadeIn();
		btnConcluirCompra.next().fadeOut();
	}

}

/**
 * Rastrear pedido. Recebe o id do pedido com um dos parametros e faz o redirecionamento
 * 
 * @param orderId(int)
 * @param siteUrl(str)
 * 
 * @since 1.1.0
 */
function redirectToOrder(orderId, siteUrl)
{
	if (orderId == '' || siteUrl == '')
	{
		return;
	}
	var to = siteUrl + '/minha-conta/view-order/' + orderId;
	window.location.href = to;
}


/**
 * Recebe o número de volumes e gera os inputs referentes aos volumes
 * 
 * @param vols(int) numero de volumes no input cotacao-volumes
 * 
 * @since 1.1.0
 */
function generateInputs(vols)
{
	console.log(vols);
	var output = '',
		container = jQuery('#inputs-vols');
	if (vols < 1)
	{
		output = 'Por favor, insira a quantidade de volumes no campo "Número de volumes".';
		container.append(output);
		return;
	}
	
	// Comprimento x Largura x Altura
	for(i = 1; i <= vols; i++)
	{
		// console.log(i);
		output += '<div style="display: flex;">' +
						'<div style="width: 20%; margin: 0 4px;"><input type="text" class="form-control uk-form-width-small comprimento" name="" required placeholder="Comp."></div> x ' +
						'<div style="width: 20%; margin: 0 4px;"><input type="text" class="form-control uk-form-width-small largura" name="" required placeholder="Larg."></div> x ' +
						'<div style="width: 20%; margin: 0 4px;"><input type="text" class="form-control uk-form-width-small altura" name="" required placeholder="Alt."></div>' +
						'<div style="width: 20%; margin: 0 4px;"><select class="uk-select" onclick="calcPesoCubado(jQuery(this))"><option>Medida</option><option>CM</option><option>M</option></select></div>' +
						'<div style="width: 15%; margin: 0 4px;"><input type="text" class="form-control uk-form-width-small total" name="" disabled></div>' +
					'</div>';
	}
	
	container.html(output);
}

/**
 * Recebe o elemento clicado e busca os valores comprimento, largura e altura compativeis para fazer o cálculo.
 * 
 * @param el(nó) o elemento clicado
 * 
 * @since 1.1.0
 */
function calcPesoCubado(el)
{
	var typeMeasure = el.val(),
		factor = 0;

	switch(typeMeasure)
	{
		case 'CM':
			factor = 6000;
			break;
		case 'M':
			factor = 300;
			break;
		default:
			return;
	}
	
	var comprimento = parseFloat(el.parent().parent().find('.comprimento').val()),
		largura = parseFloat(el.parent().parent().find('.largura').val()),
		altura = parseFloat(el.parent().parent().find('.altura').val()),
		inputTotal = el.parent().parent().find('.total'),
		inputsTotals = el.parent().parent().parent().find('.total');

	var total = 0;
	/**
	 * Fórmula CM
	 * C * L * A / 6000
	 * 
	 * Fórmula M
	 * C * L * A * 300
	 */
	if (typeMeasure == 'CM') 
	{
		total = (comprimento * largura * altura) / factor; 
	}
	else 
	{
		total = (comprimento * largura * altura) * factor;
	}
	inputTotal.val(total.toFixed(2));
	// console.log(total);
	// console.log(inputsTotals)

	var totalKg = 0;
	// console.log(typeof inputsTotals);
	inputsTotals.each(function(index, el)
	{

		if (el.value != '') 
		{
			totalKg = totalKg + parseFloat(el.value);
			console.log(el.value)
		}
	});

	console.log('TotalKG: ' + totalKg);
	// totalKg = totalKg.toFixed(2);
	var inputPeso = jQuery('#cotacao-peso'),
		currPeso = parseFloat(inputPeso.val());

	console.log('Peso typeof: ' + typeof currPeso);
	if (currPeso)
	{
		console.log('Peso: ' + currPeso);
		if (totalKg > currPeso)
		{
			inputPeso.val(totalKg).prop('disabled', true);
		}
	}
	else
	{
		inputPeso.val(totalKg).prop('disabled', true);
	}
}

/**
 * Soma e altera o número de cotações no carrinho feitas pelo form de cotação na homepage
 * 
 * @since 1.1.2
 */
function sumAndUpdateCartItems()
{
	var currItems = parseInt(jQuery('#header_cart').children('span').text());
	currItems++;

	jQuery('#header_cart').children('span').text(currItems);
}

/**
 * Envia o valor do campo mercado para o backend
 * 
 * @since 1.1.2
 */
function sendMerchandiseToBackEnd(el, productId, ajaxUrl)
{
	var merchandise = el.val();
	if(merchandise.length > 3){
		jQuery.ajax({
			url: ajaxUrl,
			type: 'POST',
			data: {
				action: 'save_merchandise',
				cotacao_id: productId,
				value: merchandise,
			},
			success: function(data)
			{
				UIkit.notification("<span class='uk-box-shadow-small uk-padding'>Salvo com sucesso!</span>", {pos: 'bottom-center', status: 'success'});
				el.attr('disabled', true);
				el.css({
					'border': '1px solid green',
					'background': 'rgba(255, 255, 255, .5)'
				});
			},
			error: function(e)
			{
				UIkit.notification("<span class='uk-box-shadow-small uk-padding'>Erro. Tente novamente!</span>", {pos: 'bottom-center', status: 'error'});
				el.css({
					'border': '1px solid red'
				});
			}
		});
	}
}