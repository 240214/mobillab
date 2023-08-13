<?php
namespace Digidez;

use Electro_WC_Helper;
use WP_Query;

class Actions {

	var $active_sidebars = array(
		//'menu-bar',
		//'left-sidebar',
		//'shop-flat-sidebar',
		//'hero',
		//'statichero',
		//'footerfull',
		//'footer_col_1',
		//'footer_col_2',
		//'footer_col_3',
		'footer-bottom-widget-full',
		'after-product-tabs-widget',
	);

    public static function initialise(){
        $self = new self();

        // define all action hooks here and document if not self explanatory
	    remove_action('wp_head','feed_links_extra', 3); // ссылки на дополнительные rss категорий
	    remove_action('wp_head','feed_links', 2); //ссылки на основной rss и комментарии
	    remove_action('wp_head','rsd_link');  // для сервиса Really Simple Discovery
	    remove_action('wp_head','wlwmanifest_link'); // для Windows Live Writer
	    remove_action('wp_head','wp_generator');  // убирает версию wordpress
	    //отключение Emoji start
	    remove_action('wp_head', 'print_emoji_detection_script', 7 );
	    remove_action('wp_print_styles', 'print_emoji_styles' );
	    // убираем разные ссылки при отображении поста - следующая, предыдущая запись, оригинальный url и т.п.
	    //remove_action('wp_head','start_post_rel_link',10,0);
	    //remove_action('wp_head','index_rel_link');
	    //remove_action('wp_head','rel_canonical');
	    //remove_action( 'wp_head','adjacent_posts_rel_link_wp_head', 10, 0 );
	    //remove_action( 'wp_head','wp_shortlink_wp_head', 10, 0 );

	    add_action('init', array($self, 'delete_post_type'));
	    //add_action('init', array($self, 'unregister_tags'));
	    add_action('widgets_init', array($self, 'sidebars_init'));
	    add_action('after_setup_theme', array($self, 'theme_setup'));
	    //add_action('after_setup_theme', array($self, 'footer_enqueue_scripts'), 100);

	    add_action('wp_enqueue_scripts', array($self, 'dequeueCssAndJavascript'), 99);
        add_action('wp_enqueue_scripts', array($self, 'enqueueCssAndJavascript'), 100);
	    add_action('admin_enqueue_scripts', array($self, 'enqueueCssAndJavascriptAdmin'));
	    //add_action('admin_menu', array($self, 'change_admin_menu'), 999);
	    //add_action('admin_init', array($self, 'wph_hide_editor'));

	    add_action('template_redirect', array($self, 'template_redirect'), 10);

	    //add_action('get_menu_bar_widget', array($self, 'get_menu_bar_widget'));

	    //add_action('wpcf7_before_send_mail', array($self, 'change_wpcf7_recipient_before_send_mail'), 10, 3);
	    //add_action('wpcf7_mail_sent', array($self, 'custom_wpcf7_mail_sent'));
	    //add_action('wpcf7_mail_failed', array($self, 'custom_wpcf7_mail_failed'));

	    if(is_admin()){
		    add_action('save_post', array($self, 'save_post_product'), 19, 3);

		    // Save and Close
		    add_action('post_submitbox_misc_actions', array($self, 'add_button'));
		    add_action('admin_notices', array($self, 'saved_notice'));

		    add_action('manage_post_posts_custom_column', array($self, 'fetch_post_custom_columns'));
		    add_action('manage_page_posts_custom_column', array($self, 'fetch_page_custom_columns'));
		    add_action('manage_product_posts_custom_column', array($self, 'fetch_product_custom_columns'));
		    //add_action('manage_acf-field-group_posts_custom_column', array($self, 'fetch_acf_field_group_custom_columns'));
		    add_action('pre_get_posts', array($self, 'pre_get_posts_admin'), 10);
	    }

	    //add_action('show_user_profile', array($self, 'extra_profile_fields'), 10);
	    //add_action('edit_user_profile', array($self, 'extra_profile_fields'), 10);
	    //add_action('personal_options_update', array($self, 'save_extra_profile_fields'));
	    //add_action('edit_user_profile_update', array($self, 'save_extra_profile_fields'));

	    //add_action('acf/init', array($self, 'acf_init'));

	    //remove_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10);
	    //remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
	    //remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
	    /*
	    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
	    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
	    remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
	    remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
	    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
	    remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
	    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
	    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
	    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
	    remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
	    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
	    remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
	    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

	    add_action('woocommerce_shop_loop_item_title', array($self, 'woocommerce_shop_loop_item_title'), 10);
	    add_action('woocommerce_before_shop_loop_item_title', array($self, 'woocommerce_template_loop_product_thumbnail'), 10);

	    add_action('woocommerce_before_single_product_summary', array($self, 'woocommerce_show_product_sale_flash'), 6);
	    add_action('woocommerce_single_product_summary', array($self, 'woocommerce_template_single_logo'), 5);
	    add_action('woocommerce_single_product_summary', array($self, 'woocommerce_template_single_title'), 6);
	    add_action('woocommerce_single_product_summary', 'woocommerce_show_product_sale_flash', 7);
	    add_action('woocommerce_single_product_summary', array($self, 'woocommerce_show_product_delim_after_price'), 19);
	    //add_action('woocommerce_single_product_summary_category', array($self, 'woocommerce_template_single_category'), 5);
	    add_action('woocommerce_single_product_summary_image', array($self, 'woocommerce_show_product_image'), 10);
	    add_action('woocommerce_single_variation_cart_button', array($self, 'woocommerce_single_variation_add_to_cart_button'), 10);
	    add_action( 'woocommerce_after_single_product', array($self, 'woocommerce_output_related_products'), 10);
	    add_action( 'woocommerce_review_order_before_submit', array($self, 'woocommerce_review_order_before_submit'), 10);
	    add_action( 'woocommerce_before_single_product', array($self, 'woocommerce_back_to_prev_step'), 20);

	    add_action( 'premmerce_filter_render_item_before', array($self, 'premmerce_filter_render_item_before'), 10);
	    */


	    add_action('electro_after_page', array($self, 'electro_after_page'), 10);
	    add_action('electro_before_header', array($self, 'electro_before_header'), 1);

	    add_action('wc_update_product_lookup_tables_column', array($self, 'wc_update_product_lookup_tables_column'), 90);
	    add_action('woocommerce_after_single_product_summary', array($self, 'woocommerce_after_single_product_summary'), 11);
	    add_action('woocommerce_single_product_summary', array($self, 'woocommerce_template_single_meta_after_title'), 12);
	    add_action('init', array($self, 'ec_child_add_brand_name_to_loop'), 20);
	    //add_action('woocommerce_before_add_to_cart_form', array($self, 'woocommerce_before_add_to_cart_form'), 20);
	    add_action('ywpar_after_single_product_summary', array($self, 'woocommerce_before_add_to_cart_form'), 1);
	    add_action('woocommerce_before_checkout_form', array($self, 'woocommerce_before_checkout_form'), 5);

    }

	public static function template_redirect(){
    	global $wp_query, $post;

		$is_product = (strstr($_SERVER['REQUEST_URI'], '/product/') !== false) ? true : false;
		$redirect_location = '/tovar-ne-dostupen/';
		$status = 302;
		$slug = 'templates//page/product-not-found';

		#Functions::_debug($wp_query, 1); exit;

		if($is_product && is_404()){
			http_response_code($status);
			Functions::get_template_part($slug);
			//wp_redirect($redirect_location, $status);
			exit;
		}
	}

	public function sidebars_init(){
	    $sidebars = include_once "options/sidebars.php";
	    require_once 'widgets/class.electro_wc_widget_recently_viewed.php';

	    foreach($sidebars as $sidebar){
	    	if(in_array($sidebar['id'], $this->active_sidebars)){
			    register_sidebar($sidebar);
		    }
	    }

	    register_widget('Electro_WC_Widget_Recently_Viewed');
    }

	public function theme_setup(){
		load_theme_textdomain( THEME_TD, THEME_DIR . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(array(
			'primary' => __('Primary Menu', THEME_TD),
			'mobile'    => __('Mobile Nav', THEME_TD),
			'footer'  => __('Footer Menu', THEME_TD),
			'user' => __('User Menu', THEME_TD),
		));

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			//'search-form',
			//'comment-form',
			//'comment-list',
			'gallery',
			'caption',
		) );

		/*
		 * Adding Thumbnail basic support
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Adding support for Widget edit icons in customizer
		 */
		//add_theme_support( 'customize-selective-refresh-widgets' );

		/*
		 * Enable support for Post Formats.
		 * See http://codex.wordpress.org/Post_Formats
		 */
		/*add_theme_support( 'post-formats', array(
			'aside',
			'image',
			'video',
			'quote',
			'link',
		) );*/

		// Set up the WordPress core custom background feature.
		//add_theme_support('custom-background', array('default-color' => 'ffffff', 'default-image' => ''));

		// Set up the WordPress Theme logo feature.
		//add_theme_support('custom-logo');

		// Check and setup theme default settings.
		/*$posts_index_style = get_theme_mod('theme_posts_index_style');
		if('' == $posts_index_style){
			set_theme_mod('theme_posts_index_style', 'default');
		}*/

		// Sidebar position.
		/*$sidebar_position = get_theme_mod('theme_sidebar_position');
		if('' == $sidebar_position){
			set_theme_mod('theme_sidebar_position', 'right');
		}*/

		// Container width.
		/*$container_type = get_theme_mod('theme_container_type');
		if('' == $container_type){
			set_theme_mod('theme_container_type', 'container');
		}*/

	}

	// не используется
	public function footer_enqueue_scripts(){
		remove_action('wp_head','wp_print_scripts');
		remove_action('wp_head','wp_print_head_scripts',9);
		//remove_action('wp_head','wp_enqueue_scripts',1);

		add_action('wp_footer','wp_print_scripts',5);
		add_action('wp_footer','wp_print_head_scripts',5);
		//add_action('wp_footer','wp_enqueue_scripts',5);
	}

	public function enqueueCssAndJavascript(){
    	global $post, $electro_version;

		$page_template = Functions::get_page_template();
		//Functions::_debug($page_template);

	    //wp_enqueue_style('child-font-Sailec', get_stylesheet_directory_uri().'/assets/fonts/Sailec.css');
	    //wp_enqueue_style('child-font-Champion', 'https://cloud.typography.com/7838156/7276192/css/fonts.css');
	    //wp_enqueue_style('fullPage', get_stylesheet_directory_uri().'/assets/js/fullPage/jquery.fullPage.css');
	    //wp_enqueue_style('fonts-google', 'https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700&amp;subset=latin-ext');
		//wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0', false);
		//wp_enqueue_style('typekit', '//use.typekit.net/wxo1cuh.css', array(), '1.0');
	    //wp_enqueue_style('bootstrap', CSS_URI.'/bootstrap.min.css');
	    //wp_enqueue_style('bootstrap-select', CSS_URI.'/bootstrap-select.css');
		if($page_template == 'page-termin-vereinbaren'){
			//wp_enqueue_style('bootstrap-scrolling-tabs', CSS_URI.'/jquery.scrolling-tabs.min.css', array(), '2.6.1');
		}
		//wp_enqueue_style('owl-main', CSS_URI.'/OwlCarousel2/owl.carousel.css');
		//wp_enqueue_style('owl-theme', CSS_URI.'/OwlCarousel2/owl.theme.default.css');
	    //wp_enqueue_style('slick', CSS_URI.'/slick/slick.css');
	    //wp_enqueue_style('slick-theme', CSS_URI.'/slick/slick-theme.css');
		//wp_enqueue_style(THEME_SHORT.'-theme-fonts', FONTS_URI.'/stylesheet.css', array(), false, 'all');
		//wp_enqueue_style(THEME_SHORT.'-animate', CSS_URI.'/animate.min.css', array(), '3.7.2', 'all');
	    $ft = filemtime(CSS_DIR.'/style.css');
	    wp_enqueue_style(THEME_SHORT.'-theme', CSS_URI.'/style.css', array(), $ft, 'all');

	    //wp_enqueue_script('tweenmax-script', get_stylesheet_directory_uri().'/assets/js/TweenMax.min.js', array(), '1.9.7', true);
	    //wp_enqueue_script('superscrollorama-script', get_stylesheet_directory_uri().'/assets/js/jquery.superscrollorama.js', array(), '1.9.7', true);
	    //wp_enqueue_script('easings-script', get_stylesheet_directory_uri().'/assets/js/fullPage/vendors/easings.js', array(), '5.2.0', true);
	    //wp_enqueue_script('scrolloverflow-script', get_stylesheet_directory_uri().'/assets/js/fullPage/vendors/scrolloverflow.js', array(), '5.2.0', true);
	    //wp_enqueue_script('fullPage-script', get_stylesheet_directory_uri().'/assets/js/fullPage/jquery.fullPage.js', array(), '2.9.7', true);
	    //wp_enqueue_script('scrollify-script', get_stylesheet_directory_uri().'/assets/js/jquery.scrollify.js', array('jquery'), '1.0.19', true);
		//wp_enqueue_script('flickity-script', get_stylesheet_directory_uri().'/assets/js/flickity.pkgd.min.js', array(), '2.0.10', true);
	    //$ft = filemtime(JS_DIR.'/main.js');
	    //wp_enqueue_script(THEME_SHORT.'-theme-js', JS_URI.'/main.js', array(), $ft, true);
		//wp_enqueue_script('bootstrap', JS_URI.'/bootstrap.min.js', array('jquery'), '3.3.7', true);
		//wp_enqueue_script('bootstrap-select', JS_URI.'/bootstrap-select.js', array('jquery'), '1.12.2', true);
		wp_enqueue_script('jquery-cookie', JS_URI.'/jquery.cookie.min.js', array('jquery'), '1.4.1', true);
		//wp_enqueue_script('owl-script', JS_URI.'/OwlCarousel2/dist/owl.carousel.min.js', array('jquery'), '2.3.4', true);
		//wp_enqueue_script('slick', JS_URI.'/slick/slick.min.js', array('jquery'), '1.9.0', true);
		//wp_enqueue_script('wow', JS_URI.'/wow.min.js', array('jquery'), '1.1.3', true);
		//wp_enqueue_script('shuffle', JS_URI.'/shuffle.min.js', array(), '5.2.1', true);
		//wp_enqueue_script('theta-carousel', JS_URI.'/theta-carousel/theta-carousel.min.js', array('jquery'), '1.6.4', true);
		//wp_enqueue_script('mustache', JS_URI.'/theta-carousel/mustache.min.js', array('jquery'), '1.4.0', true);

		if($page_template == 'page-termin-vereinbaren'){
			//wp_enqueue_script('jquery-easing', JS_URI.'/jquery.easing.min.js', array('jquery'), '1.3', true);
			//wp_enqueue_script('bootstrap-scrolling-tabs', JS_URI.'/jquery.scrolling-tabs.min.js', array('jquery'), '2.6.1', true);
		}
		if($page_template == 'page-home' || $page_template == 'page-about-us'){
			//wp_enqueue_script('parallax-js', JS_URI.'/parallax-js/parallax.min.js', array('jquery'), '1.5.0', true);
		}

        /* #Commented since 2022-10-30
		$google  = get_field('google', 'option');
		$api_key = $google['map_api_key'];
		$api_key = !empty($api_key) ? 'key='.$api_key : '';
		$api_key = esc_attr($api_key);
		wp_enqueue_script(THEME_SHORT.'-google-map-api', 'https://maps.googleapis.com/maps/api/js?'.$api_key.'&v=3.35');
		wp_enqueue_script(THEME_SHORT.'-google-map', JS_URI.'/acf-map.js', array('jquery', THEME_SHORT.'-google-map-api'));
        */
		if($page_template == 'page-about-us'){
			//wp_enqueue_script(THEME_SHORT.'-waypoint', JS_URI.'/waypoints/lib/jquery.waypoints.min.js', array('jquery'));
			//wp_enqueue_script(THEME_SHORT.'-odometer', JS_URI.'/odometer/odometer.min.js', array('jquery'));
		}

		//wp_enqueue_script(THEME_SHORT.'-electro-js', JS_URI.'/electro.js', array('jquery', 'bootstrap-js'), $electro_version, true);

		$globals_atts = array(
			'device' => Functions::$device,
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('wcpc-ajax-nonce'),
			'form_id' => 0,
			'checkout_payment_action' => 'checkout_payment_request',
			'lang' => array(
				'show_all' => __('Show all', THEME_TD),
				'sending_data' => __('Sending data ...', THEME_TD),
				'loading_calendar' => __('Loading calendar ...', THEME_TD),
			),
		);
		$ft = filemtime(JS_DIR.'/frontend.js');
		wp_enqueue_script(THEME_SHORT.'-child-theme-js', JS_URI.'/frontend.js', array('jquery'), $ft, true);
		wp_localize_script(THEME_SHORT.'-child-theme-js', 'globals', $globals_atts);
	}

    public function enqueueCssAndJavascriptAdmin(){
	    //wp_enqueue_style(THEME_SHORT.'-admin-forms', CSS_URI.'/forms.css', array(), false, false);
	    wp_enqueue_style(THEME_SHORT.'-admin-styles', CSS_URI.'/admin.css', array(), false, false);
    }

	public function dequeueCssAndJavascript(){
    	//wp_dequeue_script('electro-js');
		//wp_dequeue_style('roboto');
		//wp_dequeue_style('open-sans');
	}

	public function page_excerpt(){
		//add_post_type_support('page', array('excerpt'));
		remove_post_type_support('page', 'comments');
	}

	public function delete_post_type(){
    	//...
	}

	public function unregister_tags(){
		unregister_taxonomy_for_object_type('post_tag', 'post');
	}

	// не используется
	public function change_admin_menu(){
		global $menu;
		global $submenu;
		#Functions::_debug($submenu);

		$menu[5][0] = __('News', THEME_TD);
		//$submenu['edit.php'][5][0] = __('News Items', 'avia_framework');
		//$submenu['edit.php'][10][0] = __('Add News Item', 'avia_framework');


		//remove_menu_page('options-general.php'); // Удаляем раздел Настройки
		//remove_menu_page('tools.php'); // Инструменты
		//remove_menu_page('users.php'); // Пользователи
		//remove_menu_page('plugins.php'); // Плагины
		//remove_menu_page('themes.php'); // Внешний вид
		//remove_menu_page('edit.php'); // Посты блога
		//remove_menu_page('upload.php'); // Медиабиблиотека
		//remove_menu_page('edit.php?post_type=page'); // Страницы
		remove_menu_page('edit-comments.php'); // Комментарии
		remove_menu_page('link-manager.php'); // Ссылки
		//remove_menu_page('wpcf7');   // Contact form 7
		//remove_menu_page('options-framework'); // Cherry Framework

		//remove_submenu_page( 'themes.php', 'themes.php'); // Удаляем подпункт с выбором тем
		remove_submenu_page( 'themes.php', 'theme-editor.php'); // Редактирование шаблона
		remove_submenu_page( 'themes.php', 'customize.php'); // Редактирование шаблона
		//remove_submenu_page( 'themes.php', 'theme_options' ); // Настройки темы
		//remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' ); // Скрытие Тегов для Постов
		//remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=category' ); // Скрытие Категорий для Постов

		// Removing customize.php
		unset($submenu['themes.php'][6]);
	}

	// не используется
	public function wph_hide_editor(){
		$post_id = null;

    	if(isset($_GET['post'])){
		    $post_id = $_GET['post'];
	    }else{
		    if(isset($_POST['post_ID'])){
			    $post_id = $_POST['post_ID'];
		    }
	    }

		if(is_null($post_id)) return;

		$template_file = get_post_meta($post_id, '_wp_page_template', true);
		#Functions::_debug($template_file);
		if($template_file != 'default'){
			remove_post_type_support('page', 'editor');
		}
	}

	// не используется
	public function acf_init(){
		$google  = get_field('google', 'option');
		$api_key = $google['map_api_key'];
		$api_key = esc_attr($api_key);
		#Functions::_debug($api_key);
		acf_update_setting('google_api_key', $api_key);
	}

	public function fetch_page_custom_columns($column){
		global $post;
		#\Digidez\Functions::_debug($column);
		switch($column){
			case 'featured-image':
				$editlink = get_edit_post_link($post->ID);
				echo '<a href="'.$editlink.'">'.get_the_post_thumbnail($post->ID, 'medium').'</a>';
				break;
			case 'template':
				echo trim(str_replace(array(ABSPATH.'wp-content/themes/'.THEME_SHORT, ABSPATH.'wp-content/themes/'.THEME_PARENT, '//'), array('', '', '/'), get_page_template()), '/');
				//echo get_post_meta($post->ID, 'lambda_partner_link', true);
				break;
			default:
				// do nothing
				break;
		}
	}

	public function fetch_post_custom_columns($column){
		global $post;
		#Functions::_debug($column);
		switch($column){
			case 'featured-image':
				$editlink = get_edit_post_link($post->ID);
				echo '<a href="'.$editlink.'">'.get_the_post_thumbnail($post->ID, 'medium').'</a>';
				break;
			case 'ext-url':
				if(class_exists('ACF')){
					echo get_post_meta($post->ID, 'news_external_url', true);
				}else{
					echo '';
				}
				break;
			default:
				// do nothing
				break;
		}
	}

	public function fetch_product_custom_columns($column){
		global $post;
		#Functions::_debug($column);
		switch($column){
			case 'date-modified':
				echo $post->post_modified;
				break;
			case 'product-code':
				if(class_exists('ACF')){
					echo get_field('products_model', $post->ID);
				}else{
					echo get_post_meta($post->ID, 'products_model', true);
				}
				break;
			default:
				// do nothing
				break;
		}
	}

	// не используется
	public function fetch_acf_field_group_custom_columns($column){
		global $post;
		#Functions::_debug($column);
		switch($column){
			case 'location':
				echo '-';
				break;
			default:
				// do nothing
				break;
		}
	}

	// не используется
	public function change_wpcf7_recipient_before_send_mail($contact_form, &$abort, $obj){}

	// не используется
	public function custom_wpcf7_mail_sent($contact_form){}

	// не используется
	public function custom_wpcf7_mail_failed($contact_form){}

	// не используется
	public function extra_profile_fields( $user ) {
		$settings = get_user_meta( $user->ID, 'acf_user_settings', true );
    	?>

		<h3><?php _e('Extra User Details'); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="gallery_height">ACF gallery field height</label></th>
				<td>
					<input type="text" name="acf_user_settings[gallery_height]" id="gallery_height" value="<?=esc_attr($settings['gallery_height']);?>" class="regular-text" /><br />
					<span class="description">Number.</span>
				</td>
			</tr>
		</table>
		<?php

	}

	// не используется
	public function save_extra_profile_fields( $user_id ) {

		if ( !current_user_can( 'edit_user', $user_id ) )
			return false;

		/* Edit the following lines according to your set fields */
		update_usermeta( $user_id, 'acf_user_settings', $_POST['acf_user_settings']);
		//update_usermeta( $user_id, 'yahoo', $_POST['yahoo'] );
		//update_usermeta( $user_id, 'hotmail', $_POST['hotmail'] );
	}

	// не используется
	public function get_menu_bar_widget(){
    	return dynamic_sidebar('menu-bar');
	}

	// не используется
	public function woocommerce_shop_loop_item_title(){
    	global $product;

		$terms = wc_get_product_terms($product->get_id(), 'product_cat', array('fields' => 'id=>name'));
		$_html = array();

		$html = '<div class="product-info">';
		$html .= '<h2 class="product-title fs20"><a href="'.get_the_permalink().'">'.get_the_title().'</a></h2>';
		$html .= '<div class="product-category">';
		if(!empty($terms)){
			foreach($terms as $term_id => $term_name){
				$_html[] = '<a href="'.esc_url(get_term_link($term_id, 'product_cat')).'">'.$term_name.'</a>';
			}
		}
		$html .= implode(', ', $_html);
		$html .= '</div>';
		$html .= '</div>';

		echo $html;
	}

	// не используется
	public function woocommerce_template_loop_product_thumbnail() {
		echo '<a href="'.get_the_permalink().'">'.woocommerce_get_product_thumbnail().'</a>';
	}

	// не используется
	public function woocommerce_show_product_sale_flash(){
    	if(Functions::$device == 'mobile'){
		    wc_get_template('single-product/sale-flash-mobile.php');
	    }
	}

	// не используется
	public function woocommerce_template_single_category(){
    	if(Functions::$device != 'mobile'){
		    wc_get_template('single-product/category.php');
	    }
	}

	// не используется
	public function woocommerce_template_single_title(){
    	if(Functions::$device != 'mobile'){
		    wc_get_template('single-product/title.php');
	    }
	}

	// не используется
	public function woocommerce_show_product_image(){
		wc_get_template('single-product/product-image-single.php');
	}

	// не используется
	public function woocommerce_template_single_logo(){
		wc_get_template('single-product/logo.php');
	}

	// не используется
	public function woocommerce_show_product_delim_after_price(){
    	global $product;

    	if($product->is_type('simple')){
		    echo '<div class="line-space clearfix"></div>';
	    }
	}

	// не используется
	public function woocommerce_single_variation_add_to_cart_button(){
		wc_get_template( 'single-product/add-to-cart/variation-add-to-cart-button.php' );
	}

	// не используется
	public function woocommerce_review_order_before_submit(){
		//wc_get_template('checkout/terms.php');
		/*echo '
		<p class="legal form-row checkbox-legal validate-required woocommerce-invalid woocommerce-invalid-required-field" data-checkbox="terms">
			<label for="legal" class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
				<input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="legal" id="legal">
				<span class="woocommerce-gzd-legal-checkbox-text">Mit deiner Bestellung erklärst du dich mit unseren <a href="http://wordpress-bridestories.p440856.webspaceconfig.de/agb/" target="_blank">Allgemeinen Geschäftsbedingungen</a>, <a href="http://wordpress-bridestories.p440856.webspaceconfig.de/widerrufsrecht/" target="_blank">Widerrufsbestimmungen</a> und <a href="http://wordpress-bridestories.p440856.webspaceconfig.de/datenschutz/" target="_blank">Datenschutzbestimmungen</a> einverstanden.</span>
			</label>
		</p>
		';*/
	}

	// не используется
	public function woocommerce_output_related_products(){
    	if(Functions::$device == 'desktop'){
		    $args = array('posts_per_page' => 3, 'columns' => 3, 'orderby' => 'rand');
	    }elseif(Functions::$device == 'tablet'){
		    $args = array('posts_per_page' => 4, 'columns' => 4, 'orderby' => 'rand');
	    }else{
		    $args = array('posts_per_page' => 4, 'columns' => 4, 'orderby' => 'rand');
	    }
		#Functions::_debug($args);
		woocommerce_related_products(apply_filters('woocommerce_output_related_products_args', $args));
	}

	// не используется
	public function woocommerce_back_to_prev_step(){
    	global $product;

		$back_link = '';

    	if(isset($_SERVER['HTTP_REFERER'])){
			$back_link = $_SERVER['HTTP_REFERER'];
	    }
    	if(empty($back_link)){
		    $product_terms = wc_get_product_terms($product->get_id(), 'product_cat', array('fields' => 'id=>name', 'parent' => 0));
		    if(!empty($product_terms)){
		    	$back_link = get_term_link(key($product_terms), 'product_cat');
		    }
	    }
    	#Functions::_debug($back_link);

    	wc_get_template('single-product/back-link.php', array('back_link' => $back_link));
	}

	// не используется
	public function premmerce_filter_render_item_before($attribute){
    	#Functions::_debug($attribute);

    	echo '<div class="filter__item_label"><div class="lbl">'.$attribute->attribute_label.'</div></div>';
	}

	// ----------- NEW METHODS --------------

	// Save and Close * Adds the custom button into the post edit page
	public static function add_button() {
		// work out if post is published or not
		$status = get_post_status($_REQUEST['post']);
		// if the post is already published, label the button as "update"
		$button_label = ($status == 'publish' || $status == 'private') ? esc_attr__('Update and Close', THEME_TD) : esc_attr__('Publish and Close', THEME_TD);
		$button_label2 = ($status == 'draft' || $status == 'pending') ? esc_attr__('Save and Close', THEME_TD) : '';

		// TODO: fix duplicated IDs
		?>

		<?php if($status == 'draft' || $status == 'pending'):?>
			<div id="major-publishing-actions" style="overflow:hidden">
				<div id="saving-action">
					<input type="hidden" name="saveclose_referer" value="<?=$_SERVER['HTTP_REFERER'];?>&post=<?=$_REQUEST['post'];?>">
					<input type="submit" tabindex="5" value="<?=$button_label2;?>" class="button-primary green" name="save-close">
				</div>
			</div>
		<?php endif;?>
		<div id="major-publishing-actions" style="overflow:hidden">
			<div id="publishing-action">
				<input type="hidden" name="saveclose_referer" value="<?=$_SERVER['HTTP_REFERER'];?>&post=<?=$_REQUEST['post'];?>">
				<input type="submit" tabindex="5" value="<?=$button_label;?>" class="button-primary" name="save-close">
			</div>
		</div>

		<?php
	}

	// Save and Close * Display a notice on the post listing page to inform the user that a post was saved
	public static function saved_notice() {
		if(isset($_GET['lbsmessage'])){
			?>
			<style type="text/css">
				tr#post-<?=$_REQUEST['post'];?> {background-color: #c1ff79 !important;}
			</style>
			<div class="updated">
				<p><?=esc_attr__('Post saved', THEME_TD);?></p>
			</div>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					<?php if(!empty($_REQUEST['post'])):?>
					$('html, body').animate({scrollTop: $('#post-<?=$_REQUEST['post'];?>').offset().top-50}, 800);
					<?php endif;?>
				});
			</script>
			<?php
		}
	}

	public static function wc_update_product_lookup_tables_column($column){
		Functions::_log('BEGIN - '.__CLASS__.'::'.__FUNCTION__);
		if(empty($column)){
			return;
		}
		global $wpdb;


		switch($column){
			case 'min_max_price':
				Functions::_log('Case : min_max_price');
				// Удаляем просроченные акции
				$meta_prices = Functions::get_products_meta_prices();
				if(!empty($meta_prices)){
					foreach($meta_prices as $post_id => $prices){
						$mk = array_keys($prices);
						if(!in_array('_regular_price', $mk)){
							unset($meta_prices[$post_id]);
						}elseif(!in_array('_sale_price', $mk) && intval($prices['_price']) == intval($prices['_regular_price'])){
							unset($meta_prices[$post_id]);
						}elseif(in_array('_sale_price', $mk)){
							if(isset($prices['_sale_price_dates_from']) && isset($prices['_sale_price_dates_to'])){
								if(time() > $prices['_sale_price_dates_to']){
									delete_post_meta($post_id, '_sale_price_dates_from');
									delete_post_meta($post_id, '_sale_price_dates_to');
									delete_post_meta($post_id, '_sale_price');
									update_post_meta($post_id, '_price', $prices['_regular_price']);
									update_post_meta($post_id, '_ywpar_max_point_discount', '');
								}
							}elseif(isset($prices['_sale_price_dates_from']) && !isset($prices['_sale_price_dates_to'])){
								if(time() > $prices['_sale_price_dates_from']){
									delete_post_meta($post_id, '_sale_price_dates_from');
									delete_post_meta($post_id, '_sale_price');
									update_post_meta($post_id, '_price', $prices['_regular_price']);
									update_post_meta($post_id, '_ywpar_max_point_discount', '');
								}
							}elseif(!isset($prices['_sale_price_dates_from']) && isset($prices['_sale_price_dates_to'])){
								if(time() > $prices['_sale_price_dates_to']){
									delete_post_meta($post_id, '_sale_price_dates_to');
									delete_post_meta($post_id, '_sale_price');
									update_post_meta($post_id, '_price', $prices['_regular_price']);
									update_post_meta($post_id, '_ywpar_max_point_discount', '');
								}
							}
						}
					}
				}
				// end
				#Functions::_debug($meta_prices); exit;
				break;
		}

		Functions::_log('END - '.__CLASS__.'::'.__FUNCTION__);
	}

	/**
     * Вычисление и сохранение _sale_price
     * обновлен 2022-11-06
     *
	 * @param $post_ID
	 * @param $post
	 * @param $update
	 * @return void
	 */
	public function save_post_product($post_ID, $post, $update){
        global $wpdb;

		if($post->post_type != 'product') return;
		#Functions::_dd($_POST);

		$_sale_price = get_post_meta($post_ID, '_sale_price', true);
		$_regular_price = get_post_meta($post_ID, '_regular_price', true);

		if(!empty($_sale_price) && intval($_sale_price) < 0){
            $_price = round($_regular_price * (100 - abs($_sale_price)) / 100, -1, PHP_ROUND_HALF_DOWN);
            update_post_meta($post_ID, '_price', $_price);
            update_post_meta($post_ID, '_sale_price', $_price);
            $wpdb->query("UPDATE ".$wpdb->prefix."wc_product_meta_lookup SET min_price = ".$_price.", max_price = ".$_price." WHERE product_id = ".$post_ID);
		}

		Functions::update_product_popup_params($post_ID);
	}

	public function woocommerce_template_single_meta_after_title(){
		wc_get_template('single-product/meta-after-title.php');
	}

	public function woocommerce_after_single_product_summary(){
		dynamic_sidebar('after-product-tabs-widget');
	}

	public function ec_child_add_brand_name_to_loop(){
		remove_action('woocommerce_after_shop_loop_item_title', 'electro_wrap_price_and_add_to_cart', 100);
		add_action('woocommerce_after_shop_loop_item_title', array(__CLASS__, 'electro_template_loop_brand'), 103);
		add_action('woocommerce_after_shop_loop_item_title', 'electro_wrap_price_and_add_to_cart', 105);
	}

	public static function electro_template_loop_brand(){
		global $product;

		$product_id = electro_wc_get_product_id($product);
		$brands_tax = electro_get_brands_taxonomy();
		$terms      = get_the_terms($product_id, $brands_tax);
		$brand_img  = '';
		$brand_name = '';

		if($terms && !is_wp_error($terms)){

			foreach($terms as $term){
				$thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);

				if($thumbnail_id){
					$image_attributes = wp_get_attachment_image_src($thumbnail_id, 'full');

					if($image_attributes){
						$image_src = $image_attributes[0];
					}
				}else{
					$image_src = wc_placeholder_img_src();
				}

				if(!empty($image_src)){
					$image_src = str_replace(' ', '%20', $image_src);
				}
				$brand_name .= '<a href="'.esc_url(get_term_link($term)).'">'.esc_html($term->name).'</a>';
			}
		}

		echo '<div class="loop-brand-name brand">';
		echo (!empty($brand_name)) ? wp_kses_post($brand_name) : '&nbsp;';
		echo '</div>';
	}

	public static function electro_enqueue_scripts(){

		global $electro_version;

		wp_enqueue_script('tether-js', PARENT_THEME_URI.'assets/js/tether.min.js', array('jquery'), $electro_version, true);
		wp_enqueue_script('bootstrap-js', PARENT_THEME_URI.'assets/js/bootstrap.min.js', array('jquery', 'tether-js'), $electro_version, true);

		$waypoints_js_handler = function_exists('is_elementor_activated') && is_elementor_activated() ? 'elementor-waypoints' : 'waypoints-js';
		wp_enqueue_script($waypoints_js_handler, PARENT_THEME_URI.'assets/js/jquery.waypoints.min.js', array('jquery'), $electro_version, true);

		if(apply_filters('electro_enable_sticky_header', true) || apply_filters('electro_enable_hh_sticky_header', false)){
			wp_enqueue_script('waypoints-sticky-js', PARENT_THEME_URI.'assets/js/waypoints-sticky.min.js', array('jquery'), $electro_version, true);
		}

		if(apply_filters('electro_enable_live_search', false)){
			wp_enqueue_script('typeahead', PARENT_THEME_URI.'assets/js/typeahead.bundle.min.js', array('jquery'), $electro_version, true);
			wp_enqueue_script('handlebars', PARENT_THEME_URI.'assets/js/handlebars.min.js', array('typeahead'), $electro_version, true);
		}

		if(apply_filters('electro_enable_scrollup', true)){
			wp_enqueue_script('easing-js', PARENT_THEME_URI.'assets/js/jquery.easing.min.js', array('jquery'), $electro_version, true);
			wp_enqueue_script('scrollup-js', PARENT_THEME_URI.'assets/js/scrollup.min.js', array('jquery'), $electro_version, true);
		}

		if(apply_filters('electro_enable_bootstrap_hover', true)){
			wp_enqueue_script('bootstrap-hover-dropdown-js', PARENT_THEME_URI.'assets/js/bootstrap-hover-dropdown.min.js', array('bootstrap-js'), $electro_version, true);
		}

		wp_enqueue_script('jquery-mCustomScrollbar-js', PARENT_THEME_URI.'assets/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.js', array('jquery'), $electro_version, true);


		wp_enqueue_script('electro-js', JS_URI.'/electro.js', array('jquery', 'bootstrap-js'), $electro_version, true);

		if(is_singular() && comments_open() && get_option('thread_comments')){
			wp_enqueue_script('comment-reply');
		}

		wp_enqueue_script('owl-carousel-js', PARENT_THEME_URI.'assets/js/owl.carousel.min.js', array('jquery'), $electro_version, true);

		wp_enqueue_script('pace', PARENT_THEME_URI.'assets/js/pace.min.js', array('jquery'), $electro_version, true);

		$admin_ajax_url = admin_url('admin-ajax.php');
		$current_lang   = apply_filters('wpml_current_language', null);

		if($current_lang){
			$admin_ajax_url = add_query_arg('lang', $current_lang, $admin_ajax_url);
		}

		$electro_options = apply_filters('electro_localize_script_data', array(
			'rtl'                     => is_rtl() ? '1' : '0',
			'ajax_url'                => $admin_ajax_url,
			'ajax_loader_url'         => PARENT_THEME_URI.'assets/images/ajax-loader.gif',
			'enable_sticky_header'    => apply_filters('electro_enable_sticky_header', true),
			'enable_hh_sticky_header' => apply_filters('electro_enable_hh_sticky_header', false),
			'enable_live_search'      => apply_filters('electro_enable_live_search', false),
			'live_search_limit'       => apply_filters('electro_live_search_limit', 10),
			'live_search_template'    => apply_filters('electro_live_search_template', '<a href="{{url}}" class="media live-search-media"><img src="{{image}}" class="media-left media-object flip pull-left" height="60" width="60"><div class="media-body"><p>{{{value}}}</p></div></a>'),
			'live_search_empty_msg'   => apply_filters('electro_live_search_empty_msg', esc_html__('Unable to find any products that match the current query', 'electro')),
			'deal_countdown_text'     => apply_filters('electro_deal_countdown_timer_clock_text', array(
				'days_text'  => esc_html__('Days', 'electro'),
				'hours_text' => esc_html__('Hours', 'electro'),
				'mins_text'  => esc_html__('Mins', 'electro'),
				'secs_text'  => esc_html__('Secs', 'electro'),
			)),
			'typeahead_options'       => array('hint' => false, 'highlight' => true),
			'offcanvas_mcs_options'   => array(
				'axis'               => 'y',
				'theme'              => 'minimal-dark',
				'contentTouchScroll' => 100,
				'scrollInertia'      => 1500
			),
		));

		wp_localize_script('electro-js', 'electro_options', $electro_options);
	}

	public static function woocommerce_before_add_to_cart_form(){
    	global $product;

    	$products_promotion_method = get_field('products_promotion_method', $product->get_ID());
    	if($products_promotion_method == 'coupons'){
		    $products_promotion_popup_window = get_field('products_promotion_popup_window', $product->get_ID());
		    #Functions::_debug($products_promotion_popup_window);
		    $class = '';
		    if(!empty($products_promotion_popup_window)){
		    	$class = 'popmake-'.strtolower($products_promotion_popup_window->post_title);
		    }
		    echo '<div class="yith-par-message electro-par-message '.$class.'">'.__('This item can be purchased using a coupon.', THEME_TD).'</div>';
	    }
	}

	public static function woocommerce_before_checkout_form($checkout){
		if(isset($GLOBALS['exclude_gateways']) && !empty($GLOBALS['exclude_gateways'])){
			wc_add_notice(__('Payment by credit card to the courier is not possible due to the availability of promotional items in the basket.', THEME_TD), 'error');
		}
	}

	public static function electro_home_v2_hook_control(){
		if(is_page_template(array('template-homepage-v2.php'))){
			remove_all_actions('homepage_v2');

			$home_v2 = electro_get_home_v2_meta();

			$is_enabled = isset($home_v2['hpc']['is_enabled']) ? $home_v2['hpc']['is_enabled'] : 'no';
			if($is_enabled !== 'no'){
				add_action('homepage_v2', 'electro_page_template_content', isset($home_v2['hpc']['priority']) ? intval($home_v2['hpc']['priority']) : 5);
			}
			if(!wp_is_mobile()){
				add_action('electro_content_top', 'electro_home_v2_slider', isset($home_v2['sdr']['priority']) ? intval($home_v2['sdr']['priority']) : 10);
			}
			add_action('homepage_v2', 'electro_home_v2_ads_block', isset($home_v2['ad']['priority']) ? intval($home_v2['ad']['priority']) : 20);
			add_action('homepage_v2', 'electro_home_v2_products_carousel_tabs', isset($home_v2['pct']['priority']) ? intval($home_v2['pct']['priority']) : 30);
			add_action('homepage_v2', 'electro_home_v2_onsale_product', isset($home_v2['dow']['priority']) ? intval($home_v2['dow']['priority']) : 40);
			add_action('homepage_v2', 'electro_home_v2_product_cards_carousel', isset($home_v2['pcc']['priority']) ? intval($home_v2['pcc']['priority']) : 50);
			add_action('homepage_v2', 'electro_home_v2_ad_banner', isset($home_v2['bd']['priority']) ? intval($home_v2['bd']['priority']) : 60);
			add_action('homepage_v2', 'electro_home_v2_products_category_width_image_1', isset($home_v2['pcwi1']['priority']) ? intval($home_v2['pcwi1']['priority']) : 70);
			add_action('homepage_v2', 'electro_home_v2_products_category_width_image_2', isset($home_v2['pcwi2']['priority']) ? intval($home_v2['pcwi2']['priority']) : 80);
			add_action('homepage_v2', 'electro_home_v2_two_banners', isset($home_v2['tbrs']['priority']) ? intval($home_v2['tbrs']['priority']) : 90);
			add_action('homepage_v2', 'electro_home_v2_products_carousel', isset($home_v2['pc']['priority']) ? intval($home_v2['pc']['priority']) : 100);
			add_action('homepage_v2', 'electro_home_v2_products_carousel_2', isset($home_v2['pc2']['priority']) ? intval($home_v2['pc2']['priority']) : 110);
			add_action('homepage_v2', 'electro_home_v2_products_carousel_3', isset($home_v2['pc3']['priority']) ? intval($home_v2['pc3']['priority']) : 120);

		}
	}

	public static function electro_product_cards_carousel($section_args, $carousel_args){
		global $electro_version;

		$default_section_args = apply_filters('electro_product_cards_carousel_default_args', array(
			'section_title'     => '',
			'section_class'     => '',
			'show_nav'          => true,
			'show_top_text'     => true,
			'show_categories'   => true,
			'show_carousel_nav' => false,
			'products'          => '',
			'columns'           => 2,
			'columns_wide'      => 3,
			'rows'              => 1,
			'total'             => '',
			'cat_limit'         => 5,
			'cat_slugs'         => '',
			'animation'         => '',
		));

		$default_carousel_args = array(
			'items'           => 1,
			'nav'             => false,
			'slideSpeed'      => 300,
			'dots'            => true,
			'rtl'             => is_rtl() ? true : false,
			'paginationSpeed' => 400,
			'navText'         => array('', ''),
			'margin'          => 0,
			'touchDrag'       => true
		);

		$section_args  = wp_parse_args($section_args, $default_section_args);
		$carousel_args = wp_parse_args($carousel_args, $default_carousel_args);

		extract($section_args);

		$columns      = intval($columns);
		$columns_wide = intval($columns_wide);
		$rows         = intval($rows);

		$cat_args = array('number' => $cat_limit, 'hide_empty' => false);

		if(!empty($cat_slugs)){
			$slugs            = explode(',', $cat_slugs);
			$cat_args['slug'] = $slugs;

			$include = array();

			foreach($slugs as $slug){
				$include[] = "'".$slug."'";
			}

			if(!empty($include)){
				$cat_args['include'] = $include;
				$cat_args['orderby'] = 'include';
			}
		}

		if(!empty($section_args['categories_args'])){
			$cat_args = wp_parse_args($section_args['categories_args'], $cat_args);
		}

		$categories         = get_terms('product_cat', $cat_args);
		$products_card_html = '';
		$carousel_id        = uniqid();

		if($products instanceof WP_Query){
			$cache_file = 'raw-'.md5($section_args['section_class'].'-'.implode('-', $products->posts));
			#Functions::_debug($cache_file); exit;
			$products_card_html = Functions::get_cache_file($cache_file);
			if(is_null($products_card_html)){
				$products_card_html = Electro_WC_Helper::product_card_loop($products, $columns, $rows, $columns_wide);
				Functions::set_cache_file($cache_file, $products_card_html);
			}
		}

		$section_class = empty( $section_class ) ? 'section-product-cards-carousel' : 'section-product-cards-carousel ' . $section_class;

		if(!empty($animation)){
			$section_class .= ' animate-in-view';
		}

		if(!empty($products_card_html)){
			wp_enqueue_script('owl-carousel-js', get_template_directory_uri().'/assets/js/owl.carousel.min.js', array('jquery'), $electro_version, true); ?>
			<section class="<?php echo esc_attr($section_class); ?>" <?php if(!empty($animation)) : ?>data-animation="<?php echo esc_attr($animation); ?>"<?php endif; ?>>
				<?php if(!empty($section_title)) : ?>
					<header <?php if($show_nav) : ?>class="show-nav"<?php endif; ?>>
						<h2 class="h1"><?php echo esc_html($section_title); ?></h2>
						<?php if($show_nav) : ?>
							<ul class="nav nav-inline">
								<?php if($show_top_text) : ?>
									<li class="nav-item active">
										<span class="nav-link"><?php echo sprintf(esc_html__('Top %s', 'electro'), $products->post_count); ?></span>
									</li>
								<?php endif; ?>
								<?php if($show_categories && !empty ($categories) && !is_wp_error($categories)) : ?>
									<?php foreach($categories as $category) : ?>
										<li class="nav-item">
											<a class="nav-link" href="<?php echo esc_url(get_term_link($category)); ?>"><?php echo esc_html($category->name); ?></a>
										</li>
									<?php endforeach; ?>
								<?php endif; ?>
							</ul>
						<?php elseif($show_carousel_nav) : ?>
							<div class="owl-nav">
								<?php if(is_rtl()) : ?>
									<a href="#products-cards-carousel-prev" data-target="#<?php echo esc_attr($carousel_id); ?>" class="slider-prev"><i class="fa fa-angle-right"></i></a>
									<a href="#products-cards-carousel-next" data-target="#<?php echo esc_attr($carousel_id); ?>" class="slider-next"><i class="fa fa-angle-left"></i></a>
								<?php else : ?>
									<a href="#products-cards-carousel-prev" data-target="#<?php echo esc_attr($carousel_id); ?>" class="slider-prev"><i class="fa fa-angle-left"></i></a>
									<a href="#products-cards-carousel-next" data-target="#<?php echo esc_attr($carousel_id); ?>" class="slider-next"><i class="fa fa-angle-right"></i></a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</header>
				<?php endif; ?>
				<div id="<?php echo esc_attr($carousel_id); ?>" data-ride="owl-carousel" data-carousel-selector=".product-cards-carousel" data-carousel-options="<?php echo esc_attr(json_encode($carousel_args)); ?>">
					<?php echo $products_card_html; ?>
				</div>
			</section>
			<?php
		}
	}

	public static function electro_home_v2_products_carousel_3(){
		if(is_woocommerce_activated()){

			$home_v2     = electro_get_home_v2_meta();
			$pc3_options = $home_v2['pc3'];

			$is_enabled = isset($pc3_options['is_enabled']) ? $pc3_options['is_enabled'] : 'no';

			if($is_enabled !== 'yes'){
				return;
			}

			$animation = isset($pc3_options['animation']) ? $pc3_options['animation'] : '';

			$args = apply_filters('electro_home_v2_products_carousel_3_args', array(
				'limit'         => $pc3_options['product_limit'],
				'columns'       => $pc3_options['product_columns'],
				'columns_wide'  => isset($pc3_options['product_columns_wide']) ? $pc3_options['product_columns_wide'] : 5,
				'section_args'  => array(
					'section_title' => $pc3_options['section_title'],
					'section_class' => 'section-products-carousel',
					'animation'     => $animation
				),
				'carousel_args' => array(
					'items'      => $pc3_options['product_columns'],
					'autoplay'   => isset($pc3_options['carousel_args']['autoplay']) ? filter_var($pc3_options['carousel_args']['autoplay'], FILTER_VALIDATE_BOOLEAN) : false,
					'responsive' => array(
						'0'    => array('items' => 2),
						'480'  => array('items' => 3),
						'768'  => array('items' => 3),
						'992'  => array('items' => 3),
						'1200' => array('items' => $pc3_options['product_columns']),
					)
				)
			));

			if(electro_is_wide_enabled()){
				$args['carousel_args']['responsive']['1480'] = array('items' => $args['columns_wide']);
				$args['carousel_args']['responsive']['768']  = array('items' => 4);
				$args['carousel_args']['responsive']['992']  = array('items' => 4);
			}

			if(apply_filters('electro_enable_home_carousel_args_responsive', false) && !empty($pc3_options['carousel_args']['responsive'])){
				$responsive_args = array();
				foreach($pc3_options['carousel_args']['responsive'] as $key => $responsive){
					if(isset($responsive['items']) && intval($responsive['items']) > 0){
						$responsive_args[$key]['items'] = intval($responsive['items']);
					}elseif(isset($args['carousel_args']['responsive'][$key]['items'])){
						$responsive_args[$key]['items'] = $args['carousel_args']['responsive'][$key]['items'];
					}else{
						$responsive_args[$key]['items'] = $pc3_options['product_columns'];
					}
				}
				$args['carousel_args']['responsive'] = $responsive_args;
			}

			$default_atts = array('per_page' => intval($args['limit']), 'columns' => intval($args['columns']));
			$atts         = electro_get_atts_for_shortcode($pc3_options['content']);
			$atts         = wp_parse_args($atts, $default_atts);
			$products = Functions::electro_do_shortcode($pc3_options['content']['shortcode'], $atts);

			$args['section_args']['products_html'] = $products;

			electro_products_carousel($args['section_args'], $args['carousel_args']);
		}
	}

	public static function pre_get_posts_admin($query){
	    #Functions::_debug($query->query['post_type']);

	    $exclude_post_types = ['acf-field-group', 'acf-field', 'attachment'];

		if(isset($query->query['post_type'])){
			if(!isset($_GET['orderby'])){
				if(!in_array($query->query['post_type'], $exclude_post_types)){
					$query->set('orderby', 'title');
				}
			}
			if(!isset($_GET['order'])){
				if(!in_array($query->query['post_type'], $exclude_post_types)){
					$query->set('order', 'ASC');
				}
			}
		}
	}

	public static function electro_after_page(){
    	#include $_SERVER['DOCUMENT_ROOT'].'/counters.php';
	}

	public static function electro_before_header(){
		$html = '';
    	$theme_option_general = get_field('theme_option_general', 'option');
    	if($theme_option_general['header_notify_display']){
		    $html .= '<div class="header-notify" style="background-color: '.$theme_option_general['header_notify_background_color'].'; color: '.$theme_option_general['header_notify_text_color'].';">';
		    $html .= '<div class="inner">';
		    $html .= $theme_option_general['header_notify_content'];
		    $html .= '</div>';
		    $html .= '</div>';
	    }
    	echo $html;
	}


}

