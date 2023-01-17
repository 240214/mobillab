<?php

return array(
	'pinloader_metas' => array(
		'id'       => 'pinloader_metas',
		'title'    => __('WcPinLoader Metas', PINLOADER_TEXT_DOMAIN),
		'object_types'    => array('pinloader'),
		'context'  => 'normal',
		'priority' => 'high',
		'fields'   => array(
			/*'image'        => array(
				'name'       => __('Image', PINLOADER_TEXT_DOMAIN),
				'id'         => '_image',
				'type'       => 'file',
				//'preview_size' => array(150, 150),
				'sortable'   => false,
				'desc'       => false,
				'dashboard'  => false,
				'priority'   => 0
			),*/
			'image_url'  => array(
				'name'       => __('Image URL', PINLOADER_TEXT_DOMAIN),
				'id'         => '_image_url',
				'type'       => 'text_url',
				'sortable'   => false,
				'desc'       => false,
				'dashboard'  => false,
				'priority'   => 0,
				'protocols' => array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet' ), // Array of allowed protocols
			),
			'target_url'  => array(
				'name'       => __('Target URL', PINLOADER_TEXT_DOMAIN),
				'id'         => '_target_url',
				'type'       => 'text_url',
				'sortable'   => false,
				'desc'       => false,
				'dashboard'  => false,
				'priority'   => 0,
				'protocols' => array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet' ), // Array of allowed protocols
			),
		),
	),

);
