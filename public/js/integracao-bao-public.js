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