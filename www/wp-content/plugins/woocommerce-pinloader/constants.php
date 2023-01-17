<?php
define('PINLOADER_PLUGIN__FILE__', __FILE__);
define('PINLOADER_PLUGIN_BASE', plugin_basename(PINLOADER_PLUGIN__FILE__));

define('PINLOADER_PLUGIN_DIR', untrailingslashit(plugin_dir_path(__FILE__)));
define('PINLOADER_PLUGIN_URL', untrailingslashit(plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__))));
define('PINLOADER_TEXT_DOMAIN', 'pinloader');

define('PINLOADER_LOG_DIR', ABSPATH.'wp-logs');
define('PINLOADER_CACHE_DIR_NAME', 'pinloader_cache');

define('PINLOADER_ASSETS_DIR', PINLOADER_PLUGIN_DIR.'/assets');
define('PINLOADER_ASSETS_URI', PINLOADER_PLUGIN_URL.'/assets');

define('PINLOADER_CSS_DIR', PINLOADER_ASSETS_DIR.'/css');
define('PINLOADER_CSS_URI', PINLOADER_ASSETS_URI.'/css');

define('PINLOADER_JS_DIR', PINLOADER_ASSETS_DIR.'/js');
define('PINLOADER_JS_URI', PINLOADER_ASSETS_URI.'/js');

define('PINLOADER_IMG_DIR', PINLOADER_ASSETS_DIR.'/img');
define('PINLOADER_IMG_URI', PINLOADER_ASSETS_URI.'/img');

define('PINLOADER_FONTS_DIR', PINLOADER_ASSETS_DIR.'/fonts');
define('PINLOADER_FONTS_URI', PINLOADER_ASSETS_URI.'/fonts');

define('PINLOADER_ICONS_DIR', PINLOADER_ASSETS_DIR.'/fontawesome');
define('PINLOADER_ICONS_URI', PINLOADER_ASSETS_URI.'/fontawesome');

define('PINLOADER_TEMPLATES_PATH', '/templates');
define('PINLOADER_PARTIALS_PATH', PINLOADER_TEMPLATES_PATH.'/partials');

