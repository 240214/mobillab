<?php
/**
 * Products Carousel Tab
 *
 * @package Electro/Templates
 */

use Digidez\Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args['nav-align'] = empty ( $args['nav-align'] ) ? 'center' : $args['nav-align'];
$section_class = empty( $section_class ) ? 'products-carousel-tabs' : 'products-carousel-tabs ' . $section_class;

if ( ! empty( $animation ) ) {
	$section_class .= ' animate-in-view';
}

$tab_uniqid = 'home-tab-' . uniqid();

?><section class="<?php echo esc_attr( $section_class ); ?>" <?php if ( ! empty( $animation ) ): ?>data-animation="<?php echo esc_attr( $animation ); ?>"<?php endif; ?>>
	<h2 class="sr-only"><?php echo esc_html__( 'Product Carousel Tabs', 'electro' ); ?></h2>
	<ul class="nav nav-inline text-xs-<?php echo esc_attr( $args['nav-align'] ); ?>">

	<?php

	foreach( $args['tabs'] as $key => $tab ) {

		$tab_id = ! empty( $tab['id'] ) ? $tab['id'] : $tab_uniqid . '-' . $key;

	?>
		<li class="nav-item">
			<a class="nav-link<?php if ( $key == 0 ) echo esc_attr( ' active' ); ?>" href="#<?php echo esc_attr( $tab_id ); ?>" data-toggle="tab">
				<?php echo wp_kses_post ( $tab['title'] ); ?>
			</a>
		</li>

	<?php }	?>

	</ul>

	<div class="tab-content">

		<?php

		foreach( $args['tabs'] as $key => $tab ) :

			$tab_id = ! empty( $tab['id'] ) ? $tab['id'] : $tab_uniqid . '-' . $key;
		?>

		<div class="tab-pane <?php if ( $key == 0 ) echo esc_attr( 'active' ); ?>" id="<?php echo esc_attr( $tab_id ); ?>" role="tabpanel">

		<?php
			$default_atts 	= array( 'per_page' => intval( $args['limit'] ), 'columns' => intval( $args['columns'] ) );
			if ( isset( $args['columns_wide'] ) ) {
				$default_atts['columns_wide'] = $args['columns_wide'];
			}
			$atts 			= isset( $tab['atts'] ) ? $tab['atts'] : array();
			$atts 			= wp_parse_args( $atts, $default_atts );

			if ( $tab['shortcode_tag'] == 'products' && !isset( $atts['orderby'] ) ) {
				$atts['orderby'] = 'post__in';
			}

			#Functions::_debug($tab['shortcode_tag']);
			#Functions::_debug($atts);
			#$GLOBALS['debug'] = $tab['shortcode_tag'] == 'product_category';
			$products_html = Functions::electro_do_shortcode($tab['shortcode_tag'], $atts);
			#Functions::_debug($products_html);


			$section_args = array(
				'products_html'		=> $products_html,
				'show_custom_nav'	=> false
			);

			if( ! isset( $carousel_args ) ) {
				$carousel_args = array(
					'items'			=> intval( $args['columns'] ),
					'responsive'	=> array(
						'0'		=> array( 'items'	=> 2 ),
						'480'	=> array( 'items'	=> 2 ),
						'768'	=> array( 'items'	=> 2 ),
						'992'	=> array( 'items'	=> 3 ),
						'1200'	=> array( 'items' => intval( $args['columns'] ) )
					)
				);
			}

			electro_products_carousel( $section_args, $carousel_args );
		?>
		</div>

		<?php endforeach; ?>

	</div>
</section>
