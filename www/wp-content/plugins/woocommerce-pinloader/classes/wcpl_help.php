<?php
namespace Pinloader;

class WCPL_Help {

	var $dir;
	var $local;
	var $plugin_slug = "pinloader";

    public function __construct(){
	    $domain = $this->plugin_slug;
	    $this->dir = plugin_dir_path(__DIR__);
	    $this->locale = apply_filters('plugin_locale', get_locale(), $domain);

	    add_action('in_admin_header', array($this, 'contextual_help'));
    }

    public function contextual_help() {
        $screen = get_current_screen();
        //WcPinLoader::instance()->_debug($screen);

        switch ( $screen->id ){
	        case 'settings_page_pinloader':
		        $screen->add_help_tab(array(
			        'title'   => __('Shortcode', PINLOADER_TEXT_DOMAIN),
			        'id'      => 'pinloader-overview',
			        'content' => file_get_contents($this->dir.'help_content/languages/'.$this->locale.'/shortcode.html'),
		        ));
		        break;
        }
    }
}
