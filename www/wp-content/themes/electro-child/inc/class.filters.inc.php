<?php
namespace Digidez;

use WC_Data;

class Filters {

	public static $self;

    /**
     * Static function must be called after require within functions.inc.php
     * This will setup all filter hooks
     */
    public static function initialise(){
        $self = new self();

        // define all filter hooks here and document if not self explanatory

		add_filter('theme_page_templates', array($self, 'redefine_page_templates'));
	    //add_filter('theme_post_templates', array($self, 'redefine_post_templates'));

	    if(is_admin()){
		    // Save snd close
		    add_filter('redirect_post_location', array($self, 'redirect_post_location'), '99'); // change redirect URL

		    //add_filter('dynamic_sidebar_params', array($self, 'redefine_dynamic_sidebar_params'), 0);
		    add_filter('manage_edit-page_columns', array($self, 'edit_columns_page'));
		    add_filter('manage_edit-post_columns', array($self, 'edit_columns_post'));
		    add_filter('manage_edit-product_columns', array($self, 'edit_columns_product'));
		    add_filter('manage_edit-product_sortable_columns', array($self, 'product_set_sortable_columns'));

		    add_filter('ajax_query_attachments_args', array($self, 'ajax_query_attachments_args'));
	    }

	    if(is_admin() || wp_is_json_request() || wp_is_jsonp_request() || is_ajax()){
		    //add_filter('intermediate_image_sizes_advanced', array($self, 'intermediate_image_sizes_advanced'), 99, 3);
		    //add_filter('intermediate_image_sizes', array($self, 'intermediate_image_sizes'), 10, 1);
		    //add_filter('wp_generate_attachment_metadata', array($self, 'replace_uploaded_image__woocommerce_single'), 10, 2);
		    add_filter('wp_handle_upload', array($self, 'wp_handle_upload'), 10, 2);
		    add_filter('wp_get_original_image_path', array($self, 'wp_get_original_image_path'), 10, 2);
	    }

	    //add_filter('manage_edit-acf-field-group_columns', array($self, 'edit_columns_acf_field_group'), 11);
	    // Enable the option show in rest
	    //add_filter('acf/rest_api/field_settings/show_in_rest', '__return_true');
	    // Enable the option edit in rest
	    //add_filter('acf/rest_api/field_settings/edit_in_rest', '__return_true');

	    //add_filter('post_type_link', array($self, 'post_type_permalink'), 1, 2);

	    //add_filter('wpcf7_skip_mail', '__return_true', 999); // For testing without mail sending process

	    //add_filter('get_meta_sql', array($self, 'get_meta_sql'), 99, 1);
		//add_filter('pre_option_posts_per_page', array($self, 'pre_option_posts_per_page'), 10, 2);
		add_filter('body_class', array($self, 'body_class'), 20, 2);

	    //add_filter('custom_menu_order', '__return_true');
		//add_filter('menu_order', array($self, 'custom_menu_order'), 10, 1);
		//add_filter('megamenu_output_public_toggle_block_spacer', array($self, 'megamenu_output_public_toggle_block_spacer'), 10, 2);

		/*
	    //add_filter( 'woocommerce_enqueue_styles', '__return_empty_array');
	    add_filter('woocommerce_enqueue_styles', array($self, 'woocommerce_enqueue_styles'));
	    //add_filter('loop_shop_per_page', array($self, 'loop_shop_per_page'));
	    add_filter('woocommerce_product_tabs', array($self, 'woocommerce_product_tabs'), 10, 1);
	    //add_filter('woocommerce_variable_price_html', array($self, 'woocommerce_variable_price_html'), 10, 2);
	    add_filter('woocommerce_get_price_html', array($self, 'woocommerce_get_price_html'), 9, 2);
	    //add_filter('woocommerce_format_sale_price', array($self, 'woocommerce_format_sale_price'), 10, 3);
	    add_filter('woocommerce_format_price_range', array($self, 'woocommerce_format_price_range'), 10, 3); // for display only min price
	    
	    add_filter('woocommerce_dropdown_variation_attribute_options_args', array($self, 'woocommerce_dropdown_variation_attribute_options_args'), 10, 1);
	    add_filter('woocommerce_widget_cart_is_hidden', '__return_false'); // for permanent display the "mini-cart" widget
	    //add_filter('woocommerce_germanized_checkout_show_terms', '__return_false');
	    //add_filter('woocommerce_billing_fields', array($self, 'woocommerce_billing_fields'), 10, 1);
	    add_filter('woocommerce_endpoint_order-received_title', array($self, 'woocommerce_endpoints_title'), 10, 2);
	    add_filter('woocommerce_account_menu_items', array($self, 'woocommerce_account_menu_items'), 10, 2);
	    add_filter('tawcvs_swatch_html', array($self, 'tawcvs_swatch_html'), 10, 4);
	    add_filter('woocommerce_dropdown_variation_attribute_options_html', array($self, 'woocommerce_dropdown_variation_attribute_options_html'), 110, 2);
	    add_filter('woocommerce_available_variation', array($self, 'woocommerce_available_variation'), 10, 3);
	    add_filter('premmerce_product_filter_items', array($self, 'premmerce_product_filter_items'), 10, 1);
	    add_filter('premmerce_filter_render_item_title', array($self, 'premmerce_filter_render_item_title'), 10, 2);
		*/
	    add_filter('woocommerce_default_address_fields', array($self, 'woocommerce_default_address_fields'), 10, 1);
	    add_filter('woocommerce_checkout_fields', array($self, 'woocommerce_checkout_fields'), 10, 1);
	    add_filter('woocommerce_attribute_taxonomies', array($self, 'woocommerce_attribute_taxonomies'), 5, 1);
	    //add_filter('woocommerce_cart_product_price', array($self, 'woocommerce_cart_product_price'), 10, 2);
	    //add_filter('woocommerce_cart_product_subtotal', array($self, 'woocommerce_cart_product_subtotal'), 10, 3);
	    //add_filter('woocommerce_get_price_excluding_tax', array($self, 'woocommerce_get_price_excluding_tax'), 10, 3);
	    add_filter('electro_get_sale_flash', array($self, 'electro_get_sale_flash'), 10, 3);
	    add_filter('woocommerce_screen_ids', array($self, 'woocommerce_screen_ids'), 99999, 1);
	    #add_filter('woocommerce_product_get_sale_price', array($self, 'woocommerce_product_get_sale_price'), 99999, 2);
	    //add_filter('woocommerce_get_catalog_ordering_args', array($self, 'woocommerce_get_catalog_ordering_args'), 10, 1);

	    add_filter('woocommerce_cheque_process_payment_order_status', array($self, 'woocommerce_cheque_process_payment_order_status'), 10, 1);
	    add_filter('woocommerce_payment_gateways', array($self, 'woocommerce_payment_gateways'), 10, 1);
	    add_filter('woocommerce_available_payment_gateways', array($self, 'woocommerce_available_payment_gateways'), 10, 1);

	    add_filter('electro_product_categories_widget_top_level_list_categories_args', array($self, 'electro_product_categories_widget_top_level_list_categories_args'), 10, 1);

	    /**
	     * Call from /wp-content/plugins/woocommerce/includes/wc-core-functions.php
	     * Line 830
	     */
	    add_filter('woocommerce_get_image_size_thumbnail', array($self, 'woocommerce_get_image_size_thumbnail'), 10, 1);
	    add_filter('woocommerce_get_image_size_single', array($self, 'woocommerce_get_image_size_single'), 10, 1);
	    add_filter('woocommerce_get_image_size_gallery_thumbnail', array($self, 'woocommerce_get_image_size_gallery_thumbnail'), 10, 1);

	    add_filter('ywpar_single_product_message_in_loop', array($self, 'ywpar_single_product_message_in_loop'), 10, 3);
    }


    /** YWPAR */

    public function ywpar_single_product_message_in_loop($html, $product, $product_points){
	    $products_promotion_method = get_field('products_promotion_method', $product->get_ID());

	    if($products_promotion_method == 'coupons'){
		    $html .= '<div class="clearfix"></div><div class="yith-par-message electro-par-message"><b>%</b> '.__('This item can be purchased using a coupon.', THEME_TD).'</div>';
	    }

	    return $html;
    }

    /** Premmerce */
	public function premmerce_filter_render_item_title($attribute_label, $attribute){
		#Functions::_debug($attribute->attribute_name);

		switch($attribute->attribute_name){
			case 'sizes':
				$attribute_label = esc_attr__('Choose size', THEME_TD);
				break;
			case 'marke':
				$attribute_label = esc_attr__('Search by brand', THEME_TD);
				break;
		}

		return $attribute_label;
	}

	public function premmerce_product_filter_items($items){
		#Functions::_debug($items);

		$ordered_filter_items = [
			'sizes' => '',
			'colors' => '',
			'price' => '',
			'marke' => '',
		];

		foreach($items as $item){
			#Functions::_debug($item->getSlug());
			$ordered_filter_items[$item->getSlug()] = $item;
		}
		#Functions::_debug($ordered_filter_items);

		return $ordered_filter_items;
	}

    /** Woocommerce */

	public function woocommerce_get_image_size_thumbnail($size){
		$size['width'] = 200;
		$size['height'] = '';
		$size['crop']   = 0; //0 - не обрезаем, 1 - обрезка
		return $size;
	}

	public function woocommerce_get_image_size_single($size){
		$size['width'] = 440;
		$size['height'] = '';
		$size['crop']   = 0; //0 - не обрезаем, 1 - обрезка
		return $size;
	}

	public function woocommerce_get_image_size_gallery_thumbnail($size){
		$size['width'] = 100;
		$size['height'] = 100;
		$size['crop']   = 1; //0 - не обрезаем, 1 - обрезка
		return $size;
	}

	public function woocommerce_payment_gateways($load_gateways){

		if(is_admin()){
			return $load_gateways;
		}

		$GLOBALS['exclude_gateways'] = array();
		$on_sale_products = array();
		$do_filter = false;
		$exclude_gateway = array(
			'WC_Gateway_Cheque',
		);

		foreach($load_gateways as $k => $name){
			if(in_array($name, $exclude_gateway)){
				$do_filter = true;
			}
		}

		if($do_filter && !WC()->cart->is_empty()){
			foreach(WC()->cart->get_cart() as $cart_item_key => $cart_item){
				if($cart_item['data']->is_on_sale()){
					$on_sale_products[] = $cart_item['product_id'];
				}
			}

			if(!empty($on_sale_products)){
				foreach($load_gateways as $k => $name){
					if(in_array($name, $exclude_gateway)){
						$GLOBALS['exclude_gateways'][] = $name;
						unset($load_gateways[$k]);
					}
				}
				//wc_add_notice('test', 'error');
			}
		}

		return $load_gateways;
	}

	public function woocommerce_available_payment_gateways($gateways){

		if(empty($gateways)){
			return $gateways;
		}

		$exclude_gateway = array(
			'cheque' => 'WC_Gateway_Cheque',
		);

		if(isset($GLOBALS['exclude_gateways']) && !empty($GLOBALS['exclude_gateways'])){
			foreach($GLOBALS['exclude_gateways'] as $k => $name){
				if(in_array($name, $exclude_gateway)){
					unset($gateways[$k]);
				}
			}
			#WC()->payment_gateways()->set_current_gateway($gateways);
			#WC()->session->set('chosen_payment_method', 'cod');
		}

		return $gateways;
	}

	// Другая версия - не используется
	public function _woocommerce_available_payment_gateways($gateways){

		if(empty($gateways)){
			return $gateways;
		}

		#Functions::_debug($gateways);

		$on_sale_products = array();
		$do_filter = false;
		$gateways_keys = array_keys($gateways);
		$exclude_gateway = array(
			'cheque',
		);

		foreach($gateways_keys as $name){
			if(in_array($name, $exclude_gateway)){
				$do_filter = true;
			}
		}

		if($do_filter && !WC()->cart->is_empty()){
			foreach(WC()->cart->get_cart() as $cart_item_key => $cart_item){
				if($cart_item['data']->is_on_sale()){
					$on_sale_products[] = $cart_item['product_id'];
				}
			}

			if(!empty($on_sale_products)){
				foreach($exclude_gateway as $name){
					unset($gateways[$name]);
				}
				//wc_add_notice('test', 'error');
				//wc_print_notices();
			}
		}

		#Functions::_debug($gateways);

		return $gateways;
	}

    public function woocommerce_cheque_process_payment_order_status($order){
	    return 'processing';
    }

	// не используется
	public function woocommerce_get_catalog_ordering_args($args){
		Functions::_debug($args);
		$orderby_value = isset($_GET['orderby']) ? woocommerce_clean($_GET['orderby']) : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby'));

		if('name_list' == $orderby_value){
			$args['orderby']  = 'name';
			$args['order']    = 'ASC';
			$args['meta_key'] = '';
		}

		return $args;
	}

    public function woocommerce_screen_ids($screen_ids){
	    $ex_arr = array('toplevel_page_wpcf7', 'contact-form-7_page_wpcf7-integration');

    	foreach($screen_ids as $k => $v){
    		if(in_array($v, $ex_arr)){
			    unset($screen_ids[$k]);
		    }
	    }

    	return $screen_ids;
    }

    public function wp_handle_upload($params, $action){
	    #Functions::_debug($_SERVER); exit;
	    if(
	    	strstr($_SERVER['HTTP_REFERER'], '/wp-admin/post.php') !== false ||
		    strstr($_SERVER['HTTP_REFERER'], '/wp-admin/post-new.php') !== false
	    ){
		    if(preg_match('!^image/!', $params['type'])){
			    Functions::create_square_image($params['file']);
		    }
	    }

	    return $params;
    }

    public function wp_get_original_image_path($original_image, $attachment_id){
    	global $wpdb;
	    #Functions::_debug($_SERVER); exit;

	    if(strstr($_SERVER['REQUEST_URI'], '/regenerate-thumbnails/') !== false){
	    	$post_parent = $wpdb->get_var("SELECT post_parent FROM {$wpdb->posts} WHERE ID = {$attachment_id}");
	    	if(intval($post_parent) > 0){
			    Functions::create_square_image($original_image);
		    }
	    }

	    return $original_image;
    }

    public function intermediate_image_sizes($default_sizes){
    	Functions::_debug($default_sizes);
    	exit;
    }

    public function intermediate_image_sizes_advanced($sizes, $image_meta, $attachment_id){
    	if(isset($sizes['woocommerce_single'])){
		    $sizes['woocommerce_single']['height'] = $sizes['woocommerce_single']['width'];
		    //$sizes['woocommerce_single']['crop'] = 1;
	    }
    	if(isset($sizes['shop_single'])){
		    $sizes['shop_single']['height'] = $sizes['shop_single']['width'];
		    //$sizes['shop_single']['crop'] = 1;
	    }
    	#Functions::_debug($attachment_id);
    	#Functions::_debug($sizes);
    	#Functions::_debug($image_meta);
    	#exit;

    	return $sizes;
    }

	public function replace_uploaded_image__woocommerce_single($metadata, $attachment_id){
    	global $wpdb;

		$upload_dir = wp_upload_dir();

		#Functions::_debug($upload_dir); exit;

		$image_size_name = 'woocommerce_single';

		if(!isset($metadata['sizes'][$image_size_name])){
			return $metadata;

			$fa = explode('/', $metadata['file']);
			$file = array_pop($fa);
			$subdir = implode('/', $fa);
			$file_ext = Functions::get_file_ext($file);
			$new_file = str_replace('.'.$file_ext, '-440x440.'.$file_ext, $file);
			copy($upload_dir['basedir'].'/'.$subdir.'/'.$file, $upload_dir['basedir'].'/'.$subdir.'/'.$new_file);

			switch($file_ext){
				case "jpg":
				case "jpeg":
					$mime_type = 'image/jpeg';
					break;
				case "png":
					$mime_type = 'image/png';
					break;
				case "gif":
					$mime_type = 'image/gif';
					break;
				case "bmp":
					$mime_type = 'image/bmp';
					break;
			}

			$metadata['sizes'][$image_size_name] = array(
				'file' => $new_file,
				'width' => 440,
				'height' => 440,
				'mime-type' => $mime_type,
			);

			$metadata['sizes']['shop_single'] = $metadata['sizes'][$image_size_name];
			/*$args = array(
				'post_title' => basename($new_file),
				'guid' => $upload_dir['baseurl'].'/'.$subdir.'/'.$new_file
			);
			$wpdb->update($wpdb->posts, $args, array('ID' => $attachment_id,));*/
			$wpdb->update($wpdb->postmeta, array('meta_value' => $subdir.'/'.$new_file), array('meta_key' => '_wp_attached_file', 'post_id' => $attachment_id));
		}

		#Functions::_debug($metadata); exit;

		//Set our desired static height / width (440px * 440px)
		$staticWidth = $staticHeight = $metadata['sizes'][$image_size_name]['width'];

		// paths to the uploaded image and the large image
		$a = explode('/', $metadata['file']);
		array_pop($a);
		$file_path = implode('/', $a);
		$dst_image_path = $brands_image_location = $upload_dir['basedir'].'/'.$file_path.'/'.$metadata['sizes'][$image_size_name]['file'];
		$brands_image_location_url = $upload_dir['baseurl'].'/'.$file_path.'/'.$metadata['sizes'][$image_size_name]['file'];
		//$brands_image_location_url = $upload_dir['baseurl'].'/'.$file_path.'/'.$metadata['file'];

		// get the attributes of the source image
		list($imageWidth, $imageHeight, $imageType, $imageAttr) = getimagesize($brands_image_location);

		//Calculate where the image should start so its centered
		if(($imageWidth > $imageHeight) || ($imageHeight > $imageWidth)){
			$resized_image_data = aq_resize_custom($brands_image_location_url, $staticWidth, $staticHeight, false, false);
			#Functions::_debug($resized_image_data); exit;
			$imageWidth = $resized_image_data[1];
			$imageHeight = $resized_image_data[2];
			$brands_image_location = $resized_image_data[3];
		}

		if($imageWidth == $staticWidth){
			$x_pos = 0;
		}else{
			$x_pos = round(($staticWidth - $imageWidth) / 2);
		}
		if($imageHeight == $staticHeight){
			$y_pos = 0;
		}else{
			$y_pos = round(($staticHeight - $imageHeight) / 2);
		}

		#Functions::_debug($brands_image_location); exit;

		// set our temp image file
		$brands_image_location_tmp = "$brands_image_location.tmp";

		//Create a fixed white canvas
		$newimage = imagecreatetruecolor($staticWidth, $staticHeight);

		// there are different php functions depending on what type of image it is, so check the type
		switch($imageType){
			case 1:
				imagealphablending($newimage, false);
				imagesavealpha($newimage, true);
				$transparent = imagecolorallocatealpha($newimage, 255, 255, 255, 127);
				imagefilledrectangle($newimage, 0, 0, $staticWidth, $staticHeight, $transparent);
				$src = imagecreatefromgif($brands_image_location);
				break;
			case 2:
				$white = imagecolorallocate($newimage, 255, 255, 255);
				imagefilledrectangle($newimage, 0, 0, $staticWidth, $staticHeight, $white);
				$src = imagecreatefromjpeg($brands_image_location);
				break;
			case 3:
				imagealphablending($newimage, false);
				imagesavealpha($newimage, true);
				$transparent = imagecolorallocatealpha($newimage, 255, 255, 255, 127);
				imagefilledrectangle($newimage, 0, 0, $staticWidth, $staticHeight, $transparent);
				$src = imagecreatefrompng($brands_image_location);
				break;
		}

		//imagecopyresampled($newimage, $src, $x_pos, $y_pos, 0, 0, $staticWidth, $staticHeight, $imageWidth, $imageHeight);
		//imagecopyresized($newimage, $src, $x_pos, $y_pos, 0, 0, $staticWidth, $staticHeight, $imageWidth, $imageHeight);
		imagecopy($newimage, $src, $x_pos, $y_pos, 0, 0, $imageWidth, $imageHeight);

		switch($imageType){
			case 1:
				imagegif($newimage, $brands_image_location_tmp);
				break;
			case 2:
				imagejpeg($newimage, $brands_image_location_tmp);
				break;
			case 3:
				imagepng($newimage, $brands_image_location_tmp);
				break;
		}

		// delete the uploaded image
		unlink($brands_image_location);

		// rename the temporary brands image
		rename($brands_image_location_tmp, $dst_image_path);

		// update image metadata and return them
		$metadata['sizes'][$image_size_name]['width']  = $staticWidth;
		$metadata['sizes'][$image_size_name]['height'] = $staticHeight;

		return $metadata;
	}

	// Display variation's price even if min and max prices are the same
    public function woocommerce_available_variation($value, $object = null, $variation = null){
	    if ($value['price_html'] == '') {
		    $value['price_html'] = '<span class="price">' . $variation->get_price_html() . '</span>';
	    }
	    return $value;
    }

    public function woocommerce_enqueue_styles($scripts){
    	#Functions::_debug($scripts);
    	if(isset($scripts['woocommerce-smallscreen'])){
		    $scripts['woocommerce-smallscreen']['src'] = CSS_URI.'/woocommerce-smallscreen.css';
	    }
	    $scripts['woocommerce-general']['src'] = CSS_URI.'/woocommerce.css';

	    return $scripts;
    }

	public function loop_shop_per_page($count){
		$shop_post = Functions::wc_get_shop_page();
		#Functions::_debug($shop_post->cf['products_per_page']);
		if(isset($shop_post->cf['products_per_page']) && intval($shop_post->cf['products_per_page'])){
			$count = $shop_post->cf['products_per_page'];
		}
		return $count;
	}


	/**  WC Price **/

	// не используется
	public function woocommerce_get_price_excluding_tax($return_price, $qty, $product){
		if($return_price < 0){
			#Functions::_debug((100 - abs($product->sale_price)));
			$return_price = round($product->regular_price * (100 - abs($product->sale_price)) / 100, -1, PHP_ROUND_HALF_DOWN);
		}

		return $return_price;
	}

	// не используется
	public function woocommerce_cart_product_subtotal($product_subtotal, $product, $quantity){
		if($product->sale_price < 0){
			#Functions::_debug((100 - abs($product->sale_price)));
			$product_subtotal = wc_price(round($product->regular_price * (100 - abs($product->sale_price)) / 100, -1, PHP_ROUND_HALF_DOWN) * $quantity);
		}

		return $product_subtotal;
	}

	// не используется
	public function woocommerce_cart_product_price($price, $product){
		if($product->sale_price < 0){
			#Functions::_debug((100 - abs($product->sale_price)));
			$price = wc_price(round($product->regular_price * (100 - abs($product->sale_price)) / 100, -1, PHP_ROUND_HALF_DOWN));
		}

		return $price;
	}

	// не используется
	public function woocommerce_format_price_range($price, $from, $to){
		$shop_page_id = get_query_var('shop_page_id');
		global $product;

		if(wc_get_page_id('shop') == $shop_page_id || $from){
			if($product->is_on_sale()){
				$available_variations_prices = Functions::get_peoduct_available_variations_prices($product);
				$price_del = '<del>'.wc_price($available_variations_prices['max']).'</del>';
				$price_ins = '<ins>'.wc_price($available_variations_prices['min']).'</ins>';
				#Functions::_debug($available_variations_prices);
				return $price_del.$price_ins;
			}else{
				return wc_price($from);
			}
		}else{
			return $price;
		}
	}

	// не используется
	public function woocommerce_variable_price_html($price, $product){
		$shop_page_id = get_query_var('shop_page_id');

		if(wc_get_page_id('shop') == $shop_page_id){
			return str_replace('</span></span> – ', '</span> –</span>', $price);
		}else{
			return $price;
		}
	}

	// не используется
	public function woocommerce_format_sale_price($price, $regular_price, $sale_price){
		#Functions::_debug($price);
		$price_del = '<del>'.(is_numeric($regular_price) ? wc_price($regular_price) : $regular_price).'</del>';
		$price_ins = '<ins>'.(is_numeric($sale_price) ? wc_price($sale_price) : $sale_price).'</ins>';

    	return $price;
	}

	// не используется
	public function woocommerce_get_price_html($price, $product){
    	#Functions::_debug($product->product_type);
    	if($product->is_type('variation')){
    		ob_start();
    		wc_get_template('single-product/add-to-cart/variation-price.php', array( 'product' => $product ));
    		$price = ob_get_clean();
	    }

		return $price;
	}

	// не используется
	public function woocommerce_product_get_sale_price($value, $wc_data){

		if(!empty($value) && intval($value) < 0){
			#Functions::_log($wc_data->get_id());
			$_regular_price = get_post_meta($wc_data->get_id(), '_regular_price', true);
			$value = round($_regular_price * (100 - abs($value)) / 100, -1, PHP_ROUND_HALF_DOWN);
		}

		return $value;
	}

	public function electro_get_sale_flash($html, $post, $product){
		$by_percent = false;

		if($by_percent){
			$sale_price = $product->get_sale_price();
			if(intval($sale_price) < 0){
				$html = '<span class="onsale"><span class="percentage">'.$sale_price.'%</span></span>';
			}
		}else{
			$regular_price = $product->get_regular_price();
			$end_price = $product->get_price();
			if(intval($regular_price) > intval($end_price)){
				$html = '<span class="onsale"><span class="percentage">-'.wc_price($regular_price-$end_price).'</span></span>';
			}
		}

		return $html;
	}

	public function woocommerce_product_tabs($tabs){
		global $product;

		$tabs = array();

		$product_cf = Functions::get_cpt_custom_fields($product);

		#Functions::_debug($product_cf);

		$i = 0;
		foreach($product_cf as $k => $v){
			if(strstr($k, 'product_tab_') !== false){
				$i++;
				$tabs[$k] = array(
					'title'    => $v['title'],
					'description'    => $v['description'],
					'priority' => ($i * 10),
					'callback' => array(__CLASS__, 'woocommerce_product_custom_tab'),
				);
			}
		}

		return $tabs;
	}

	public function woocommerce_dropdown_variation_attribute_options_args($args){
    	if($args['attribute'] == 'pa_sizes'){
		    $args['show_option_none'] = __('Your size', THEME_TD);
	    }

    	return $args;
	}

	public static function woocommerce_product_custom_tab($key, $tab){
		global $product;
		$product_cf = Functions::get_cpt_custom_fields($product);
		#Functions::_debug($product_cf[$key]);
    	echo $product_cf[$key]['description'];
	}

	public function woocommerce_billing_fields($address_fields){

    	$ordered_fields = array(
    		//'billing_title' => '',
		    'billing_first_name' => '',
		    'billing_last_name' => '',
		    'billing_address_1' => '',
		    'billing_city' => '',
		    'billing_postcode' => '',
		    //'billing_state' => '',
		    'billing_country' => '',
		    'billing_phone' => '',
		    'billing_email' => '',
	    );

    	#Functions::_debug($address_fields);

		foreach($address_fields as $k => $v){
			$ordered_fields[$k] = $v;
		}

		#Functions::_debug($ordered_fields);
		return $ordered_fields;
	}

	public function woocommerce_default_address_fields($address_fields){
		$address_fields['postcode']['required'] = false;
		$address_fields['postcode']['class'][] = 'form-field-hidden';
		$address_fields['city']['class'][0] = 'form-row-first';
		$address_fields['city']['placeholder'] = __('City, Village, Town', THEME_TD);
		$address_fields['state']['class'][0] = 'form-row-last';
		$address_fields['state']['required'] = false;
		$address_fields['state']['label'] = __('County', 'woocommerce');
		$address_fields['country']['class'][] = 'form-field-hidden';
		$address_fields['address_1']['placeholder'] = __('Street Name, House, Apt (Office)', THEME_TD);
		$address_fields['address_2']['class'][] = 'form-field-hidden';

		#Functions::_debug($address_fields, 1);

		return $address_fields;
	}

	public function woocommerce_checkout_fields($checkout_fields){
		#Functions::_debug($checkout_fields, 1);


		//$checkout_fields['billing']['billing_address_1']['priority'] = 30;
    	//$checkout_fields['shipping']['shipping_address_1']['priority'] = 30;

		//$checkout_fields['billing']['billing_city']['priority'] = 40;
		//$checkout_fields['billing']['billing_city']['class'][0] = 'form-row-first';
		//$checkout_fields['shipping']['shipping_city']['priority'] = 40;
		//$checkout_fields['shipping']['shipping_city']['class'][0] = 'form-row-first';

    	//$checkout_fields['billing']['billing_postcode']['priority'] = 50;
    	//$checkout_fields['shipping']['shipping_postcode']['priority'] = 50;

		//$checkout_fields['billing']['billing_country']['priority'] = 70;
		//$checkout_fields['shipping']['shipping_country']['priority'] = 70;

    	$checkout_fields['billing']['billing_phone']['class'][0] = 'form-row-first';
    	$checkout_fields['billing']['billing_email']['class'][0] = 'form-row-last';

		#Functions::_debug($checkout_fields['billing'], 1);

		return $checkout_fields;
	}

	public function woocommerce_endpoints_title($title, $endpoint){
		global $wp;

		switch ( $endpoint ) {
			case 'order-pay':
				//$title = __( 'Pay for order', THEME_TD);
				break;
			case 'order-received':
				$title = __( 'Order received', THEME_TD);
				break;
			case 'orders':
				/*if ( ! empty( $wp->query_vars['orders'] ) ) {
					$title = sprintf( __( 'Orders (page %d)', THEME_TD), intval( $wp->query_vars['orders'] ) );
				} else {
					$title = __( 'Orders', THEME_TD);
				}*/
				break;
			case 'view-order':
				//$order = wc_get_order( $wp->query_vars['view-order'] );
				//$title = ( $order ) ? sprintf( __( 'Order #%s', THEME_TD), $order->get_order_number() ) : '';
				break;
			case 'downloads':
				//$title = __( 'Downloads', THEME_TD);
				break;
			case 'edit-account':
				//$title = __( 'Account details', THEME_TD);
				break;
			case 'edit-address':
				//$title = __( 'Addresses', THEME_TD);
				break;
			case 'payment-methods':
				//$title = __( 'Payment methods', THEME_TD);
				break;
			case 'add-payment-method':
				//$title = __( 'Add payment method', THEME_TD);
				break;
			case 'lost-password':
				//$title = __( 'Lost password', THEME_TD);
				break;
		}

		return $title;
	}

	public function woocommerce_account_menu_items($items, $endpoints){
		unset($items['downloads']);

		return $items;
	}

	public function tawcvs_swatch_html($html, $term, $type, $args){
		$selected = sanitize_title( $args['selected'] ) == $term->slug ? 'selected' : '';
		$name     = esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) );

		switch ( $type ) {
			case 'color':
				/*$color = get_term_meta( $term->term_id, 'color', true );
				list( $r, $g, $b ) = sscanf( $color, "#%02x%02x%02x" );
				$html = sprintf(
					'<span class="swatch swatch-color swatch-%s %s" style="background-color:%s;color:%s;" title="%s" data-value="%s">%s</span>',
					esc_attr( $term->slug ),
					$selected,
					esc_attr( $color ),
					"rgba($r,$g,$b,0.5)",
					esc_attr( $name ),
					esc_attr( $term->slug ),
					$name
				);*/
				break;

			case 'image':
				$image = get_term_meta( $term->term_id, 'image', true );
				$image = $image ? Functions::get_the_attachment_thumbnail($image, '42x40', '', false) : '';
				$image = $image ? $image : WC()->plugin_url() . '/assets/images/placeholder.png';
				$html  = sprintf(
					'<span class="swatch swatch-image swatch-%s %s" title="%s" data-value="%s"><img src="%s" alt="%s"><span class="attr-label">%s</span></span>',
					esc_attr( $term->slug ),
					$selected,
					esc_attr( $name ),
					esc_attr( $term->slug ),
					esc_url( $image ),
					esc_attr( $name ),
					esc_attr( $name )
				);
				break;

			case 'label':
				/*$label = get_term_meta( $term->term_id, 'label', true );
				$label = $label ? $label : $name;
				$html  = sprintf(
					'<span class="swatch swatch-label swatch-%s %s" title="%s" data-value="%s">%s</span>',
					esc_attr( $term->slug ),
					$selected,
					esc_attr( $name ),
					esc_attr( $term->slug ),
					esc_html( $label )
				);*/
				break;
		}

		return $html;
	}

	public function woocommerce_dropdown_variation_attribute_options_html($html, $args){
		if(Functions::$device == 'mobile'){
			if($args['attribute'] == 'pa_sizes'){
				#Functions::_debug($args['selected']);
				$old_html = $html;
				$old_html = str_replace('class=""', 'class="hidden"', $old_html);
				$new_html = str_replace(
					array('<select', '</select>', '<option', '</option>'),
					array('<ul data-selected="'.$args['selected'].'"', '</ul>', '<li><label><input type="radio" name="attribute_pa_sizes"', '</label></li>'),
					$html
				);
				$html = $old_html.$new_html;
			}
		}

		return $html;
	}

	// Меняем параметры виджета категорий для установки сортировки
	public function electro_product_categories_widget_top_level_list_categories_args($args){
		//Functions::_debug($args);

		$args['orderby'] = 'order';
		$args['order'] = 'ASC';

		$args['meta_query'] = [[
			'key' => 'order',
			'type' => 'NUMERIC',
		]];

		return $args;
	}

	// Сортировка атрибутов товара в админке
	public function woocommerce_attribute_taxonomies($raw_attribute_taxonomies){
    	global $wpdb;

    	if(isset($_SERVER['QUERY_STRING'])){
		    if(strstr($_SERVER['QUERY_STRING'], 'product_attributes') !== false){
			    $raw_attribute_taxonomies = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name != '' ORDER BY attribute_label ASC;");
			    set_transient('wc_attribute_taxonomies', $raw_attribute_taxonomies);
		    }
	    }

		return $raw_attribute_taxonomies;
	}

    /**
     * [filterExample description]
     * @return [type] [description]
     */
    public function body_class($classes, $class){
    	global $post;

	    $classes[] = Functions::$device;
	    $classes[] = Functions::$device_os;
	    $classes[] = Functions::$device_name;
	    $classes[] = Functions::$device_version;
	    $classes[] = Functions::$device_browser;

	    if(is_object($post)){
		    $classes[] = $post->post_type;
	    }

	    $uri = trim($_SERVER['REQUEST_URI'], '/');
	    if(!empty($uri)){
	    	if(strstr($uri, '?') !== false){
			    $a = explode('?', $uri);
			    $uri = $a[0];
		    }
	    	//$a = explode('/', $uri);
		    //$classes[] = $a[0];
		    $classes[] = str_replace('/', ' ', $uri);
	    }else{
		    $classes[] = 'homepage';
	    }

        //Functions::_debug($uri);
        //Functions::_debug($class);
        //Functions::_debug($classes); exit;

        return $classes;
    }

	/**
	 * @param $post_templates
	 *
	 * @return mixed
	 */
	public function redefine_page_templates($post_templates){
		if(is_dir(THEME_DIR.PAGE_TEMPLATES_PATH)){
			$templates_files = array_diff(scandir(THEME_DIR.PAGE_TEMPLATES_PATH), array(".", ".."));
			if(!empty($templates_files)){
				//$post_templates = array();
				foreach($templates_files as $file){
					if(!is_dir(THEME_DIR.PAGE_TEMPLATES_PATH.$file)){
						$post_templates[PAGE_TEMPLATES_PATH.$file] = Functions::_get_file_description(THEME_DIR.PAGE_TEMPLATES_PATH.$file);
					}
				}
			}
		}

		return $post_templates;
	}

	/**
	 * @param $post_templates
	 *
	 * @return mixed
	 */
	public function redefine_post_templates($post_templates){
		$templates_files = array_diff(scandir(THEME_DIR.POST_TEMPLATES_PATH), array(".", ".."));
		if(!empty($templates_files)){
			$post_templates = array();
			foreach($templates_files as $file){
				if(!is_dir(THEME_DIR.POST_TEMPLATES_PATH.$file)){
					$post_templates[POST_TEMPLATES_PATH.$file] = Functions::_get_file_description(THEME_DIR.POST_TEMPLATES_PATH.$file);
				}
			}
		}

		return $post_templates;
	}

	public function redefine_dynamic_sidebar_params($params){
		//Functions::_debug($params);
		$params['before_widget'] = '<div id="quick-browsing" class="mb-4"><div class="quick-browsing">';
		$params['after_widget'] = '</div></div>';
		$params['before_title'] = '<h2>';
		$params['after_title'] = '</h2>';

		return $params;
	}

	/** Default: null - defaults to below Comments
		5 - below Posts
		10 - below Media
		15 - below Links
		20 - below Pages
		25 - below comments
		60 - below first separator
		65 - below Plugins
		70 - below Users
		75 - below Tools
		80 - below Settings
		100 - below second separator
	 */
	public function custom_menu_order($menu_order){
		#Functions::_debug($menu_order);

		// define your new desired menu positions here
		// for example, move 'upload.php' to position #9 and built-in pages to position #1
		$new_positions = array(
			'upload.php' => 1,
			'edit.php?post_type=page' => 1,
			'wpcf7' => 13,
			'edit-comments.php' => 14,
		);
		// helper function to move an element inside an array
		function move_element(&$array, $a, $b) {
			$out = array_splice($array, $a, 1);
			array_splice($array, $b, 0, $out);
		}
		// traverse through the new positions and move
		// the items if found in the original menu_positions
		foreach( $new_positions as $value => $new_index ) {
			if( $current_index = array_search( $value, $menu_order ) ) {
				move_element($menu_order, $current_index, $new_index);
			}
		}

		#Functions::_debug($menu_order);
		return $menu_order;
	}

	public function edit_columns_page($columns){
		$columns = array(
			'cb'             => '<input type="checkbox" />',
			'title'          => __('Name', THEME_TD),
			'template'       => __('Template', THEME_TD),
			'author'         => __('Author', THEME_TD),
			'featured-image' => __('Image', THEME_TD),
			'date'           => __('Date', THEME_TD),
		);

		return $columns;
	}

	public function edit_columns_post($columns){
		$columns = array(
			'cb'             => '<input type="checkbox" />',
			'featured-image' => __('Image', THEME_TD),
			'title'          => __('Title', THEME_TD),
			'categories'     => __('Categories', THEME_TD),
			'author'         => __('Author', THEME_TD),
			'date'           => __('Date', THEME_TD),
		);

		return $columns;
	}

	public function edit_columns_product($columns){
		$new_columns = array();
		#Functions::_debug($columns);
		foreach($columns as $k => $v){
			$new_columns[$k] = $v;
			if($k == 'name'){
				$new_columns['product-code'] = __('Product code', THEME_TD);
			}
			if($k == 'date'){
				$new_columns['date-modified'] = __('Date (modify)', THEME_TD);
			}
			if($k == 'taxonomy-suppliers'){
				$new_columns[$k] = __('Suppliers', THEME_TD);
			}
		}

		return $new_columns;
	}

	public function edit_columns_acf_field_group($columns){
		$columns = array(
			'cb'             => '<input type="checkbox" />',
			'title'          => __('Title', THEME_TD),
			'acf-fg-description'          => __('Description', THEME_TD),
			'location'          => __('Location', THEME_TD),
			'acf-fg-status'           => '<i class="acf-icon -dot-3 small acf-js-tooltip" title="Status"></i>',
			'acf-fg-count'          => __('Fields', THEME_TD),
		);

		return $columns;
	}
	
	public function product_set_sortable_columns($columns){
		$columns['date-modified'] = 'post_modified';
		
		return $columns;
	}

	public function post_type_permalink($permalink, $post){
		// выходим если это не наш тип записи: без холдера %products%
		if(strpos($permalink, '%event-cat%') === false){
			return $permalink;
		}

		// Получаем элементы таксы
		$terms = get_the_terms($post, 'event-cat');
		// если есть элемент заменим холдер
		if(!is_wp_error($terms) && !empty($terms) && is_object($terms[0])){
			$taxonomy_slug = $terms[0]->slug;
		}else{// элемента нет, а должен быть...
			$taxonomy_slug = 'no-event-cat';
		}

		return str_replace('%event-cat%', $taxonomy_slug, $permalink);
	}

	public function get_meta_sql($args){
		global $wpdb;

		$prefix = $wpdb->prefix;

		if(isset($args['where']) && !empty($args['where'])){
			if(strstr($args['where'], 'event_datetime') !== false){
				if(isset($_REQUEST['events_month']) && isset($_REQUEST['events_year'])){

					$where = array();
					if(intval($_REQUEST['events_year']) > 0){
						$where[] = "({$prefix}postmeta.meta_key = 'event_datetime' AND YEAR({$prefix}postmeta.meta_value) = '".$_REQUEST['events_year']."')";
					}

					if(intval($_REQUEST['events_month']) > 0){
						$where[] = "({$prefix}postmeta.meta_key = 'event_datetime' AND MONTH({$prefix}postmeta.meta_value) = '".$_REQUEST['events_month']."')";
					}

					if(!empty($where)){
						$args['where'] = "AND (".implode(' AND ', $where).")";
					}
				}
			}
		}
		#Functions::_debug($args);

		return $args;
	}

	public function pre_option_posts_per_page($default, $option){
		#Functions::_debug($option);
		return 30;
	}

	public function megamenu_output_public_toggle_block_spacer($html, $block){
		if($block['type'] == 'spacer' && $block['align'] == 'right' && Functions::$device != 'desktop'){
			//Functions::_debug($block);
			$contacts_n_search = get_field('contacts_n_search', 'option');
			switch($block['width']){
				case 1:
					$phone_number = $contacts_n_search['phone_number'];
					$phone_icon = $contacts_n_search['phone_icon_mobile'];
					$html = '<a class="phone-number" role="button" href="tel:'.str_replace(' ', '', $phone_number).'"><img src="'.$phone_icon.'" class="icon"></a>';
					break;
				case 2:
					$search_icon = $contacts_n_search['search_icon_mobile'];
					$html = '<form class="search trans_me" action="" method="get"><input type="text" name="search" value="" placeholder="Search"><a role="button" class="search-toggle-close"></a></form>
						<a class="search-toggle" role="button" href="javascript:;"><img src="'.$search_icon.'" class="icon"></a>';
					break;
			}
		}

		return $html;
	}

	/**
	 * Save and Close
	 * Generates the URL to redirect to
	 * @param $location The redirect location (we're overwriting this)
	 * @return string The new URL to redirect to, which should be the post listing page of the relevant post type
	 */
	public static function redirect_post_location($location){
		if(!isset($_POST['save-close'])){
			return $location;
		}
		//WCPL_Helper::_debug($_POST['saveclose_referer']); exit;
		// determine the post status (private if selected, else published)
		//$post_status = ($_POST['post_status'] == 'private') ? 'private' : 'publish';

		// we want to publish new posts
		$post_status = 'publish';

		if($_POST['save-close'] == esc_attr__('Save and Close', THEME_TD)){
			if($_POST['original_post_status'] == 'publish' || $_POST['original_post_status'] == 'private' || $_POST['original_post_status'] == 'draft' || $_POST['original_post_status'] == 'pending'){
				$post_status = $_POST['post_status'];
			}
		}else{
			// if the post was published, allow the status to be changed to something else (eg. draft)
			if($_POST['original_post_status'] == 'publish' || $_POST['original_post_status'] == 'private'){
				$post_status = $_POST['post_status'];
			}
		}
		// handle private post visibility
		if($_POST['post_status'] == 'private'){
			$post_status = 'private';
		}

		wp_update_post(array('ID' => $_POST['post_ID'], 'post_status' => $post_status));

		// if we have an HTTP referer saved, and it's a post listing page, redirect back to that (maintains pagination, filters, etc.)
		if(isset($_POST['saveclose_referer']) && strstr($_POST['saveclose_referer'], 'edit.php') !== false){
			$_POST['saveclose_referer'] = str_replace('s=', '', $_POST['saveclose_referer']);
			if(strstr($_POST['saveclose_referer'], 'product_cat') === false){
				$paged = Functions::get_product_page_position($_POST['post_ID']);
				$_POST['saveclose_referer'] .= '&paged='.$paged;
			}
			if(strstr($_POST['saveclose_referer'], 'lbsmessage') === false){
				if(strstr($_POST['saveclose_referer'], '?') === false){
					return $_POST['saveclose_referer'].'?lbsmessage=1';
				}

				return $_POST['saveclose_referer'].'&lbsmessage=1';
			}

			return $_POST['saveclose_referer'];
		}else{// no referer saved, just redirect back to the main post listing page for the post type
			//if(strstr($_POST['saveclose_referer'], 'page=wcpl') !== false){
			$paged = Functions::get_product_page_position($_POST['post_ID']);
			//}
			return get_admin_url().'edit.php?lbsmessage=1&post_type='.$_POST['post_type'].'&paged='.$paged.'&post='.$_POST['post_ID'];
		}
	}

	/**
	 * Для сортировки изображений в медиа-библиотеке
	 * @param $query
	 *
	 * @return mixed
	 */
	public static function ajax_query_attachments_args($query){
		#Functions::_debug($query);
		$query['orderby'] = 'ID';
		$query['order'] = 'DESC';

		return $query;
	}


}
