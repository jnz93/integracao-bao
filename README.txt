=== Integração Bao ===
Contributors: @joanezandrades
Tags: #INTEGRACAOCRMxWP, #WOOCOMMERCE, #APIRESTFULL, #BAO, #COTACAOCARGA, #FRETEONLINE, #LOGISTICA, #NACIONAL
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

O plugin conecta o TMS Brudam(Sistema de gerenciamento de transporte) ao Woocommerce

== Description ==

Conecta o TMS Brudam(Sistema de gerenciamento de transporte) ao Woocommerce, de forma segura e consistente, para permitir que a BAO, empresa de logística e entregas, facilite a vida de consumidores com cotações de cargas e acompanhamento de entregas online. 

_Todas as API's citadas abaixo estão linkadas nas dependências_
Webservice SOAP(Protocolo de troca de informações estruturados) da BRUDAM para fazer as cotações.
API RESTFULL Woocommerce para criar as cotações, adição no carrinho, criação do pedido e acompanhamento da coleta e entrega.
AJAX/JSON para coletar e salvar dados de origem e destino de forma segura, eficaz e ágil.
API RESTFULL BRUDAM para registrar minutas e salvar dados no banco de dados do TMS os pedidos com informações de origem, entrega e confirmação do pagamento.

BAO uma empresa de Logistíca, que tem filiais pelo Brasil inteiro, tem acesso a um painel administrativo onde acompanha os novos pedidos, analisa o processamento cotações feitas e acompanha tudo acontecer automaticamente desde a criação da Minuta no CRM BRIX, os dados dos pedidos são enviados via API RESTFULL do sistema(LINK), até a atualização do estatus das cargas. Tudo por tarefas automatizadas no servidor.

O Sistema foi separado em alguns módulos:
- Cotação no site(front-end)
-- Coleta de dados: Origem, Destino, Volumes/Cargas(coleta das dimensões e cálculo do peso cubado), Peso, Valor da Carga - API RESTFULL
- Criação do pedido no woocommerce
- Criação da Minuta no Sistema BRIX
- Atualização do status da entrega

DOCUMENTAÇÕES E AJUDA
DOCS API BRIX: https://brix.ws.brudam.com.br/docs/integracao
DOSC API SOAP: https://brix.ws.brudam.com.br/docs/soap
WOOCOMERCE REST API: https://woocommerce.github.io/woocommerce-rest-api-docs/?shell#product-properties
Resolução item 2: https://rudrastyh.com/woocommerce/rest-api-create-update-remove-products.html

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `integracao-bao.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 1.0 =
* A change since the previous version.
* Another change.

= 0.5 =
* List versions from most recent at top to oldest at bottom.

== Upgrade Notice ==

= 1.0 =
Upgrade notices describe the reason a user should upgrade.  No more than 300 characters.

= 0.5 =
This version fixes a security related bug.  Upgrade immediately.

== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.

== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`
