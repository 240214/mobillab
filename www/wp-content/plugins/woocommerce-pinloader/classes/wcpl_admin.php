<?php

namespace Pinloader;


class WCPL_Admin {

	public static $options;
	public static $admin_menu;
	public static $admin_submenu_order = array(0,1,2,3,4,5,6,7,8,9,10,11);

	public static function initialise(){
		$self = new self();

		// define all action hooks here and document if not self explanatory
		add_action('init', array($self, 'init'), 0);
		add_action('admin_menu', array($self, 'action_admin_menu'), 30);
		if(is_admin()){
			add_action('admin_init', array($self, 'register_settings'), 10);
			add_action('admin_enqueue_scripts', array($self, 'admin_enqueue_scripts'));
			//add_filter('plugin_action_links_'.PINLOADER_PLUGIN_BASE, array($self, 'plugin_action_links'));
		}
	}

	public function init(){
		self::$options = $this->get_options();

		self::$admin_menu = array(
			'mainmenu' => array(
				WcPinLoader::$plugin_slug => array('tab' => false, 'title' => esc_attr__('WC Pinloader', PINLOADER_TEXT_DOMAIN))
			),
			'submenu'  => array(
				'price-upload'         => array('tab' => true, 'order' => 0, 'title' => esc_attr__('Upload prices', PINLOADER_TEXT_DOMAIN)),
				'products-off'         => array('tab' => true, 'order' => 1, 'title' => esc_attr__('Products to turn off', PINLOADER_TEXT_DOMAIN)),
				'price-diff'           => array('tab' => true, 'order' => 2, 'title' => esc_attr__('Price diff', PINLOADER_TEXT_DOMAIN)),
				'products-on'          => array('tab' => true, 'order' => 6, 'title' => esc_attr__('Products to turn on', PINLOADER_TEXT_DOMAIN)),
				'products-new'         => array('tab' => true, 'order' => 3, 'title' => esc_attr__('New products', PINLOADER_TEXT_DOMAIN)),
				'products-duplicate'   => array('tab' => true, 'order' => 4, 'title' => esc_attr__('Products duplicate', PINLOADER_TEXT_DOMAIN)),
				'products-ignored'     => array('tab' => true, 'order' => 5, 'title' => esc_attr__('Ignored products', PINLOADER_TEXT_DOMAIN)),
				'products-no-supplier' => array('tab' => true, 'order' => 7, 'title' => esc_attr__('Products without suppliers', PINLOADER_TEXT_DOMAIN)),
				'price-monitoring'     => array('tab' => true, 'order' => 8, 'title' => esc_attr__('Price monitoring', PINLOADER_TEXT_DOMAIN)),
				'price-ranges'         => array('tab' => false, 'order' => 9, 'title' => esc_attr__('Price ranges', PINLOADER_TEXT_DOMAIN)),
				'suppliers'            => array('tab' => false, 'order' => 10, 'title' => esc_attr__('Suppliers', PINLOADER_TEXT_DOMAIN)),
				//'products-content'     => array('tab' => false, 'order' => 11, 'title' => esc_attr__('Fill products content', PINLOADER_TEXT_DOMAIN)),
				//'help'                 => array('tab' => false, 'order' => 12, 'title' => esc_attr__('Help', PINLOADER_TEXT_DOMAIN)),
			)
		);

		/*$cache_dir = WcPinLoader::instance()->cache_dir;
		$cache_file = WcPinLoader::instance()->cache_file;
		$d = intval(date('Ymd'));

		if(self::$options['pinloader_cache_dir'] == ''){
			$cache_dir = WcPinLoader::instance()->set_cache_dir($d.'_cache');
			$cache_file = WcPinLoader::instance()->set_cache_file($cache_dir.'/'.$cache_file);
			self::$options['pinloader_cache_dir'] = $d;
			$this->update_options(self::$options);
			$this->create_caches($cache_dir, $cache_file);
		}else{
			if($d != self::$options['pinloader_cache_dir']){
				@unlink($cache_dir.self::$options['pinloader_cache_dir']);
				$cache_dir = WcPinLoader::instance()->set_cache_dir($d.'_cache');
				$cache_file = WcPinLoader::instance()->set_cache_file($cache_dir.'/'.$cache_file);
				self::$options['pinloader_cache_dir'] = $d;
				$this->update_options(self::$options);
				$this->create_caches($cache_dir, $cache_file);
			}else{
				$cache_dir = WcPinLoader::instance()->set_cache_dir(self::$options['pinloader_cache_dir'].'_cache');
				$cache_file = WcPinLoader::instance()->set_cache_file($cache_dir.'/'.$cache_file);
				$this->create_caches($cache_dir, $cache_file);
			}
		}*/

	}

	public function plugin_action_links($links){
		$settings_link = sprintf( '<a href="%s">%s</a>', admin_url('admin.php?page=pinloader'), esc_attr__( 'Settings', PINLOADER_TEXT_DOMAIN ) );
		array_unshift( $links, $settings_link );

		return $links;
	}

	public function admin_enqueue_scripts(){
		$page = $this->is_plugin_page();
		#WCPL_Helper::_debug($page);

		if(false !== $page){
			//wp_enqueue_style('wp-color-picker');
			wp_enqueue_style('jquery-ui', PINLOADER_CSS_URI.'/jquery-ui.min.css');
			wp_enqueue_style('bootstrap', PINLOADER_CSS_URI.'/bootstrap.css');
			wp_enqueue_style('bootstrap-select', PINLOADER_CSS_URI.'/bootstrap-select.min.css');
			wp_enqueue_style('font-awesome-style', PINLOADER_CSS_URI.'/font-awesome.min.css');
		}

			wp_enqueue_style('pinloader-style', PINLOADER_CSS_URI.'/backend.css');

		if(false !== $page){
			//wp_enqueue_style('pinloader-settings', PINLOADER_CSS_URI.'/settings.css');

			$params = array(
				'lang'                                     => array(
					'sending_request' => esc_attr__('Sending request...', PINLOADER_TEXT_DOMAIN),
				),
				'ajax_url'                                 => admin_url('admin-ajax.php'),
				'nonce'                                    => wp_create_nonce('sc-ajax-nonce'),
				'supplier_save_action'                     => 'supplier_save_request',
				'product_add_to_shop_action'               => 'product_add_to_shop_request',
				'product_ignore_action'                    => 'product_ignore_request',
				'products_add_to_shop_action'              => 'products_add_to_shop_request',
				'products_ignore_action'                   => 'products_ignore_request',
				'autocomplete_get_products_action'         => 'autocomplete_get_products_request',
				'change_product_nacenka_action'            => 'change_product_nacenka_request',
				'change_product_nacenka_all_manual_action' => 'change_product_nacenka_all_manual_request',
				'change_product_nacenka_all_auto_action'   => 'change_product_nacenka_all_auto_request',
				'switchon_one_action'                      => 'switchon_one_request',
				'switchon_all_manual_action'               => 'switchon_all_manual_request',
				'switchon_all_auto_action'                 => 'switchon_all_auto_request',
				'switchoff_one_action'                     => 'switchoff_one_request',
				'switchoff_all_action'                     => 'switchoff_all_request',
				'restore_one_action'                       => 'restore_one_request',
				'restore_all_action'                       => 'restore_all_request',
				'change_price_action'                      => 'change_price_request',
				'monitor_change_color_action'              => 'monitor_change_color_request',
				'group_products_action'                    => 'group_products_request',
				'ungroup_products_action'                  => 'ungroup_products_request',
				'get_ym_products_action'                   => 'get_ym_products_request',
				'update_ymf_prices_action'                 => 'update_ymf_prices_request',
				'update_images_alt_action'                 => 'update_images_alt_request',
			);
			wp_register_script('pinloader-scripts-local', '', array(), false, false);
			wp_localize_script('pinloader-scripts-local', 'globals', $params);
			wp_enqueue_script('pinloader-scripts-local');

			wp_enqueue_script('jquery-ui', PINLOADER_JS_URI.'/jquery-ui.min.js', array('jquery'), '1.12.1');
			wp_enqueue_script('bootstrap', PINLOADER_JS_URI.'/bootstrap.min.js');
			wp_enqueue_script('bootstrap-select', PINLOADER_JS_URI.'/bootstrap-select.min.js');
			wp_enqueue_script('pinloader-script', PINLOADER_JS_URI.'/backend.js');
			//wp_enqueue_script('pinloader-settings', PINLOADER_JS_URI.'/settings.js', array('jquery'));
			//wp_enqueue_script('cpa_custom_js', plugins_url( 'jquery.custom.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), '', true  );
		}
	}

	public function action_admin_menu(){
		/*$page = self::get_setup_section();

		switch($page){
			case "price-diff":
				$admin_submenu_order = array(0,1,2,6,3,4,5,7,8,9,10,11);
				break;
			default:
				$admin_submenu_order = self::$admin_submenu_order;
				break;
		}

		self::$admin_menu['submenu'] = self::order_submenu($admin_submenu_order, self::$admin_menu['submenu']);*/

		foreach(self::$admin_menu['mainmenu'] as $slug => $params){
			add_menu_page($params['title'], $params['title'], 'manage_options', $slug, array($this, 'display_admin_page'), 'dashicons-editor-table', 56);
		}

		foreach(self::$admin_menu['submenu'] as $slug => $params){
			add_submenu_page(WcPinLoader::$plugin_slug, $params['title'], $params['title'], 'manage_options', WcPinLoader::$menu_prefix.$slug, array($this, 'display_admin_page'));
		}
	}

	public static function order_submenu($orders, $submenus){
		$new_submenu = array();
		foreach($orders as $order){
			foreach($submenus as $slug => $submenu){
				if($submenu['order'] == $order){
					$new_submenu[$slug] = $submenu;
				}
			}
		}

		//WCPL_Helper::_debug($new_submenu);

		return $new_submenu;
	}

	private function menu_print_tabs(){
		$current_section = $this->get_setup_section();

		foreach(self::$admin_menu['submenu'] as $slug => $params){
			$display_tab = false;
			$active = $current_section === $slug ? 'nav-tab-active' : '';
			$url    = add_query_arg('page', WcPinLoader::$menu_prefix.$slug, '/wp-admin/admin.php');
			if($params['tab']){
				$display_tab = true;
			}
			if($display_tab){
				echo '<a class="nav-tab '.$active.'" href="'.esc_url($url).'">'.$params['title'].'</a>';
			}
		}
	}

	public function register_settings(){
		//If no options exist, create them.
		if(!get_option(WcPinLoader::$plugin_slug)){
			update_option(WcPinLoader::$plugin_slug, array(
				'pinloader_interval' => '5',
				'pinloader_update_yml_feed' => 0,
			));
		}

		register_setting('pinloader-options', WcPinLoader::$plugin_slug, array($this, 'validate_options'));
		$page = $this->get_setup_section();

		switch($page){
			case 'pinloader':
				add_settings_section(
					'settings_section',
					esc_attr__('Settings', PINLOADER_TEXT_DOMAIN),
					array(WCPL_Tools::class, 'inner_section_description'),
					WcPinLoader::$plugin_slug
				);

				add_settings_field(
					'pinloader_interval',
					esc_attr__('Cron upadte interval (minutes)', PINLOADER_TEXT_DOMAIN),
					array(WCPL_Tools::class, 'text_field'),
					WcPinLoader::$plugin_slug,
					'settings_section',
					array(
						'id'      => 'pinloader_interval',
						'page'    => WcPinLoader::$plugin_slug,
						'classes' => array('auto-text'),
						'type'    => 'text',
						'sub_desc'=> '',
						'desc'    => '3 hours = 180 minutes<br>6 hours = 360 minutes<br>12 hours = 720 minutes<br>24 hours = 1440 minutes<br>2 days = 2880 minutes<br>5 days = 7200 minutes<br>10 days = 14400 minutes',
					)
				);

				add_settings_field(
					'pinloader_update_yml_feed',
					esc_attr__('Yandex Market feed auto update via Cron', PINLOADER_TEXT_DOMAIN),
					array(WCPL_Tools::class, 'yesno2_field'),
					WcPinLoader::$plugin_slug,
					'settings_section',
					array(
						'id'      => 'pinloader_update_yml_feed',
						'page'    => WcPinLoader::$plugin_slug,
						'classes' => array(),
						'type'    => 'radio',
						'sub_desc'=> '',
						'desc'    => '',
					)
				);
				break;
		}
	}

	public function is_plugin_page(){
		if(isset($_REQUEST['page'])){
			$page = str_replace(WcPinLoader::$menu_prefix, '', strtolower($_REQUEST['page']));
			foreach(self::$admin_menu as $admin_menu){
				foreach($admin_menu as $slug => $mrnu){
					if($page == $slug){
						return $page;
						break;
					}
				}
			}
		}
		return false;
	}

	public static function get_setup_section(){
		if(isset($_REQUEST['page'])){
			return str_replace(WcPinLoader::$menu_prefix, '', strtolower($_REQUEST['page']));
		}
		return 'pinloader';
	}

	public function validate_options($input){
		WCPL_Helper::log('[function '.__FUNCTION__.'] is called');
		WCPL_Cron::stop();

		$output = array();

		if(isset($input['pinloader_timestamp'])){
			//$output['pinloader_timestamp'] = time();
		}

		// merge with current settings
		$output = array_merge(self::$options, $input, $output);

		return $output;
	}

	public function display_admin_page(){
		$page = $this->get_setup_section();

		echo '<div class="wrap pinloader">';
		/*if(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON){
			echo '<div id="setting-error" class="error settings-error notice">';
			echo '<p><strong>'.__('Attention! wp_cron on this site is disabled! For periodic data retrieval, this plugin will need wp_cron. Please delete the DISABLE_WP_CRON constant or set it to false. Usually, this constant is declared in the file /wp-config.php', PINLOADER_TEXT_DOMAIN).'</strong></p>';
			echo '</div>';
		}*/

		$page_title = isset(self::$admin_menu['submenu'][$page]['title']) ? self::$admin_menu['submenu'][$page]['title'] : self::$admin_menu['mainmenu'][$page]['title'];

		echo '<h1 class="pinloader-page-title">'.$page_title.'</h1>';
		settings_errors();
		echo '<h2 class="nav-tab-wrapper">';
		$this->menu_print_tabs();
		echo '</h2>';
		echo '<div class="fullbox-container">';
		echo '<div class="postbox">';
		echo '<div class="inside">';
		include PINLOADER_PLUGIN_DIR."/backend/".$page.".php";
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
	
	public static function set_option($key = '', $value = '', $update = false){
		if($key == '')
			return false;

		self::$options[$key] = $value;

		if($update){
			self::update_options();
		}
	}

	public static function update_options($options = array()){
		if(!empty($options)){
			self::$options = $options;
		}

		return update_option(WcPinLoader::$plugin_slug, self::$options);
	}

	public static function get_option($key = ''){
		$settings = self::get_options();

		return !empty($settings[$key]) ? $settings[$key] : false;
	}

	public static function get_options(){
		// Allow other plugins to get WcPinLoader's options.
		if(isset(self::$options) && is_array(self::$options) && !empty(self::$options)){
			return self::$options;
		}

		return get_option(WcPinLoader::$plugin_slug, array());
	}

	public function slug(){
		return WcPinLoader::$plugin_slug;
	}

}
