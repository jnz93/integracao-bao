# README

## Integração BAO
O plugin conecta o [TMS Brudam(Transportation Management System)](https://www.brudam.com.br/) ao Woocommerce da [BAO logística e transportes](https://bao.com.br) para fazer cotações, criar pedidos de frete online e acompanhar a entrega.

## Documentações das apis utilizadas
* [TMS Brix-Brudam - 'RESTFUL'](https://brix.brudam.com.br/docs/)
* [TMS Brix-Brudam - 'SOAP'](https://brix.ws.brudam.com.br/docs/soap)
* [Woocommerce 'RESTFUL v3'](https://woocommerce.github.io/woocommerce-rest-api-docs/)
* [Wordpress Plugin API](https://codex.wordpress.org/Plugin_API)

## Módulos do sistema
* Cotação de Cargas
* Criação do Pedido via API Restful v3 do Woocommerce
* Coleta e tratamento de dados para criação da Minuta no Sistema TMS via API Restful
* Exportação dos pedidos para criação de minutas no TMS Brudam via API Restful
* Atualização do status de entrega do pedido via API Restful

## Instalação
1. Upload `integracao-bao Plugin` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

-----------

* Contributors: @jnz93
* Tags: #BAO, #freteonline, #logistica, #integracaoTMS, #woocommerce, #apirestful
* Requires at least: 3.0.1
* Tested up to: 3.4
* Stable tag: 4.3
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
