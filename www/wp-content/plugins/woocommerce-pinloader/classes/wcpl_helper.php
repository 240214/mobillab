<?php

namespace Pinloader;


class WCPL_Helper {

	public static $log_file = "wcpl_process.log";
	public static $cache_dir;
	public static $yandex_search_url = "http://market.yandex.ru/search.xml?text=";
	public static $product_edit_url = "/wp-admin/post.php?post={PID}&action=edit&classic-editor=1";
	public static $product_trash_url = "/wp-admin/edit.php?post_status=trash&post_type=product&s={TITLE}";

	function __construct(){

	}

	public static function initialise(){
		global $wpdb;
		$self = new self();

		add_action('init', array($self, 'init'), 0);
	}

	public function init(){
		if(is_admin()){
			self::create_cache_dir();
		}else{
			$upload_dir = wp_get_upload_dir();
			self::$cache_dir = $upload_dir['basedir'].'/'.PINLOADER_CACHE_DIR_NAME;
		}
	}

	public static function _dd($data = [], $show_for_users = false, $echo = true, $strip_tags = true){
		self::_debug($data, $show_for_users, $echo, $strip_tags);
		exit;
	}

	public static function _debug($data = [], $show_for_users = false, $echo = true, $strip_tags = true){
		if(current_user_can('manage_options') || $show_for_users){
			$count = count($data);
			if(is_array($data) || is_object($data)){
				$data = print_r($data, true);
			}
			$data = htmlspecialchars($data);
			if($strip_tags){
				$data = strip_tags($data);
			}
			if($echo){
				echo '<pre class="debug">Debug info:(', $count, ')<br>', $data, '</pre>';
			}else{
				return $data;
			}
		}
	}

	public static function log($content){
		$enter = chr(13).chr(10);
		$date  = date('Y-m-d H:i:s');

		if(is_array($content) || is_object($content)){
			$content = '['.$date.'] - '.print_r($content, true).$enter;
		}else{
			$content = '['.$date.'] - '.$content.$enter;
		}

		@chmod(PINLOADER_LOG_DIR.'/'.self::$log_file, 0666);
		file_put_contents(PINLOADER_LOG_DIR.'/'.self::$log_file, $content, FILE_APPEND);
	}

	public static function create_cache_dir(){
		$upload_dir = wp_get_upload_dir();
		self::$cache_dir = $upload_dir['basedir'].'/'.PINLOADER_CACHE_DIR_NAME;

		if(!is_dir(self::$cache_dir)){
			//self::_debug(self::$cache_dir);
			@mkdir(self::$cache_dir, 0777);
		}

		if(!file_exists(PINLOADER_LOG_DIR.'/'.self::$log_file)){
			//self::_debug(self::$cache_dir.'/'.self::$log_file);
			self::log('#START');
			@chmod(PINLOADER_LOG_DIR.'/'.self::$log_file, 0666);
		}
	}

	public static function set_cache_file($file, $data){
		#self::_debug(self::$cache_dir.'/'.$file.'.php');
		file_put_contents(self::$cache_dir.'/'.$file.'.php', '<?php return '.var_export($data, true).';');
	}

	public static function get_cache_file($file){
		#self::_debug(self::$cache_dir.'/'.$file.'.php');
		if(file_exists(self::$cache_dir.'/'.$file.'.php')){
			return include self::$cache_dir.'/'.$file.'.php';
		}else{
			return [];
		}
	}

	public static function get_cache_dir(){
		return self::$cache_dir;
	}

	public static function yandex_search_link($search_text){
		return self::$yandex_search_url.urlencode($search_text);
	}

	public static function product_edit_link($product_id){
		return str_replace('{PID}', $product_id, self::$product_edit_url);
	}

	public static function product_trash_link($product_title){
		return str_replace('{TITLE}', $product_title, self::$product_trash_url);
	}

	public static function generate_color($transparent = '0.5', $min = 100, $max = 240){
		$r = rand($min, $max);
		$g = rand($min, $max);
		$b = rand($min, $max);

		return "rgba($r, $g, $b, $transparent)";
	}

	public static function set_session_data(){

		if(isset($_GET['id_supplier'])){
			$_SESSION['get']['id_supplier'] = $_GET['id_supplier'];
		}elseif(isset($_POST['id_supplier'])){
			$_SESSION['get']['id_supplier'] = $_POST['id_supplier'];
		}

		if(!isset($_SESSION['get']['paged']) || !isset($_GET['paged'])){
			$_SESSION['get']['paged'] = 0;
		}else{
			if(isset($_GET['paged'])){
				$_SESSION['get']['paged'] = $_GET['paged'];
			}
		}

	}

	public static function get_file_ext($file_path){
		$base_name = basename($file_path);
		$a = explode('.', $base_name);
		$ext = end($a);

		return strtolower($ext);
	}

}
