<?php
namespace Pinloader;

class WCPL_View {

	public $ads = null;
	public $template_params;
	public $gen_id;

	public static function initialise(){
		$self = new self();

		add_action('init', array($self, 'init'), 0);
		add_action('wp_enqueue_scripts', array($self, 'enqueue_styles'), 10);
		add_action('wp_enqueue_scripts', array($self, 'enqueue_scripts'), 20);
	}

	public function init(){
	}

	public function enqueue_styles($script_version = ''){
		if(!is_admin()){
			wp_enqueue_style('wcpl-frontend', PINLOADER_CSS_URI.'/frontend'.$script_version.'.css', false, '1.0.0');
		}
	}

	public function enqueue_scripts($script_version = ''){
		if(!is_admin()){
			wp_enqueue_script('na-frontend-js', PINLOADER_JS_URI.'/frontend'.$script_version.'.js', array('jquery'), false, true);
		}
	}

	public function get_post_link($post){
		//$link = urlencode($post->target);
		$link = $post->target;
		return $link;
	}

}