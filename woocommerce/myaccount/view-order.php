<?php
/**
 * View Order
 *
 * Shows the details of a particular order on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/view-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

$notes = $order->get_customer_order_notes();
$products = $order->get_items();

foreach($products as $product)
{
	$product_id = $product['product_id'];

	// Get current data of collect
	$collect_fullname 		= get_post_meta($product_id, 'bao_product_collect_name', true);
	$collect_tel 			= get_post_meta($product_id, 'bao_product_collect_phone', true);
	$collect_city 			= get_post_meta($product_id, 'bao_product_collect_city', true);
	$collect_neighborhood 	= get_post_meta($product_id, 'bao_product_collect_neighborhood', true);
	$collect_address 		= get_post_meta($product_id, 'bao_product_collect_address', true);
	$collect_cep 			= get_post_meta($product_id, 'bao_product_collect_zip', true);
	$collect_number 		= get_post_meta($product_id, 'bao_product_collect_number', true);
	$collect_complement 	= get_post_meta($product_id, 'bao_product_collect_complement', true);

	// Get current data of shipping
	$shipping_fullname 		= get_post_meta($product_id, 'bao_product_shipping_name', true);
	$shipping_tel 			= get_post_meta($product_id, 'bao_product_shipping_phone', true);
	$shipping_city 			= get_post_meta($product_id, 'bao_product_shipping_city', true);
	$shipping_neighborhood 	= get_post_meta($product_id, 'bao_product_shipping_neighborhood', true);
	$shipping_address 		= get_post_meta($product_id, 'bao_product_shipping_address', true);
	$shipping_cep 			= get_post_meta($product_id, 'bao_product_shipping_zip', true);
	$shipping_number 		= get_post_meta($product_id, 'bao_product_shipping_number', true);
	$shipping_complement 	= get_post_meta($product_id, 'bao_product_shipping_complement', true);
	?>
	<div class="" style="display: flex;">
		<div class="" style="width:45%; margin-right: 4%">
			<p>Dados coleta pacote #<?php echo $product_id; ?></p>
			<table class="table table-bordered table-sm">
				<tbody>
					<tr>
						<th>Nome</th>
						<td><?php echo $collect_fullname; ?></td>
					</tr>
					<tr>
						<th>Telefone</th>
						<td><?php echo $collect_tel; ?></td>
					</tr>
					<tr>
						<th>Cidade</th>
						<td><?php echo $collect_city; ?></td>
					</tr>
					<tr>
						<th>Bairro</th>
						<td><?php echo $collect_neighborhood; ?></td>
					</tr>
					<tr>
						<th>Endereço</th>
						<td><?php echo $collect_address; ?></td>
					</tr>
					<tr>
						<th>CEP</th>
						<td><?php echo $collect_cep; ?></td>
					</tr>
					<tr>
						<th>Número</th>
						<td><?php echo $collect_number; ?></td>
					</tr>
					<tr>
						<th>Complemento</th>
						<td><?php echo $collect_complement; ?></td>
					</tr>
				</tbody>
			</table>
		</div>

		<!-- entrega -->
		<div class="" style="width:45%;">
			<p>Dados entrega pacote #<?php echo $product_id; ?></p>
			<table class="table table-bordered table-sm">
				<tbody>
					<tr>
						<th>Nome</th>
						<td><?php echo $shipping_fullname; ?></td>
					</tr>
					<tr>
						<th>Telefone</th>
						<td><?php echo $shipping_tel; ?></td>
					</tr>
					<tr>
						<th>Cidade</th>
						<td><?php echo $shipping_city; ?></td>
					</tr>
					<tr>
						<th>Bairro</th>
						<td><?php echo $shipping_neighborhood; ?></td>
					</tr>
					<tr>
						<th>Endereço</th>
						<td><?php echo $shipping_address; ?></td>
					</tr>
					<tr>
						<th>CEP</th>
						<td><?php echo $shipping_cep; ?></td>
					</tr>
					<tr>
						<th>Número</th>
						<td><?php echo $shipping_number; ?></td>
					</tr>
					<tr>
						<th>Complemento</th>
						<td><?php echo $shipping_complement; ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
}
?>

<p>
<?php
printf(
	/* translators: 1: order number 2: order date 3: order status */
	esc_html__( 'Order #%1$s was placed on %2$s and is currently %3$s.', 'woocommerce' ),
	'<mark class="order-number">' . $order->get_order_number() . '</mark>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'<mark class="order-date">' . wc_format_datetime( $order->get_date_created() ) . '</mark>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'<mark class="order-status">' . wc_get_order_status_name( $order->get_status() ) . '</mark>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
?>
</p>

<?php if ( $notes ) : ?>
	<h2><?php esc_html_e( 'Order updates', 'woocommerce' ); ?></h2>
	<ol class="woocommerce-OrderUpdates commentlist notes">
		<?php foreach ( $notes as $note ) : ?>
		<li class="woocommerce-OrderUpdate comment note">
			<div class="woocommerce-OrderUpdate-inner comment_container">
				<div class="woocommerce-OrderUpdate-text comment-text">
					<p class="woocommerce-OrderUpdate-meta meta"><?php echo date_i18n( esc_html__( 'l jS \o\f F Y, h:ia', 'woocommerce' ), strtotime( $note->comment_date ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
					<div class="woocommerce-OrderUpdate-description description">
						<?php echo wpautop( wptexturize( $note->comment_content ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
			</div>
		</li>
		<?php endforeach; ?>
	</ol>
<?php endif; ?>

<?php do_action( 'woocommerce_view_order', $order_id ); ?>
