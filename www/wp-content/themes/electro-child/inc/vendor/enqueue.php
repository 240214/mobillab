<?php
/**
 * Understrap enqueue scripts
 *
 * @package understrap
 */

if ( ! function_exists( 'understrap_scripts' ) ) {
	/**
	 * Load theme's JavaScript sources.
	 */
	function understrap_scripts() {
		// Get the theme data.
		$the_theme = wp_get_theme();
		wp_enqueue_style( 'understrap-styles', get_stylesheet_directory_uri() . '/assets/css/vendor/theme.min.css', array(), $the_theme->get( 'Version' ) );

		wp_enqueue_script( 'jquery');
		wp_enqueue_script( 'popper-scripts', get_template_directory_uri() . '/assets/js/vendor/popper.min.js', array(), true);
		wp_enqueue_script( 'understrap-scripts', get_template_directory_uri() . '/assets/js/vendor/theme.min.js', array(), $the_theme->get( 'Version' ), true );
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

	}
} // endif function_exists( 'understrap_scripts' ).

add_action( 'wp_enqueue_scripts', 'understrap_scripts' );

