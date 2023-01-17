<?php

/**
 * Module Name            : Visual Composer Addons
 * Module Description    : Provides additional Visual Composer Elements for the Electro theme
 */

// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

class Theme_JS_Composer{
	
	protected $shortcode;
	
	/**
	 * Constructor function.
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function __construct(){
		add_action('init', array($this, 'includes'), 10);
	}
	
	/**
	 * Include required files
	 * @access public
	 * @return void
	 * @since  1.0.0
	 */
	public function includes(){
		
		#-----------------------------------------------------------------
		# Shortcodes
		#-----------------------------------------------------------------
		require_once THEME_DIR.'inc/elements/electro_empty_space.php';

		require_once THEME_DIR.'inc/elements/map.php';
	}
	
	public function getShortcode() {
		return $this->shortcode;
	}
	
	public function getExtraClass( $el_class ) {
		$output = '';
		if ( '' !== $el_class ) {
			$output = ' ' . str_replace( '.', '', $el_class );
		}
		
		return $output;
	}
	
}

// Finally initialize code
new Theme_JS_Composer();
