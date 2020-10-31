<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       unitycode.tech
 * @since      1.0.0
 *
 * @package    Integracao_Bao
 * @subpackage Integracao_Bao/admin/partials
 */  
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="uk-container uk-container-expand uk-margin-small-top" uk-grid>

    <!-- Card Gerar Minutas -->
    <div class="uk-width-1-1">
      <div class="uk-card uk-card-default uk-card-body">
        <button>Gerar Minuta(s)</button>
      </div>
    </div>

    <!-- Tabela de configurações -->
    <div class="uk-width-1-2">
      <div class="uk-card uk-card-default uk-card-body">
        <h3>Dados TMS Brix-Brudam</h3>
        <table class="uk-table uk-table-small uk-table-divider uk-margin-small">
          <caption class="uk-margin-small-bottom">Configurações da integração</caption>
          <tbody>
              <tr>
                  <th>Doc. Api Cotação(SOAP)</th>
                  <td><a href="http://brix.ws.brudam.com.br/docs/cotacao">http://brix.ws.brudam.com.br/docs/cotacao</a></td>
              </tr>
              <tr>
                  <th>TOKEN BRUDAM</th>
                  <td>0sIplJGJRTbgSP9DnE1jnxVG7ckKdG</td>
              </tr>   
              <tr>
                  <th>Cod. Serv. Cotação</th>
                  <td>38</td>
              </tr>
              <tr>
                  <th>Cod. Cliente</th>
                  <td>74767410053</td>
              </tr>
              <tr>
                  <th>CNPJ</th>
                  <td>11926752000102</td>
              </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Formulário de configurações -->
    <div class="uk-width-1-2">
      <div class="uk-card uk-card-default uk-card-body">
        <h3>Form. Integração TMS x Woocommerce</h3>
        <form class="uk-form-horizontal uk-margin-small" method="post">
          <?php settings_fields('bao_settings_plugin'); ?>
          <?php do_settings_sections('bao_settings_plugin'); ?>
          <div class="uk-margin">
              <label class="uk-form-label" for="brix_token">Token TMS(Brix)</label>
              <div class="uk-form-controls">
                <input name="brix_token" type="text" id="brix_token" value="<?=strval(get_option('brix_token'))?>" class="uk-input uk-form-small" required>
              </div>
          </div>

          <div class="uk-margin">
              <label class="uk-form-label" for="brix_cliente">Cód. Cliente Brix</label>
              <div class="uk-form-controls">
                <input name="brix_cliente" type="text" id="brix_cliente" value="<?=strval(get_option('brix_cliente'))?>" class="uk-input uk-form-small">
              </div>
          </div>

          <div class="uk-margin">
              <label class="uk-form-label" for="brix-cotacao-servico">Cód. Serviço Cotação</label>
              <div class="uk-form-controls">
                <input name="brix-cotacao-servico" type="text" id="brix-cotacao-servico" value="<?=strval(get_option('brix_cotacao_servico'))?>" class="uk-input uk-form-small">
              </div>
          </div>

          <div class="uk-margin">
              <label class="uk-form-label" for="brix-woocomerce-public-key">Woocomerce public API key</label>
              <div class="uk-form-controls">
                <input name="brix-woocomerce-public-key" type="text" id="brix-woocomerce-public-key" value="<?=strval(get_option('brix-woocomerce-public-key'))?>" class="uk-input uk-form-small">
              </div>
          </div>

          <div class="uk-margin">
              <label class="uk-form-label" for="brix-woocomerce-secret-key">Woocomerce secret API key</label>
              <div class="uk-form-controls">
                <input name="brix-woocomerce-secret-key" type="password" id="brix-woocomerce-secret-key" value="<?=strval(get_option('brix-woocomerce-secret-key'))?>" class="uk-input uk-form-small">
              </div>
          </div>

          <button type="submit" class="btn btn-success button button-primary">Salvar</button>
        </form>
      </div>
    </div>

</div>