<?php
define('THEME_PARENT', get_template());
define('THEME_SHORT', get_stylesheet());
define('THEME_TD', 'electro-child');

define('PARENT_THEME_DIR', get_template_directory().'/');
define('PARENT_THEME_URI', get_template_directory_uri().'/');

define('THEME_DIR', get_stylesheet_directory().'/');
define('THEME_URI', get_stylesheet_directory_uri().'/');

define('LOG_DIR_NAME', ABSPATH.'wp-logs');
define('CACHE_DIR_NAME', THEME_SHORT.'_cache');

define('CONFIG_DIR', THEME_DIR.'config');

define('ASSETS_DIR', THEME_DIR.'assets');
define('ASSETS_URI', THEME_URI.'assets');

define('CSS_DIR', ASSETS_DIR.'/css');
define('CSS_URI', ASSETS_URI.'/css');

define('JS_DIR', ASSETS_DIR.'/js');
define('JS_URI', ASSETS_URI.'/js');

define('IMG_DIR', ASSETS_DIR.'/img');
define('IMG_URI', ASSETS_URI.'/img');

define('FONTS_DIR', ASSETS_DIR.'/fonts');
define('FONTS_URI', ASSETS_URI.'/fonts');

define('ICONS_DIR', ASSETS_DIR.'/fontawesome');
define('ICONS_URI', ASSETS_URI.'/fontawesome');

define('PARTIALS_PATH', '/templates/partials');
define('PAGE_TEMPLATES_PATH', '/templates/page/');
define('POST_TEMPLATES_PATH', '/templates/post/');
define('SHORTCODES_PATH', THEME_DIR.'/templates/shortcodes/');

