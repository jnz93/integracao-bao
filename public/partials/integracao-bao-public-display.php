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
    <div class="notice-box" style="position: relative; display: flex; justify-content: flex-end; cursor: pointer" onclick="showNotice(jQuery(this))">
        <span id="" class="notice-msg" style="display: none; font-size: 11px; text-align: right; background: #EFEFEF; padding: 4px; border-radius: 4px; position: absolute; width: 315px; left: 0px; top: -40px;">Caso a origem ou destino desejados não constem nas opções, aguarde e em breve a BAO estará disponível em sua cidade!</span>
        <i class="icon-info-circled"></i>
    </div>
    <div class="form-group cotacao-cepremetente">
        <label for="cotacao-cepremetente">Cidade Origem</label>
        <!-- <input type="text" class="form-control" name="cotacao-cepremetente" id="cotacao-cepremetente" required> -->
        <select id="cotacao-cepremetente" name="select-origin" class="form-control">
            <option value="" selected>Selecione a origem</option>
            <option value="04348-070">São Paulo - SP</option>
            <option value="91350-240">Porto Alegre - RS</option>
        </select>
    </div>
    <div class="form-group cotacao-cepdestinatario">
        <label for="cotacao-cepdestinatario">Cidade Destino</label>
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
        <label for="cotacao-peso">Peso (KG)</label>
        <input type="number" class="form-control" name="cotacao-peso" id="cotacao-peso" required>
    </div>
    
    <div class="form-group cotacao-valor">
        <label for="cotacao-valor">Valor da mercadoria (R$)</label>
        <input type="text" class="form-control" name="cotacao-valor" id="cotacao-valor" required>
    </div>

    <div class="form-group">
        <button type="button" id="buttonCotacao" onclick="request_cotacao()">Faça sua carga</button>
    </div>

    
	<div id="bao-cotacao-result" style="display: none; margin-top: 25px;">
        <table class="table">
            <tbody>
                <tr>
                    <th scope="row">Valor Frete R$</th>
                    <td id="price"></td>
                </tr>
                <tr>
                    <th scope="row">Prazo de entrega (dias úteis)</th>
                    <td id="deliveryTime"></td>
                </tr>
            </tbody>
        </table>

        <input type="hidden" name="delivery_price" id="delivery_price" value="">
        <input type="hidden" name="delivery_time" id="delivery_time" value="">
        <input type="hidden" name="zip_code" id="zip_code" value="">

        <?php if ( is_user_logged_in() ) : ?>
            <div class="uk-text-center">
                <h4 class="uk-card-title uk-text-warning"><i class="icon-attention"></i></h4>
                <span class="uk-text-warning">As informações prestadas são responsabilidade do usuário e qualquer informação divergente pode alterar o valor do frete</span>
            </div>
            <button type="submit" class="">Finalizar frete</button>
        <?php else : ?>
            <div class="uk-text-center">
                <h4 class="uk-card-title uk-text-warning"><i class="icon-attention"></i></h4>
                <span class="uk-text-warning">As informações prestadas são responsabilidade do usuário e qualquer informação divergente pode alterar o valor do frete</span>
            </div>
            <button>Cadastre-se para continuar</button>
        <?php endif; ?>
    </div>
    <p id="error-message" class="d-none"></p>
    <div id="bao-loader" class="lds-grid" style="margin-top: 35px;">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <span style="position: absolute; top: -25px;">Calculando...</span>
    </div>
</form>
<?php #Integracao_Bao_Admin::send_order_to_brix_brudam(); ?>
<script type="text/javascript">
function request_cotacao()
{
    jQuery('#bao-cotacao-result').hide();
    jQuery('#error-message').hide();

    // DATA
    var cepOrigem = jQuery('#cotacao-cepremetente').val(),
        cepDestino = jQuery('#cotacao-cepdestinatario').val(),
        volumes = jQuery('#cotacao-volumes').val(),
        peso = jQuery('#cotacao-peso').val(),
        valor = jQuery('#cotacao-valor').val(),
        action_wp = 'send_cotacao_data';

    // Elements
    var inputPrice = jQuery('#price'),
        inputDeliveryTime = jQuery('#deliveryTime'),
        inputDeliveryPrice = jQuery('#delivery_price'),
        inputDeliveryTime2 = jQuery('#delivery_time'),
        inputZipCode = jQuery('#zip_code'),
        elBaoResult = jQuery('#bao-cotacao-result'),
        elMessage = jQuery('#error-message'),
        loader = jQuery('#bao-loader');

    // Request
    jQuery.ajax({
    	url: '<?php echo admin_url('admin-ajax.php'); ?>',
    	type: 'POST',
    	data: {
    		'action': action_wp,
    		'cepremetente': cepOrigem,
    		'cepdestinatario': cepDestino,
    		'volumes': volumes,
    		'peso':   peso,
    		'valor': valor
    	},
    	dataType: 'JSON',
        beforeSend: function()
        {
            loader.show().fadeIn();
        },
    	success: function (response) {
    		if(response.status == 1){
    			inputPrice.text(response.servicos.item.valorFrete);
    			inputDeliveryTime.text(response.servicos.item.prazoEntrega);

    			inputDeliveryPrice.val(response.servicos.item.valorFrete);
    			inputDeliveryTime2.val(response.servicos.item.prazoEntrega);
    			inputZipCode.val(response.servicos.item.codigoCidade);

    			elBaoResult.show();
                elMessage.hide();
    		}else{
                console.log(response.erro);
                elMessage.text('Destino não disponível. Selecione outra cidade.').show();
                elBaoResult.hide();
    		}
            
    	},
        complete: function()
        {
            loader.fadeOut().hide();
        }
    });
}

function showNotice(el)
{
    el.children('.notice-msg').toggle().fade();
};

jQuery('document').ready(function()
{
    // Masks
    jQuery('#cotacao-valor').mask("#,##0.00", {reverse: true});
})
</script>