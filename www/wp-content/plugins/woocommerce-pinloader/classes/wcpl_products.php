<?php


namespace Pinloader;

use WC_Admin_Duplicate_Product;
use WC_Product;
use WC_Product_Variable;
use WP_Screen;
use Pinloader\WCPL_Data_Source;

class WCPL_Products{

	public static function initialise(){
		$self = new self();

		add_action('init', array($self, 'init'), 0);
		add_action('delete_post', array($self, 'delete_post'), 10);

		add_filter('posts_join', array($self, 'custom_search_join'), 10, 2);
		add_filter('posts_where', array($self, 'custom_search_where'), 500, 2);
		add_filter('posts_distinct', array($self, 'custom_search_distinct'));

		//add_action('pre_get_posts', array($self, 'pre_get_posts_global'), 10);
		//add_action('pre_get_posts', array($self, 'product_meta_search'), 20);
		
		if(!is_admin()){
			add_action('template_redirect', array($self, 'template_redirect'), 1);
		}

		add_action('save_post', array($self, 'save_post_product'), 20, 3);
		add_action('after_delete_post', array($self, 'after_delete_post'), 20, 1);
		//add_action('updated_post_meta', array($self, 'format_specifications_meta'), 99, 4);
		//add_action('added_post_meta', array($self, 'format_specifications_meta'), 99, 4);
		//add_action('woocommerce_after_product_object_save', array($self, 'woocommerce_after_product_object_save'), 99, 2);

		//add_action('get_terms_args', array($self, 'get_terms_args'), 10, 4);
		add_filter('electro_wc_live_search_query_args', array($self, 'electro_wc_live_search_query_args'), 10, 1);
	}

	public function init(){}

	public static function sync_mod_products_single($product_id, $product = null){

		if(is_null($product)){
			$_sql = "SELECT p.ID AS products_id, 
       				p.post_title AS products_name, 
       				p.post_status AS product_status, 
       				s.id_supplier AS id_supplier, 
       				s.name_supplier AS name_supplier, 
       				pm.meta_value AS products_model, 
       				pm2.meta_value AS products_price, 
       				pm3.meta_value AS products_filled 
				 FROM ml_posts p 
				 LEFT JOIN ml_postmeta pm ON pm.post_id = p.ID 
				 LEFT JOIN ml_postmeta pm2 ON pm2.post_id = p.ID 
			     LEFT JOIN ml_postmeta pm3 ON pm3.post_id = p.ID AND pm3.meta_key = 'products_filled_via_ym' 
			     LEFT JOIN ml_term_relationships tr ON tr.object_id = p.ID 
			     LEFT JOIN ml_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id 
			     LEFT JOIN mod_suppliers s ON s.term_id = tt.term_id 
				 WHERE p.ID = ".$product_id." AND tt.taxonomy = 'suppliers' AND pm.meta_key = 'products_model' AND pm2.meta_key = '_regular_price'";

			$product = WCPL_Data_Source::get_row($_sql);
		}
		#WCPL_Helper::_debug($product); exit;

		if(!empty($product->products_id) && !empty($product->products_name) && !empty($product->id_supplier)){
			$_sql = "INSERT IGNORE INTO mod_products (products_id, products_name, products_status, id_supplier, name_supplier, products_model, products_price, products_filled) 
				 VALUES (
			         ".$product->products_id.", 
			         '".WCPL_Data_Source::db_input($product->products_name)."',
			         '".$product->product_status."', 
			         ".$product->id_supplier.", 
			         '".$product->name_supplier."', 
			         '".$product->products_model."', 
			         '".$product->products_price."', 
			         '".$product->products_filled."'
			     ) 
				 ON DUPLICATE KEY UPDATE 
				    products_name = '".WCPL_Data_Source::db_input($product->products_name)."',
				    products_status = '".$product->product_status."',
				    id_supplier = ".$product->id_supplier.",
				    name_supplier = '".$product->name_supplier."',
				    products_model = '".$product->products_model."',
				    products_price = '".$product->products_price."',
				    products_filled = '".$product->products_filled."'";

			WCPL_Data_Source::query($_sql);
		}
	}
	
	/**
	 * Перенаправление на активный товар из группы товаров
	 */
	public static function template_redirect(){
		global $wp_rewrite, $is_IIS, $wp_query, $wpdb, $wp;
		
		$is_product = (strstr($_SERVER['REQUEST_URI'], '/product/') !== false) ? true : false;
		$redirect_location = '';
		$status = 302;
		
		if($is_product && is_404()){
			$_sql = isset($wp_query->request) ? $wp_query->request : '';
			if(!empty($_sql)){
				$product = WCPL_Data_Source::get_row($_sql);
				if($product->post_status != 'publish'){
					$redirect_location = WCPL_Data_Source::get_active_product_url_from_group($product->ID);
					#WCPL_Helper::_debug($redirect_location, 1); exit;
				}
			}
			
			if(!empty($redirect_location)){
				wp_redirect($redirect_location, $status);
				exit;
			}
		}
	}
	
	
	// for WP & WC
	public static function delete_post($post_id){
		$post_type = get_post_type($post_id);

		switch($post_type){
			case 'product':
			case 'product_variation':
				$data['table']   = 'mod_products_new';
				$data['primary'] = array('id' => $post_id, 'name' => 'products_id');
				WCPL_Data_Source::delete_data($data);
				$data['table']   = 'mod_products_price';
				$data['primary'] = array('id' => $post_id, 'name' => 'products_id');
				WCPL_Data_Source::delete_data($data);
				break;
			case 'shop_order':
				break;
		}

	}

	public function save_post_product($post_ID, $post, $update){
		if($post->post_type == 'product'){
			#WCPL_Helper::_debug($_POST); exit;

			$is_specification_formated = 0;
			$sprcification_from_ym = 0;
			$sprcification_formating_variant = 3;

			if(isset($_POST['_specifications']) && isset($_POST['acf'])){
				$_specifications = $_POST['_specifications'];

				$_sql = "SELECT meta_key, meta_value FROM prefix_postmeta WHERE post_id = ".$post_ID." AND meta_key IN ('_products_filled_via_ym', '_sprcification_from_ym', '_sprcification_formating_variant')";
				$results = WCPL_Data_Source::get_results($_sql);
				if(!is_null($results)){
					foreach($results as $result){
						if($result->meta_key == '_products_filled_via_ym'){
							$field_products_filled_via_ym = $result->meta_value;
						}
						if($result->meta_key == '_sprcification_from_ym'){
							$field_sprcification_from_ym = $result->meta_value;
						}
						if($result->meta_key == '_sprcification_formating_variant'){
							$field_sprcification_formating_variant = $result->meta_value;
						}
					}

					#WCPL_Helper::_debug([$field_products_filled_via_ym, $field_sprcification_from_ym, $_POST]); exit;

					$is_specification_formated = isset($_POST['acf'][$field_products_filled_via_ym]) ? $_POST['acf'][$field_products_filled_via_ym] : 0;
					$sprcification_from_ym = isset($_POST['acf'][$field_sprcification_from_ym]) ? $_POST['acf'][$field_sprcification_from_ym] : 0;
					$sprcification_formating_variant = isset($_POST['acf'][$field_sprcification_formating_variant]) ? $_POST['acf'][$field_sprcification_formating_variant] : $sprcification_formating_variant;
				}
			}elseif(class_exists('ACF')){
				$_specifications = get_field('_specifications', $post_ID);
				$is_specification_formated = get_field('products_filled_via_ym', $post_ID);
				$sprcification_from_ym = get_field('sprcification_from_ym', $post_ID);
			}else{
				$_specifications = get_post_meta($post_ID, '_specifications', true);
				$is_specification_formated = get_post_meta($post_ID, 'products_filled_via_ym', true);
				$sprcification_from_ym = get_post_meta($post_ID, 'sprcification_from_ym', true);
			}

			#WCPL_Helper::_debug([$is_specification_formated, $sprcification_from_ym]); exit;

			if(!$is_specification_formated){
				if($sprcification_from_ym){
					switch($sprcification_formating_variant){
						case 1:
							update_post_meta($post_ID, '_specifications', WCPL_Yandex_Market::format_specifications_meta($_specifications));
							break;
						case 2:
							update_post_meta($post_ID, '_specifications', WCPL_Yandex_Market::format_specifications_meta2($_specifications));
							break;
						case 3:
						default:
							update_post_meta($post_ID, '_specifications', WCPL_Yandex_Market::format_specifications_meta3($_specifications));
							break;
					}
					update_post_meta($post_ID, 'sprcification_from_ym', 1);
				}else{
					update_post_meta($post_ID, '_specifications', WCPL_Yandex_Market::format_specifications_meta2($_specifications));
				}
				update_post_meta($post_ID, 'products_filled_via_ym', 1);
			}
		}

		self::sync_mod_products_single($post_ID);

		$data['table'] = "mod_products";
		$data['primary'] = array('id' => $post_ID, 'name' => 'products_id');
		$data['yoast_seo'] = 0;
		WCPL_Data_Source::update_data($data);

		// Обновляем закэшированные файлы
		WCPL_Data_Source::get_exclude_product_ids_from_groups(false);

	}

	public function after_delete_post($post_id){
		$data['table']   = 'mod_products';
		$data['primary'] = array('id' => $post_id, 'name' => 'products_id');
		WCPL_Data_Source::delete_data($data);
	}

	public static function get_terms_args($args, $taxonomies){
		if($taxonomies[0] == $_GET['taxonomy']){
			WCPL_Helper::_debug($args);
			WCPL_Helper::_debug($taxonomies);
		}
		return $args;
	}

	// for WC

	// @param string $type = simple | grouped | external | variable
	public static function add_product($type = "simple", $args){
		switch($type){
			case "variable":
				$objProduct = new WC_Product_Variable();
				break;
			case "simple":
			default:
				$objProduct = new WC_Product();
				break;
		}
		$objProduct->set_name($args['product_title']);
		$objProduct->set_status("publish");  // can be publish,draft or any wordpress post status
		$objProduct->set_catalog_visibility('visible'); // add the product visibility status
		$objProduct->set_description($args['product_description']);
		$objProduct->set_sku($args['product_sku']); //can be blank in case you don't have sku, but You can't add duplicate sku's
		$objProduct->set_price($args['product_price']); // set product price
		$objProduct->set_regular_price($args['product_regular_price']); // set product regular price
		$objProduct->set_manage_stock(true); // true or false
		$objProduct->set_stock_quantity($args['product_quantity']);
		$objProduct->set_stock_status('instock'); // in stock or out of stock value
		$objProduct->set_backorders('no');
		$objProduct->set_reviews_allowed(true);
		$objProduct->set_sold_individually(false);
		//$objProduct->set_category_ids(array(1,2,3)); // array of category ids, You can get category id from WooCommerce Product Category Section of Wordpress Admin
		$product_id = $objProduct->save(); // it will save the product and return the generated product id

		if(isset($args['terms']) && !empty($args['terms'])){
			foreach($args['terms'] as $k => $term){
				wp_set_object_terms($product_id, intval(current($term)), key($term));
			}
		}

		if(isset($args['metas']) && !empty($args['metas'])){
			foreach($args['metas'] as $k => $meta){
				update_post_meta($product_id, key($meta), current($meta));
			}
		}

		$objProduct->set_sku(str_replace('+', $product_id, $args['product_sku']));
		$objProduct->save();

		return $product_id;

	}

	public static function update_product($product_id, $objProduct, $args){

		$objProduct->set_name($args['product_title']);
		$objProduct->set_status("publish");
		$objProduct->set_catalog_visibility('visible');
		//$objProduct->set_description($args['product_description']);
		$objProduct->set_sku($args['product_sku']);
		$objProduct->set_price($args['product_price']);
		$objProduct->set_regular_price($args['product_regular_price']);
		$objProduct->set_manage_stock(true);
		$objProduct->set_stock_quantity($args['product_quantity']);
		$objProduct->set_stock_status('instock');
		$objProduct->set_backorders('no');
		$objProduct->set_reviews_allowed(true);
		$objProduct->set_sold_individually(false);
		//$objProduct->set_category_ids(array(1,2,3));
		$objProduct->save();

		if(isset($args['terms']) && !empty($args['terms'])){
			foreach($args['terms'] as $k => $term){
				wp_set_object_terms($product_id, intval(current($term)), key($term));
			}
		}

		if(isset($args['metas']) && !empty($args['metas'])){
			foreach($args['metas'] as $k => $meta){
				update_post_meta($product_id, key($meta), current($meta));
			}
		}
	}

	public static function update_product_metas($src_product_id, $dst_product_id, $metas){
		$exclude_metas = array('_sale_price', '_sale_price_dates_from', '_sale_price_dates_to');
		foreach($metas as $meta){
			if(!in_array($meta, $exclude_metas)){
				$meta_value = get_post_meta($src_product_id, $meta, true);
				update_post_meta($dst_product_id, $meta, $meta_value);
			}
		}
	}

	public static function duplicate_product($product_id, $args, $group_id){
		$product = wc_get_product($product_id);
		if(false === $product){
			wp_die(sprintf(__('Product creation failed, could not find original product: %s', 'woocommerce'), $product_id));
		}
		$WC_Admin_Duplicate_Product = new WC_Admin_Duplicate_Product();
		$duplicate                  = $WC_Admin_Duplicate_Product->product_duplicate($product);
		$duplicate_id               = $duplicate->get_id();
		// Hook rename to match other woocommerce_product_* hooks, and to move away from depending on a response from the wp_posts table.
		do_action('woocommerce_product_duplicate', $duplicate, $product);

		//if($group_id){ // Замена Заголовка товара при добавлении и дублировании одновременно.
			$args['product_title'] = str_replace(' (Копировать)', '', $duplicate->get_name());
		//}
		$args['product_sku'] = str_replace('+', $duplicate_id, $args['product_sku']);

		self::update_product($duplicate_id, $duplicate, $args);
		self::update_product_metas($product_id, $duplicate_id, array('_specifications', 'products_filled_via_ym'));

		return $duplicate_id;
	}

	public static function woocommerce_after_product_object_save($product){}

	// Search on custom fields
	public static function custom_search_join($join, $wp_query){
		global $pagenow, $wpdb;
		$types = ['product'];
		// I want the filter only when performing a search on edit page of Custom Post Type in $types array
		if(isset($_GET['s']) || isset($wp_query->query['s'])){
			if($wp_query->is_main_query() || $wp_query->query['post_type'] == 'product'){
				$join .= 'LEFT JOIN '.$wpdb->postmeta.' wcpl_pm ON '.$wpdb->posts.'.ID = wcpl_pm.post_id ';
				#WCPL_Helper::_debug($join);
			}
		}

		return $join;
	}

	public static function custom_search_where($where, $wp_query){
		global $pagenow, $wpdb;

		#WCPL_Helper::_debug($_REQUEST);
		#WCPL_Helper::_debug($wp_query);
		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'main';
		$types = ['product'];
		$str = '';

		if(isset($_GET['s']) && !empty($_GET['s'])){
			$str = $_GET['s'];
		}elseif(isset($wp_query->query['s'])){
			$str = $wp_query->query['s'];
		}
		$str = trim($str);

		#WCPL_Helper::_debug($str);
		$find_by_sku = false;

		if(!empty($str)){
			if(substr($str, 0, 1) == '*'){
				$find_by_sku = true;
				$str = str_replace('*', '', $str);
			}
			//$where = preg_replace("/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/", "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where);
			if($wp_query->is_main_query() || $wp_query->query['post_type'] == 'product'){
				//$where .= " OR (REPLACE({$wpdb->posts}.post_title, ' ', '') LIKE '%".$str."%')";
				if(is_admin() && $action != 'products_live_search'){
					$where .= " OR (wcpl_pm.meta_key = 'products_model' AND wcpl_pm.meta_value LIKE '%".$str."%')";
				}
				// Поиск по спецификациям
				if(!is_admin() && $action != 'products_live_search'){
					//$where .= " OR (wcpl_pm.meta_key = '_specifications' AND wcpl_pm.meta_value LIKE '%".$str."%')";
				}
				// end
				if($action == 'products_live_search'){
					$where = preg_replace("/OR \(\s*".$wpdb->posts.".post_excerpt\s+LIKE\s*(\'[^\']+\')\s*\)/", "", $where);
					$where = preg_replace("/OR \(\s*".$wpdb->posts.".post_content\s+LIKE\s*(\'[^\']+\')\s*\)/", "", $where);
				}
				if($find_by_sku){
					$where .= " OR (wcpl_pm.meta_key = '_sku' AND wcpl_pm.meta_value LIKE '%".$str."%')";
				}
				#WCPL_Helper::_debug($where);
			}
		}

		return $where;
	}

	public static function custom_search_distinct($where){
		global $pagenow, $wpdb;
		$types = ['product'];
		if(is_admin() && $pagenow == 'edit.php' && in_array($_GET['post_type'], $types) && isset($_GET['s'])){
			return "DISTINCT";
		}

		return $where;
	}
	// End Search on custom fields

	// Не используется
	public static function product_meta_search($query){
		global $wp_query;

		// use your post type
		$post_type = 'product';
		// Use your Custom fields/column name to search for
		$custom_fields = array(
			"products_model",
			"_specifications",
		);

		if(!is_admin()){
			//return;
		}

		if($query->query['post_type'] != $post_type){
			//return;
		}

		$search_term = $_GET['s'];

		// Set to empty, otherwise it won't find anything
		//$query->query_vars['s'] = '';

		if($search_term != ''){
			$meta_query = array('relation' => 'OR');

			foreach($custom_fields as $custom_field){
				array_push($meta_query, array(
					'key'     => $custom_field,
					'value'   => $search_term,
					'compare' => 'LIKE'
				));
			}

			$query->set('meta_query', $meta_query);
			$query->meta_query = new \WP_Meta_Query($meta_query);
		}
		//WCPL_Helper::_debug($search_term);
		//WCPL_Helper::_debug($wp_query);
		return $query;
	}

	public static function pre_get_posts_global($query){
		if(!is_admin()){
			WCPL_Helper::_debug($query->query);
			/*if($query->is_search() || $query->is_main_query() || $query->is_tax()){
				if(isset($query->query['post_type']) || isset($query->query['product_cat'])){
					if((isset($query->query['post_type']) && $query->query['post_type'] == 'product') || (isset($query->query_vars['wc_query']) && $query->query_vars['wc_query'] == 'product_query')){
						#WCPL_Helper::_debug($query);
						$exclude_product_ids = WCPL_Data_Source::get_exclude_product_ids_from_groups(true);
						//WCPL_Helper::_debug($exclude_product_ids);
						if(!empty($exclude_product_ids)){
							$query->set('post__not_in', $exclude_product_ids);
							#WCPL_Helper::_debug($query->query);
						}
						//WCPL_Helper::_debug($query->query['post_type']);
					}
				}
			}*/
		}
	}

	public static function electro_wc_live_search_query_args($args){
		/*$exclude_product_ids = WCPL_Data_Source::get_exclude_product_ids_from_groups(true);
		if(!empty($exclude_product_ids)){
			$args['post__not_in'] = $exclude_product_ids;
		}*/
		$args['post_status'] = 'publish';

		return $args;
	}

}
