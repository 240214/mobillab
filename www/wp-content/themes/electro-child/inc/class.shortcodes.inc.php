<?php

namespace Digidez;

class Shortcodes{

	/**
	 * Global varibls for templates
	 */

	public static function initialise(){
		$self = new self();

		add_shortcode('main_banner', [$self, 'main_banner']);
	}

	public static function main_banner($atts, $content = ''){
		// setup options
		#\Digidez\Functions::_debug($content);

		$theme_option_homepage_banner = get_field('theme_option_homepage_banner', 'option');

		$atts = shortcode_atts([
			'title' => $theme_option_homepage_banner['title'],
			'content' => $theme_option_homepage_banner['content'],
			'button_text' => $theme_option_homepage_banner['button_text'],
			'button_link' => $theme_option_homepage_banner['button_link'],
			'image' => $theme_option_homepage_banner['image'],
			'bg_image' => $theme_option_homepage_banner['background_image'],
		], $atts);

		$template_file = SHORTCODES_PATH.'/main_banner.php';

		ob_start();
		include($template_file);
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

}
