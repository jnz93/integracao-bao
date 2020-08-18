<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       unitycode.tech
 * @since      1.0.0
 *
 * @package    Integracao_Bao
 * @subpackage Integracao_Bao/public/partials
 */

 $page_cart = wc_get_cart_url();
?>
<form method="post" action="<?php echo $page_cart; ?>" style="margin-bottom: 150px;">
    <div class="form-group cotacao-cepremetente">
        <label for="cotacao-cepremetente">Cidade Origem</label>
        <!-- <input type="text" class="form-control" name="cotacao-cepremetente" id="cotacao-cepremetente" required> -->
        <select id="cotacao-cepremetente" name="select-origin" class="form-control">
            <option value="" selected>Selecione a origem</option>
            <option value="04348-070">São Paulo - SP</option>
        </select>
    </div>
    <div class="form-group cotacao-cepdestinatario">
        <label for="cotacao-cepdestinatario">Cidade destino</label>
        <!-- <input type="text" class="form-control" name="cotacao-cepdestinatario" id="cotacao-cepdestinatario" required> -->
        <select id="cotacao-cepdestinatario" name="select-destiny" class="form-control">
            <option value="" selected>Selecione o Destino</option>
            <option value="31270-700">Belo Horizonte - MG</option>
            <option value="71608-900">Brasília - DF</option>
            <option value="13051-154">Campinas - SP</option>
            <option value="83040-540">Curitiba - PR</option>
            <option value="88015-902">Florianópolis - SC</option>
            <option value="75133-320">Goiânia - GO</option>
            <option value="69028-140">Manaus - AM</option>
            <option value="91350-240">Porto Alegre - RS</option>
            <option value="21020-190">Rio de Janeiro - RJ</option>
        </select>
    </div>
    <div class="form-group cotacao-volumes">
        <label for="cotacao-volumes">Número de volumes</label>
        <input type="number" class="form-control" name="cotacao-volumes" id="cotacao-volumes" required>
    </div>
    
    <div class="form-group cotacao-peso">
        <label for="cotacao-peso">Peso</label>
        <input type="number" class="form-control" name="cotacao-peso" id="cotacao-peso" required>
    </div>
    
    <div class="form-group cotacao-valor">
        <label for="cotacao-valor">Valor</label>
        <input type="number" class="form-control" name="cotacao-valor" id="cotacao-valor" required>
    </div>

    <div class="form-group">
        <button type="button" id="buttonCotacao" onclick="request_cotacao()">Buscar cotação</button>
    </div>

    
	<div id="bao-cotacao-result" style="display: none;">
		<dl>
		  <dt>Valor do Frete: </dt>
		  <dd id="price"></dd>
		  <dt>Prazo de entrega: </dt>
		  <dd id="deliveryTime"></dd>
          <input type="hidden" name="delivery_price" id="delivery_price" value="">
          <input type="hidden" name="delivery_time" id="delivery_time" value="">
          <input type="hidden" name="zip_code" id="zip_code" value="">
        </dl>

        <?php if ( is_user_logged_in() ) : ?>
            <button type="submit" class="">Finalizar frete</button>
        <?php else : ?>
            <button>Cadastre-se para continuar</button>
        <?php endif; ?>
    </div>
</form>
<?php #echo 'login: ' . Integracao_Bao_Admin::login_brudam_api(); ?>
<?php #echo 'Brix record: '; Integracao_Bao_Admin::send_order_to_brix_brudam(); ?>
<script type="text/javascript">
function request_cotacao()
{
    var protocol = window.location.protocol,
        hostname = window.location.hostname,
        // wpAjaxUrl = protocol + '//' + hostname + '/bao/wp-admin/admin-ajax.php';
        wpAjaxUrl = protocol + '//' + hostname + '/bao/wp-admin/admin-ajax.php';
        console.log(wpAjaxUrl);

    jQuery.ajax({
    	url: wpAjaxUrl,
    	type: 'POST',
    	data: {
    		'action': 'send_cotacao_data',
    		'cepremetente': jQuery('#cotacao-cepremetente').val(),
    		'cepdestinatario': jQuery('#cotacao-cepdestinatario').val(),
    		'volumes': jQuery('#cotacao-volumes').val(),
    		'peso':   jQuery('#cotacao-peso').val(),
    		'valor': jQuery('#cotacao-valor').val()
    	},
    	dataType: 'JSON',
    	success: function (response) {
    		if(response.status == 1){
    			console.log('Valor Frete: '+response.servicos.item.valorFrete);
    			console.log('Prazo de entraga: '+response.servicos.item.prazoEntrega);
    			console.log('Código cidade: '+response.servicos.item.codigoCidade);
    			jQuery('#price').text(response.servicos.item.valorFrete);
    			jQuery('#deliveryTime').text(response.servicos.item.prazoEntrega);
    			jQuery('#bao-cotacao-result').show();

    			jQuery('#delivery_price').val(response.servicos.item.valorFrete);
    			jQuery('#delivery_time').val(response.servicos.item.prazoEntrega);
    			jQuery('#zip_code').val(response.servicos.item.codigoCidade);
    		}else{
                console.log(response);
    		}
            
    	}
    });
}
</script>