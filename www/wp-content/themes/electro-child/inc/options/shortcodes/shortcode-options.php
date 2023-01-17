<?php
$font_sizes = array();
for($i = 8; $i <= 100; $i++){
	if($i > 30){
		$i++;
	}
	$font_sizes["fs".$i] = __($i, THEME_TD);
}

return array(
	// SECTION
	'svg_inline'       => array(
		'shortcode'   => 'svg_inline',
		'title'       => __('SVG inline', THEME_TD),
		'desc'        => __('SVG inline by Armen', THEME_TD),
		'insert_with' => 'dialog',
		'has_content' => false,
		'sections'    => array(
			array(
				'title'  => __('Extra Options', THEME_TD),
				'fields' => array(
					array(
						'name'    => __('Image Source', THEME_TD),
						'id'      => 'image',
						'type'    => 'upload',
						'store'   => 'id',
						'default' => '',
						'desc'    => __('Place the source path of the image here', THEME_TD),
					),
					/*
					array(
						'name'    => __('Image size', THEME_TD),
						'desc'    => __('Choose the size for the image', THEME_TD),
						'id'      => 'image_size',
						'type'    => 'select',
						'default' => 'full',
						'admin_label' => true,
						'options' => array(
							'thumbnail' => __('Thumbnail', THEME_TD),
							'medium'    => __('Medium', THEME_TD),
							'large'     => __('Large', THEME_TD),
							'full'      => __('Full', THEME_TD)
						),
					),
					array(
						'name'    => __('Custom Image Size', THEME_TD),
						'id'      => 'custom_image_size',
						'type'    => 'text',
						'default' => '',
						'desc'    => __('Enter size in pixels (Example: 400x400 (Width x Height)). Leave empty to use Image size option above by default.', THEME_TD),
					),
					array(
						'name'    => __('Custom Image Size Crop', THEME_TD),
						'id'      => 'custom_image_size_crop',
						'type'    => 'select',
						'default' => 'false',
						'options' => array(
							'true'  => __('Crop', THEME_TD),
							'false' => __('Keep aspect ratio', THEME_TD),
						),
						'desc'    => __('Crop image to the exact dimensions set in the Custom Image Size option or keep original aspect ratio.', THEME_TD),
					),
					array(
						'name'    => __('Link Type', THEME_TD),
						'desc'    => __('Select the link type to use for the item.', THEME_TD),
						'id'      => 'link_type',
						'type'    => 'select',
						'default' => 'magnific',
						'options' => array(
							'magnific' => __('Magnific', THEME_TD),
							'item'     => __('Link', THEME_TD),
							'no-link'  => __('No Link ', THEME_TD),
						),
					),
					array(
						'name'    => __('Hover Effect', THEME_TD),
						'desc'    => __('Select an effect to add when you hover over the image.', THEME_TD),
						'id'      => 'hover_effect',
						'type'    => 'select',
						'default' => '',
						'options' => array(
							''                    => __('No Effect', THEME_TD),
							'image-effect-zoom-in'  => __('Zoom In', THEME_TD),
							'image-effect-zoom-out' => __('Zoom Out', THEME_TD),
							'image-effect-scroll-left'  => __('Scroll Left', THEME_TD),
							'image-effect-scroll-right' => __('Scroll Right', THEME_TD)
						),
					),
					array(
						'name'    => __('Link', THEME_TD),
						'id'      => 'link',
						'type'    => 'text',
						'default' => '',
						'desc'    => __('Link that the item will link leave blank to link to original image source.', THEME_TD),
					),
					array(
						'name'    => __('Open Link In', THEME_TD),
						'id'      => 'link_target',
						'type'    => 'select',
						'default' => '_self',
						'options' => array(
							'_self'   => __('Same page as it was clicked ', THEME_TD),
							'_blank'  => __('Open in new window/tab', THEME_TD),
						),
						'desc'    => __('Where the link will open.', THEME_TD),
					),
					array(
						'name'    => __('Image Alt', THEME_TD),
						'id'      => 'alt',
						'type'    => 'text',
						'default' => '',
						'desc'    => __('Place the alt of the image here', THEME_TD),
					)
					*/
				)
			),
			array(
				'title'  => __('Extra Options', THEME_TD),
				'fields' => include OXY_THEME_DIR.'inc/options/shortcodes/shared/global.php'
			)
		)
	),
	'breadcrumbs'      => array(
		'shortcode'   => 'breadcrumbs',
		'title'       => __('Breadcrumbs', THEME_TD),
		'desc'        => __('Breadcrumbs by Armen', THEME_TD),
		'insert_with' => 'dialog',
		'has_content' => true,
		'sections'    => array(
			array(
				'title'  => __('Breadcrumbs', THEME_TD),
				'fields' => array(
					array(
						'name'    => __('No options', THEME_TD),
						'desc'    => __('Click to button "Save changes"', THEME_TD),
						'id'      => 'no_options',
						'type'    => 'label',
						'default' => '',
					),
				),

			)
		)
	),
	'related_services' => array(
		'shortcode'   => 'related_services',
		'title'       => __('Rlated Services', THEME_TD),
		'desc'        => __('Displays a horizontal / vertical list of services.', THEME_TD),
		'insert_with' => 'dialog',
		'has_content' => false,
		'sections'    => array(
			array(
				'title'  => __('Services', THEME_TD),
				'fields' => array(
					array(
						'name'        => __('Choose a category', THEME_TD),
						'desc'        => __('Category of services to show', THEME_TD),
						'id'          => 'category',
						'default'     => '',
						'admin_label' => true,
						'type'        => 'select',
						'options'     => 'taxonomy',
						'taxonomy'    => 'oxy_service_category',
						'blank_label' => __('All Categories', THEME_TD)
					),
					array(
						'name'        => __('Services Count', THEME_TD),
						'desc'        => __('Number of services to show(set to 0 to show all)', THEME_TD),
						'id'          => 'count',
						'type'        => 'slider',
						'default'     => '3',
						'admin_label' => true,
						'attr'        => array(
							'max'  => 30,
							'min'  => 0,
							'step' => 1
						)
					),
					array(
						'name'    => __('Columns (horizontal style)', THEME_TD),
						'desc'    => __('Number of columns to show the services in', THEME_TD),
						'id'      => 'columns',
						'type'    => 'select',
						'options' => array(
							2 => __('Two columns', THEME_TD),
							3 => __('Three columns', THEME_TD),
							4 => __('Four columns', THEME_TD),
							6 => __('Six columns', THEME_TD),
						),
						'default' => '3',
					)
				)
			),
			array(
				'title'  => __('Text', THEME_TD),
				'fields' => include OXY_THEME_DIR.'inc/options/shortcodes/shared/text-color.php'
			),
			array(
				'title'  => __('Service Item Options', THEME_TD),
				'fields' => include OXY_THEME_DIR.'inc/options/shortcodes/shared/service.php'
			),
			array(
				'title'  => __('Extra Options', THEME_TD),
				'fields' => array_merge(include OXY_THEME_DIR.'inc/options/shortcodes/shared/global.php', array(
					array(
						'name'    => __('Animation Timing', THEME_TD),
						'desc'    => __('Will animate all services at once or each one individually .', THEME_TD),
						'id'      => 'scroll_animation_timing',
						'type'    => 'select',
						'default' => 'staggered',
						'options' => array(
							'all-same'  => __('All items appear at same time', THEME_TD),
							'staggered' => __('Staggered over Animation Delay', THEME_TD),
						),
					)
				))
			)
		)
	),
	'services_list'    => array(
		'shortcode'   => 'services_list',
		'title'       => __('Services List', THEME_TD),
		'desc'        => __('Displays a horizontal / vertical list of services.', THEME_TD),
		'insert_with' => 'dialog',
		'has_content' => false,
		'sections'    => array(
			array(
				'title'  => __('Services', THEME_TD),
				'fields' => array(
					array(
						'name'        => __('Choose a category', THEME_TD),
						'desc'        => __('Category of services to show', THEME_TD),
						'id'          => 'category',
						'default'     => '',
						'admin_label' => true,
						'type'        => 'select',
						'options'     => 'taxonomy',
						'taxonomy'    => 'oxy_service_category',
						'blank_label' => __('All Categories', THEME_TD)
					),
					array(
						'name'        => __('Services Count', THEME_TD),
						'desc'        => __('Number of services to show(set to 0 to show all)', THEME_TD),
						'id'          => 'count',
						'type'        => 'slider',
						'default'     => '3',
						'admin_label' => true,
						'attr'        => array(
							'max'  => 30,
							'min'  => 0,
							'step' => 1
						)
					),
					array(
						'name'    => __('Columns (horizontal style)', THEME_TD),
						'desc'    => __('Number of columns to show the services in', THEME_TD),
						'id'      => 'columns',
						'type'    => 'select',
						'options' => array(
							1 => __('One column', THEME_TD),
							2 => __('Two columns', THEME_TD),
							3 => __('Three columns', THEME_TD),
							//4 => __('Four columns', THEME_TD),
							//6 => __('Six columns', THEME_TD),
						),
						'default' => '1',
					)
				)
			),
			array(
				'title'  => __('Text', THEME_TD),
				'fields' => include OXY_THEME_DIR.'inc/options/shortcodes/shared/text-color.php'
			),
			array(
				'title'  => __('Service Item Options', THEME_TD),
				'fields' => include OXY_THEME_DIR.'inc/options/shortcodes/shared/service.php'
			),
			array(
				'title'  => __('Extra Options', THEME_TD),
				'fields' => array_merge(include OXY_THEME_DIR.'inc/options/shortcodes/shared/global.php', array(
					array(
						'name'    => __('Animation Timing', THEME_TD),
						'desc'    => __('Will animate all services at once or each one individually .', THEME_TD),
						'id'      => 'scroll_animation_timing',
						'type'    => 'select',
						'default' => 'staggered',
						'options' => array(
							'all-same'  => __('All items appear at same time', THEME_TD),
							'staggered' => __('Staggered over Animation Delay', THEME_TD),
						),
					)
				))
			)
		)
	),
	'map_new'          => array(
		'shortcode'   => 'map_new',
		'title'       => __('Google Map New', THEME_TD),
		'desc'        => __('Adds a Google Map to the page.', THEME_TD),
		'insert_with' => 'dialog',
		'has_content' => false,
		'sections'    => array(
			array(
				'title'  => __('Map', THEME_TD),
				'fields' => array(
					array(
						'name'    => __('Map ID', THEME_TD),
						'desc'    => __('Enter the map container ID', THEME_TD),
						'id'      => 'map_id',
						'default' => '',
						'type'    => 'text',
					),
					array(
						'name'    => __('Map Type', THEME_TD),
						'id'      => 'map_type',
						'desc'    => __('Choose a type of map to show from Google Maps.', THEME_TD),
						'type'    => 'select',
						'default' => 'ROADMAP',
						'options' => array(
							'ROADMAP'   => __('Roadmap', THEME_TD),
							'SATELLITE' => __('Satellite', THEME_TD),
							'TERRAIN'   => __('Terrain', THEME_TD),
							'HYBRID'    => __('Hybrid', THEME_TD),
						),
					),
					array(
						'name'    => __('Map Style', THEME_TD),
						'id'      => 'map_style',
						'desc'    => __('Set a drawing style for the map.', THEME_TD),
						'type'    => 'select',
						'default' => 'regular',
						'options' => array(
							'blackwhite' => __('Black & White', THEME_TD),
							'regular'    => __('Regular', THEME_TD),
						),
					),
					array(
						'name'    => __('Center Map', THEME_TD),
						'id'      => 'auto_center',
						'type'    => 'select',
						'default' => 'auto',
						'desc'    => __('Sets the center the map automatically based on the markers, or manually.', THEME_TD),
						'options' => array(
							'auto'   => __('Auto center markers ', THEME_TD),
							'manual' => __('I will tell you where to center map below', THEME_TD),
						),
					),
					array(
						'name'    => __('Center Map Lat/Lng', THEME_TD),
						'desc'    => __('Latitude and Longitude position to center the Map (separate with lat and long with commas).', THEME_TD),
						'id'      => 'center_latlng',
						'default' => '',
						'type'    => 'text',
					),
					array(
						'name'    => __('Map Zoom', THEME_TD),
						'id'      => 'map_zoom',
						'desc'    => __('Sets the zoom level of the map.  NOTE - will be overridden by the auto center map option', THEME_TD),
						'type'    => 'slider',
						'default' => '15',
						'attr'    => array(
							'max'  => 20,
							'min'  => 1,
							'step' => 1
						)
					),
					array(
						'name'    => __('Map Scrollable', THEME_TD),
						'id'      => 'map_scrollable',
						'desc'    => __('Toggles draggable scrolling of the map.', THEME_TD),
						'type'    => 'select',
						'default' => 'on',
						'options' => array(
							'on'  => __('On', THEME_TD),
							'off' => __('Off', THEME_TD),
						),
					),
				)
			),
			array(
				'title'  => __('Marker', THEME_TD),
				'fields' => array(
					array(
						'name'    => __('Marker Image', THEME_TD),
						'desc'    => __('Set the url of a custom marker image.', THEME_TD),
						'id'      => 'marker_link',
						'type'    => 'text',
						'default' => '',
					),
					array(
						'name'    => __('Show Markers', THEME_TD),
						'id'      => 'marker',
						'type'    => 'select',
						'default' => 'show',
						'desc'    => __('Toggle showing or hiding the small marker points on your map.', THEME_TD),
						'options' => array(
							'hide' => __('Hide', THEME_TD),
							'show' => __('Show', THEME_TD),
						),
					),
					array(
						'name'    => __('Marker Labels', THEME_TD),
						'desc'    => __('Labels to show above the marker. Divide labels with pipe character |', THEME_TD),
						'id'      => 'label',
						'default' => '',
						'type'    => 'textarea',
					),
					array(
						'name'    => __('Marker Addresses', THEME_TD),
						'desc'    => __('Addresses to show markers. Divide addresses with pipe character |', THEME_TD),
						'id'      => 'address',
						'default' => '',
						'type'    => 'textarea',
					),
					array(
						'name'    => __('Markers Lat/Lng', THEME_TD),
						'desc'    => __('Latitude and Longitude of markers(separate with commas), if you dont want to use address. Divide markers with pipe character |', THEME_TD),
						'id'      => 'latlng',
						'default' => '',
						'type'    => 'textarea',
					),
					array(
						'name'    => __('Data Layer: Polygon', THEME_TD),
						'desc'    => __('Latitude and Longitude of Polygons (separate with commas) and divide coords with pipe character ; and divide polygons pipe character |', THEME_TD),
						'id'      => 'polygons',
						'default' => '',
						'type'    => 'textarea',
					),
				)
			),
			array(
				'title'  => __('Section', THEME_TD),
				'fields' => array(
					array(
						'name'    => __('Map Height', THEME_TD),
						'id'      => 'height',
						'desc'    => __('Map height in pixels.', THEME_TD),
						'type'    => 'slider',
						'default' => '500',
						'attr'    => array(
							'max'  => 800,
							'min'  => 50,
							'step' => 1
						)
					),
				)
			),
			array(
				'title'  => __('Extra Options', THEME_TD),
				'fields' => include OXY_THEME_DIR.'inc/options/shortcodes/shared/global.php'
			)
		)
	),
	'partner_list'     => array(
		'shortcode'   => 'partner_list',
		'title'       => __('Partner List', THEME_TD),
		'desc'        => __('Displays a list of partner members in columns.', THEME_TD),
		'insert_with' => 'dialog',
		'has_content' => false,
		'sections'    => array(
			array(
				'title'  => __('Partner logo list', THEME_TD),
				'fields' => array(
					array(
						'name'        => __('Number Of partners', THEME_TD),
						'desc'        => __('Number of partners to display(set to 0 to show all)', THEME_TD),
						'id'          => 'count',
						'type'        => 'slider',
						'admin_label' => true,
						'default'     => '0',
						'attr'        => array(
							'max'  => 30,
							'min'  => 0,
							'step' => 1
						)
					),
					array(
						'name'        => __('List Columns', THEME_TD),
						'desc'        => __('Number of columns to show partner in', THEME_TD),
						'id'          => 'columns',
						'type'        => 'select',
						'admin_label' => true,
						'options'     => array(
							2 => __('Two columns', THEME_TD),
							3 => __('Three columns', THEME_TD),
							4 => __('Four columns', THEME_TD),
							6 => __('Six columns', THEME_TD),
						),
						'default'     => '4',
					)
				)
			),
			array(
				'title'  => __('Extra Options', THEME_TD),
				'fields' => array_merge(include OXY_THEME_DIR.'inc/options/shortcodes/shared/global.php', array(
					array(
						'name'    => __('Animation Timing', THEME_TD),
						'desc'    => __('Will animate all partner at once or each one individually .', THEME_TD),
						'id'      => 'scroll_animation_timing',
						'type'    => 'select',
						'default' => 'staggered',
						'options' => array(
							'all-same'  => __('All items appear at same time', THEME_TD),
							'staggered' => __('Staggered over Animation Delay', THEME_TD),
						),
					)
				))
			)
		),
	),
	'icon_box'         => array(
		'shortcode'   => 'icon_box',
		'title'       => __('Icon box', THEME_TD),
		'desc'        => __('Displays a image with content.', THEME_TD),
		'insert_with' => 'dialog',
		'has_content' => false,
		'sections'    => array(
			array(
				'title'  => __('Main Options', THEME_TD),
				'fields' => array(
					array(
						'name'    => __('Image Source', THEME_TD),
						'id'      => 'image',
						'type'    => 'upload',
						'store'   => 'id',
						'default' => '',
						'desc'    => __('Place the source path of the image here', THEME_TD),
					),
					array(
						'name'        => __('Image type', THEME_TD),
						'desc'        => __('Select iamge type', THEME_TD),
						'id'          => 'image_type',
						'type'        => 'select',
						'options'     => array(
							'image' => __('Image', THEME_TD),
							'svg'   => __('SVG', THEME_TD),
						),
						'default'     => 'image',
						'admin_label' => true,
					),
					array(
						'name'    => __('Custom Image size', THEME_TD),
						'id'      => 'custom_image_size',
						'type'    => 'text',
						'default' => '',
						'desc'    => __('Enter size in pixels (Example: 400x400 or 350xAuto or Autox500 (Width x Height)). Leave empty to use Image size option above by default.', THEME_TD),
					),
					array(
						'name'        => __('Title', THEME_TD),
						'id'          => 'title',
						'type'        => 'text',
						'default'     => '',
						'desc'        => __('Box title', THEME_TD),
						'admin_label' => true,
					),
					array(
						'name'    => __('Title Color', THEME_TD),
						'desc'    => __('Set the color of the Title', THEME_TD),
						'id'      => 'title_color_hex',
						'type'    => 'colour',
						'default' => '#000000',
					),
					array(
						'name'    => __('Title Font Size', THEME_TD),
						'desc'    => __('Choose size of the font to use in your Title', THEME_TD),
						'id'      => 'title_size',
						'type'    => 'select',
						'options' => $font_sizes,
						'default' => 'fs18',
					),
					array(
						'name'    => __('Description', THEME_TD),
						'id'      => 'description',
						'type'    => 'textarea',
						'default' => '',
						'desc'    => __('Box description', THEME_TD),
					),
					array(
						'name'    => __('Description Color', THEME_TD),
						'desc'    => __('Set the color of the Description', THEME_TD),
						'id'      => 'description_color_hex',
						'type'    => 'colour',
						'default' => '#000000',
					),
					array(
						'name'    => __('Description Font Size', THEME_TD),
						'desc'    => __('Choose size of the font to use in your Description', THEME_TD),
						'id'      => 'description_size',
						'type'    => 'select',
						'options' => $font_sizes,
						'default' => 'fs14',
					),
				)
			),
			array(
				'title'  => __('Extra Options', THEME_TD),
				'fields' => include OXY_THEME_DIR.'inc/options/shortcodes/shared/global.php'
			)
		),
	),
	'latest_posts'     => array(
		'shortcode'   => 'latest_posts',
		'title'       => __('Latest posts', THEME_TD),
		'desc'        => __('Displays a Tail Latest post 5.', THEME_TD),
		'insert_with' => 'dialog',
		'has_content' => false,
		'sections'    => array(
			array(
				'title'  => __('Partner logo list', THEME_TD),
				'fields' => array(
					/*array(
						'name'        => __('Data source', THEME_TD),
						'desc'        => __('Select content type for your grid.', THEME_TD),
						'id'          => 'post_type',
						'type'        => 'select',
						'admin_label' => true,
						'options'     => \Digidez\Functions::get_post_type_list(),
						'default'     => 'post',
					),*/
					/*array(
						'name'        => __('Number Of posts', THEME_TD),
						'desc'        => __('Number of posts to display (set to 0 to show all)', THEME_TD),
						'id'          => 'posts_per_page',
						'type'        => 'slider',
						'admin_label' => true,
						'default'     => '0',
						'attr'        => array(
							'max'  => 500,
							'min'  => 0,
							'step' => 5
						)
					),*/
					array(
						'name'        => __('Layout type', THEME_TD),
						'desc'        => __('Select layout type. For example: 1x3 = 1-row, 3-cols', THEME_TD),
						'id'          => 'layout_type',
						'type'        => 'select',
						'admin_label' => true,
						'options'     => array(
							'1x2_1x3'  => __('1x2 on top, 1x3 on the bottom', THEME_TD),
							'1x1_4x3'    => __('1x1 on top, 4x3 on the bottom', THEME_TD),
							//'5x3' => __('5x3 - full page', THEME_TD),
						),
						'default'     => '1x2_1x3',
					),
					array(
						'name'    => __('Excerpt Length', THEME_TD),
						'id'      => 'post_excerpt_length',
						'type'    => 'text',
						'default' => '20',
						'desc'    => __('Set the excerpt length or leave blank to set default 55 words.', THEME_TD),
					),
					array(
						'name'        => __('Choose a category', THEME_TD),
						'desc'        => __('Category of posts to show', THEME_TD),
						'id'          => 'category',
						'default'     => '',
						'admin_label' => true,
						'type'        => 'select',
						'options'     => 'taxonomy',
						'taxonomy'    => 'category',
						'blank_label' => __('All Categories', THEME_TD)
					),
					array(
						'name'        => __('Order by', THEME_TD),
						'desc'        => __('Select order type.', THEME_TD),
						'id'          => 'orderby',
						'type'        => 'select',
						'admin_label' => true,
						'options'     => array(
							'date'  => __('Date', THEME_TD),
							'id'    => __('Post ID', THEME_TD),
							'title' => __('Post title', THEME_TD),
						),
						'default'     => 'date',
					),
					array(
						'name'        => __('Sort order', THEME_TD),
						'desc'        => __('Select sorting order.', THEME_TD),
						'id'          => 'order',
						'type'        => 'select',
						'admin_label' => true,
						'options'     => array(
							'DESC' => __('Descending', THEME_TD),
							'ASC'  => __('Ascending', THEME_TD),
						),
						'default'     => 'DESC',
					),
				)
			),
			array(
				'title'  => __('Extra Options', THEME_TD),
				'fields' => include OXY_THEME_DIR.'inc/options/shortcodes/shared/global.php'
			)
		),
	),
	'price_table'     => array(
		'shortcode'   => 'price_table',
		'title'       => esc_html__( 'Price Table', THEME_TD),
		'desc'        => esc_html__( 'Create pricing table.', THEME_TD),
		'insert_with' => 'dialog',
		'has_content' => false,
		'sections'    => array(
			array(
				'title'  => __('Partner logo list', THEME_TD),
				'fields' => array(
					array(
						'name'        => esc_html__( 'Featured?', THEME_TD),
						'desc'        => '',
						'id'          => 'featured',
						'type'        => 'select',
						'options'     => array(
							'yes' => esc_html__( 'Yes', THEME_TD ),
							'no' => esc_html__( 'No', THEME_TD ),
						),
						'default'     => 'yes',
					),
					array(
						'name'    => esc_html__( 'Title', THEME_TD),
						'id'      => 'title',
						'type'    => 'text',
						'default' => '',
						'desc'    => '',
					),
					array(
						'name'    => esc_html__('Sub Title', THEME_TD),
						'id'      => 'subtitle',
						'type'    => 'text',
						'default' => '',
						'desc'    => '',
					),
					array(
						'name'    => esc_html__('Description', THEME_TD),
						'id'      => 'description',
						'type'    => 'text',
						'default' => '',
						'desc'    => '',
					),
					array(
						'name'    => esc_html__('Price', THEME_TD),
						'id'      => 'price',
						'type'    => 'text',
						'default' => '',
						'desc'    => '',
						'admin_label' => true,
					),
					array(
						'name'    => esc_html__('Currency', THEME_TD),
						'id'      => 'currency',
						'type'    => 'text',
						'default' => '',
						'desc'    => '',
						'admin_label' => true,
					),
					array(
						'name'    => esc_html__('Features', THEME_TD),
						'id'      => 'content',
						'type'    => 'textarea_html',
						'default' => '',
						'desc'    => '',
					),
					array(
						'name'        => esc_html__( 'Show Button?', THEME_TD),
						'desc'        => '',
						'id'          => 'show_button',
						'type'        => 'select',
						'options'     => array(
							'yes' => esc_html__( 'Yes', THEME_TD ),
							'no' => esc_html__( 'No', THEME_TD ),
						),
						'default'     => 'yes',
					),
					array(
						'name'    => esc_html__( 'Button label', THEME_TD),
						'id'      => 'button_label',
						'type'    => 'text',
						'default' => '',
						'desc'    => '',
					),
					array(
						'name'    => esc_html__('Button Link', THEME_TD),
						'id'      => 'button_link',
						'type'    => 'vc_link',
						'default' => '',
						'desc'    => '',
					),
					array(
						'name'    => esc_html__('Open Link In', THEME_TD),
						'id'      => 'button_link_target',
						'type'    => 'select',
						'default' => '_self',
						'options' => array(
							'_self'   => __('Same page as it was clicked ', THEME_TD),
							'_blank'  => __('Open in new window/tab', THEME_TD),
						),
						'desc'    => '',
					),
				)
			),
			array(
				'title'  => __('Extra Options', THEME_TD),
				'fields' => include OXY_THEME_DIR.'inc/options/shortcodes/shared/global.php'
			)
		),
	),
	'testimonials_grid' => array(
		'shortcode' => 'testimonials_grid',
		'title'     => esc_html__('Testimonials Grid', THEME_TD),
		'desc'      => esc_html__('Displays a grid of testimonials.', THEME_TD),
		'insert_with' => 'dialog',
		'has_content'   => false,
		'sections'   => array(
			array(
				'title' => esc_html__('Testimonials', THEME_TD),
				'fields' => array(
					array(
						'name'    => esc_html__('Choose a group', THEME_TD),
						'desc'    => esc_html__('Group of testimonials to show', THEME_TD),
						'id'      => 'group',
						'default' =>  '',
						'type'    => 'select',
						'admin_label' => true,
						'options' => 'taxonomy',
						'taxonomy' => 'oxy_testimonial_group',
						'blank_label' => esc_html__('All Testimonials', THEME_TD)
					),
					array(
						'name'    => esc_html__('Number Of Testimonials', THEME_TD),
						'desc'    => esc_html__('Number of Testimonials to display(set to 0 to show all)', THEME_TD),
						'id'      => 'count',
						'type'    => 'slider',
						'admin_label' => true,
						'default' => '3',
						'attr'    => array(
							'max'   => 10,
							'min'   => 0,
							'step'  => 1
						)
					),
					array(
						'name'    => esc_html__('Number Of Columns', THEME_TD),
						'desc'    => esc_html__('Number of Columns to display', THEME_TD),
						'id'      => 'columns',
						'type'    => 'slider',
						'admin_label' => true,
						'default' => '2',
						'attr'    => array(
							'max'   => 4,
							'min'   => 1,
							'step'  => 1
						)
					),
					/*
					array(
						'name'      => esc_html__('Layout', THEME_TD),
						'id'        => 'layout',
						'type'      => 'select',
						'default'   => 'image',
						'options' => array(
							'image'           => esc_html__('Quote, Quotee & Image', THEME_TD),
							'no-image'        => esc_html__('Quote, Quotee', THEME_TD),
							'quote'           => esc_html__('Quote', THEME_TD),
							'quotee'          => esc_html__('Quotee & Image', THEME_TD),
							'quotee-no-image' => esc_html__('Quotee', THEME_TD),
						),
						'desc'    => esc_html__('Sets layout style of the quote', THEME_TD),
					),
					array(
						'name'    => esc_html__('Minimum Height', THEME_TD),
						'desc'    => esc_html__('Set a minimum height for the slider(in pxs), i.e 500', THEME_TD),
						'id'      => 'min_height',
						'default' =>  '',
						'type'    => 'text',
					),
					array(
						'name'      => esc_html__('Speed', THEME_TD),
						'desc'      => esc_html__('Set the speed of the slideshow cycling, in milliseconds', THEME_TD),
						'id'        => 'speed',
						'type'      => 'slider',
						'default'   => '7000',
						'attr'      => array(
							'max'       => 15000,
							'min'       => 2000,
							'step'      => 1000
						)
					),
					array(
						'name'      => esc_html__('Transition type', THEME_TD),
						'id'        => 'animation_type',
						'type'      => 'select',
						'default'   => 'slide',
						'options' => array(
							'slide' => esc_html__('Slide', THEME_TD),
							'fade'  => esc_html__('Fade', THEME_TD),
						),
						'desc' => esc_html__('Sets the type of animation that occurs between quotes.', THEME_TD),
					),
					array(
						'name'      => esc_html__('Show Controls', THEME_TD),
						'id'        => 'show_controls',
						'type'      => 'select',
						'default'   => 'show',
						'options' => array(
							'show' => esc_html__('Show', THEME_TD),
							'hide' => esc_html__('Hide', THEME_TD),
						),
						'desc'    => esc_html__('Toggles the slideshow bullet nav controls at the bottom.', THEME_TD),
					),
					array(
						'name'    => esc_html__('Randomize', THEME_TD),
						'desc'    => esc_html__('Randomize the ordering of the testimonials', THEME_TD),
						'id'      => 'randomize',
						'type'    => 'select',
						'default' => 'off',
						'options' => array(
							'on'   => esc_html__('On', THEME_TD),
							'off'  => esc_html__('Off', THEME_TD),
						),
					),
					array(
						'name'      => esc_html__('Text Align', THEME_TD),
						'id'        => 'text_align',
						'type'      => 'select',
						'default'   => 'center',
						'options' => array(
							'left'   => esc_html__('Left', THEME_TD),
							'center' => esc_html__('Center', THEME_TD),
							'right'  => esc_html__('Right', THEME_TD),
							'justify'  => esc_html__('Justify', THEME_TD)
						),
						'desc'    => esc_html__('Sets the text alignment of the blockquote and citation of the testimonial', THEME_TD),
					),
					*/
				)
			),
			array(
				'title' => esc_html__('Text', THEME_TD),
				'fields' => include OXY_THEME_DIR . 'inc/options/shortcodes/shared/text-color.php'
			),
			array(
				'title' => esc_html__('Extra Options', THEME_TD),
				'fields' => include OXY_THEME_DIR . 'inc/options/shortcodes/shared/global.php'
			)
		)
	),
	'premium_box' => array(
		'shortcode' => 'premium_box',
		'title'     => esc_html__('Premium box', THEME_TD),
		//'desc'      => esc_html__('Displays a grid of testimonials.', THEME_TD),
		'insert_with' => 'dialog',
		'has_content'   => false,
		'sections'   => array(
			array(
				'title' => esc_html__('Premium box', THEME_TD),
				'fields' => array(
					array(
						'name'    => __('Icon Source', THEME_TD),
						'id'      => 'icon',
						'type'    => 'upload',
						'store'   => 'id',
						'default' => '',
						'desc'    => __('Place the source path of the image here', THEME_TD),
					),
					array(
						'name'    => esc_html__('Title', THEME_TD),
						'desc'    => '',
						'id'      => 'title',
						'default' =>  '',
						'type'    => 'text',
					),
					array(
						'name'    => esc_html__('Subtitle', THEME_TD),
						'desc'    => '',
						'id'      => 'subtitle',
						'default' =>  '',
						'type'    => 'text',
					),
					array(
						'name'    => esc_html__('Description', THEME_TD),
						'desc'    => '',
						'id'      => 'description',
						'default' =>  '',
						'type'    => 'textarea',
					),
					array(
						'name'    => esc_html__( 'Button label', THEME_TD),
						'id'      => 'button_label',
						'type'    => 'text',
						'default' => '',
						'desc'    => '',
					),
					array(
						'name'    => esc_html__('Button Link', THEME_TD),
						'id'      => 'button_link',
						'type'    => 'vc_link',
						'default' => '',
						'desc'    => '',
					),
					array(
						'name'    => esc_html__('Open Link In', THEME_TD),
						'id'      => 'button_link_target',
						'type'    => 'select',
						'default' => '_self',
						'options' => array(
							'_self'   => esc_html__('Same page as it was clicked ', THEME_TD),
							'_blank'  => esc_html__('Open in new window/tab', THEME_TD),
						),
						'desc'    => '',
					),

				)
			),
			array(
				'title' => esc_html__('Text', THEME_TD),
				'fields' => include OXY_THEME_DIR . 'inc/options/shortcodes/shared/text-color.php'
			),
			array(
				'title' => esc_html__('Extra Options', THEME_TD),
				'fields' => include OXY_THEME_DIR . 'inc/options/shortcodes/shared/global.php'
			)
		)
	),
);
