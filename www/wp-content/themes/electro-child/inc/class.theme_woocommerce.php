<?php

namespace Digidez;

class Theme_WooCommerce{
	
	private static $remont_link = '/remont-texniki/';
	private static $remont_cats = [
		'stiralnye-mashiny',
		'posudomoechniye-maschini',
		'holodilniki',
		'morozilniki',
		'varochnye-paneli',
		'vstraivaemye-xolodilniki',
		'stiralnye-mashiny-vstraivaemye',
		'vstraivaemye-posudomoechnye-mashiny',
		'vstraivaemye-morozilniki',
		'duxovye-shkafy'
	];
	private static $remont_exclude_phrase = [
		'газов',
		'комбиниров',
	];
	
	/**
	 * Setup class.
	 */
	public static function initialise(){
		$self = new self();
		
		#add_filter( 'woocommerce_output_related_products_args', 			[$self, 'modify_linked_product_args']);
		#add_filter( 'woocommerce_upsell_display_args', 					[$self, 'modify_linked_product_args']);
		#add_filter( 'woocommerce_product_description_heading',				[$self, 'hide_tab_heading']);
		#add_filter( 'woocommerce_product_additional_information_heading', 	[$self, 'hide_tab_heading']);
		#add_filter( 'woocommerce_product_tabs',							[$self, 'modify_product_tabs']);
		#add_filter( 'comments_template', 									[$self, 'comments_template_loader'], 20);
		#add_filter( 'get_terms_orderby',									[$self, 'orderby_slug_order'], 10, 2);
		#add_action( 'wp_enqueue_scripts', 									[$self, 'woocommerce_scripts'],	20);
		#add_filter('woocommerce_product_get_image',                        [$self, 'woocommerce_product_get_image'], 10, 6);
		add_filter('electro_template_loop_product_thumbnail',               [$self, 'electro_template_loop_product_thumbnail'], 9999);
		add_action('woocommerce_single_product_summary',                    [$self, 'woocommerce_single_product_summary_open'], 10);
		add_action('woocommerce_single_product_summary',                    [$self, 'woocommerce_single_product_summary_remont'], 11);
		add_action('woocommerce_single_product_summary',                    [$self, 'woocommerce_single_product_summary_close'], 11);
	}
	
	public function orderby_slug_order($orderby, $args){
		if(isset($args['orderby']) && 'include' == $args['orderby']){
			$include = implode(',', array_map('sanitize_text_field', $args['include']));
			$orderby = "FIELD( t.term_order, $include )";
		}
		
		return $orderby;
	}
	
	public function comments_template_loader($template){
		
		if(get_post_type() !== 'product' || !apply_filters('electro_use_advanced_reviews', true)){
			return $template;
		}
		
		$check_dirs = array(
			trailingslashit(get_stylesheet_directory()).'templates/shop/',
			trailingslashit(get_template_directory()).'templates/shop/',
			trailingslashit(get_stylesheet_directory()).WC()->template_path(),
			trailingslashit(get_template_directory()).WC()->template_path(),
			trailingslashit(get_stylesheet_directory()),
			trailingslashit(get_template_directory()),
			trailingslashit(WC()->plugin_path()).'templates/'
		);
		
		if(WC_TEMPLATE_DEBUG_MODE){
			$check_dirs = array(array_pop($check_dirs));
		}
		
		foreach($check_dirs as $dir){
			if(file_exists(trailingslashit($dir).'single-product-advanced-reviews.php')){
				return trailingslashit($dir).'single-product-advanced-reviews.php';
			}
		}
	}
	
	public function modify_product_tabs($tabs){
		
		global $product, $post;
		
		$product_id                        = electro_wc_get_product_id($product);
		$specifications                    = get_post_meta($product_id, '_specifications', true);
		$specifications_display_attributes = get_post_meta($product_id, '_specifications_display_attributes', true);
		
		if(isset($tabs['description'])){
			$tabs['description']['callback'] = 'electro_product_description_tab';
		}
		
		if(isset($tabs['reviews'])){
			$tabs['reviews']['title'] = esc_html__('Reviews', 'electro');
		}
		
		if(isset($tabs['additional_information'])){
			$tabs['additional_information']['title'] = esc_html__('Specification', 'electro');
		}
		
		if(isset($tabs['additional_information'])){
			unset($tabs['additional_information']);
		}
		
		// Specification tab - shows attributes
		if($product && (!empty($specifications) || ($specifications_display_attributes == 'yes' && ($product->has_attributes() || (apply_filters('wc_product_enable_dimensions_display', true) && ($product->has_dimensions() || $product->has_weight())))))){
			$tabs['specification'] = array(
				'title'    => esc_html__('Specification', 'electro'),
				'priority' => 20,
				'callback' => 'electro_product_specification_tab'
			);
		}
		
		$accessories = Electro_WC_Helper::get_accessories($product);
		
		if(sizeof($accessories) !== 0 && array_filter($accessories) && $product->is_type(array('simple', 'variable'))){
			$tabs['accessories'] = array(
				'title'    => esc_html__('Accessories', 'electro'),
				'priority' => 5,
				'callback' => 'electro_product_accessories_tab',
			);
		}
		
		return $tabs;
	}
	
	public function hide_tab_heading($heading){
		return '';
	}
	
	public function modify_linked_product_args($args){
		
		if('full-width' === electro_get_single_product_layout()){
			
			$args['columns']        = 5;
			$args['posts_per_page'] = 5;
			
		}else{
			
			$args['columns']        = 4;
			$args['posts_per_page'] = 4;
			
		}
		
		return $args;
	}
	
	public function woocommerce_scripts(){
		global $electro_version;
		
		if(is_checkout() && apply_filters('electro_sticky_order_review', false)){
			wp_enqueue_script('waypoints-sticky-js', get_template_directory_uri().'/assets/js/waypoints-sticky.min.js', array('jquery'), $electro_version, true);
			wp_enqueue_script('electro-sticky-payment', get_template_directory_uri().'/assets/js/checkout.min.js', array('jquery'), $electro_version, true);
		}
	}
	
	public function electro_template_loop_product_thumbnail($thumbnail){
		global $product;
		
		if(is_product_category(self::$remont_cats)){
			$exclude = false;
			$product_name = $product->get_name();
			foreach(self::$remont_exclude_phrase as $needle){
				if(mb_stripos($product_name, $needle, 0, 'utf-8') !== false){
					$exclude = true;
				}
			}
			
			if(!$exclude){
				$t         = __('Repair', THEME_TD);
				$a         = sprintf('<a href="%s" class="remont-icon" title="%s">%s</a>', self::$remont_link, $t, $t);
				$div       = '<div class="product-thumbnail product-item__thumbnail">';
				$thumbnail = str_replace($div, $div.$a, $thumbnail);
			}
		}
		
		return $thumbnail;
	}
	
	public function woocommerce_single_product_summary_remont(){
		global $product;
		
		
		if(has_term(self::$remont_cats, 'product_cat', $product->ID)){
			$exclude = false;
			$product_name = $product->get_name();
			foreach(self::$remont_exclude_phrase as $needle){
				if(mb_stripos($product_name, $needle, 0, 'utf-8') !== false){
					$exclude = true;
				}
			}
			#_debug($exclude ? 1 : 0);
			
			if(!$exclude){
				$t = __('Repair', THEME_TD);
				echo sprintf('<a href="%s" class="remont-icon full" title="%s">%s</a>', self::$remont_link, $t, $t);
			}
		}
	}
	
	public function woocommerce_single_product_summary_open(){
		echo '<div class="brand-wrap">';
	}
	public function woocommerce_single_product_summary_close(){
		echo '</div>';
	}
}

