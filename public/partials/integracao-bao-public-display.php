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
 $page_login = get_permalink( wc_get_page_id( 'myaccount' ) );
?>

<form method="post" action="<?php echo is_user_logged_in() ? $page_cart : $page_login; ?>" id="formCotacao" style="margin-bottom: 150px;">
    <div class="form-row">
        <div class="form-group col-md-12">
            <div class="notice-box" style="position: relative; display: flex; justify-content: flex-end; cursor: pointer" onclick="showNotice(jQuery(this))">
                <span id="" class="notice-msg" style="display: none; font-size: 11px; text-align: right; background: #EFEFEF; padding: 4px; border-radius: 4px; position: absolute; width: 315px; left: 0px; top: -40px;">Caso a origem ou destino desejados não constem nas opções, aguarde e em breve a BAO estará disponível em sua cidade!</span>
                <i class="icon-info-circled"></i>
            </div>
        </div>

        <div class="form-group col-md-6 cotacao-cepremetente">
            <label for="cotacao-cepremetente">Cidade Origem</label>
            <select id="cotacao-cepremetente" name="select-origin" class="form-control">
                <option value="" selected>Selecione a origem</option>
                <option value="04348-070">São Paulo - SP</option>
                <option value="91350-240">Porto Alegre - RS</option>
            </select>
        </div>

        <div class="form-group col-md-6 cotacao-cepdestinatario">
            <label for="cotacao-cepdestinatario">Cidade Destino</label>
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

        <div class="form-group col-md-6 cotacao-volumes">
            <label for="cotacao-volumes">Número de volumes</label>
            <input type="number" class="form-control" name="cotacao-volumes" id="cotacao-volumes" required>

            <a class="" id="" href="#modal-example" uk-toggle onclick="generateInputs(jQuery(this).siblings('.form-control').val())">Dimensões</a>
            <!-- This is the modal -->

            <div id="modal-example" uk-modal>
                <div class="uk-modal-dialog uk-modal-body">
                    <button class="uk-modal-close-default" type="button" uk-close></button>
                    <h2 class="uk-modal-title">Insira os valores para cada volume(item)</h2>
                    <p>Comprimento x Largura x Altura</p>
                    <div id="inputs-vols"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
        
        <div class="form-group col-md-6 cotacao-peso" style="margin-bottom: 25px;">
            <label for="cotacao-peso">Peso (KG)</label>
            <input type="number" class="form-control" name="cotacao-peso" id="cotacao-peso" required>
        </div>
        
        <div class="form-group col-md-12 valor-carga">
            <label for="valor-carga" style="text-align: center;">Valor da Mercadoria (R$)</label>
            <input type="text" class="form-control" name="valor-carga" id="valor-carga" required>
        </div>

        <div class="form-group col-md-12">
            <button type="button" id="buttonCotacao" onclick="request_cotacao(jQuery(this).parents('form'))">Faça sua carga</button>
        </div>
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
            <button type="button" class="" onClick="addToCart(jQuery(this).parents('form'), '<?php echo admin_url('admin-ajax.php'); ?>')">Adicionar ao carrinho</button>
            <button type="button" class=""><a href="<?php echo wc_get_cart_url(); ?>">Finalizar frete</a></button>
        <?php else : ?>
            <div class="uk-text-center">
                <h4 class="uk-card-title uk-text-warning"><i class="icon-attention"></i></h4>
                <span class="uk-text-warning">As informações prestadas são responsabilidade do usuário e qualquer informação divergente pode alterar o valor do frete</span>
            </div>
            <button type="button" class="" onClick="addToCart(jQuery(this).parents('form'), '<?php echo admin_url('admin-ajax.php'); ?>')">Adicionar ao carrinho</button>
            <button>Cadastre-se para continuar</button>
        <?php endif; ?>
    </div>

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
function request_cotacao(parent)
{
    parent.find('#bao-cotacao-result').hide();
    parent.find('#error-message').hide();

    var cepOrigem   = parent.find('#cotacao-cepremetente').val(),
        cepDestino  = parent.find('#cotacao-cepdestinatario').val(),
        volumes     = parent.find('#cotacao-volumes').val(),
        peso        = parent.find('#cotacao-peso').val(),
        valor       = parent.find('#valor-carga').val(),
        action_wp   = 'send_cotacao_data';

    // Elements
    var inputPrice = parent.find('#price'),
        inputDeliveryTime = parent.find('#deliveryTime'),
        inputDeliveryPrice = parent.find('#delivery_price'),
        inputDeliveryTime2 = parent.find('#delivery_time'),
        inputZipCode = parent.find('#zip_code'),
        elBaoResult = parent.find('#bao-cotacao-result'),
        elMessage = parent.find('#error-message'),
        loader = parent.find('#bao-loader');

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
    			inputPrice.text(response.servicos.item.valorFrete).mask("#.##0,00", {reverse: true});
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
    jQuery('.valor-carga').children('input').mask("#.##0,##", {reverse: true});
    jQuery('.comprimento, .largura, .altura').mask("#.##", {reverse: true});
})
</script>