<?php
/*
Plugin Name: WooCommerce PinLoader
Plugin URI: http://digidez.com/
Description: Price loader for Woocommerce
Author: Armen Khojoyan
Author URI: http://www.digidez.com
Version: 1.0.0
Text Domain: pinloader
License: GPLv2 or later
*/

namespace Pinloader;


if(!defined('ABSPATH')) exit; // Exit if accessed directly



final class WcPinLoader {

	private static $_instance = null;
	public static $plugin_name = "Pinloader";
	public static $plugin_slug = "pinloader";
	public static $menu_prefix = "wcpl-";

	protected function __construct(){
		session_start();
		// Activate plugin when new blog is added
		add_action('wpmu_new_blog', array($this, 'activate_new_site'));

	}

	public function __clone(){
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', PINLOADER_TEXT_DOMAIN ), '1.0.0' );
	}

	public function __wakeup(){
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', PINLOADER_TEXT_DOMAIN ), '1.0.0' );
	}

	public static function instance(){
		if(is_null(self::$_instance))
			self::$_instance = new WcPinLoader();
		return self::$_instance;
	}

	public function init(){
		$this->define_constants();
		register_activation_hook(PINLOADER_PLUGIN__FILE__, array($this, 'activate'));
		register_deactivation_hook(PINLOADER_PLUGIN__FILE__, array($this, 'deactivate'));
		add_action('plugins_loaded', array($this, 'on_plugins_loaded'));
	}

	protected function define($name, $value){
		if(!defined($name)){
			define($name, $value);
		}
	}

	public static function activate($network_wide){
		if(function_exists('is_multisite') && is_multisite()){
			if($network_wide){
				// Get all blog ids
				$blog_ids = WCPL_Core::get_blog_ids();

				foreach($blog_ids as $blog_id){
					switch_to_blog($blog_id);
					self::single_activate();
				}

				restore_current_blog();
			}else{
				self::single_activate();
			}
		}else{
			self::single_activate();
		}
	}

	public static function deactivate($network_wide){
		if(function_exists('is_multisite')&& is_multisite()){
			if($network_wide){
				// Get all blog ids
				$blog_ids = WCPL_Core::get_blog_ids();

				foreach($blog_ids as $blog_id){
					switch_to_blog($blog_id);
					self::single_deactivate();
				}

				restore_current_blog();
			}else{
				self::single_deactivate();
			}
		}else{
			self::single_deactivate();
		}
	}

	public function activate_new_site($blog_id){
		if(1 !== did_action('wpmu_new_blog')) return;

		switch_to_blog($blog_id);
		self::single_activate();
		restore_current_blog();
	}

	private static function single_activate(){
		require_once('classes/wcpl_core.php');
		WCPL_Core::create_tables();
	}

	private static function single_deactivate(){
		WCPL_Core::destroy();
	}

	protected function define_constants(){
		$this->define('PINLOADER_PLUGIN__FILE__', __FILE__);
		$this->define('PINLOADER_PLUGIN_BASE', plugin_basename(__FILE__));
		$this->define('PINLOADER_PLUGIN_DIR_SHORT', basename(dirname(__FILE__)));
		$this->define('PINLOADER_PLUGIN_DIR', untrailingslashit(plugin_dir_path(__FILE__)));
		$this->define('PINLOADER_PLUGIN_URL', untrailingslashit(plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__))));
		$this->define('PINLOADER_TEXT_DOMAIN', 'pinloader');

		$this->define('PINLOADER_LOG_DIR', ABSPATH.'wp-logs');
		$this->define('PINLOADER_CACHE_DIR_NAME', 'pinloader_cache');

		$this->define('PINLOADER_ASSETS_DIR', PINLOADER_PLUGIN_DIR.'/assets');
		$this->define('PINLOADER_ASSETS_URI', PINLOADER_PLUGIN_URL.'/assets');

		$this->define('PINLOADER_CSS_DIR', PINLOADER_ASSETS_DIR.'/css');
		$this->define('PINLOADER_CSS_URI', PINLOADER_ASSETS_URI.'/css');

		$this->define('PINLOADER_JS_DIR', PINLOADER_ASSETS_DIR.'/js');
		$this->define('PINLOADER_JS_URI', PINLOADER_ASSETS_URI.'/js');

		$this->define('PINLOADER_IMG_DIR', PINLOADER_ASSETS_DIR.'/img');
		$this->define('PINLOADER_IMG_URI', PINLOADER_ASSETS_URI.'/img');

		$this->define('PINLOADER_FONTS_DIR', PINLOADER_ASSETS_DIR.'/fonts');
		$this->define('PINLOADER_FONTS_URI', PINLOADER_ASSETS_URI.'/fonts');

		$this->define('PINLOADER_ICONS_DIR', PINLOADER_ASSETS_DIR.'/fontawesome');
		$this->define('PINLOADER_ICONS_URI', PINLOADER_ASSETS_URI.'/fontawesome');

		$this->define('PINLOADER_TEMPLATES_PATH', '/templates');
		$this->define('PINLOADER_PARTIALS_PATH', PINLOADER_TEMPLATES_PATH.'/partials');

		#$this->define('PINLOADER_STOP_CRON', false);
	}

	public function includes() {
		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		//require_once('vendor/autoload.php');
		require_once('classes/wcpl_helper.php');
		require_once('classes/wcpl_paginate_navigation_builder.php');
		require_once('classes/wcpl_core.php');
		//require_once('classes/cmb2/init.php');
		require_once('classes/wcpl_tools.php');
		require_once('classes/wcpl_admin.php');
		//require_once('classes/wcpl_api_ui.php');
		require_once('classes/wcpl_posttypes.php');
		require_once('classes/wcpl_taxonomies.php');
		require_once('classes/wcpl_products.php');
		require_once('classes/wcpl_data_source.php');
		require_once('classes/wcpl_yandex_market.php');
		//require_once('classes/wcpl_geo.php');
		//require_once('classes/wcpl_shortcodes.php');
		//require_once('classes/wcpl_view.php');
		require_once('classes/wcpl_cron.php');

		$section = WCPL_Admin::get_setup_section();

		if($section == 'price-upload' && !empty($_FILES)){
			require_once('inc/excel_reader2.php');
		}

		if(is_admin()){
			require_once('inc/simplehtmldom/simple_html_dom.php');
		}
	}

	public function on_plugins_loaded(){
		$this->includes();

		WCPL_Helper::initialise();
		WCPL_Core::initialise();
		WCPL_Tools::initialise();
		WCPL_Admin::initialise();
		WCPL_PostTypes::initialise();
		WCPL_Taxonomies::initialise();
		WCPL_Products::initialise();
		WCPL_Data_Source::initialise();
		WCPL_Yandex_Market::initialise();
		//WCPL_View::initialise();
		WCPL_Cron::initialise();

		WCPL_Core::load_textdomain();
	}

}

WcPinLoader::instance()->init();