<?php

namespace Pinloader;

use Exception;

class WCPL_API_UI {

	public $options;
	public $slug = 'pinloader';
	private $errors;
	private $api_url;

	public function __construct(){
		add_action('init', array($this, 'init'));
		add_action('wp_ajax_test_api_request', array($this, 'ajax_test_api_request'));
	}

	public function init() {
		$this->slug = WcPinLoader::instance()->plugin_slug;
		$this->options = WcPinLoader::instance()->settings->get_options();
		//WcPinLoader::instance()->_debug($this->options);

		$this->build_api_url();
	}

	private function setError($key, $error){
		$this->errors[$key][] = $error;
	}

	private function build_api_url(){
		$pto = $this->options['pinloader_pub_id'];
		$pid = $this->options['pinloader_site_id'];
		$this->api_url = $this->options['pinloader_api_url'].'?pid='.$pid.'&pto='.$pto;
	}

	public function ajax_test_api_request(){
		$return = array('error' => 0, 'message' => 'Test OK! All settings have been saved. You can customize widgets.', 'result' => '');
		$post_data = array();
		parse_str($_POST['form_data'], $post_data);

		if(!isset($post_data['pinloader']['pinloader_site_id']) || empty($post_data['pinloader']['pinloader_site_id'])){
			$return['error'] = 1;
			$return['message'] = __('Website ID is not set. Please, enter your website ID and try again.', PINLOADER_TEXT_DOMAIN);
		}
		if(!isset($post_data['pinloader']['pinloader_pub_id']) || empty($post_data['pinloader']['pinloader_pub_id'])){
			$return['error'] = 1;
			$return['message'] = __('API token is not set. Please, enter your API token and try again.', PINLOADER_TEXT_DOMAIN);
		}

		if($return['error'] == 0){
			$this->options['pinloader_pub_id'] = $post_data['pinloader']['pinloader_pub_id'];
			$this->options['pinloader_site_id'] = $post_data['pinloader']['pinloader_site_id'];
			$this->build_api_url();
			$return['result'] = $this->run(false);
		}

		die(json_encode($return));
	}

	public function run($download_images = true){
		$responce = $this->get_remote_data();
		//$this->save_to_cache($responce);
		if($responce['meta']['success'] == 'true'){
			$this->options['pinloader_interval'] = $responce['meta']['interval'];
			$this->options['pinloader_validity'] = $responce['meta']['validity'];
			$this->options['pinloader_timestamp'] = $responce['meta']['timestamp'];
			$this->options['pinloader_init_scope'] = $this->update_asstets_files($responce['meta']['init_scope']);
			//WcPinLoader::instance()->log($this->options);
			if(!class_exists('WcPinLoader_Settings')){
				include_once PINLOADER_PLUGIN_DIR.'/classes/class-settings.php';
				$s = new WCPL_Settings();
				$s->update_options($this->options);
				unset($s);
			}else{
				WcPinLoader::instance()->settings->update_options($this->options);
			}

			if(!class_exists('WcPinLoader_Posts')){
				include_once PINLOADER_PLUGIN_DIR.'/classes/class-posts.php';
				$p = new WCPL_Posts();
				$p->update_posts($responce['data'], $download_images);
				unset($p);
			}else{
				WcPinLoader::instance()->posts->update_posts($responce['data'], $download_images);
			}
		}

		return $responce['meta']['result'];
	}

	private function get_remote_data(){
		$ret = false;
		$url = $this->api_url;
		switch($this->options['pinloader_getdata_method']){
			case 'file':
				$ret = $this->_get_remote_data_via_file($url);
				if($ret === false){
					//$this->options['pinloader_getdata_method'] = 'curl';
					//WcPinLoader::instance()->settings->update_options($this->options);
					//$this->get_remote_data();
				}
				break;
			case 'curl':
				$ret = $this->_get_remote_data_via_curl($url);
				if($ret === false){
					//$this->options['pinloader_getdata_method'] = 'file';
					//WcPinLoader::instance()->settings->update_options($this->options);
					//$this->get_remote_data();
				}
				break;
		}
		return $ret;
	}

	private function _get_remote_data_via_file($url){
		$ret = false;
		try{
			if($result_json = file_get_contents($url)){
				if(!empty($result_json)){
					if($result_arr = json_decode($result_json, true)){
						$ret = $result_arr;
					}else{
						$this->setError(__FUNCTION__, 'Decode JSON result error');
					}
				}
			}
		}catch(Exception $e){
			echo 'An exception has been thrown: ',  $e->getMessage(), "\n";
		}

		return $ret;
	}

	private function _get_remote_data_via_curl($url){
		$ret = false;
		if($ch = curl_init($url)){
			curl_setopt($ch , CURLOPT_RETURNTRANSFER, true);
			if($result_json = curl_exec($ch)){
				if($result_arr = json_decode($result_json, true)){
					$ret = $result_arr;
				}else{
					$this->setError(__FUNCTION__, 'Decode JSON result error');
				}
			}else{
				$this->setError(__FUNCTION__, 'cURL query error');
			}
			curl_close($ch);
		}else{
			$this->setError(__FUNCTION__, 'Init cURL error');
		}
		return $ret;
	}

	private function save_to_cache($data){
		file_put_contents(WcPinLoader::instance()->cache_dir.'/'.time().'.json.php', '<? return '.var_export($data, true).'; ?>');
	}

	private function update_asstets_files($url){
		$assets_dir = WcPinLoader::instance()->assets_dir;
		$file_basename = basename($url);
		$saved_file = $assets_dir.'/'.$file_basename;
		$result = file_put_contents($saved_file, file_get_contents($url));
		if($result !== false){
			return $saved_file;
		}else{
			return $url;
		}
	}
}
