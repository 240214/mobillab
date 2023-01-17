<?php
/**
 * Search Bar
 * @author  Transvelo
 * @package Electro/Templates
 */

if(!defined('ABSPATH')){
	exit; // Exit if accessed directly
}

if(is_rtl()){
	$dir_value = 'rtl';
}else{
	$dir_value = 'ltr';
}

$navbar_search_text          = apply_filters('electro_navbar_search_placeholder', esc_html__('Search for products', 'electro'));
$navbar_search_dropdown_text = apply_filters('electro_navbar_search_dropdown_text', esc_html__('All Categories', 'electro'));
$header_tooltip_placement    = apply_filters('electro_header_tooltip_placement', 'bottom');
?>

<?php if(is_woocommerce_activated()) : ?>
	<form class="navbar-search" method="get" action="<?=esc_url(home_url('/')); ?>" autocomplete="off">
		<label class="sr-only screen-reader-text" for="search"><?=esc_html__('Search for:', 'electro'); ?></label>
		<div class="input-group">
			<div class="input-search-field">
				<input type="text" id="search" class="form-control search-field product-search-field" dir="<?=esc_attr($dir_value); ?>" value="<?=esc_attr(get_search_query()); ?>" name="s" placeholder="<?=esc_attr($navbar_search_text); ?>" autocomplete="disabled"/>
			</div>
			<?php if(apply_filters('electro_enable_search_categories_filter', true)) : ?>
				<div class="input-group-addon search-categories">
					<?php
					$selected_cat = isset($_GET['product_cat']) ? $_GET['product_cat'] : "0";
					wp_dropdown_categories(apply_filters('electro_search_categories_filter_args', array(
						'show_option_all' => $navbar_search_dropdown_text,
						'taxonomy'        => 'product_cat',
						'hide_if_empty'   => 1,
						'name'            => 'product_cat',
						'id'              => 'electro_header_search_categories_dropdown',
						'selected'        => $selected_cat,
						'value_field'     => 'slug',
						'class'           => 'postform resizeselect',
						#Сортировка категорий
						'meta_key' => 'order',
						'orderby' => 'meta_value_num',
						'order' => 'ASC',
						#end
					)));
					?>
				</div>
			<?php endif; ?>
			<div class="input-group-btn">
				<input type="hidden" id="search-param" name="post_type" value="product"/>
				<button type="submit" class="btn btn-secondary" <?php if($header_tooltip_placement):?>data-toggle="tooltip" data-placement="<?=esc_attr($header_tooltip_placement);?>" data-title="<?=esc_attr(esc_html__('Advanced search', 'electro-child'));?>"<?php endif;?>><i class="ec ec-search"></i></button>
			</div>
		</div>
		<?php do_action('wpml_add_language_form_field'); ?>
	</form>
<?php else : ?>
	<form class="navbar-search" method="get" action="<?=esc_url(home_url('/')); ?>" autocomplete="off">
		<div class="input-group">
			<label class="sr-only screen-reader-text" for="search"><?=esc_html__('Search for:', 'electro'); ?></label>
			<input type="text" id="search" class="search-field form-control" dir="<?=esc_attr($dir_value); ?>" value="<?=esc_attr(get_search_query()); ?>" name="s" placeholder="<?=esc_attr(esc_html__('Search', 'electro')); ?>" autocomplete="disabled"/>
			<div class="input-group-btn">
				<button type="submit" class="btn btn-secondary" <?php if($header_tooltip_placement):?>data-toggle="tooltip" data-placement="<?=esc_attr($header_tooltip_placement);?>" data-title="<?=esc_attr(esc_html__('Advanced search', 'electro-child'));?>"<?php endif;?>><i class="ec ec-search"></i></button>
			</div>
		</div>
		<?php do_action('wpml_add_language_form_field'); ?>
	</form>
<?php endif; ?>
