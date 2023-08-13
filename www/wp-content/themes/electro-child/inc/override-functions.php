<?php

//namespace Digidez;
use Digidez\Functions;
use Digidez\Actions;
#remove_action( 'woocommerce_scheduled_sales', 'wc_scheduled_sales' );

function electro_off_canvas_nav(){
	Functions::electro_off_canvas_nav();
}

function woocommerce_product_loop_start($echo = true){
	Functions::woocommerce_product_loop_start($echo);
}

function electro_shop_view_switcher(){
	Functions::electro_shop_view_switcher();
}

function electro_wc_products_per_page(){
	Functions::electro_wc_products_per_page();
}

function electro_footer_bottom_widgets_v2(){
	Functions::electro_footer_bottom_widgets_v2();
}

function electro_enqueue_scripts(){
	Actions::electro_enqueue_scripts();
}

function electro_home_v2_hook_control(){
	Actions::electro_home_v2_hook_control();
}
#function woocommerce_result_count() {}
#function woocommerce_catalog_ordering() {}
/*
function electro_product_cards_carousel($section_args, $carousel_args){
	Actions::electro_product_cards_carousel($section_args, $carousel_args);
}

function electro_home_v2_products_carousel_3(){
	Actions::electro_home_v2_products_carousel_3();
}
*/
