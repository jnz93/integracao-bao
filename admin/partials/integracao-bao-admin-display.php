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
<div style="min-height:400px;border:  dashed 0px #bbb; border-radius: 0px;" class="container-fluid">
    <h1>Configurações para integração BRIX</h1>
    <blockquote><br>
    API:                    http://brix.ws.brudam.com.br/docs/cotacao<br>
    TOKEN:                  0sIplJGJRTbgSP9DnE1jnxVG7ckKdG<br>
    CNPJ:                   11926752000102<br>
    Cotacao codigo servico: 38<br>
    Cliente:                74767410053<br>

    ShortCode Formulario Cotacao: [formCotacao]<br>
    </blockquote>
    <form action="" method="post">
      <?php settings_fields('bao_settings_plugin'); ?>
      <?php do_settings_sections('bao_settings_plugin'); ?>
      <table class="form-table" role="presentation">
        <tbody>
          <tr>
            <th scope="row">
              <label for="brix-token">Token de acesso</label>
            </th>
            <td>
              <input name="brix_token" type="text" id="brix_token" value="<?=strval(get_option('brix_token'))?>" class="regular-text" required>
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="brix_cliente">Código de cliente</label>
            </th>
            <td>
              <input name="brix_cliente" type="text" id="brix_cliente" value="<?=strval(get_option('brix_cliente'))?>" class="regular-text form-control">
            </td>
          </tr>
          
          <tr>
            <th scope="row">
              <label for="brix-cotacao-servico">Código de serviço (cotação)</label>
            </th>
            <td>
              <input name="brix-cotacao-servico" type="text" id="brix-cotacao-servico" value="<?=strval(get_option('brix_cotacao_servico'))?>" class="regular-text form-control">
            </td>
          </tr>

          <tr>
            <th scope="row">
              <label for="brix-cotacao-servico">Woocomerce Rest API Public KEY</label>
            </th>
            <td>
              <input name="brix-woocomerce-public-key" type="text" id="brix-woocomerce-public-key" value="<?=strval(get_option('brix-woocomerce-public-key'))?>" class="regular-text form-control">
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="brix-cotacao-servico">Woocomerce Rest API SECRET KEY</label>
            </th>
            <td>
              <input name="brix-woocomerce-secret-key" type="password" id="brix-woocomerce-secret-key" value="<?=strval(get_option('brix-woocomerce-secret-key'))?>" class="regular-text form-control">
            </td>
          </tr>

          <tr>          
            <td colspan="2">
            <button type="submit" class="btn btn-success button button-primary">Salvar</button>
            </td>
          </tr>
                  
        </tbody>
      </table>
    </form>