<?php
/**
 * Single Product Meta after title
 */

defined('ABSPATH') || exit; // Exit if accessed directly.

global $product;
?>
<div class="product_meta_after_title df">

	<?php if(wc_product_sku_enabled() && ($product->get_sku() || $product->is_type('variable'))) : ?>
		<div class="sku_wrapper"><b><?=esc_html__('SKU:', 'woocommerce'); ?></b> <?=$product->get_sku();?></div>
	<?php endif; ?>

	<?php if(current_user_can('manage_options')):?>
		<?php $products_model = get_field('products_model', $product->get_id());?>
		<div class="code_wrapper"><b><?=esc_html__('Product code:', THEME_TD);?></b> <?=$products_model;?></div>
	<?php endif; ?>

</div>
