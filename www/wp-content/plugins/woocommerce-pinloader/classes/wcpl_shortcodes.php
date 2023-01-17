<?php
namespace Pinloader;

class WCPL_Shortcodes {

	private $errors;
	private $options;
	private $p;
	public $post_slug;

	public function __construct() {
		add_action('init', array($this, 'init'));
		add_shortcode('chameleon', array($this, 'shortcode_chameleon'));
    }

	public function init(){
		$this->p = WcPinLoader::instance()->posts;
		$this->options = WcPinLoader::instance()->settings->options;
	}

    public function enqueue_scripts($type = 'main', $script_version = ''){
	    switch($type){
		    case "main":
				wp_enqueue_style('na-frontend', PINLOADER_PLUGIN_URL.'/assets/css/frontend'.$script_version.'.css', array(), false, false);
				wp_enqueue_script('na-frontend-js', PINLOADER_PLUGIN_URL.'/assets/js/frontend'.$script_version.'.js', array(), false, false);
				break;
	    }
    }

	public function shortcode_chameleon($atts){
		$html = "";

		//$this->enqueue_scripts('main');
		if(isset($atts['cats'])){
			$atts['cats'] = explode(',', $atts['cats']);
		}
		$params = shortcode_atts(array(
			'id' => 0,
			'cats' => array('*'),
		), $atts);
		//WcPinLoader::instance()->_debug($params);

		if(intval($params['id']) > 0){
			$html = WcPinLoader::instance()->widgets_api->get_native_ads($params);
		}

		return $html;
	}

	public function format_shortcode_attrs($attr, $to_format = 'trim_string'){
		switch($to_format){
			case "trim_string":
				$attr = explode(',', $attr);
				$attr = array_map('trim', $attr);
				$attr = implode(',', $attr);
				break;
			case "trim_string_replace_spacing":
				$attr = str_replace(' ', '+', $attr);
				$attr = explode(',', $attr);
				$attr = array_map('trim', $attr);
				$attr = implode(',', $attr);
				break;
			case "string_to_array":
				$attr = explode(',', $attr);
				$attr = array_map('trim', $attr);
				break;
			case "array_to_string":
				$attr = implode(',', $attr);
				break;
		}
		return $attr;
	}

	private function setError($key, $error){
		$this->errors[$key][] = $error;
	}

}
