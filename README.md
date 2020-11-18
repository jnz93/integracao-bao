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

## Instalação e configuração
1) Faça o upload no diretório ou instale o plugin de forma comum pelo painel do wordpress
2) Configure forneça o token de acesso, código do cliente e código de serviço do TMS Brudam.
3) Gere as chaves publica e privada do woocommerce e salve na página do plugin
4) Utilize o shortcode "[bao_cotacao]" para mostrar o formulário de cotação na página desejada
5) Adicione a pasta "woocommerce" na raiz do seu tema para susbstituir os templates: Carrinho, checkout e view-order no painel do usuári.

-----------

* Contributors: @jnz93
* Tags: #BAO, #freteonline, #logistica, #integracaoTMS, #woocommerce, #apirestful
* Requires at least: 3.0.1
* Tested up to: 3.4
* Stable tag: 4.3
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
