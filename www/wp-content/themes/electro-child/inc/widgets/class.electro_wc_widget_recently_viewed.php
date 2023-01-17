<?php
/**
 * Recent Products Widget (Electro-Child).
 * @package WooCommerce/Widgets
 * @version 3.3.0
 */

use Digidez\Functions;

defined('ABSPATH') || exit;

require_once ABSPATH.'/wp-content/plugins/woocommerce/includes/abstracts/abstract-wc-widget.php';

/**
 * Widget recently viewed.
 */
class Electro_WC_Widget_Recently_Viewed extends WC_Widget{

	/**
	 * Constructor.
	 */
	public function __construct(){
		$this->widget_cssclass    = 'woocommerce widget_recently_viewed_products';
		$this->widget_description = __("Display a list of a customer's recently viewed products.", 'woocommerce');
		$this->widget_id          = 'electro_woocommerce_recently_viewed_products';
		$this->widget_name        = __('Electro-Child Recent Viewed Products', THEME_TD);
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __('Recently Viewed Products', 'woocommerce'),
				'label' => __('Title', 'woocommerce'),
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => 15,
				'std'   => 10,
				'label' => __('Number of products to show', 'woocommerce'),
			),
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @param array $args Arguments.
	 * @param array $instance Widget instance.
	 *
	 * @see WP_Widget
	 */
	public function widget($args, $instance){
		$viewed_products = !empty($_COOKIE['electro_wc_recently_viewed']) ? (array)explode('|', wp_unslash($_COOKIE['electro_wc_recently_viewed'])) : array(); // @codingStandardsIgnoreLine
		$viewed_products = array_reverse(array_filter(array_map('absint', $viewed_products)));

		#Functions::_debug($args);

		if(empty($viewed_products)){
			$args['before_widget'] = '<div class="widget-column"><aside class="widget clearfix woocommerce widget_recently_viewed_products_empty"><div class="body">';
			$number = !empty($instance['number']) ? absint($instance['number']) : $this->settings['number']['std'];
			ob_start();
			$this->widget_start($args, $instance);
			echo wp_kses_post(apply_filters('woocommerce_before_widget_product_list', '<div class="product_list_widget">'));
			echo sprintf(__('Here will be displayed the last viewed %d products', THEME_TD), $number);
			echo wp_kses_post(apply_filters('woocommerce_after_widget_product_list', '</div>'));
			$this->widget_end($args);
			$content = ob_get_clean();
		}else{

			ob_start();

			$number = !empty($instance['number']) ? absint($instance['number']) : $this->settings['number']['std'];

			$query_args = array(
				'posts_per_page' => $number,
				'no_found_rows'  => 1,
				'post_status'    => 'publish',
				'post_type'      => 'product',
				'post__in'       => $viewed_products,
				'orderby'        => 'post__in',
			);

			if('yes' === get_option('woocommerce_hide_out_of_stock_items')){
				$query_args['tax_query'] = array(
					array(
						'taxonomy' => 'product_visibility',
						'field'    => 'name',
						'terms'    => 'outofstock',
						'operator' => 'NOT IN',
					),
				); // WPCS: slow query ok.
			}

			$r = new WP_Query(apply_filters('woocommerce_recently_viewed_products_widget_query_args', $query_args));

			if($r->have_posts()){
				$this->widget_start($args, $instance);
				echo wp_kses_post(apply_filters('woocommerce_before_widget_product_list', '<ul class="product_list_widget">'));
				$template_args = array(
					'widget_id' => $args['widget_id'],
				);
				while($r->have_posts()){
					$r->the_post();
					wc_get_template('content-widget-product.php', $template_args);
				}
				echo wp_kses_post(apply_filters('woocommerce_after_widget_product_list', '</ul>'));
				$this->widget_end($args);
			}

			wp_reset_postdata();
			$content = ob_get_clean();
		}

		echo $content; // WPCS: XSS ok.
	}

}
