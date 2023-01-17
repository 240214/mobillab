<?php
namespace Digidez;

use WP_Post;
use WP_Query;
use Digidez\WC_Shortcodes_Child;


class Functions {

	public static $log_file = "process.log";
	public static $log_dir;
	public static $cache_dir;
	public static $device = 'desktop';
	public static $device_os = '';
	public static $device_name = '';
	public static $device_version = '';
	public static $device_browser = '';
	public static $months = array(
		1 => 'January',
		2 => 'February',
		3 => 'March',
		4 => 'April',
		5 => 'May',
		6 => 'June',
		7 => 'July',
		8 => 'August',
		9 => 'September',
		10 => 'October',
		11 => 'November',
		12 => 'December',
	);
	public static $shared_links = array(
		'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=%POST_URL%',
		'instagram' => '',
		'linkedin' => 'http://www.linkedin.com/shareArticle?mini=true&url=%POST_URL%&title=%POST_TITLE%',
		'twitter' => 'http://twitter.com/home?status=%POST_TITLE%-%POST_URL%',
		'youtube' => '',
		'google-plus' => 'https://plus.google.com/share?url=%POST_TITLE%-%POST_URL%',
		'reddit' => '',
		'vk' => 'https://vk.com/share.php?url=%POST_URL%',
		'ok' => 'https://connect.ok.ru/dk?st.cmd=WidgetSharePreview&st.shareUrl=%POST_URL%',
		'pinterest' => 'https://pinterest.com/pin/create/button/?description=%POST_TITLE%&media=%POST_IMG%&url=%POST_URL%',
		'mail' => 'mailto:?subject=%POST_TITLE%&body=%POST_URL%',
	);
	public static $element_align_classes = array(
		1 => array('m-align-center'),
		2 => array('m-align-left', 'm-align-right'),
		3 => array('m-align-left', 'm-align-center', 'm-align-right'),
	);
	public static $in_col_align_classes = array(
		1 => array('text-center'),
		2 => array('text-left', 'text-right'),
		3 => array('text-left', 'text-center', 'text-right'),
	);

	public static $popup_trigger = [];
	public static $popup_cookies = [];


	public static function initialise(){
		$self = new self();

		$detect = new Device();
		//self::_debug($detect, 1);

		if($detect->isTablet()){
			self::$device = 'tablet';
		}elseif($detect->isMobile()){
			self::$device = 'mobile';
		}

		if($detect->isiOS()){
			self::$device_os = 'iOS';
			if($detect->isIphone()){
				self::$device_name = 'iPhone';
				$vers = $detect->version('iPhone', $detect::VERSION_TYPE_FLOAT);
				if(strstr($vers, '.') !== false){
					$v = explode('.', $vers);
					$vers = $v[0];
				}
				self::$device_version = 'v'.$vers;
				if($detect->isSafari()){
					self::$device_browser = 'Safari';
				}
				if($detect->isChrome()){
					self::$device_browser = 'Chrome';
				}
			}
		}

		if($detect->isAndroidOS()){
			self::$device_os = 'Android';
		}

		#self::_debug(self::$device_browser, 1);

		$upload_dir = wp_upload_dir();
		//self::$log_dir = $upload_dir['basedir'].'/'.LOG_DIR_NAME;
		self::$log_dir = LOG_DIR_NAME;
		self::$cache_dir = $upload_dir['basedir'].'/'.CACHE_DIR_NAME;

		if(!is_dir(self::$log_dir)){
			//self::_debug(self::$log_dir);
			@mkdir(self::$log_dir, 0777);
		}

		if(!is_dir(self::$cache_dir)){
			//self::_debug(self::$log_dir);
			@mkdir(self::$cache_dir, 0777);
		}
		if(!is_dir(self::$cache_dir.'/posts')){
			@mkdir(self::$cache_dir.'/posts', 0777);
		}

		if(!file_exists(self::$log_dir.'/'.self::$log_file)){
			//self::_debug(self::$log_dir.'/'.self::$log_file);
			self::_log('#START');
		}

		add_action('init', array($self, 'create_theme_options'));
	}

	public static function _dd($data = [], $show_for_users = false, $format = 'html', $echo = true, $strip_tags = true){
        self::_debug($data, $show_for_users, $format, $echo, $strip_tags);
        exit;
    }

	/**
	 * Displaying debug information
	 * @param array $data
	 * @param bool $echo
	 * @param bool $strip_tags
	 * @param bool $show_for_users
	 *
	 * @return array|mixed|string
	 */
	public static function _debug($data = [], $show_for_users = false, $format = 'html', $echo = true, $strip_tags = true){
		if(current_user_can('manage_options') || $show_for_users){
			$count = 0;
			if(is_array($data) || is_object($data)){
				$count = count($data);
				$data = print_r($data, true);
			}
			$data = htmlspecialchars($data);
			if($strip_tags)
				$data = strip_tags($data);
			if($echo){
				switch($format){
					case "html":
						echo '<pre class="debug">Debug info:(', $count, ')<br>', $data, '</pre>';
						break;
					case "json":
						echo json_encode($data);
						break;
					case "raw":
						print_r($data);
						break;
				}
			}else{
				return $format == 'json' ? json_encode($data) : $data;
			}
		}
	}

	public static function _log($content){
		$enter = chr(13).chr(10);
		$date  = date('Y-m-d H:i:s');

		if(is_array($content)){
			$content = $date.' - '.var_export($content, true).$enter;
		}else{
			$content = $date.' - '.$content.$enter;
		}

		@chmod(self::$log_dir.'/'.self::$log_file, 0666);

		file_put_contents(self::$log_dir.'/'.self::$log_file, $content, FILE_APPEND);
	}

	/**
	 * Get the description of template files
	 *
	 * @param string $file_path Filesystem path width filename
	 * @return string Description of file from $wp_file_descriptions or basename of $file if description doesn't exist.
	 * Appends 'Template' to basename of $file if the file is a page template
	 */
	public static function _get_file_description($file_path){
		$template_data = implode( '', file( $file_path ) );
		if(preg_match( '|Template Name:(.*)$|mi', $template_data, $name) ) {
			return sprintf( __( '%s' ), _cleanup_header_comment( $name[1] ) );
		}

		return trim( basename( $file_path ) );
	}


	/** THEME SETTINGS & OPTIONS **/

	public function create_theme_options(){
		if(function_exists('acf_add_options_page')){
			acf_add_options_page(array(
				'page_title' 	=> 'General',
				'menu_title'	=> 'Theme Options',
				'menu_slug' 	=> 'theme-options',
				'capability'	=> 'edit_posts',
				'parent_slug'   => 'themes.php',
				'position'      => false,
				'icon_url'      => false,
				'redirect'		=> false
			));

			/*acf_add_options_sub_page(array(
				'page_title' 	=> 'Page options',
				'menu_title'	=> 'Pages',
				'menu_slug' 	=> 'theme-options-pages',
				'capability'	=> 'edit_posts',
				'parent_slug'	=> 'theme-options',
				'position'      => false,
				'icon_url'      => false,
			));*/

			/*acf_add_options_sub_page(array(
				'page_title' 	=> 'Jobs options',
				'menu_title'	=> 'Jobs',
				'menu_slug' 	=> 'theme-options-jobs',
				'capability'	=> 'edit_posts',
				'parent_slug'	=> 'theme-options',
				'position'      => false,
				'icon_url'      => false,
			));*/

			/*acf_add_options_page(array(
				'page_title' 	=> 'Popups options',
				'menu_title'	=> 'Popups',
				'menu_slug' 	=> 'theme-options-popups',
				'capability'	=> 'edit_posts',
				'parent_slug'	=> 'themes.php',
				'position'      => false,
				'icon_url'      => false,
				'redirect'		=> false
			));*/

			/*acf_add_options_sub_page(array(
				'page_title' 	=> 'CF7 options',
				'menu_title'	=> 'Contact Form 7',
				'menu_slug' 	=> 'theme-options-cf7',
				'capability'	=> 'edit_posts',
				'parent_slug'	=> 'theme-options',
				'position'      => false,
				'icon_url'      => false,
			));*/
		}
	}

	public static function slbd_count_widgets($sidebar_id){
		// If loading from front page, consult $_wp_sidebars_widgets rather than options
		// to see if wp_convert_widget_settings() has made manipulations in memory.
		global $_wp_sidebars_widgets;
		if(empty($_wp_sidebars_widgets)) :
			$_wp_sidebars_widgets = get_option('sidebars_widgets', []);
		endif;

		$sidebars_widgets_count = $_wp_sidebars_widgets;

		if(isset($sidebars_widgets_count[$sidebar_id])) :
			$widget_count   = count($sidebars_widgets_count[$sidebar_id]);
			$widget_classes = 'widget-count-'.count($sidebars_widgets_count[$sidebar_id]);
			if($widget_count % 4 == 0 || $widget_count > 6) :
				// Four widgets per row if there are exactly four or more than six
				$widget_classes .= ' col-md-3';
			elseif(6 == $widget_count) :
				// If two widgets are published
				$widget_classes .= ' col-md-2';
			elseif($widget_count >= 3) :
				// Three widgets per row if there's three or more widgets
				$widget_classes .= ' col-md-4';
			elseif(2 == $widget_count) :
				// If two widgets are published
				$widget_classes .= ' col-md-6';
			elseif(1 == $widget_count) :
				// If just on widget is active
				$widget_classes .= ' col-md-12';
			endif;

			return $widget_classes;
		endif;
	}


	/** MENU NAVS **/

	public static function get_menu($menu_name = 'main_menu'){
		global $post;
		//self::_debug($post->post_name);

		$menu       = wp_get_nav_menu_object($menu_name);
		$menu_items = wp_get_nav_menu_items($menu->term_id, array('update_post_term_cache' => false));
		//self::_debug($menu_items);

		$sorted_menu = [];
		foreach((array)$menu_items as $menu_item){
			$sorted_menu[$menu_item->ID] = array(
				'id'      => $menu_item->ID,
				'name'    => $menu_item->title,
				'url'     => $menu_item->url,
				'target'     => $menu_item->target,
				'classes' => (isset($menu_item->classes) ? implode(' ', $menu_item->classes) : ''),
				'active_class' => (strstr($menu_item->url, $post->post_name) !== false || $post->ID == $menu_item->post_name || $post->post_name == $menu_item->post_name ? 'active' : ''),
			);
		}

		//self::_debug($sorted_menu);

		return $sorted_menu;
	}

	public static function get_menu_sections($menu_name = 'main_menu'){
		$menu       = wp_get_nav_menu_object($menu_name);
		$menu_items = wp_get_nav_menu_items($menu->term_id, array('update_post_term_cache' => false));
		//self::_debug($menu_items);

		$sorted_menu = $sorted_menu_items = $menu_items_with_children = [];
		foreach((array)$menu_items as $menu_item){
			$sorted_menu_items[$menu_item->menu_order] = $menu_item;
			if($menu_item->menu_item_parent){
				$menu_items_with_children[$menu_item->menu_item_parent] = true;
			}
		}
		//self::_debug($menu_items_with_children);

		foreach($menu_items_with_children as $menu_section => $val){
			$sorted_menu[$menu_section] = '';
		}
		//self::_debug($sorted_menu);
		//self::_debug($sorted_menu_items);

		foreach($sorted_menu_items as $menu_item){
			if(array_key_exists($menu_item->ID, $sorted_menu)){
				$sorted_menu[$menu_item->ID] = array(
					'name'    => $menu_item->title,
					'url'     => $menu_item->url,
					'classes' => (isset($menu_item->classes) ? implode(' ', $menu_item->classes) : ''),
				);
			}

			if(array_key_exists($menu_item->menu_item_parent, $sorted_menu)){
				$sorted_menu[$menu_item->menu_item_parent]['items'][] = array(
					'name'    => $menu_item->title,
					'url'     => $menu_item->url,
					'classes' => (isset($menu_item->classes) ? implode(' ', $menu_item->classes) : ''),
				);
			}
		}
		//self::_debug($sorted_menu);
		return $sorted_menu;
	}


	/** PRODUCTS **/

	public static function get_peoduct_available_variations_prices($product){
		$prices = [];
		$min = 99999999999;
		$max = 0;
		$available_variations = $product->get_available_variations();
		foreach($available_variations as $available_variation){
			$prices[] = array(
				'display_price' => $available_variation['display_price'],
				'display_regular_price' => $available_variation['display_regular_price'],
			);

			if($available_variation['display_price'] < $min){
				$min = $available_variation['display_price'];
				$max = $available_variation['display_regular_price'];
			}
		}

		$prices['min'] = $min;
		$prices['max'] = $max;

		return $prices;
	}

	public static function get_product_categories($taxonomy = 'product_cat'){
		$_terms = get_terms(array('taxonomy' => $taxonomy, 'fields' => 'all', 'hide_empty' => false, 'parent' => 0));

		$terms = [];
		foreach($_terms as $k => $term){
			$term_thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);
			if($term_thumbnail_id){
				$terms[$k] = $term;
				$terms[$k]->link = get_term_link($term);
				$terms[$k]->image_id = $term_thumbnail_id;
				//$terms[$k]->image = self::get_the_attachment_thumbnail($term_thumbnail_id, 'full', '', false, false);
			}
		}

		return $terms;
	}

	/**
	 * Обновляем мета-поля попапов и купонов определенного товара по его ID
	 *
	 * Вызов метода происходит во время сохранения товара
	 * из Actions::save_post_product при экшене save_post (строка 63)
	 *
	 * @param $product_ID
	 */
	public static function update_product_popup_params($product_ID){
        #Functions::_dd($product_ID);
		$products_promotion_method = get_post_meta($product_ID, 'products_promotion_method', true);

		$popup_id = get_post_meta($product_ID, 'products_promotion_popup_window', true);

		self::_remove_product_from_popups($product_ID);
		self::_remove_product_from_coupons($product_ID);

		if(!is_null($popup_id)){

			$popup_settings = get_post_meta($popup_id, 'popup_settings', true);

			if(!is_null($popup_settings)){

				//$popup_settings = self::_remove_product_from_popup_params($product_ID, $popup_settings);

				if($products_promotion_method == 'coupons'){
					$popup_settings = self::_add_product_to_popup_params($product_ID, $popup_settings);
					self::_add_product_to_popup_coupon_post($product_ID, $popup_id);
				}elseif($products_promotion_method == 'points'){
					delete_post_meta($product_ID, 'products_promotion_popup_window');
				}

				update_post_meta($popup_id, 'popup_settings', $popup_settings);
			}
		}
	}

	/**
	 * Ддавляем ID товара в попап
	 * @param $product_ID
	 * @param $popup_settings
	 *
	 * @return mixed
	 */
	private static function _add_product_to_popup_params($product_ID, $popup_settings){
		$popup_settings['conditions'][0][] = [
			'target'   => 'product_ID',
			'settings' => ['selected' => $product_ID]
		];
		//$popup_settings['triggers'] = self::$popup_trigger;
		//$popup_settings['cookies']  = self::$popup_cookies;

		return $popup_settings;
	}

	/**
	 * Удаляем ID товара из определенного попапа
	 * @param $product_ID
	 * @param $popup_settings
	 *
	 * @return mixed
	 */
	private static function _remove_product_from_popup_params($product_ID, $popup_settings){

		if(isset($popup_settings['conditions']) && !empty($popup_settings['conditions']) && is_array($popup_settings['conditions'])){
			foreach($popup_settings['conditions'] as $c => $conditions){
				if(!empty($conditions)){
					foreach($conditions as $k => $condition){
						if($condition['target'] == 'product_ID'){
							if(isset($condition['settings']) && !empty($condition['settings'])){
								if(isset($condition['settings']['selected']) && !empty($condition['settings']['selected'])){
									if(strstr($condition['settings']['selected'], ',') !== false){
										$a = array_map('trim', explode(',', $condition['settings']['selected']));
										if(in_array($product_ID, $a)){
											unset($popup_settings['conditions'][$c][$k]);
											/*if(empty($popup_settings['conditions'][$c])){
												unset($popup_settings['conditions'][$c]);
											}*/
										}
									}else{
										if($condition['settings']['selected'] == $product_ID){
											unset($popup_settings['conditions'][$c][$k]);
											/*if(empty($popup_settings['conditions'][$c])){
												unset($popup_settings['conditions'][$c]);
											}*/
										}
									}
								}
							}
						}
					}
				}else{
					#unset($popup_settings['conditions'][$c]);
				}
			}
			reset($popup_settings['conditions']);
		}

		return $popup_settings;
	}

	/**
	 * Удаляем ID товара из всех попапов
	 * @param $product_ID
	 */
	private static function _remove_product_from_popups($product_ID){
		global $wpdb;

		self::_log(__METHOD__);

		$results = $wpdb->get_results("SELECT * FROM {$wpdb->postmeta} WHERE meta_key = 'popup_settings' ORDER BY post_id ASC");

		if($results){
			foreach($results as $result){
				#self::_log('- product_ID '.$product_ID.', popup_post_id '.$result->post_id.' meta_value before update: '.$result->meta_value);
				$popup_settings = self::_remove_product_from_popup_params($product_ID, unserialize($result->meta_value));
				/*if(empty($popup_settings['conditions'])){
					self::$popup_trigger = $popup_settings['triggers'];
					self::$popup_cookies = $popup_settings['cookies'];
					$popup_settings['triggers'] = [];
					$popup_settings['cookies']  = [];
				}*/
				update_post_meta($result->post_id, 'popup_settings', $popup_settings);
				#self::_log('- product_ID '.$product_ID.', popup_post_id '.$result->post_id.' meta_value after update: '.serialize($popup_settings));
			}
		}
	}

	/**
	 * Добавляем ID товара в купон
	 * @param $product_ID
	 * @param $popup_id
	 */
	private static function _add_product_to_popup_coupon_post($product_ID, $popup_id){
		global $wpdb;

		$_sql = "SELECT coupon.ID AS coupon_ID, popup.ID AS popup_ID 
				 FROM {$wpdb->posts} coupon 
				 LEFT JOIN {$wpdb->posts} popup ON popup.post_title = coupon.post_title
				 WHERE popup.ID = ".$popup_id." AND coupon.post_type = 'shop_coupon'";
		$result = $wpdb->get_row($_sql);

		if($result){
			$product_ids = get_post_meta($result->coupon_ID, 'product_ids', true);
			if(empty($product_ids)){
				update_post_meta($result->coupon_ID, 'product_ids', $product_ID);
			}else{
				$product_ids = array_map('trim', explode(',', $product_ids));
				if(!in_array($product_ID, $product_ids)){
					$product_ids[] = $product_ID;
					$product_ids = implode(',', $product_ids);
					update_post_meta($result->coupon_ID, 'product_ids', $product_ids);
				}
			}
		}
	}

	/**
	 * Удаляем ID товара из определенного купона
	 * @param $product_ID
	 * @param $coupon_ID
	 * @param $popup_ID
	 */
	private static function _remove_product_from_coupon_post($product_ID, $coupon_ID, $popup_ID){
		$product_ids = get_post_meta($coupon_ID, 'product_ids', true);

		if($product_ids){
			$product_ids = array_map('trim', explode(',', $product_ids));
			if(!empty($product_ids)){
				foreach($product_ids as $k => $pid){
					if($pid == $product_ID){
						unset($product_ids[$k]);
					}
				}
			}
			if(!empty($product_ids)){
				$product_ids = implode(',', $product_ids);
				update_post_meta($coupon_ID, 'product_ids', $product_ids);
			}else{
				delete_post_meta($coupon_ID, 'product_ids');
			}
		}

	}

	/**
	 * Удаляем ID товара из всех купонов
	 * @param $product_ID
	 */
	private static function _remove_product_from_coupons($product_ID){
		global $wpdb;

		$_sql = "SELECT coupon.ID AS coupon_ID, popup.ID AS popup_ID 
				 FROM {$wpdb->posts} coupon 
				 LEFT JOIN {$wpdb->posts} popup ON popup.post_title = coupon.post_title
				 WHERE coupon.post_type = 'shop_coupon' AND popup.post_type = 'popup'";
		$results = $wpdb->get_results($_sql);

		if($results){
			foreach($results as $result){
				self::_remove_product_from_coupon_post($product_ID, $result->coupon_ID, $result->popup_ID);
			}
		}
	}


	/** WOOCOMMERCE **/

	public static function get_products_meta_prices(){
		global $wpdb;
		$ret = [];

		$results = $wpdb->get_results("SELECT * FROM {$wpdb->postmeta} WHERE meta_key LIKE '%_price%' ORDER BY post_id ASC, meta_key ASC");
		if($results){
			foreach($results as $result){
				$ret[$result->post_id][$result->meta_key] = $result->meta_value;
			}
		}

		return $ret;
	}

	public static function woocommerce_product_loop_start($echo = true){
		ob_start();

		$loop_classes             = '';
		$product_loop_classes_arr = apply_filters('electro_product_loop_additional_classes', []);

		$columns      = apply_filters('loop_shop_columns', 3);
		$columns_wide = apply_filters('loop_shop_columns_wide', 5);

		if(defined('WC_VERSION') && version_compare(WC_VERSION, '3.3', '<')){
			global $woocommerce_loop;
			$woocommerce_loop['loop'] = 0;
			if(isset($woocommerce_loop['columns']) && intval($woocommerce_loop['columns'])){
				$columns = $woocommerce_loop['columns'];
			}
		}else{
			wc_set_loop_prop('loop', 0);
			$columns      = wc_get_loop_prop('columns', $columns);
			$columns_wide = wc_get_loop_prop('columns_wide', $columns_wide);
		}

		$product_loop_classes_arr[] = 'columns-'.$columns;
		$product_loop_classes_arr[] = 'columns__wide--'.$columns_wide;

		$data_attr = 'regular-products';
		$data_view = 'grid';

		if(is_shop() || is_product_category() || is_product_tag() || is_tax(get_object_taxonomies('product'))){
			$data_attr  = 'shop-products';
			$shop_views = electro_get_shop_views();
			foreach($shop_views as $shop_view => $shop_view_args){
				if($shop_view_args['active']){
					$data_view = $shop_view;
					break;
				}
			}
		}

		if(isset($_COOKIE['product-view-type'])){
			$data_view = $_COOKIE['product-view-type'];
		}

		if(is_array($product_loop_classes_arr)){
			$loop_classes = implode(' ', $product_loop_classes_arr);
		}

		echo '<ul data-view="'.esc_attr($data_view).'" data-toggle="'.esc_attr($data_attr).'" class="products '.esc_attr($loop_classes).'">';

		if($echo){
			echo ob_get_clean();
		}else{
			return ob_get_clean();
		}
	}

	public static function electro_shop_view_switcher(){
		global $wp_query;

		if(1 === $wp_query->found_posts || !woocommerce_products_will_display()){
			return;
		}
		$html       = '';
		$shop_views = electro_get_shop_views();

		$by_cookie = false;
		if(isset($_COOKIE['product-view-type'])){
			$by_cookie = true;
			$view_type = $_COOKIE['product-view-type'];
		}

		$html .= '<ul class="shop-view-switcher nav nav-tabs" role="tablist">';
		foreach($shop_views as $view_id => $shop_view){
			if($by_cookie){
				$active_class = $view_type == $view_id ? 'active' : '';
			}else{
				$active_class = $shop_view['active'] ? 'active' : '';
			}
			$html .= '<li class="nav-item"><a class="nav-link '.$active_class.'" data-toggle="tab" data-archive-class="'.$view_id.'" title = "'.$shop_view['label'].'" href = "#'.$view_id.'"><i class="'.$shop_view['icon'].'"></i></a></li>';
		}
		$html .= '</ul>';

		echo $html;
	}

	public static function electro_wc_products_per_page(){

		global $wp_query;

		$action          = '';
		$cat             = '';
		$cat             = $wp_query->get_queried_object();
		$method          = apply_filters('electro_wc_ppp_method', 'post');
		$return_to_first = apply_filters('electro_wc_ppp_return_to_first', false);
		$total           = $wp_query->found_posts;
		$per_page        = $wp_query->get('posts_per_page');
		$_per_page       = electro_set_loop_shop_columns() * 4;
		//self::_debug($_per_page);

		// Generate per page options
		$products_per_page_options = [];
		while($_per_page < $total){
			$products_per_page_options[] = $_per_page;
			$_per_page                   = $_per_page * 2;
		}

		if(empty($products_per_page_options)){
			return;
		}

		$products_per_page_options[] = -1;

		if(count($products_per_page_options) > 2){
			$products_per_page_options = array_slice($products_per_page_options, 0, 2);
		}
		#self::_debug($products_per_page_options);

		// Set action url if option behaviour is true
		// Paste QUERY string after for filter and orderby support
		$query_string = !empty($_SERVER['QUERY_STRING']) ? '?'.add_query_arg(array('ppp' => false), $_SERVER['QUERY_STRING']) : null;

		if(isset($cat->term_id) && isset($cat->taxonomy) && $return_to_first) :
			$action = get_term_link($cat->term_id, $cat->taxonomy).$query_string;
		elseif($return_to_first) :
			$action = get_permalink(wc_get_page_id('shop')).$query_string;
		endif;

		// Only show on product categories
		if(!woocommerce_products_will_display()) :
			return;
		endif;

		do_action('electro_wc_ppp_before_dropdown_form');

		?>
		<form method="POST" action="<?php echo esc_url($action); ?>" class="form-electro-wc-ppp"><?php

		do_action('electro_wc_ppp_before_dropdown');

		?>
			<select name="ppp" onchange="this.form.submit()" class="electro-wc-wppp-select c-select">
			<?php foreach($products_per_page_options as $key => $value):?>
				<option value="<?php echo esc_attr($value); ?>" <?php selected($value, $per_page); ?>><?php
				$ppp_text = apply_filters('electro_wc_ppp_text', __('Show %s', 'electro'), $value);
				esc_html(printf($ppp_text, $value == -1 ? __('All', 'electro') : $value)); // Set to 'All' when value is -1
				?>
				</option>
			<?php endforeach;?>
			</select>

			<?php foreach($_GET as $key => $val): // Keep query string vars intact
				if('ppp' === $key || 'submit' === $key) :
					continue;
				endif;
				if(is_array($val)) :
					foreach($val as $inner_val):?>
						<input type="hidden" name="<?php echo esc_attr($key); ?>[]" value="<?php echo esc_attr($inner_val); ?>" />
					<?php endforeach;
				else:?>
					<input type="hidden" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($val); ?>" />
				<?php endif;
			endforeach;

			do_action('electro_wc_ppp_after_dropdown');
			?>
		</form>
		<?php

		do_action('electro_wc_ppp_after_dropdown_form');
	}

	public static function electro_footer_bottom_widgets_v2(){
		$show_footer_bottom_widgets    = apply_filters('electro_show_footer_bottom_widgets', true);
		$show_footer_contact_block     = apply_filters('electro_enable_footer_contact_block', true);
		$footer_bottom_widgets_columns = apply_filters('electro_footer_bottom_widgets_columns', 3);

		if($show_footer_bottom_widgets || $show_footer_contact_block) : ?>

			<div class="footer-bottom-widgets">
			<div class="container">
				<div class="footer-bottom-widgets-inner">
					<?php if($show_footer_contact_block) : ?>
						<div class="footer-contact">
							<?php electro_footer_contact(); ?>
						</div>
					<?php endif; ?>
					<?php if($show_footer_bottom_widgets) : ?>
						<div class="footer-bottom-widgets-menu">
							<div class="footer-bottom-widgets-menu-inner <?php echo esc_attr('columns-'.$footer_bottom_widgets_columns); ?>">
								<?php electro_display_footer_bottom_widgets(); ?>
							</div>
							<div class="footer-bottom-widgets-menu-inner full columns-1">
								<?php dynamic_sidebar('footer-bottom-widget-full'); ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
			</div><?php

		endif;
	}


	/** HELPERS **/

	public static function set_cache_file($file, $data, $path = ''){
		#self::_debug(self::$cache_dir.'/'.$file.'.php');
		$data_type = substr($file, 0, 3);
		switch($data_type){
			case "obj":
			case "arr":
				file_put_contents(self::$cache_dir.'/'.$path.$file.'.php', '<?php return '.var_export($data, true).';');
				break;
			case "raw":
				file_put_contents(self::$cache_dir.'/'.$path.$file.'.php', $data);
				break;
		}
	}

	public static function get_cache_file($file, $path = ''){
		#self::_debug(self::$cache_dir.'/'.$file.'.php');
		$data_type = substr($file, 0, 3);
		if(file_exists(self::$cache_dir.'/'.$path.$file.'.php')){
			switch($data_type){
				case "obj":
				case "arr":
					return include self::$cache_dir.'/'.$path.$file.'.php';
					break;
				case "raw":
					return file_get_contents(self::$cache_dir.'/'.$path.$file.'.php');
					break;
			}
		}else{
			return null;
		}
	}

	public static function get_post_from_cache($post_id){
		$cache_file = 'obj-'.$post_id;
		$_post = self::get_cache_file($cache_file, 'posts/');
		#self::_debug($_post);
		if(is_null($_post)){
			$_post = get_post($post_id);
			self::set_cache_file($cache_file, (array)$_post, 'posts/');
			return $_post;
		}else{
			#self::_debug($post_id);
			$_post['post_title'] = 'from-cache :: '.$_post['post_title'];
			//$_post = sanitize_post( $_post, 'raw' );
			return new WP_Post((object)$_post);
		}
	}

	public static function get_template_part_by_device($file = '', $base_path = 'common', $args = []){
		$base_path = trim($base_path, '/');
		$base_path = trim(PARTIALS_PATH, '/').'/'.$base_path.'/%device%/'.$file;
		$f_path = str_replace('%device%', self::$device, $base_path);

		if(!file_exists(THEME_DIR.$f_path.'.php')){
			switch(self::$device){
				case "desktop":
					$f_path = str_replace('%device%/', '', $base_path);
					break;
				case "tablet":
					$f_path = str_replace('%device%', 'tablet', $base_path);
					if(!file_exists(THEME_DIR.$f_path.'.php')){
						$f_path = str_replace('%device%', 'desktop', $base_path);
					}
					break;
				case "mobile":
					$f_path = str_replace('%device%', 'tablet', $base_path);
					if(!file_exists(THEME_DIR.$f_path.'.php')){
						$f_path = str_replace('%device%', 'desktop', $base_path);
					}
					break;
			}
		}
		#self::_debug($f_path);

		self::get_template_part('/'.$f_path, $args, true);
	}

	public static function get_related_pages($exclude_id = 0, $parnet_id = 0, $short = true){
		global $wpdb;

		$fields = $short ? "ID, post_title" : "*";
		$_sql = "SELECT {$fields} FROM {$wpdb->posts} WHERE ID != {$exclude_id} AND post_parent = {$parnet_id} AND post_type = 'page' AND post_status = 'publish' AND post_title != ''";
		$result = $wpdb->get_results($_sql);
		//\Digidez\Functions::_debug($result);

		return $result;
	}

	public static function get_post_type_related_posts($post_id, $number_posts = 6, $post_type = 'post', $taxonomy = 'category'){

		if( 0 == $number_posts ) {
			return false;
		}

		$item_array = [];
		$item_cats = get_the_terms( $post_id, $taxonomy );
		if ( $item_cats ) {
			foreach( $item_cats as $item_cat ) {
				$item_array[] = $item_cat->term_id;
			}
		}

		if( empty( $item_array ) ) {
			return false;
		}

		$args = array(
			'post_type'				=> $post_type,
			'posts_per_page'		=> $number_posts,
			'post__not_in'			=> array( $post_id ),
			'ignore_sticky_posts'	=> 0,
			'tax_query'				=> array(
				array(
					'field'		=> 'id',
					'taxonomy'	=> $taxonomy,
					'terms'		=> $item_array
				)
			)
		);

		return new WP_Query( $args );
	}

	public static function get_parent_pageID_by_term($post_terms){
		$terms_ids = [];
		foreach($post_terms as $term){
			$terms_ids[] = $term->term_id;
		}

		$args   = array(
			'posts_per_page' => -1,
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'     => 'events_section_category',
					'value'   => $terms_ids,
					'compare' => 'IN',
				)
			)
		);
		$_posts = get_posts($args);
		//Functions::_debug($_posts);
		$pID = 0;
		if(!empty($_posts)){
			foreach($_posts as $_post){
				if($_post->post_name == 'events' && $_post->post_parent > 0){
					$pID = $_post->post_parent;
				}
			}
		}

		return $pID;
	}

	public static function get_post_type_list(){
		$postTypes         = get_post_types(array(
			'public' => true
		), 'objects');
		$postTypesList     = [];
		$excludedPostTypes = array(
			'revision',
			'nav_menu_item',
			'rella-footer',
			'rella-header',
			'rella-mega-menu',
			'wpcf7_contact_form',
			'vc_grid_item',
			'oxy_modal',
			'oxy_portfolio_image',
			'oxy_testimonial',
			'oxy_service',
		);
		#\Digidez\Functions::_debug($postTypes);

		if(is_array($postTypes) && !empty($postTypes)){
			foreach($postTypes as $postType => $obj){
				if(!in_array($postType, $excludedPostTypes)){
					$postTypesList[$postType] = $obj->label;
				}
			}
		}
		/*$postTypesList[] = array(
			'custom',
			esc_html__('Custom query', 'infinite-addons'),
		);
		$postTypesList[] = array(
			'ids',
			esc_html__('List of IDs', 'infinite-addons'),
		);*/

		return $postTypesList;
	}

	public static function get_cpt_custom_fields($post){
		$cf = [];

		$cpt = $post->post_type;
		#self::_debug($cpt);
		$cf = get_fields($post->ID);

		switch($cpt){
			case "event":
				$terms = wp_get_post_terms($post->ID, 'event-cat', array('fields' => 'id=>name'));
				$d = strtotime($cf['event_datetime']);
				$cf['day_month'] = date('jS F', $d);
				$cf['weekday'] = date('l', $d);
				$cf['time'] = date('H:i', $d);
				$cf['year'] = date('Y', $d);
				$cf['weekday_time_cat'] = date('l | G:i', $d).'<br>'.current($terms);
				$cf['categories'] = implode(', ', $terms);
				break;
			case "faq":
				$terms = wp_get_post_terms($post->ID, 'faq-cat', array('fields' => 'id=>name'));
				$cf['categories'] = implode(', ', $terms);
				break;
			default:
				break;
		}

		return $cf;
	}

	public static function get_page_custom_fields(){
		global $post;

		return get_fields($post->ID);
	}

	public static function get_cpt_cats($post){
		$tax = $post->post_type.'-cat';
		$terms = wp_get_post_terms($post->ID, array($tax), array('fields' => 'id=>name'));

		return $terms;
	}

	public static function get_cpt_terms($taxonomy){
		$queried_category = get_term_by('slug', get_query_var($taxonomy), $taxonomy);
		$terms = get_terms(array('taxonomy' => $taxonomy, 'fields' => 'all', 'hide_empty' => false));

		foreach($terms as $k => $term){
			$terms[$k]->active = 0;
			$terms[$k]->active_class = '';
			if(!empty($queried_category) && $queried_category->term_id == $term->term_id){
				$terms[$k]->active = 1;
				$terms[$k]->active_class = 'active';
			}
		}

		return $terms;
	}

	public static function get_the_attachment_thumbnail($attachment_id, $size = 'rella-thumbnail', $attr = '', $return_img_tag = true, $retina = true){
		$html = '';
		if(intval($attachment_id) > 0){
			$image = wp_get_attachment_image_src($attachment_id, 'full', false);
		}else{
			list($imageWidth, $imageHeight, $imageType, $imageAttr) = getimagesize($attachment_id);
			$image = array(
				'src' => $attachment_id,
				'width' => $imageWidth,
				'height' => $imageHeight,
			);
			unset($imageWidth, $imageHeight, $imageType, $imageAttr);
		}
		//self::_debug($image);
		if($image){

			@list($src, $width, $height) = $image;

			if($size == 'full_src'){
				return $src;
			}

			$file_ext = self::get_file_ext($src);
			//self::_debug($file_ext);

			//Get image sizes
			$aq_size = self::get_image_sizes($size);

			if($file_ext != 'svg' && is_array($aq_size) && !empty($aq_size['height'])){

				$resize_width  = $aq_size['width'];
				$resize_height = $aq_size['height'];
				$resize_crop   = $aq_size['crop'];

				if($resize_width >= $width){
					$resize_width = $width;
				}
				if($resize_height >= $height && !empty($resize_height)){
					$resize_height = $height;
				}

				//Double the size for the retina display
				$retina_width  = $resize_width * 2;
				$retina_height = $resize_height * 2;
				if($retina_width >= $width){
					$retina_width = $width;
				}
				if($retina_height >= $height){
					$retina_height = $height;
				}

				//Get resized images
				$retina_src = aq_resize_custom($src, $retina_width, $retina_height, true);
				$src        = aq_resize_custom($src, $resize_width, $resize_height, $resize_crop);
				$hwstring   = image_hwstring($resize_width, $resize_height);

				if(empty($retina)){
					$retina_src = $src;
				}

			}else{
				$retina_src = $src;
				$hwstring   = image_hwstring($width, $height);
			}

			if($return_img_tag){
				$size_class = $size;
				if(is_array($size_class)){
					$size_class = join('x', $size_class);
				}
				$attachment   = get_post($attachment_id);
				$default_attr = array(
					'src'      => $src,
					'class'    => "attachment-$size_class size-$size_class",
					'alt'      => get_the_title(),
					'data-rjs' => $retina_src,
				);

				$attr = wp_parse_args($attr, $default_attr);

				$attr = apply_filters('wp_get_attachment_image_attributes', $attr, $attachment, $size);

				$attr = array_map('esc_attr', $attr);
				$html = rtrim("<img $hwstring");
				foreach($attr as $name => $value){
					$html .= " $name=".'"'.$value.'"';
				}
				$html .= ' />';
			}else{
				$html = $src;
			}
		}

		return $html;
	}

	public static function get_the_post_thumbnail($post_id, $size = 'rella-thumbnail', $attr = '', $return_img_tag = true, $retina = true){
		$attachment_id = get_post_thumbnail_id($post_id);
		return self::get_the_attachment_thumbnail($attachment_id, $size, $attr, $return_img_tag, $retina);
	}

	public static function get_image_sizes($size){

		$sizes = array(
			THEME_SHORT.'-medium'          => array('width' => '300', 'height' => '300', 'crop' => true),
			THEME_SHORT.'-large'           => array('width' => '1024', 'height' => '', 'crop' => false),
			THEME_SHORT.'-large-slider'    => array('width' => '1024', 'height' => '700', 'crop' => true),
			THEME_SHORT.'-default-blog'    => array('width' => '690', 'height' => '460', 'crop' => true),
			THEME_SHORT.'-thumbnail'       => array('width' => '150', 'height' => '150', 'crop' => true),
			THEME_SHORT.'-agency-blog'     => array('width' => '370', 'height' => '270', 'crop' => true),
			THEME_SHORT.'-cloud-blog'      => array('width' => '370', 'height' => '205', 'crop' => true),
			THEME_SHORT.'-cloud-blog2'     => array('width' => '370', 'height' => '195', 'crop' => false),
			THEME_SHORT.'-shop-blog'       => array('width' => '370', 'height' => '300', 'crop' => true),
			THEME_SHORT.'-dhover-blog'     => array('width' => '370', 'height' => '370', 'crop' => true),
			THEME_SHORT.'-medium-blog'     => array('width' => '740', 'height' => '640', 'crop' => true),
			THEME_SHORT.'-university-blog' => array('width' => '400', 'height' => '280', 'crop' => true),
			THEME_SHORT.'-classic-blog'    => array('width' => '750', 'height' => '440', 'crop' => true),
			THEME_SHORT.'-widget'          => array('width' => '180', 'height' => '180', 'crop' => true),
			THEME_SHORT.'-slider-nav'      => array('width' => '180', 'height' => '130', 'crop' => true),
			THEME_SHORT.'-masonry-blog'    => array('width' => '450', 'height' => '', 'crop' => false),

			THEME_SHORT.'-masonry-header-small' => array('width' => '295', 'height' => '220', 'crop' => true),
			THEME_SHORT.'-masonry-header-big'   => array('width' => '295', 'height' => '440', 'crop' => true),

			THEME_SHORT.'-timeline-blog'  => array('width' => '470', 'height' => '', 'crop' => false),
			THEME_SHORT.'-puzzle-blog'    => array('width' => '300', 'height' => '300', 'crop' => true),
			THEME_SHORT.'-split-blog'     => array('width' => '385', 'height' => '450', 'crop' => true),
			THEME_SHORT.'-thumbnail-post' => array('width' => '765', 'height' => '400', 'crop' => true),

			THEME_SHORT.'-related-post'                 => array('width' => '370', 'height' => '190', 'crop' => true),
			THEME_SHORT.'-related-post-three-col'       => array('width' => '488', 'height' => '230', 'crop' => true),
			THEME_SHORT.'-related-post-two-col'         => array('width' => '730', 'height' => '400', 'crop' => true),
			// Edited height. original = 425
			THEME_SHORT.'-related-post-one-col'         => array('width' => '1463', 'height' => '640', 'crop' => true),

			//Portfolio sizes
			THEME_SHORT.'-portfolio'                    => array('width' => '480', 'height' => '480', 'crop' => true),
			THEME_SHORT.'-portfolio-sq'                 => array('width' => '285', 'height' => '285', 'crop' => true),
			THEME_SHORT.'-portfolio-big-sq'             => array('width' => '570', 'height' => '570', 'crop' => true),
			THEME_SHORT.'-portfolio-portrait'           => array('width' => '285', 'height' => '570', 'crop' => true),
			THEME_SHORT.'-portfolio-double-portrait'    => array('width' => '570', 'height' => '1140', 'crop' => true),
			THEME_SHORT.'-portfolio-portrait-tall'      => array('width' => '570', 'height' => '867', 'crop' => true),
			THEME_SHORT.'-portfolio-wide'               => array('width' => '570', 'height' => '285', 'crop' => true),
			THEME_SHORT.'-portfolio-related'            => array('width' => '285', 'height' => '275', 'crop' => true),
			THEME_SHORT.'-portfolio-grid-hover-elegant' => array('width' => '720', 'height' => '560', 'crop' => true),

			THEME_SHORT.'-portfolio-full'      => array('width' => '1463', 'height' => '', 'crop' => false),
			THEME_SHORT.'-portfolio-one-col'   => array('width' => '1463', 'height' => '', 'crop' => false),
			THEME_SHORT.'-portfolio-two-col'   => array('width' => '731', 'height' => '', 'crop' => false),
			THEME_SHORT.'-portfolio-three-col' => array('width' => '488', 'height' => '', 'crop' => false),
			THEME_SHORT.'-portfolio-four-col'  => array('width' => '366', 'height' => '', 'crop' => false),
			THEME_SHORT.'-portfolio-six-col'   => array('width' => '244', 'height' => '', 'crop' => false),

			THEME_SHORT.'-full-6' => array('width' => '555', 'height' => '400', 'crop' => true),


			THEME_SHORT.'-gallery-large' => array('width' => '1170', 'height' => '500', 'crop' => true),
			THEME_SHORT.'-gallery-nav'   => array('width' => '70', 'height' => '70', 'crop' => true),

			THEME_SHORT.'-woo-elegant' => array('width' => '360', 'height' => '470', 'crop' => true),

			THEME_SHORT.'-relative-event' => array('width' => '780', 'height' => '440', 'crop' => true),
			THEME_SHORT.'-job-small' => array('width' => '306', 'height' => '284', 'crop' => true),
			THEME_SHORT.'-618x285' => array('width' => '618', 'height' => '285', 'crop' => true),
			THEME_SHORT.'-236x250' => array('width' => '236', 'height' => '250', 'crop' => true),
			THEME_SHORT.'-484x558' => array('width' => '484', 'height' => '558', 'crop' => true),
			THEME_SHORT.'-50x50' => array('width' => '50', 'height' => '50', 'crop' => true),
			THEME_SHORT.'-440x440' => array('width' => '440', 'height' => '440', 'crop' => true),

		);

		if(!isset($sizes[THEME_SHORT.'-'.$size])){
			if(strstr($size, 'x') !== false){
				$a = explode('x', $size);
				$sizes[THEME_SHORT.'-'.$size] = array('width' => $a[0], 'height' => $a[1], 'crop' => true);
			}
		}

		$image_sizes = !empty($sizes[THEME_SHORT.'-'.$size]) ? $sizes[THEME_SHORT.'-'.$size] : $size;

		return $image_sizes;
	}

	public static function get_file_ext($file_path){
		$base_name = basename($file_path);
		$a = explode('.', $base_name);
		$ext = end($a);
		#\Digidez\Functions::_debug($ext);
		return strtolower($ext);
	}

	public static function create_square_image($file, $min_size = 440){

		$dst_image_path = $file;
		
		if(basename($file) == 'mob-remont.jpg'){
			return true;
		}
		
		list($imageWidth, $imageHeight, $imageType, $imageAttr) = getimagesize($file);

		$staticWidth = $imageWidth;
		$staticHeight = $imageHeight;
		$x_pos = $y_pos = 0;

		if($imageWidth < $min_size && $imageHeight < $min_size){
			$staticWidth = $staticHeight = 440;
			$x_pos       = round(($staticWidth - $imageWidth) / 2);
			$y_pos       = round(($staticHeight - $imageHeight) / 2);
		}else{
			if($imageWidth == $imageHeight){
				return true;
			}elseif($imageWidth > $imageHeight){
				$staticHeight = $imageWidth;
				$y_pos        = round(($staticHeight - $imageHeight) / 2);
			}elseif($imageHeight > $imageWidth){
				$staticWidth = $imageHeight;
				$x_pos       = round(($staticWidth - $imageWidth) / 2);
			}
		}

		#Functions::_debug($brands_image_location); exit;

		$brands_image_location_tmp = "$file.tmp";
		$newimage = imagecreatetruecolor($staticWidth, $staticHeight);

		switch($imageType){
			case 1:
				imagealphablending($newimage, false);
				imagesavealpha($newimage, true);
				$transparent = imagecolorallocatealpha($newimage, 255, 255, 255, 127);
				imagefilledrectangle($newimage, 0, 0, $staticWidth, $staticHeight, $transparent);
				$src = imagecreatefromgif($file);
				break;
			case 2:
				$white = imagecolorallocate($newimage, 255, 255, 255);
				imagefilledrectangle($newimage, 0, 0, $staticWidth, $staticHeight, $white);
				$src = imagecreatefromjpeg($file);
				break;
			case 3:
				imagealphablending($newimage, false);
				imagesavealpha($newimage, true);
				$transparent = imagecolorallocatealpha($newimage, 255, 255, 255, 127);
				imagefilledrectangle($newimage, 0, 0, $staticWidth, $staticHeight, $transparent);
				$src = imagecreatefrompng($file);
				break;
		}

		//imagecopyresampled($newimage, $src, $x_pos, $y_pos, 0, 0, $staticWidth, $staticHeight, $imageWidth, $imageHeight);
		//imagecopyresized($newimage, $src, $x_pos, $y_pos, 0, 0, $staticWidth, $staticHeight, $imageWidth, $imageHeight);
		imagecopy($newimage, $src, $x_pos, $y_pos, 0, 0, $imageWidth, $imageHeight);

		switch($imageType){
			case 1:
				imagegif($newimage, $brands_image_location_tmp);
				break;
			case 2:
				imagejpeg($newimage, $brands_image_location_tmp);
				break;
			case 3:
				imagepng($newimage, $brands_image_location_tmp);
				break;
		}

		unlink($file);

		rename($brands_image_location_tmp, $dst_image_path);

		return true;
	}

	public static function get_text_col_css_classes($text_align = 'full'){
		switch($text_align){
			case "center":
				$ret = "offset-md-3 col-md-6";
				break;
			case "left":
				$ret = "col-md-5";
				break;
			case "right":
				$ret = "offset-md-7 col-md-5";
				break;
			case "left_center":
				$ret = "col-md-8";
				break;
			case "right_center":
				$ret = "offset-md-4 col-md-8";
				break;
			case "full":
			default:
				$ret = "col-md-12";
				break;
		}
		return $ret;
	}

	public static function create_excerpt($text, $max_length = 100){
		$_text = strip_tags($text);
		if(mb_strlen($_text, 'utf-8') <= $max_length){
			return $text;
		}

		$_text = trim($_text, ".,?:><;");
		$a = explode(' ', $_text);

		$r = '';
		$n = [];
		foreach($a as $k => $t){
			$r .= $t.' ';
			if(mb_strlen($r, 'utf-8') >= $max_length){
				continue;
			}else{
				$n[$k] = $t;
			}
		}

		return implode(' ', $n).'...';

	}

	public static function format_iframe_video($iframe){

		// use preg_match to find iframe src
		preg_match('/src="(.+?)"/', $iframe, $matches);
		$src = $matches[1];
		// add extra params to iframe src
		$params = array(
			'controls' => 0,
			'hd' => 1,
			'autohide' => 1,
			'rel' => 0,
			'showinfo' => 0,
			'autoplay' => 0,
			'enablejsapi' => 1,
			'modestbranding' => 1,
			'iv_load_policy' => 3,
			'color' => 'white',
			'title' => 0,
			'byline' => 0,
			'portrait' => 0,
		);
		$new_src = add_query_arg($params, $src);
		$iframe = str_replace($src, $new_src, $iframe);

		preg_match('/width="(.+?)"/', $iframe, $matches);
		$width = $matches[1];
		$iframe = str_replace('width="'.$width.'"', 'width="100%"', $iframe);

		preg_match('/height="(.+?)"/', $iframe, $matches);
		$height = $matches[1];
		$iframe = str_replace('height="'.$height.'"', 'height="100%"', $iframe);

		// add extra attributes to iframe html
		$attributes = 'frameborder="0"';
		$iframe = str_replace('></iframe>', ' '.$attributes.'></iframe>', $iframe);

		return $iframe;
	}

	public static function get_play_btn_svg(){
		return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="btn-video-play" width="138" height="138" viewBox="0 0 138 138"><defs><path id="cjcza" d="M951.049 3345.785v52.113l26.056-26.056-26.056-26.056zm8.953 85.633c-8.064 0-15.889-1.58-23.256-4.695a59.773 59.773 0 0 1-18.99-12.802 60.117 60.117 0 0 1-7.295-8.841 59.744 59.744 0 0 1-5.51-10.148c-3.115-7.364-4.695-15.188-4.695-23.256 0-8.067 1.58-15.89 4.695-23.253a59.743 59.743 0 0 1 5.51-10.148 60.11 60.11 0 0 1 16.137-16.137 59.794 59.794 0 0 1 10.148-5.508c7.367-3.116 15.191-4.695 23.256-4.695 8.066 0 15.889 1.58 23.252 4.695a59.646 59.646 0 0 1 10.148 5.508 60.068 60.068 0 0 1 16.137 16.137 59.744 59.744 0 0 1 5.51 10.148c3.115 7.363 4.695 15.186 4.695 23.253 0 8.068-1.58 15.892-4.695 23.256a59.743 59.743 0 0 1-5.51 10.148 60.118 60.118 0 0 1-16.137 16.136 59.625 59.625 0 0 1-10.148 5.507c-7.363 3.115-15.186 4.695-23.252 4.695z"/><mask id="cjczc" width="2" height="2" x="-1" y="-1"><path fill="#fff" d="M900 3311h120v121H900z"/><use xlink:href="#cjcza"/></mask><filter id="cjczb" width="164" height="165" x="878" y="3289" filterUnits="userSpaceOnUse"><feOffset in="SourceGraphic" result="FeOffset1023Out"/><feGaussianBlur in="FeOffset1023Out" result="FeGaussianBlur1024Out" stdDeviation="4.8 4.8"/></filter></defs><g><g transform="translate(-891 -3303)"><g filter="url(#cjczb)"><use fill="none" stroke-opacity=".16" stroke-width="0" mask="url(&quot;#cjczc&quot;)" xlink:href="#cjcza"/><use fill-opacity=".16" xlink:href="#cjcza"/></g><use class="circle" fill="#fff" xlink:href="#cjcza"/></g></g></svg>';
	}

	public static function get_page_by_template($template_path){
		$pages = get_pages(array(
			'meta_key' => '_wp_page_template',
			'meta_value' => $template_path
		));

		return $pages[0];
	}

	public static function get_page_template(){
		global $post;

		$template_name = '';

		if(!is_null($post)){
			$_wp_page_template = get_post_meta($post->ID, '_wp_page_template', true);

			if(!empty($_wp_page_template)){
				$a = explode('/', $_wp_page_template);
				$template_name = str_replace('.php', '', end($a));
			}
		}

		return $template_name;
	}

	public static function wc_get_shop_page(){
		$woocommerce_shop_page_id = get_option('woocommerce_shop_page_id');
		$post = get_post($woocommerce_shop_page_id);
		$post->cf = get_fields($post->ID);

		return $post;
	}

	public static function get_align_class_for_elem($index = 0, $cols_count = 3, $revers = false){
		$align_classes = self::$element_align_classes[$cols_count];
		if($revers){
			$align_classes = array_flip($align_classes);
		}

		return $align_classes[$index%$cols_count];
	}

	public static function get_align_class_in_col($index = 0, $cols_count = 3, $revers = false){
		$align_classes = self::$in_col_align_classes[$cols_count];
		if($revers){
			$align_classes = array_flip($align_classes);
		}

		return $align_classes[$index%$cols_count];
	}

	public static function get_device_slider_class(){
		$css_class = 'owl-carousel';
		if(self::$device == 'desktop'){
			//$css_class = 'theta-carousel';
		}

		return $css_class;
	}

	public static function sort_page_section($cf){
		$ret_cf = $cf;

		if(isset($cf['sections_ordering']) && !empty($cf['sections_ordering'])){

			$a = $cf['sections_ordering'];
			asort($a);
			//self::_debug($a);
			$ret_cf = [];
			foreach($a as $k => $v){
				$ret_cf[$k] = $cf[$k];
			}
		}

		return $ret_cf;
	}

	public static function get_instagram_auth(){
		return isset($_COOKIE['user_instagram_auth']) ? $_COOKIE['user_instagram_auth'] : 0;
	}

	public static function set_instagram_auth(){
		$exp = (180 * 24 * 60 * 60 * 1000);
		$_COOKIE['user_instagram_auth'] = 1;
		setcookie('user_instagram_auth', 1, $exp, '/', '.'.$_SERVER['HTTP_HOST']);
	}

	/**
	 * Этот метод используется для отображения конкретного
	 * виджета на последней странице категории товаров.
	 * Он работает в связке с плагином widget-logic.
	 * В поле Widget logic необходимо прописать так: Digidez\Functions::is_shop_last_category()
	 * @return bool
	 */
	public static function is_shop_last_category(){
		if(is_product_category()) {
			global $wp_query;
			$cat = $wp_query->get_queried_object();
			$childs = get_term_children($cat->term_id, 'product_cat');
			#self::_debug($childs);
			if(empty($childs)){
				return true;
			}
		}

		return false;
	}

	public static function get_product_page_position($post_id = 0){
		global $wpdb;
		$page_num = 0;

		$edit_product_per_page = get_user_meta(get_current_user_id(), 'edit_product_per_page', true);
		$post_ids = $wpdb->get_col("SELECT SQL_CALC_FOUND_ROWS ID FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status IN ('publish', 'draft', 'pending') ORDER BY post_title ASC");
		#$products_found = $wpdb->get_var("SELECT FOUND_ROWS()");
		$post_position = array_search($post_id, $post_ids);

		if($post_position !== ''){
			$post_position += 1;
			$page_num = ceil($post_position / $edit_product_per_page);
		}

		#self::_debug($post_id);
		#self::_debug($post_ids);
		#self::_debug($products_found);
		#self::_debug($post_position);
		#self::_debug($page_num);
		#exit;

		return $page_num;
	}

	public static function electro_do_shortcode($tag, array $atts = [], $content = null){
		global $shortcode_tags;
		if(!isset($shortcode_tags[$tag])){
			return false;
		}

		if($tag == 'products' && !isset($atts['orderby'])){
			$atts['orderby'] = 'post__in';
		}
		$func = str_replace('WC_Shortcodes', __NAMESPACE__.'\WC_Shortcodes_Child', $shortcode_tags[$tag]);
		#Functions::_debug($shortcode_tags[$tag]);
		#Functions::_debug($func);

		return call_user_func($func, $atts, $content, $tag);
	}


	/** RENDERING **/

	public static function render_page_sections($post){
		$_SESSION['form_id'] = 0;
		$post->cf = get_fields($post->ID);
		$post->cf = self::sort_page_section($post->cf);

		foreach($post->cf as $k => $section_data){
			if(isset($section_data['display_this_section']) && $section_data['display_this_section']){
				$s = '';
				$f = str_replace('section_', '', $k);
				if(strstr($f, '_') !== false){
					$a = explode('_', $f);
					$f = array_shift($a);
					$s = '-'.implode('-', $a);
					#self::_debug($s);
				}

				$section_name = $f;
				if(!empty($s)){
					$section_name .= $s;
				}

				if(isset($section_data['menu_target_hash']) && !empty($section_data['menu_target_hash'])){
					$menu_target_hash = str_replace(array('/', '#'), '', $section_data['menu_target_hash']);
					echo '<div id="'.$menu_target_hash.'-section"></div>';
				}
				#self::_debug(PARTIALS_PATH.'/'.$f.'/section'.$s);
				self::get_template_part(PARTIALS_PATH.'/'.$f.'/section'.$s, ['section_name' => $section_name, 'section_data' => $section_data]);
			}
		}

	}

	public static function render_social_share($echo = false){
		$html = '';
		$column_right = get_field('column_right', 'option');
		$socail_links = $column_right['social_links'];

		foreach($socail_links as $k => $item){
			$html .= '<a href="'.self::$shared_links[$item['service']].'" title="'.$item['service'].'"><i class="fa fa-'.$item['service'].' fa-fw"></i></a>';
		}

		if(!$echo){
			return $html;
		}

	}

	public static function render_modals($echo = true){
		$html = '';

		if(class_exists('ACF')){
			$modals_data = [];

			$display_cookie_policy = get_field('display_cookie_policy', 'option');
			if($display_cookie_policy){
				$modals_data['cookie-policy'] = array(
					'title'   => get_field('cookie_policy_popup_title', 'option'),
					'content' => get_field('cookie_policy_popup_content', 'option')
				);
			}
			/*$modals_data['privacy-policy'] = array(
				'title' => get_field('privacy_policy_popup_title', 'option'),
				'content' => get_field('privacy_policy_popup_content', 'option')
			);*/


			if(!empty($modals_data)){
				if($echo){
					set_query_var('modals_data', $modals_data);
					get_template_part(PARTIALS_PATH.'/modal');
				}else{
					ob_start();
					include locate_template(PARTIALS_PATH.'/modal.php');
					$html = ob_get_clean();
				}
			}
		}

		if(!$echo){
			return $html;
		}
	}

	public static function render_cookiebox($echo = true){
		$html = '';

		if(class_exists('ACF')){
			$display_cookie_policy = get_field('display_cookie_policy', 'option');

			if(isset($_COOKIE['acceptCookies']) && intval($_COOKIE['acceptCookies']) == 10){
				$display_cookie_policy = false;
			}

			if($display_cookie_policy){
				$box_data = array(
					'text'   => get_field('cookies_box_text', 'option'),
					'button' => get_field('cookies_box_button', 'option')
				);

				if($echo){
					set_query_var('box_data', $box_data);
					get_template_part(PARTIALS_PATH.'/cookiebox');
				}else{
					ob_start();
					include locate_template(PARTIALS_PATH.'/cookiebox.php');
					$html = ob_get_clean();
				}
			}
		}

		if(!$echo){
			return $html;
		}
	}

	public static function render_footer($echo = true){
		global $post;
		$html = '';

		if(class_exists('ACF')){
			$footer = get_field('footer', 'option');
			$data   = array(
				'copyright_bar_color' => $footer['copyright_bar_color'],
				'footer_color'        => $footer['footer_color'],
				'copyright_text'      => $footer['copyright_text'],
				'creator_text'        => $footer['creator_text'],
				'nav'                 => [], //self::get_menu_tree('footer-menu'),
				'col'                 => 12,
			);
			if(!empty($data['nav'])){
				$data['col'] = floor(12 / count($data['nav']));
			}
			//self::_debug($data);
			if($echo){
				set_query_var('data', $data);
				get_template_part(PARTIALS_PATH.'/footer');
			}else{
				ob_start();
				include locate_template(PARTIALS_PATH.'/footer.php');
				$html = ob_get_clean();
			}
		}

		if(!$echo){
			return $html;
		}
	}

	public static function render_header($echo = true){
		global $post;
		$html = '';

		if(class_exists('ACF')){
			$data = [];

			if($echo){
				set_query_var('data', $data);
				get_template_part(PARTIALS_PATH.'/header');
			}else{
				ob_start();
				include locate_template(PARTIALS_PATH.'/header.php');
				$html = ob_get_clean();
			}
		}

		if(!$echo){
			return $html;
		}
	}

	public static function render_media_block($post_meta_data, $echo = true, $color = 'indigo'){
		global $post;
		$html = '';

		$data = array(
			'type_of_illustration' => $post_meta_data['type_of_illustration'],
			'video_embed' => $post_meta_data['video_embed'],
			'video_local' => $post_meta_data['video_local'],
			'image' => $post_meta_data['image'],
			'color' => $color,
		);

		if($echo){
			set_query_var('data', $data);
			get_template_part(PARTIALS_PATH.'/common/illustration');
		}else{
			ob_start();
			include locate_template(PARTIALS_PATH.'/common/illustration.php');
			$html = ob_get_clean();
		}

		if(!$echo){
			return $html;
		}
	}

	public static function get_cloud_bg($color, $type = 'auto'){
		$filename = '';
		switch($type){
			case "homepage":
				$filename = "header_cloud";
				break;
			case "page":
				$filename = "page_cloud";
				break;
			default:
				if(is_front_page()){
					$filename = "header_cloud";
				}else{
					$filename = "page_cloud";
				}
				break;
		}

		$f_color = strtolower(trim($color, '#'));

		if(!empty($filename)){
			$dst_file = IMG_DIR.'/'.$filename.'_'.$f_color.'.svg';
			$src_file = IMG_DIR.'/'.$filename.'.svg';

			$create = false;
			if(!file_exists($dst_file)){
				$create = true;
			}else{
				if(filemtime($dst_file) + 3600 < time()){
					$create = true;
				}
			}

			if($create){
				$fcontent = file_get_contents($src_file);
				$fcontent = str_replace('#C4C4C4', $color, $fcontent);
				file_put_contents($dst_file, $fcontent);
			}

			return IMG_URI.'/'.$filename.'_'.$f_color.'.svg';
		}

		return '';
	}

	public static function get_author_link($args = []){
		global $authordata;

		if(!is_object($authordata)){
			return;
		}

		$before = $after = '';

		$defaults = array(
			'before' => '<i class="fa fa-user"></i> ',
			'after' => ''
		);
		extract(wp_parse_args($args, $defaults));

		$link = sprintf(
			'<a class="url fn" href="%1$s" title="%2$s" rel="author">%3$s</a>',
			esc_url(get_author_posts_url($authordata->ID, $authordata->user_nicename)),
			esc_attr(sprintf(esc_html__('Posts by %s', 'boo'), get_the_author())),
			$before.get_the_author().$after
		);
		return $link;
	}

	public static function get_comments_callback( $comment, $args, $depth ) {

		// Get the comment type of the current comment.
		$comment_type = get_comment_type( $comment->comment_ID );

		// Create an empty array if the comment template array is not set.
		$comment_template = [];

		// Check if a template has been provided for the specific comment type.  If not, get the template.
		if(!isset($comment_template[$comment_type])){

			// Create an array of template files to look for.
			$templates = array( "templates/comment/{$comment_type}.php" );

			// If the comment type is a 'pingback' or 'trackback', allow the use of 'comment-ping.php'.
			if ( 'pingback' == $comment_type || 'trackback' == $comment_type ) {
				$templates[] = 'templates/comment/ping.php';
			}

			// Add the fallback 'comment.php' template.
			$templates[] = 'templates/comment/comment.php';

			// Allow devs to filter the template hierarchy.
			//$templates = apply_filters( 'rella_comment_template_hierarchy', $templates, $comment_type );

			// Locate the comment template.
			$template = locate_template( $templates );

			// Set the template in the comment template array.
			$comment_template[ $comment_type ] = $template;
		}

		// If a template was found, load the template.
		if ( ! empty($comment_template[ $comment_type ] ) ) {
			require($comment_template[ $comment_type ] );
		}
	}

	public static function get_template_part($slug, $params = [], $echo = true){
		if($echo){
			if(!empty($params)){
				foreach($params as $k => $param){
					set_query_var($k, $param);
				}
			}
			get_template_part($slug);
		}else{
			ob_start();
			extract($params);
			include locate_template($slug.'.php');
			return ob_get_clean();
		}

	}


	/** PAGINATION/POST NAVS **/

	public static function get_pagination($pages = '', $args = []){
		global $wp_query;
		global $paged;

		$defaults = array(
			'wrap_class' => '',
			'range' => 2,
			'blog' => true,
			'infinite' => false,
			'echo' => true,
		);

		$args = wp_parse_args($args, $defaults);
		$range = $args['range'];
		$blog = $args['blog'];
		$infinite = $args['infinite'];
		$echo = $args['echo'];


		$blog_pagination = 'pages';
		$output          = '';
		if($infinite == false){
			// we don't use infinite pagination, show display the chosen pagination style
			switch($blog_pagination){
				case 'next_prev':
					if($wp_query->max_num_pages > 1){
						$output .= '<nav id="nav-below" class="post-navigation padded '.$args['wrap_class'].'">';
						$output .= '<ul class="pager">';
						$output .= '<li class="previous">'.get_next_posts_link('<i class="fa fa-angle-left"></i>'.esc_html__('Previous', 'lambda-td')).'</li>';
						$output .= '<li class="next">'.get_previous_posts_link(esc_html__('Next', 'lambda-td').'<i class="fa fa-angle-right"></i>').'</li>';
						$output .= '</ul>';
						$output .= '</nav>';
					}
					break;
				case 'pages':
				default:
					$showitems = ($range * 2) + 1;
					if(empty($paged)){
						$paged = 1;
					}

					if($pages == ''){
						global $wp_query;
						$pages = $wp_query->max_num_pages;
						if(!$pages){
							$pages = 1;
						}
					}
					$output = '<div class="text-center pagination-wrap '.$args['wrap_class'].'">';
					if(1 != $pages){
						$output .= '<ul class="post-navigation pagination">';
						$output .= ($paged > 1) ? '<li><a href="'.get_pagenum_link($paged - 1).'" class="arrow prev">&lsaquo;</a></li>' : '<li class="disabled"><a class="arrow prev">&lsaquo;</a></li>';

						for($i = 1; $i <= $pages; $i++){
							if(1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems)){
								$output .= ($paged == $i) ? '<li class="active"><span class="current">'.$i.'</span></li>' : '<li><a href="'.get_pagenum_link($i).'" class="inactive">'.$i.'</a></li>';
							}
						}

						$output .= ($paged < $pages) ? "<li><a href='".get_pagenum_link($paged + 1)."' class='arrow next'>&rsaquo;</a></li>" : "<li class='disabled'><a class='arrow next'>&rsaquo;</a></li>";
						$output .= "</ul>";
					}
					$output .= "</div>\n";
					break;
			}
		}

		if($echo == true){
			echo $output;
		}else{
			return $output;
		}
	}

	public static function post_nav(){
		// Don't print empty markup if there's nowhere to navigate.
		$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
		$next     = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous ) {
			return;
		}
		?>
		<nav class="container navigation post-navigation">
			<h2 class="sr-only"><?php _e( 'Post navigation', 'understrap' ); ?></h2>
			<div class="row nav-links justify-content-between">
				<?php

				if ( get_previous_post_link() ) {
					previous_post_link( '<span class="nav-previous">%link</span>', _x( '<i class="fa fa-angle-left"></i>&nbsp;%title', 'Previous post link', 'understrap' ) );
				}
				if ( get_next_post_link() ) {
					next_post_link( '<span class="nav-next">%link</span>',     _x( '%title&nbsp;<i class="fa fa-angle-right"></i>', 'Next post link', 'understrap' ) );
				}
				?>
			</div><!-- .nav-links -->
		</nav><!-- .navigation -->

		<?php
	}


    /** THEME OVERRIDE **/

	public static function electro_off_canvas_nav() {
		$classes = '';
		if( apply_filters( 'electro_off_canvas_nav_hide_in_desktop', false ) ) {
			$classes = 'off-canvas-hide-in-desktop';
		}
		?>
        <div class="off-canvas-navigation-wrapper <?php echo esc_attr( $classes ); ?>">
            <div class="off-canvas-navbar-toggle-buttons clearfix">
                <button class="navbar-toggler navbar-toggle-hamburger " type="button">
                    <i class="ec ec-menu"></i>
                </button>
                <button class="navbar-toggler navbar-toggle-close " type="button">
                    <i class="ec ec-close-remove"></i>
                </button>
            </div>

            <div class="off-canvas-navigation" id="default-oc-header">
				<?php
				wp_nav_menu([
					'theme_location'    => 'primary-nav',
					'container'         => false,
					'menu_class'        => 'nav nav-inline yamm',
					'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
					'walker'            => new \wp_bootstrap_navwalker()
				]);
				wp_nav_menu([
					'theme_location'    => 'hand-held-nav',
					'container'         => false,
					'menu_class'        => 'nav nav-inline yamm',
					'fallback_cb'       => 'electro_handheld_nav_fallback',
					'walker'            => new \wp_bootstrap_navwalker()
				]);
				?>
            </div>
        </div>
		<?php
	}

	/** FORM FIELDS **/

	public static function form_input($args){
		$default = array(
			'type' => 'text',
			'placeholder' => '',
			'class' => array('form-control'),
			'name' => 'text_'.md5('test'.time()),
			'id' => '',
			'value' => (isset($_REQUEST[$args['name']]) ? $_REQUEST[$args['name']] : false),
			'mobile' => false,
		);

		$args = wp_parse_args($args, $default);

		$html = sprintf('<input type="%s" name="%s" value="%s" id="%s" class="%s" placeholder="%s" data-mobile="%s">',
			$args['type'],
			$args['name'],
			$args['value'],
			$args['id'],
			implode(' ', $args['class']),
			$args['placeholder'],
			$args['mobile']
		);

		return $html;
	}

	public static function form_select($args, $options){
		$default = array(
			'title' => '',
			'class' => array('selectpicker'),
			'name' => md5('test'.time()),
			'id' => '',
			'value' => (isset($_REQUEST[$args['name']]) ? $_REQUEST[$args['name']] : false),
			'mobile' => false,
		);

		$args = wp_parse_args($args, $default);

		$def_class = array('selectpicker');
		$args['class'] = wp_parse_args($args['class'], $def_class);

		//self::_debug($args);

		$_options = [];
		foreach($options as $k => $v){
			$selected = ($k == $args['value']) ? 'selected="selected"' : '';
			$_options[] = sprintf('<option value="%d" %s>%s</option>', $k, $selected, $v);
		}

		$html = sprintf('<select name="%s" id="%s" class="%s" data-title="%s" data-mobile="%s">%s</select>',
			$args['name'],
			$args['id'],
			implode(' ', $args['class']),
			$args['title'],
			$args['mobile'],
			implode('', $_options)
		);

		return $html;
	}

	public static function form_month_select($args){
		$default = array(
			'title' => 'Month',
			'class' => array('selectpicker'),
			'name' => 'month',
			'id' => '',
			'value' => (isset($_REQUEST[$args['name']]) ? $_REQUEST[$args['name']] : false),
			'mobile' => false,
		);

		$args = wp_parse_args($args, $default);

		if(false == $args['mobile']){
			self::$months[0] = "Month";
			ksort(self::$months);
		}

		return self::form_select($args, self::$months);
	}

	public static function form_year_select($args){
		$default = array(
			'title' => 'Year',
			'class' => array('selectpicker'),
			'name' => 'year',
			'id' => '',
			'value' => (isset($_REQUEST[$args['name']]) ? $_REQUEST[$args['name']] : false),
			'mobile' => false,
		);

		$args = wp_parse_args($args, $default);

		if(false == $args['mobile']){
			$options = array(0 => $args['title']);
		}else{
			$options = [];
		}
		for($i = date('Y'); $i <= date('Y')+10; $i++){
			$options[$i] = $i;
		}

		return self::form_select($args, $options);
	}

	public static function form_cats_select($args){
		$default = array(
			'title' => 'Categories',
			'class' => array('selectpicker'),
			'name' => 'categories',
			'taxonomy' => 'category',
			'id' => '',
			'value' => (isset($_REQUEST[$args['name']]) ? $_REQUEST[$args['name']] : false),
			'mobile' => false,
		);

		$args = wp_parse_args($args, $default);

		$terms = get_terms(array('taxonomy' => $args['taxonomy'], 'fields' => 'id=>name'));

		if(false == $args['mobile']){
			$terms[0] = "Category";
			ksort($terms);
		}

		return self::form_select($args, $terms);
	}

	public static function form_course_select($args){
		$default = array(
			'title' => 'Date',
			'class' => array('selectpicker'),
			'name' => 'course_date',
			'id' => '',
			'value' => (isset($_REQUEST[$args['name']]) ? $_REQUEST[$args['name']] : false),
			'mobile' => false,
			'options' => [],
		);

		$args = wp_parse_args($args, $default);

		$options = array(0 => $args['title']);
		if(!empty($args['options'])){
			foreach($args['options'] as $time){
				$options[] = date('H:i', strtotime($time['time']));
			}
		}


		return self::form_select($args, $options);
	}

	public static function form_course_dropdown($args){
		$default = array(
			'title' => 'Date',
			'class' => [],
			'name' => 'course_date',
			'id' => '',
			'options' => [],
		);

		$args = wp_parse_args($args, $default);

		$options_html = '
			<li data-original-index="%d">
				<a tabindex="0" class="%s" role="option" aria-disabled="false" aria-selected="false">
					<span class="text">%s</span>
				</a>
			</li>
		';
		$options = [];
		if(!empty($args['options'])){
			foreach($args['options'] as $k => $time){
				$options[] = sprintf($options_html, ($k+1), '', date('H:i', strtotime($time['time'])));
			}
		}


		$html = '
			<div class="btn-group '.implode(' ', $args['class']).'">
				<button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" role="button" data-id="" title="'.$args['title'].'" aria-expanded="false">
					<span class="filter-option pull-left">'.$args['title'].'</span>&nbsp;<span class="bs-caret"><span class="caret"></span></span>
				</button>
				<div class="dropdown-menu" role="combobox">
					<ul class="dropdown-menu inner" role="listbox" aria-expanded="false">
					'.implode('', $options).'
					</ul>
				</div>
			</div>
		';


		return $html;
	}

	public static function form_course_panel($args){
		$default = array(
			'z_index' => 1,
			'label' => 'Date',
			'wrap_class' => array('panel', 'toggle-panel', 'dropdown'),
			'title_class' => array('title', 'collapsed', 'small-arrow'),
			'id' => '',
			'options' => [],
		);

		$args['wrap_class'] = wp_parse_args($args['wrap_class'], $default['wrap_class']);
		$args['title_class'] = wp_parse_args($args['title_class'], $default['title_class']);
		$args = wp_parse_args($args, $default);

		$options_html = '<li data-original-index="%d"><a class="link">%s</a></li>';
		$options = [];
		if(!empty($args['options'])){
			foreach($args['options'] as $k => $time){
				$options[] = sprintf($options_html, ($k+1), date('H:i', strtotime($time['time'])));
			}
		}


		$html = '
			<div class="'.implode(' ', $args['wrap_class']).'" style="z-index:'.$args['z_index'].';">
				<a class="'.implode(' ', $args['title_class']).'" role="button" data-toggle="collapse" data-parent="#'.$args['id'].'" href="#'.$args['id'].'" aria-expanded="true" aria-controls="collapse">
					'.$args['label'].'
				</a>
				<div id="'.$args['id'].'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading">
					<ul class="links">
						'.implode('', $options).'
					</ul>
				</div>
			</div>
		';


		return $html;
	}


}
