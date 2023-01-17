<?php

return array(
	'pinloader' => array(
		/*array(
			'menu_title' => __('Listings', PINLOADER_TEXT_DOMAIN),
			'page_title' => __('Listings', PINLOADER_TEXT_DOMAIN),
			'page_link_name' => '%POST_TYPE%',
			'function_name' => '',
		),*/
		array(
			'menu_title' => __('Settings', PINLOADER_TEXT_DOMAIN),
			'page_title' => __('Settings', PINLOADER_TEXT_DOMAIN),
			'page_link_name' => '%POST_TYPE%-settings',
			'function_name' => 'listings_settings_page',
		),
	),
);
