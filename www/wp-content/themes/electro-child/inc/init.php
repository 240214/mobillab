<?php
/**
 * Theme functions loads the main theme class and extra options
 */

namespace Digidez;

require 'constants.inc.php';

/*
 * Эти два класса нужно активировать, если необходимо будет использовать шаблон
 * /wp-content/themes/electro-child/templates/homepage/_products-carousel-tabs.php
 * или дочернюю функцию electro_home_v2_products_carousel_3 из файла
 * /wp-content/themes/electro-child/inc/override-functions.php
 */
//require 'class.wc_shortcodes_child.php';
//require 'class.wc_shortcode_products_child.php';

//require 'class.google_calendar.inc.php';
require 'class.actions.inc.php';
require 'class.ajax_actions.inc.php';
require 'class.filters.inc.php';
require 'class.functions.inc.php';
require 'class.theme_woocommerce.php';
//require 'class.custom_post_types.inc.php';
//require 'class.custom_taxonomies.inc.php';
require 'class.shortcodes.inc.php';
//require 'class.api.inc.php';
#require 'class.theme_js_composer.inc.php';
//require_once 'vendor/autoload.php';
//require_once 'vendor/updates.php';
//require_once 'vendor/media_uploader_cb.php';
require_once 'vendor/aq_resizer.php';
//require 'vendor/browser.php';                     #* Browser Detect Library
require_once 'vendor/device.php';                   #* Mobile Detect Library
//require 'vendor/enqueue.php';                     #* Enqueue scripts and styles.
//require 'vendor/pagination.php';                  #* Custom template tags for this theme.
//require 'vendor/extras.php';                      #* Custom functions that act independently of the theme templates.
//require 'vendor/customizer.php';                  #* Customizer additions.
//require 'vendor/custom-comments.php';             #* Custom Comments file.
//require 'vendor/jetpack.php';                     #* Load Jetpack compatibility file.
//require 'vendor/bootstrap-wp-navwalker.php';      #* Load custom WordPress nav walker.
#require 'vendor/woocommerce.php';                 #* Load WooCommerce functions.
//require 'vendor/editor.php';                      #* Load Editor functions.


// Раскомментировать, если собираетесь использовать этот класс require 'class.wc_shortcodes_child.php'; выше
//WC_Shortcodes_Child::init();

Actions::initialise();
Ajax_Actions::initialise();
Filters::initialise();
Functions::initialise();
Theme_WooCommerce::initialise();
//Custom_Post_Types::initialise();
//Custom_Taxonomies::initialise();
Shortcodes::initialise();
#API::initialise();
//$gcalendar = new Google_Calendar();

//Actions::wc_update_product_lookup_tables_column('min_max_price');