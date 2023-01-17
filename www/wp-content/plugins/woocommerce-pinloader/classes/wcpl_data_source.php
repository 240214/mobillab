<?php
namespace Pinloader;

use WC_Admin_Duplicate_Product;
use WC_Product;
use WC_Product_Variable;
use Pinloader\WCPL_Products;

class WCPL_Data_Source {

	public $errorCode = 0;
	private static $db;
	public $options;
	public static $tables = array(
		'mod_products',
		'mod_diapasons',
		'mod_monitoring_data',
		'mod_products_monitor_price',
		'mod_products_new',
		'mod_products_group',
		'mod_products_price',
		'mod_products_price_change',
		'mod_products_to_switch_on',
		'mod_soho_current',
		'mod_soho_prev',
		'mod_soho_temp',
		'mod_suppliers',
		'mod_temp',
	);

	public static function initialise(){
		global $wpdb;
		$self = new self();
		//$self->db = $wpdb;

		foreach($self::$tables as $table){
			$wpdb->{$table} = $table;
		}

		add_action('init', array($self, 'init'), 0);

		//add_action('delete_post', array($self, 'delete_post'), 10);

		add_action('wp_ajax_supplier_save_request', array($self, 'ajax_supplier_save_request'));
		add_action('wp_ajax_product_add_to_shop_request', array($self, 'ajax_product_add_to_shop_request'));
		add_action('wp_ajax_products_add_to_shop_request', array($self, 'ajax_products_add_to_shop_request'));
		add_action('wp_ajax_product_ignore_request', array($self, 'ajax_product_ignore_request'));
		add_action('wp_ajax_products_ignore_request', array($self, 'ajax_products_ignore_request'));
		add_action('wp_ajax_autocomplete_get_products_request', array($self, 'ajax_autocomplete_get_products_request'));
		add_action('wp_ajax_change_product_nacenka_request', array($self, 'ajax_change_product_nacenka_request'));
		add_action('wp_ajax_change_product_nacenka_all_manual_request', array($self, 'ajax_change_product_nacenka_all_manual_request'));
		add_action('wp_ajax_change_product_nacenka_all_auto_request', array($self, 'ajax_change_product_nacenka_all_auto_request'));
		add_action('wp_ajax_switchon_one_request', array($self, 'ajax_switchon_one_request'));
		add_action('wp_ajax_switchon_all_manual_request', array($self, 'ajax_switchon_all_manual_request'));
		add_action('wp_ajax_switchon_all_auto_request', array($self, 'ajax_switchon_all_auto_request'));
		add_action('wp_ajax_switchoff_one_request', array($self, 'ajax_switchoff_one_request'));
		add_action('wp_ajax_switchoff_all_request', array($self, 'ajax_switchoff_all_request'));
		add_action('wp_ajax_restore_one_request', array($self, 'ajax_restore_one_request'));
		add_action('wp_ajax_restore_all_request', array($self, 'ajax_restore_all_request'));
		add_action('wp_ajax_change_price_request', array($self, 'ajax_change_price_request'));
		add_action('wp_ajax_monitor_change_color_request', array($self, 'ajax_monitor_change_color_request'));
		add_action('wp_ajax_group_products_request', array($self, 'ajax_group_products_request'));
		add_action('wp_ajax_ungroup_products_request', array($self, 'ajax_ungroup_products_request'));
		add_action('wp_ajax_update_ymf_prices_request', array($self, 'ajax_update_ymf_prices_request'));
		add_action('wp_ajax_update_images_alt_request', array($self, 'ajax_update_images_alt_request'));

		add_action('yfym_before_construct', array($self, 'yfym_before_construct'));
		add_action('yfym_after_construct', array($self, 'yfym_after_construct'));


		#self::_update_products_title();
	}

	public function init(){
		$this->options = WCPL_Admin::get_options();
	}

	/* ------------- Begin DB methods -------------- */

	public static function insert_data($post_data){
		global $wpdb;

		//CFM_Helper::log('[function '.__FUNCTION__.'] is called');

		$id = 0;

		if(!empty($post_data) && isset($post_data['table'])){

			$db_table = str_replace('prefix_', $wpdb->prefix, $post_data['table']);
			unset($post_data['table'], $post_data['primary']);

			if($wpdb->insert($db_table, $post_data)){
				$id = $wpdb->insert_id;
			}

			unset($db_table, $post_data);
			//CFM_Helper::log('[function '.__FUNCTION__.']: posts updated');
		}

		return $id;
	}

	public static function delete_data($post_data){
		global $wpdb;
		$ret = false;

		if(!empty($post_data) && isset($post_data['table'])){
			$id = intval($post_data['primary']['id']);
			$name = $post_data['primary']['name'];
			$db_table = $post_data['table'];
			$db_table = str_replace('prefix_', $wpdb->prefix, $db_table);
			unset($post_data['table'], $post_data['primary']);

			$ret = $wpdb->delete($db_table, array($name => $id));

			unset($db_table, $post_data);
		}

		return $ret;
	}

	public static function update_data($post_data){
		global $wpdb;

		#WCPL_Helper::log(__METHOD__);

		$id = 0;

		if(!empty($post_data) && isset($post_data['table'])){

			$id = intval($post_data['primary']['id']);
			$name = $post_data['primary']['name'];
			$db_table = $post_data['table'];
			$db_table = str_replace('prefix_', $wpdb->prefix, $db_table);
			unset($post_data['table'], $post_data['primary']);

			if($id == 0){
				if($wpdb->insert($db_table, $post_data)){
					$id = $wpdb->insert_id;
				}
			}else{
				$wpdb->update($db_table, $post_data, array($name => $id));
			}

			unset($db_table, $post_data);
			#WCPL_Helper::log('- posts updated');
		}

		return $id;
	}

	public static function query($sql){
		global $wpdb;
		$sql = str_replace('prefix_', $wpdb->prefix, $sql);
		return $wpdb->query($sql);
	}

	public static function get_var($sql){
		global $wpdb;

		$sql = str_replace('prefix_', $wpdb->prefix, $sql);

		return $wpdb->get_var($sql);
	}

	public static function get_row($sql, $output = OBJECT){
		global $wpdb;

		$sql = str_replace('prefix_', $wpdb->prefix, $sql);

		return $wpdb->get_row($sql, $output);
	}

	public static function get_col($sql){
		global $wpdb;

		$sql = str_replace('prefix_', $wpdb->prefix, $sql);

		return $wpdb->get_col($sql);
	}

	public static function get_results($sql, $output = OBJECT){
		global $wpdb;

		$sql = str_replace('prefix_', $wpdb->prefix, $sql);

		return $wpdb->get_results($sql, $output);
	}

	public static function get_found_rows(){
		return self::get_var("SELECT FOUND_ROWS()");
	}

	public static function db_input($string){
		global $wpdb;
		return $wpdb->_real_escape($string);
	}

	/* ------------- Begin COMMON methods -------------- */

	public static function get_cpt_terms($taxonomy){
		$queried_category = get_term_by('id', get_query_var($taxonomy), $taxonomy);
		#self::_debug($queried_category);
		$terms = get_terms(array('taxonomy' => $taxonomy, 'fields' => 'all', 'hide_empty' => false));

		foreach($terms as $k => $term){
			$terms[$k]->active = 0;
			$terms[$k]->active_class = '';
			$terms[$k]->selected = '';
			if(!empty($queried_category) && $queried_category->term_id == $term->term_id){
				$terms[$k]->active = 1;
				$terms[$k]->active_class = 'active';
				$terms[$k]->selected = 'selected="selected"';
			}
		}

		return $terms;
	}

	public static function get_mod_supplier($id){
		global $wpdb;

		$result = $wpdb->get_row("SELECT * FROM $wpdb->mod_suppliers WHERE id_supplier = ".$id);

		return $result;
	}

	public static function get_mod_suppliers($array_keys = ''){
		global $wpdb;
		$ret = array();

		$results = $wpdb->get_results("SELECT * FROM $wpdb->mod_suppliers");

		if(!empty($results)){
			switch($array_keys){
				case "id":
				case "id_supplier":
					foreach($results as $result){
						$ret[$result->id_supplier] = $result;
					}
					break;
				case "term_id":
					foreach($results as $result){
						$ret[$result->term_id] = $result;
					}
					break;
				default:
					$ret = $results;
					break;
			}
		}

		return $ret;
	}

	public static function get_suppliers(){
		$ret = array();
		$suppliers = (object) array('terms' => array(), 'mods' => array());
		$suppliers->terms = self::get_cpt_terms('suppliers');
		$suppliers->mods = self::get_mod_suppliers('term_id');
		#self::_debug($suppliers);

		if(!empty($suppliers->terms)){
			foreach($suppliers->terms as $k => $supplier){
				$ret[$k] = $supplier;
				if(isset($suppliers->mods[$supplier->term_id])){
					$ret[$k]->params = $suppliers->mods[$supplier->term_id];
				}else{
					$ret[$k]->params = (object)array(
						'id_supplier' => 0,
						'term_id' => 0,
						'name_supplier' => '',
						'code' => '',
						'name' => '',
						'price' => '',
						'coefficient' => '',
					);
				}
			}
		}

		#WCPL_Helper::_debug($ret);
		return $ret;
	}

	public static function get_mod_suppliers_list($fields = "id>name"){
		global $wpdb;
		$ret = array();

		$results = $wpdb->get_results("SELECT * FROM $wpdb->mod_suppliers ORDER BY name_supplier ASC");

		if(!empty($results)){
			switch($fields){
				case "all":
					$ret = $results;
					break;
				case "id>name":
				default:
					foreach($results as $result){
						$ret[$result->id_supplier] = $result->name_supplier;
					}
					break;
			}
		}

		return $ret;
	}

	public static function get_group_products($from_cache = false){
		if($from_cache){
			$return = WCPL_Helper::get_cache_file('groupped_products_data');
			if(!empty($return)){
				return $return;
			}
		}

		$return = array('product_ids' => array(), 'group_ids' => array());

		$groups = self::get_results("SELECT * FROM mod_products_group", ARRAY_A);
		#WCPL_Helper::_debug($groups);
		$group_ids = array();
		$counts = array();
		if(!empty($groups)){
			foreach($groups as $group){
				$product_ids = explode(',', $group['product_ids']);

				// Проверяем на сущетвования реальных товаров в таблице posts. Если там нет этих товаров, то обновлем запись в таблице групп
				$post_ids = self::get_col("SELECT ID FROM prefix_posts WHERE ID IN (".$group['product_ids'].") AND post_type = 'product'");
				#WCPL_Helper::_debug($post_ids);
				if(count($post_ids) > 0){
					if(count($post_ids) != count($product_ids)){
						$data['table']       = 'mod_products_group';
						$data['primary']     = array('id' => intval($group['id']), 'name' => 'id');
						$data['product_ids'] = implode(',', $post_ids);
						self::update_data($data);
						$product_ids = $post_ids;
					}
				}else{
					$data['table']       = 'mod_products_group';
					$data['primary']     = array('id' => intval($group['id']), 'name' => 'id');
					self::delete_data($data);
					$product_ids = array();
				}
				// Конец проверки

				if(!empty($product_ids)){
					$counts[$group['id']] = count($product_ids);
					foreach($product_ids as $id){
						$group_ids[$id] = array('group_id' => $group['id'], 'color' => $group['color'], 'product_ids' => $group['product_ids']);
					}
				}
			}
			$return['product_ids'] = array_keys($group_ids);
			$return['group_ids'] = $group_ids;
			$return['counts'] = $counts;
			$return['groups'] = $groups;
		}

		WCPL_Helper::set_cache_file('groupped_products_data', $return);

		return $return;
	}

	public static function get_group_mod_products(array $product_ids, $status = ['publish']){
		$where = '';
		if(!empty($product_ids)){
			sort($product_ids);
			$where = 'AND products_id IN ('.implode(',', $product_ids).')';
		}

		$_sql = "SELECT SQL_CALC_FOUND_ROWS * FROM mod_products WHERE products_status IN('".implode("','", $status)."') $where GROUP BY products_id ORDER BY ABS(products_price) ASC";
		#WCPL_Helper::_debug($_sql); exit;
		$products = WCPL_Data_Source::get_results($_sql);

		return $products;
	}

	public static function get_exclude_product_ids_from_groups($from_cache = false){
		if($from_cache){
			$return = WCPL_Helper::get_cache_file('exclude_product_ids_from_groups');
			if(!empty($return)){
				return $return;
			}
		}
		$ret = array();
		$product_groups = self::get_group_products($from_cache);
		#WCPL_Helper::_debug($product_groups['groups']); exit;
		if(!empty($product_groups['groups'])){
			foreach($product_groups['groups'] as $group){
				$product_ids = explode(',', $group['product_ids']);
				#WCPL_Helper::_debug($product_ids);
				$vc_products = self::get_group_mod_products($product_ids, ['publish', 'pending']);

				if(!empty($vc_products) && count($vc_products) > 1){
					$_arr = array();
					#WCPL_Helper::_debug($vc_products);
					foreach($vc_products as $vc_product){
						$_arr[$vc_product->products_id] = $vc_product->products_id;
					}
					#WCPL_Helper::_debug($_arr);
					//ksort($_arr, SORT_NUMERIC);
					array_shift($_arr);
					#WCPL_Helper::_debug($_arr);
					$ret = array_merge($ret, $_arr);
					#WCPL_Helper::_debug($ret);
				}
			}
		}
		#WCPL_Helper::_debug($ret);
		#exit;

		WCPL_Helper::set_cache_file('exclude_product_ids_from_groups', $ret);

		self::update_products_pending_status($ret);

		return $ret;
	}

	public static function update_products_pending_status($products_ids){
		self::query("UPDATE prefix_posts SET post_status = 'publish' WHERE post_type = 'product' AND post_status = 'pending'");
		self::query("UPDATE mod_products SET products_status = 'publish' WHERE products_status = 'pending'");

		if(!empty($products_ids)){
			self::query("UPDATE prefix_posts SET post_status = 'pending' WHERE ID IN(".implode(',', $products_ids).")");
			self::query("UPDATE mod_products SET products_status = 'pending' WHERE products_id IN(".implode(',', $products_ids).")");
		}
	}

	public static function get_products_name($product_id, $language_id = 0) {
		global $wpdb;

		$product_name = $wpdb->get_var("SELECT post_title FROM {$wpdb->posts} WHERE ID = ".$product_id);

		return $product_name;
	}

	public static function get_price_ranges(){
		$diapasons = array();

		//$_sql = "SELECT s.diapason, s.name_supplier, d.* FROM mod_suppliers s LEFT JOIN mod_diapasons d ON d.id_supplier = s.id_supplier ORDER BY d.id_supplier, d.price_low";
		$_sql = "SELECT s.diapason, s.name_supplier, d.* FROM mod_suppliers s JOIN ml_terms t ON s.term_id = t.term_id LEFT JOIN mod_diapasons d ON d.id_supplier = s.id_supplier ORDER BY d.id_supplier, d.price_low";
		$results = self::get_results($_sql);

		if(!empty($results)){
			foreach($results as $result){
				#WCPL_Helper::_debug($result);
				if(!empty($result->id) && intval($result->diapason) == 1){
					$diapasons[$result->id_supplier][$result->id] = (array)$result;
				}
			}
		}

		return $diapasons;
	}

	public static function get_new_product($id){
		global $wpdb;
		return self::get_row("SELECT * FROM $wpdb->mod_products_new WHERE id=".$id);
	}

	public static function sync_products_prices($with_cleaning = true){
		if($with_cleaning){
			self::query("DELETE FROM mod_products_new WHERE products_id AND products_id NOT IN (SELECT products_id FROM mod_products)");
		}
		self::query("INSERT IGNORE INTO mod_products_price (products_id, new_price) SELECT products_id, price FROM mod_products_new WHERE products_id AND price != 0 ON DUPLICATE KEY UPDATE new_price = mod_products_new.price");
		self::query("UPDATE mod_products_price SET price = new_price WHERE NOT price");
	}

	public static function update_mod_products_new_ids(){
		$_sql = "UPDATE mod_products_new m, mod_products p SET m.products_id = p.products_id WHERE m.products_id = 0 AND m.id_supplier = p.id_supplier AND p.products_model = m.products_model";
		return self::query($_sql);
	}

	public static function get_mod_products($select = '*', $where = '', $limit = ''){

		if(empty($select))
			$select = '*';

		if(!empty($where))
			$where = "WHERE ".$where;

		if(!empty($limit))
			$limit = "LIMIT ".$limit;

		$results = self::get_results("SELECT ".$select." FROM mod_products ".$where." ORDER BY products_id ASC ".$limit);

		return $results;
	}

	public static function get_wc_product_meta_lookup_prices(){
		$products = [];
		$results = self::get_results("SELECT product_id, min_price, max_price FROM prefix_wc_product_meta_lookup ORDER BY product_id ASC");
		if($results){
			foreach($results as $result){
				$products[$result->product_id] = $result->min_price;
			}
		}
		unset($results);

		return $products;
	}

	public static function wc_update_product_lookup_tables_column($column = 'min_max_price'){
		WCPL_Helper::log(__METHOD__);
		#WCPL_Helper::log('PINLOADER_STOP_CRON = '.$GLOBALS['PINLOADER_STOP_CRON']);

		if($GLOBALS['PINLOADER_STOP_CRON']) return;

		#WCPL_Helper::log($column);
		wc_update_product_lookup_tables_column($column);
	}

	// Этот метод временный, для исправления названий товаров в основной таблице
	public static function _update_products_title(){
		$_sql    = "SELECT p.ID, p.post_title, mp.products_name 
				 FROM ml_posts p 
				 RIGHT JOIN mod_products mp ON p.ID = mp.products_id
				 WHERE mp.products_name != ''";
		$results = self::get_results($_sql);

		$counter = 0;
		if($results){
			foreach($results as $result){
				if($result->post_title != $result->products_name){
					self::query("UPDATE ml_posts SET post_title = '".$result->products_name."' WHERE ID = ".$result->ID);
					$counter++;
				}
			}
		}

		WCPL_Helper::_debug($counter); exit;
	}

	public static function fix_yml_prices(){
		WCPL_Helper::log(__METHOD__);

		if($GLOBALS['PINLOADER_STOP_CRON']) return [];

		$upload_dir = wp_get_upload_dir()['basedir'];
		$results = $feed_files = [];

		$files = array_diff(scandir($upload_dir), array(".", ".."));
		//$results[] = $upload_dir;

		if(!empty($files)){
			foreach($files as $file){
				if(!is_dir($upload_dir.'/'.$file) && WCPL_Helper::get_file_ext($file) == 'xml'){
					if(substr($file, 0, 8) == 'feed-yml'){
						$feed_files[] = $upload_dir.'/'.$file;
					}
				}
			}
		}

		if(!empty($feed_files)){
			$products = self::get_wc_product_meta_lookup_prices();
			foreach($feed_files as $feed_file){
				$results[basename($feed_file)] = WCPL_Yandex_Market::update_yml_feed_prices($feed_file, $products);
			}
		}

		WCPL_Admin::set_option('pinloader_update_yml_feed', 0, true);
		WCPL_Helper::log('- count = '.$results['feed-yml-0.xml']['count']);

		return $results;
	}

	public static function yfym_before_construct($status = 'false'){
		WCPL_Helper::log(__METHOD__);
		WCPL_Helper::log('- status = '.$status);

		if($status == 'full' || $status == 'cache'){
			self::cron__restore_broken_products();

		}
	}

	public static function yfym_after_construct($status = 'false'){
		WCPL_Helper::log(__METHOD__);
		WCPL_Helper::log('- status = '.$status);

		if($status == 'full' || $status == 'cache'){
			self::wc_update_product_lookup_tables_column();
			self::fix_yml_prices();
		}
	}

	public static function update_products_thumb_id(){
		WCPL_Helper::log(__METHOD__);

		$ret = ['applied' => 0, 'skipped' => 0];

		$products = self::get_mod_products('products_id', 'thumb_id = 0');

		if(!empty($products)){
			foreach($products as $product){
				$thumb_id = self::get_var("SELECT ID FROM prefix_posts WHERE post_parent = ".$product->products_id." AND post_type = 'attachment'");
				$data['table']      = 'mod_products';
				$data['primary']    = array('id' => intval($product->products_id), 'name' => 'products_id');
				if(intval($thumb_id) > 0){
					$data['thumb_id'] = $thumb_id;
					if(self::update_data($data)){
						$ret['applied']++;
					}
				}else{
					$data['thumb_id'] = '-1';
					$data['image_alt'] = '1';
					if(self::update_data($data)){
						$ret['skipped']++;
					}
				}
			}
		}

		WCPL_Helper::log(sprintf(esc_attr__('Updated products thumb_id = %s, canceled = %s', PINLOADER_TEXT_DOMAIN), $ret['applied'], $ret['skipped']));

		return $ret;
	}

	public static function update_products_thumb_alt(){
		WCPL_Helper::log(__METHOD__);

		$ret = ['created' => [], 'added' => [], 'updated' => [], 'skipped' => []];

		$products = self::get_mod_products('products_id, products_name, thumb_id', 'image_alt = 0', 10);

		if(!empty($products)){
			$ids = [];
			foreach($products as $product){

				$_sql = "SELECT pm.* FROM prefix_postmeta pm WHERE pm.post_id = ".$product->thumb_id." AND pm.meta_key = '_wp_attachment_image_alt'";
				$row = self::get_row($_sql);

				$products_name = trim($product->products_name);

				$WHERE = "WHERE post_id = ".$product->thumb_id." AND meta_key = '_wp_attachment_image_alt'";

				if(is_null($row)){
					$_sql = "INSERT INTO prefix_postmeta (post_id, meta_key, meta_value) VALUES(".$product->thumb_id.", '_wp_attachment_image_alt', '".self::db_input($products_name)."')";
					if(self::query($_sql)){
						$ret['created'][$product->thumb_id] = $products_name;
						$ids[] = $product->products_id;
					}
				}elseif(empty($row->meta_value)){
					$_sql = "UPDATE prefix_postmeta SET meta_value = '".self::db_input($products_name)."' ".$WHERE;
					if(self::query($_sql)){
						$ret['added'][$product->thumb_id] = $products_name;
						$ids[] = $product->products_id;
					}
				}elseif(trim($row->meta_value) != $products_name){
					$_sql = "UPDATE prefix_postmeta SET meta_value = '".self::db_input($products_name)."' ".$WHERE;
					if(self::query($_sql)){
						$ret['updated'][$product->thumb_id] = $products_name;
						$ids[] = $product->products_id;
					}
				}else{
					$ret['skipped'][$product->thumb_id] = $products_name;
					$ids[] = $product->products_id;
				}
			}

			if(!empty($ids)){
				self::query("UPDATE mod_products SET image_alt = 1 WHERE products_id IN(".implode(',', $ids).")");
			}

			WCPL_Helper::log(WCPL_Helper::_debug($ret, true, false));
		}

		return $ret;
	}
	
	/**
	 * Для перенаправления на активный товар из группы товаров
	 * @param $src_product_id
	 *
	 * @return false|string
	 */
	public static function get_active_product_url_from_group($src_product_id){
		
		$groupped_products_data = WCPL_Helper::get_cache_file('groupped_products_data');
		if(!empty($groupped_products_data)){
			if(in_array($src_product_id, $groupped_products_data['product_ids'])){
				$product_to_group_data = $groupped_products_data['group_ids'][$src_product_id];
				return self::get_active_product_url_from_products($product_to_group_data['product_ids']);
			}
		}

		$result = self::get_row("SELECT * FROM mod_products_group WHERE product_ids LIKE '%,".$src_product_id."%' OR product_ids LIKE '%".$src_product_id.",%'");
		if($result){
			return self::get_active_product_url_from_products($result->product_ids);
		}
		
		return '';
	}
	
	/**
	 * Для перенаправления на активный товар из группы товаров
	 * @param $product_ids
	 *
	 * @return false|string
	 */
	public static function get_active_product_url_from_products($product_ids){
		$result = self::get_row("SELECT * FROM prefix_posts WHERE ID IN(".$product_ids.") AND post_status = 'publish'");
		
		return ($result) ? get_permalink($result->ID) : '';
	}
	
	/* ------------- Begin AJAX methods -------------- */

	public function ajax_update_images_alt_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Images Alt updated successfully', PINLOADER_TEXT_DOMAIN), 'result' => '');

		$results = self::update_products_thumb_id();

		if($results['applied'] > 0 || $results['skipped'] > 0){
			$a = sprintf(esc_attr__('Updated products thumb_id = %s, canceled = %s', PINLOADER_TEXT_DOMAIN), $results['applied'], $results['skipped']);
		}else{
			$results = self::update_products_thumb_alt();
			$a = '<pre class="debug">'.WCPL_Helper::_debug($results, true, false).'</pre>';
		}
		unset($results);


		$return['result'] = $return['message'].'<br>'.$a;

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	public function ajax_update_ymf_prices_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('YML feed prices updated successfully', PINLOADER_TEXT_DOMAIN), 'result' => '');

		self::wc_update_product_lookup_tables_column();
		$results = self::fix_yml_prices();

		$a = [];
		foreach($results as $k => $result){
			$a[] = $k.' -> count = '.$result['count'];
		}
		unset($results);

		$return['result'] = $return['message'].'<br>'.implode('. ', $a);

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	public static function ajax_autocomplete_get_products_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array();
		$term = $_REQUEST['term'];
		$results = self::get_results("SELECT products_id, products_name FROM mod_products WHERE products_status != 'trash' AND products_name LIKE '%".$term."%'");

		if(!empty($results)){
			foreach($results as $result){
				$data['id'] = $result->products_id;
				$data['value'] = $result->products_name;
				array_push($return, $data);
			}
		}

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		#WCPL_Helper::_debug($return);
		die(json_encode($return));
	}

	public function ajax_supplier_save_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => '');
		$post_data = $_REQUEST['form_data'];
		$post_data['table'] = 'mod_suppliers';
		$post_data['primary'] = array('id' => intval($post_data['id_supplier']), 'name' => 'id_supplier');

		$return['result'] = self::update_data($post_data);
		$return['post_data'] = $post_data;

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	public function ajax_product_ignore_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => array());
		$post_data = $_REQUEST['form_data'];
		$pid = intval($post_data['pid']);

		$data['table'] = 'mod_products_new';
		$data['primary'] = array('id' => $pid, 'name' => 'id');
		$data['ignore'] = 'yes';
		$return['result']['product'] = self::update_data($data);

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	public function ajax_products_ignore_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => array());
		$post_data = $_REQUEST['form_data'];
		$product_ids = $post_data['product_ids'];

		foreach($product_ids as $k => $pid){
			$data['table'] = 'mod_products_new';
			$data['primary'] = array('id' => $pid, 'name' => 'id');
			$data['ignore']  = 'yes';
			self::update_data($data);
		}

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	public function ajax_product_add_to_shop_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => array());
		$post_data = $_REQUEST['form_data'];
		#WCPL_Helper::_debug($post_data); exit;
		$pid = intval($post_data['pid']);
		$new_price = intval($post_data['new_price']);
		$clone_id = intval($post_data['clone_id']);
		$group_id = intval($post_data['group_id']);

		$new_product = self::get_new_product($pid);
		$supplier = self::get_mod_supplier($new_product->id_supplier);
		//$return['result']['product'] = $new_product;

		$args['product_title']             = $new_product->products_name;
		$args['product_description']       = $new_product->products_name;
		$args['product_price']             = !empty($new_price) ? $new_price : $new_product->price;
		$args['product_regular_price']     = !empty($new_price) ? $new_price : $new_product->price;
		$args['product_quantity']          = 100;
		$args['product_sku']               = '+'.$supplier->sku_suffix;
		$args['metas'][]['products_model'] = $new_product->products_model;
		$args['terms'][]['suppliers']      = $supplier->term_id;

		if($clone_id != 0){
			$product_id = WCPL_Products::duplicate_product($clone_id, $args, $group_id);
			if($group_id){
				$results = self::get_row("SELECT * FROM mod_products_group WHERE product_ids LIKE '%,".$clone_id."%' OR product_ids LIKE '%".$clone_id.",%'");
				if(!empty($results)){
					$product_ids = explode(',', $results->product_ids);
					$product_ids[] = $product_id;
					$data['primary'] = array('id' => $results->id, 'name' => 'id');
				}else{
					$product_ids[] = $clone_id;
					$product_ids[] = $product_id;
					$data['primary'] = array('id' => 0, 'name' => 'id');
					$data['color'] = WCPL_Helper::generate_color('0.5');
				}
				$data['table'] = 'mod_products_group';
				$data['product_ids'] = implode(',', $product_ids);
				self::update_data($data);
				unset($data);
			}
		}else{
			$product_id = WCPL_Products::add_product('simple', $args);
		}
		$return['result']['edit_link']     = '/wp-admin/post.php?post='.$product_id.'&action=edit&classic-editor=1';

		$data['table'] = 'mod_products_new';
		$data['primary'] = array('id' => $pid, 'name' => 'id');
		$data['products_id'] = $product_id;
		$return['result']['product'] = self::update_data($data);

		self::sync_products_prices(false);
		WCPL_Products::sync_mod_products_single($product_id);
		self::get_exclude_product_ids_from_groups(false);

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	public function ajax_products_add_to_shop_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => array());
		$post_data = $_REQUEST['form_data'];
		$products = $post_data['products'];

		foreach($products as $k => $product){
			$pid = intval($product['id']);
			$new_price = intval($product['new_price']);
			$clone_id = intval($product['clone_id']);
			$group_id = intval($product['group_id']);

			$new_product = self::get_new_product($pid);
			$supplier    = self::get_mod_supplier($new_product->id_supplier);
			//$return['result']['product'] = $new_product;

			$args['product_title']             = $new_product->products_name;
			$args['product_description']       = $new_product->products_name;
			$args['product_price']             = !empty($new_price) ? $new_price : $new_product->price;
			$args['product_regular_price']     = !empty($new_price) ? $new_price : $new_product->price;
			$args['product_quantity']          = 100;
			$args['product_sku']               = '+'.$supplier->sku_suffix;
			$args['metas'][]['products_model'] = $new_product->products_model;
			$args['terms'][]['suppliers']      = $supplier->term_id;

			if($clone_id != 0){
				$product_id = WCPL_Products::duplicate_product($clone_id, $args, $group_id);
				if($group_id){
					$results = self::get_row("SELECT * FROM mod_products_group WHERE product_ids LIKE '%,".$clone_id."%' OR product_ids LIKE '%".$clone_id.",%'");
					if(!empty($results)){
						$product_ids = explode(',', $results->product_ids);
						$product_ids[] = $product_id;
						$data['primary'] = array('id' => $results->id, 'name' => 'id');
					}else{
						$product_ids[] = $clone_id;
						$product_ids[] = $product_id;
						$data['primary'] = array('id' => 0, 'name' => 'id');
						$data['color'] = WCPL_Helper::generate_color('0.5');
					}
					$data['table'] = 'mod_products_group';
					$data['product_ids'] = implode(',', $product_ids);
					self::update_data($data);
					unset($data);
				}
			}else{
				$product_id = WCPL_Products::add_product('simple', $args);
			}

			$data['table']               = 'mod_products_new';
			$data['primary']             = array('id' => $pid, 'name' => 'id');
			$data['products_id']         = $product_id;
			self::update_data($data);
		}

		self::sync_products_prices(false);
		self::get_exclude_product_ids_from_groups(false);

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	public function ajax_change_product_nacenka_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => array());
		$post_data = $_REQUEST['form_data'];
		$pid = intval($post_data['pid']);
		$products_price = floatval($post_data['products_price']);

		self::query("UPDATE mod_products_price SET price = new_price WHERE products_id = ".$pid);
		self::query("UPDATE mod_products SET products_price = ".$products_price." WHERE products_id = ".$pid);
		self::query("UPDATE ml_postmeta SET meta_value = ".$products_price." WHERE post_id = ".$pid." AND meta_key = '_regular_price'");
		$_sale_price = self::get_var("SELECT meta_value FROM prefix_postmeta WHERE post_id = ".$pid." AND meta_key = '_sale_price'");
		if(!empty($_sale_price)){
			if(intval($_sale_price) < 0){
				$_price = round($products_price * (100 - abs($_sale_price)) / 100, -1, PHP_ROUND_HALF_DOWN);
				self::query("UPDATE ml_postmeta SET meta_value = ".$_price." WHERE post_id = ".$pid." AND meta_key = '_price'");
			}
		}else{
			self::query("UPDATE ml_postmeta SET meta_value = ".$products_price." WHERE post_id = ".$pid." AND meta_key = '_price'");
		}

		WCPL_Admin::set_option('pinloader_update_yml_feed', 1, true);

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	public function ajax_change_product_nacenka_all_manual_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => array());
		$post_data = $_REQUEST['form_data'];
		$ids = $post_data['ids'];

		self::query("UPDATE mod_products_price SET price = new_price WHERE products_id IN (".$ids.")");

		WCPL_Admin::set_option('pinloader_update_yml_feed', 1, true);

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	public function ajax_change_product_nacenka_all_auto_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => array());
		$post_data = $_REQUEST['form_data'];
		$ids = explode(",", $post_data['ids']);
		$prices = explode(",", $post_data['prices']);

		foreach($ids as $k => $id){
			if(intval($id) <= 0){
				unset($ids[$k]);
			}
		}

		for($i = 0; $i < count($ids); $i++){
			self::query("UPDATE mod_products_price SET price = new_price WHERE products_id = ".$ids[$i]);
			self::query("UPDATE mod_products SET products_price = ".$prices[$i]." WHERE products_id = ".$ids[$i]);
			self::query("UPDATE ml_postmeta SET meta_value = ".$prices[$i]." WHERE post_id = ".$ids[$i]." AND meta_key = '_regular_price'");
			$_sale_price = self::get_var("SELECT meta_value FROM prefix_postmeta WHERE post_id = ".$ids[$i]." AND meta_key = '_sale_price'");
			if(!empty($_sale_price)){
				if(intval($_sale_price) < 0){
					$_price = round($prices[$i] * (100 - abs($_sale_price)) / 100, -1, PHP_ROUND_HALF_DOWN);
					self::query("UPDATE ml_postmeta SET meta_value = ".$_price." WHERE post_id = ".$ids[$i]." AND meta_key = '_price'");
				}
			}else{
				self::query("UPDATE ml_postmeta SET meta_value = ".$prices[$i]." WHERE post_id = ".$ids[$i]." AND meta_key = '_price'");
			}
		}

		WCPL_Admin::set_option('pinloader_update_yml_feed', 1, true);

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	public function ajax_switchon_one_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => array());
		$post_data = $_REQUEST['form_data'];
		$pid = intval($post_data['pid']);
		$products_price = floatval($post_data['products_price']);

		self::query("UPDATE mod_products_price SET price = new_price WHERE products_id = ".$pid);
		self::query("UPDATE ml_posts SET post_status = 'publish' WHERE ID = ".$pid);
		self::query("REPLACE INTO mod_soho_prev (products_id, id_supplier, ostatok, roznica) SELECT products_id, id_supplier, ostatok, roznica FROM mod_soho_current WHERE products_id = ".$pid);
		self::query("UPDATE mod_products SET products_status = 'publish', products_price = ".$products_price." WHERE products_id = ".$pid);
		self::query("UPDATE ml_postmeta SET meta_value = ".$products_price." WHERE post_id = ".$pid." AND meta_key = '_regular_price'");
		$_sale_price = self::get_var("SELECT meta_value FROM prefix_postmeta WHERE post_id = ".$pid." AND meta_key = '_sale_price'");
		if(!empty($_sale_price)){
			if(intval($_sale_price) < 0){
				$_price = round($products_price * (100 - abs($_sale_price)) / 100, -1, PHP_ROUND_HALF_DOWN);
				self::query("UPDATE ml_postmeta SET meta_value = ".$_price." WHERE post_id = ".$pid." AND meta_key = '_price'");
			}
		}else{
			self::query("UPDATE ml_postmeta SET meta_value = ".$products_price." WHERE post_id = ".$pid." AND meta_key = '_price'");
		}

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	public function ajax_switchon_all_manual_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => array());
		$post_data = $_REQUEST['form_data'];
		$ids = $post_data['ids'];

		self::query("UPDATE ml_posts SET post_status = 'publish' WHERE ID IN (".$ids.")");
		self::query("UPDATE mod_products SET products_status = 'publish' WHERE products_id IN (".$ids.")");

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	public function ajax_switchon_all_auto_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => array());
		$post_data = $_REQUEST['form_data'];
		$ids = explode(",", $post_data['ids']);
		$prices = explode(",", $post_data['prices']);


		for($i = 0; $i < count($ids); $i++){
			self::query("UPDATE mod_products_price SET price = new_price WHERE products_id = ".$ids[$i]);
			self::query("UPDATE ml_posts SET post_status = 'publish' WHERE ID = ".$ids[$i]);
			self::query("UPDATE mod_products SET products_status = 'publish', products_price = ".$prices[$i]." WHERE products_id = ".$ids[$i]);
			self::query("UPDATE ml_postmeta SET meta_value = ".$prices[$i]." WHERE post_id = ".$ids[$i]." AND meta_key = '_regular_price'");
			$_sale_price = self::get_var("SELECT meta_value FROM prefix_postmeta WHERE post_id = ".$ids[$i]." AND meta_key = '_sale_price'");
			if(!empty($_sale_price)){
				if(intval($_sale_price) < 0){
					$_price = round($prices[$i] * (100 - abs($_sale_price)) / 100, -1, PHP_ROUND_HALF_DOWN);
					self::query("UPDATE ml_postmeta SET meta_value = ".$_price." WHERE post_id = ".$ids[$i]." AND meta_key = '_price'");
				}
			}else{
				self::query("UPDATE ml_postmeta SET meta_value = ".$prices[$i]." WHERE post_id = ".$ids[$i]." AND meta_key = '_price'");
			}
		}

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	public function ajax_switchoff_one_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => array());
		$post_data = $_REQUEST['form_data'];
		$pid = intval($post_data['pid']);

		self::query("UPDATE ml_posts SET post_status = 'draft' WHERE ID = ".$pid);
		self::query("UPDATE mod_products SET products_status = 'draft' WHERE products_id = ".$pid);

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	public function ajax_switchoff_all_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => array());
		$post_data = $_REQUEST['form_data'];
		$id_supplier = intval($post_data['id_supplier']);

		self::query("DELETE FROM mod_temp WHERE date < DATE_SUB(NOW(), INTERVAL 4 HOUR) ");

		$result = self::get_row("SELECT * FROM mod_temp WHERE sid = '".session_id()."' AND type = 'switchoffall'");
		if($result){
			$products_ids_to_switch_off = unserialize($result->data);
			$return['result'] = $products_ids_to_switch_off;
			if(is_array($products_ids_to_switch_off)){
				if($id_supplier == 0){
					self::query("UPDATE ml_posts SET post_status = 'draft' WHERE ID IN (".implode(",", $products_ids_to_switch_off).")");
					self::query("UPDATE mod_products SET products_status = 'draft' WHERE products_id IN (".implode(",", $products_ids_to_switch_off).")");
				}else{
					self::query("UPDATE mod_products SET products_status = 'draft' WHERE id_supplier = ".$id_supplier." AND products_id IN (".implode(",", $products_ids_to_switch_off).")");
					$results = self::get_col("SELECT products_id FROM mod_products WHERE id_supplier = ".$id_supplier." AND products_status = 'draft'");
					#WCPL_Helper::_debug($results);
					if(!empty($results)){
						self::query("UPDATE ml_posts SET post_status = 'draft' WHERE ID IN (".implode(",", $results).")");
					}
				}
			}
		}

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	public function ajax_restore_one_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => array());
		$post_data = $_REQUEST['form_data'];
		$pid = intval($post_data['pid']);

		$data['table'] = 'mod_products_new';
		$data['primary'] = array('id' => $pid, 'name' => 'id');
		$data['ignore'] = 'no';
		$return['result']['product'] = self::update_data($data);

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	public function ajax_restore_all_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => array());
		$post_data = $_REQUEST['form_data'];

		$product_ids = $post_data['product_ids'];

		foreach($product_ids as $k => $pid){
			$data['table'] = 'mod_products_new';
			$data['primary'] = array('id' => $pid, 'name' => 'id');
			$data['ignore']  = 'no';
			self::update_data($data);
		}

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	public function ajax_change_price_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => array());
		$post_data = $_REQUEST['form_data'];
		$pid = intval($post_data['pid']);
		$products_price = intval($post_data['product_price']);
		$minprice = intval($post_data['minprice']);
		$firm = trim($post_data['firm']);

		if($pid > 0 && $products_price > 0){

			//self::query("UPDATE ml_postmeta SET meta_value = ".$products_price." WHERE post_id = ".$pid." AND meta_key = '_price' OR meta_key = '_regular_price'");
			self::query("INSERT IGNORE INTO mod_monitoring_data SET products_id = ".$pid.", date = now(), minprice = ".$minprice.", firm = '".self::db_input($firm)."' ON DUPLICATE KEY UPDATE date = now(), minprice = ".$minprice.", firm = '".self::db_input($firm)."'");
			self::query("UPDATE mod_products SET products_price = ".$products_price." WHERE products_id = ".$pid);
			self::query("UPDATE ml_postmeta SET meta_value = ".$products_price." WHERE post_id = ".$pid." AND meta_key = '_regular_price'");
			$_sale_price = self::get_var("SELECT meta_value FROM prefix_postmeta WHERE post_id = ".$pid." AND meta_key = '_sale_price'");
			if(!empty($_sale_price)){
				if(intval($_sale_price) < 0){
					$_price = round($products_price * (100 - abs($_sale_price)) / 100, -1, PHP_ROUND_HALF_DOWN);
					self::query("UPDATE ml_postmeta SET meta_value = ".$_price." WHERE post_id = ".$pid." AND meta_key = '_price'");
				}
			}else{
				self::query("UPDATE ml_postmeta SET meta_value = ".$products_price." WHERE post_id = ".$pid." AND meta_key = '_price'");
			}

			$result = self::get_row("SELECT * FROM mod_products_monitor_price WHERE products_id = ".$pid);
			$return['result'] = money_format('%!.0n', $result->nacenka).":".$result->coefficient.":".$result->date.":".money_format('%!.0n', $result->raznica);
		}else{
			$return['error'] = 1;
			$return['message'] = esc_attr__('Error while saving data', PINLOADER_TEXT_DOMAIN);
		}

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	public function ajax_monitor_change_color_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => array());
		$post_data = $_REQUEST['form_data'];
		$pid = intval($post_data['pid']);

		$data['table'] = 'mod_monitoring_data';

		$color = self::get_var("SELECT color FROM mod_monitoring_data WHERE products_id = ".$pid);
		if(null != $color){
			if($color){
				$newColor = 0;
			}else{
				$newColor = 1;
			}
			$data['primary'] = array('id' => $pid, 'name' => 'products_id');
		}else{
			$newColor = 1;
			$data['primary'] = array('id' => 0, 'name' => 'products_id');
			$data['products_id'] = $pid;
		}
		$data['color'] = $newColor;
		$return['result']['query'] = self::update_data($data);
		$return['result'] = $newColor;

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));

	}

	public function ajax_group_products_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => array());
		$post_data = $_REQUEST['form_data'];
		$product_ids = $post_data['product_ids'];
		$groups = $post_data['groups'];

		$data['table'] = 'mod_products_group';
		$data['primary'] = array('id' => 0, 'name' => 'id');
		$data['product_ids'] = implode(',', $product_ids);
		$data['color'] = WCPL_Helper::generate_color('0.5');
		self::update_data($data);

		// Обновляем кэшированные файлы
		self::get_exclude_product_ids_from_groups(false);

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	public function ajax_ungroup_products_request(){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;

		$return = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => array());
		$post_data = $_REQUEST['form_data'];
		$gid = intval($post_data['gid']);

		self::query("DELETE FROM mod_products_group WHERE id = ".$gid);

		// Обновляем кэшированные файлы
		self::get_exclude_product_ids_from_groups(false);

		$GLOBALS['PINLOADER_STOP_CRON'] = false;

		die(json_encode($return));
	}

	/* ------------- Begin CRON methods -------------- */

	/**
	 * Обновдяем товары, так как есть много товаров, где post_name содержит пустое значение
	 */
	public static function cron__restore_broken_products(){
		WCPL_Helper::log(__METHOD__);

		if($GLOBALS['PINLOADER_STOP_CRON']) return;

		$products = self::get_results("SELECT ID, post_title FROM prefix_posts WHERE post_type = 'product' AND post_name = '' AND post_status = 'publish' ORDER BY ID ASC");

		if($products){
			foreach($products as $product){
				wp_update_post(['ID' => $product->ID, 'post_title' => $product->post_title]);
				WCPL_Helper::log('- Updated product '.$product->ID.', "'.$product->post_title.'"');
			}
		}else{
			WCPL_Helper::log('- No broken products');
		}
	}

	public static function cron__update_mod_products_new_ids(){
		WCPL_Helper::log(__METHOD__);

		if($GLOBALS['PINLOADER_STOP_CRON']) return;

		$_sql = "UPDATE mod_products_new SET exclude = 1 WHERE products_id != 0 AND exclude = 0";
		self::query($_sql);

		$_sql = "SELECT id FROM mod_products_new WHERE products_id = 0 AND exclude = 0 ORDER BY id ASC LIMIT 0, 10";
		$results = self::get_col($_sql);
		if(!$results){
			WCPL_Helper::log('- results-count = '.count($results));
			$_sql = "SELECT id FROM mod_products_new WHERE products_id = 0 AND exclude = 1 ORDER BY id ASC LIMIT 0, 10";
			$results = self::get_col($_sql);
		}

		if($results){
			$ids = array();
			foreach($results as $id){
				$_sql = "UPDATE mod_products_new m, mod_products p SET m.products_id = p.products_id, m.exclude = 1 WHERE m.id = ".$id." AND m.id_supplier = p.id_supplier AND p.products_model = m.products_model";
				$r = self::query($_sql);
				if($r == 0){
					$ids[] = $id;
					#WCPL_Helper::log('[function '.__FUNCTION__.'] is called. r = '.$r);
					/*$data['table'] = 'mod_products_new';
					$data['primary'] = array('id' => intval($id), 'name' => 'id');
					$data['exclude'] = 1;
					self::update_data($data);*/
				}
			}
			if(!empty($ids)){
				self::query("UPDATE mod_products_new SET exclude = 1 WHERE id IN(".implode(',', $ids).")");
			}
		}
	}

	public static function cron__sync_mod_products(){
		WCPL_Helper::log(__METHOD__);

		if($GLOBALS['PINLOADER_STOP_CRON']) return;

		$src_count = self::get_var("SELECT COUNT(*) FROM mod_products_vc");
		$dst_count = self::get_var("SELECT COUNT(*) FROM mod_products");

		$src_count = intval($src_count);
		$dst_count = intval($dst_count);

		WCPL_Helper::log('mod_products_vc count = '.$src_count.', mod_products count = '.$dst_count);

		/*if(intval($dst_count) != intval($src_count)){
			$_sql = "INSERT IGNORE INTO mod_products SELECT * FROM mod_products_vc vcp ON DUPLICATE KEY UPDATE products_name = vcp.products_name, products_status = vcp.product_status, id_supplier = vcp.id_supplier, name_supplier = vcp.name_supplier, products_model = vcp.products_model, products_price = vcp.products_price, products_filled = vcp.products_filled";
			WCPL_Data_Source::query($_sql);
		}*/

		if($src_count > $dst_count){
			WCPL_Helper::log('- insert');
			$_sql = "INSERT IGNORE INTO mod_products (products_id, products_name, products_status, id_supplier, name_supplier, products_model, products_price, products_filled) 
					 SELECT vcp.products_id, vcp.products_name, vcp.product_status, vcp.id_supplier, vcp.name_supplier, vcp.products_model, vcp.products_price, vcp.products_filled 
					 FROM mod_products_vc vcp 
					 ON DUPLICATE KEY UPDATE products_name = vcp.products_name, products_status = vcp.product_status, id_supplier = vcp.id_supplier, name_supplier = vcp.name_supplier, products_model = vcp.products_model, products_price = vcp.products_price, products_filled = vcp.products_filled";
			$result = WCPL_Data_Source::query($_sql);
			if($result === false){
				WCPL_Helper::log('- Error on table replication. Line:'.__LINE__);
			}
		}elseif($src_count < $dst_count){
			WCPL_Helper::log('- delete');
			$results = self::get_col("SELECT products_id FROM mod_products WHERE products_id NOT IN (SELECT products_id FROM mod_products_vc)");
			WCPL_Helper::log('- delete_products_ids = '.implode(",", $results));
			if(!empty($results)){
				self::query("DELETE FROM mod_products WHERE products_id IN (".implode(",", $results).")");
			}
		}else{
			WCPL_Helper::log('- update');
			self::query("UPDATE ml_posts, mod_products SET post_status = products_status WHERE ID = products_id AND post_status != products_status");
		}


	}

	public static function cron__checking_for_fix_seo_fields(){
		WCPL_Helper::log(__METHOD__);

		if($GLOBALS['PINLOADER_STOP_CRON']) return;

		$_sql = "SELECT post_id FROM prefix_postmeta WHERE meta_key = '_yoast_wpseo_focuskw' AND meta_value = '.' ORDER BY post_id ASC";
		$ids = self::get_col($_sql);
		#WCPL_Helper::log(WCPL_Helper::_debug($ids, true, false));
		WCPL_Helper::log('- Broken products = '.count($ids));

		if(!empty($ids)){
			self::query("UPDATE mod_products SET yoast_seo = 0 WHERE products_id IN(".implode(',', $ids).")");
		}
	}

	public static function cron__generate_product_seo_fields($post_id = 0){
		WCPL_Helper::log(__METHOD__);

		if($GLOBALS['PINLOADER_STOP_CRON']) return;

		$enter = chr(13).chr(10);

		if($post_id == 0){
			$_sql = "SELECT p.ID, p.post_title, p.post_excerpt FROM prefix_posts p 
					 LEFT JOIN mod_products mp ON p.ID = mp.products_id 
					 WHERE mp.yoast_seo = 0 AND mp.products_status != 'trash' 
					 ORDER BY mp.products_id ASC LIMIT 0, 50";
		}else{
			$_sql = "SELECT p.ID, p.post_title, p.post_excerpt FROM prefix_posts p WHERE p.ID = ".$post_id;
		}
		$results = self::get_results($_sql);

		if($results){
			$ids = array();
			foreach($results as $result){
				$ids[] = $result->ID;

				WCPL_Helper::log('- post_id = '.$result->ID);

				$post_title = trim($result->post_title, '.');
				$post_title = trim($post_title);
				$post_title = trim(preg_replace ('/([а-я])/ius', '', $post_title));
				if(empty($post_title)){
					$post_title = trim($result->post_title, '.');
					$post_title = trim($post_title);
				}
				$post_excerpt = str_replace($enter, ', ', trim(strip_tags($result->post_excerpt)));
				$post_excerpt = str_replace(' , ', ', ', $post_excerpt);
				$str = trim($post_title.' '.$post_excerpt);
				$str = trim($str, ",");
				if(mb_strlen($str) > 156){
					$str = mb_substr($str, 0, mb_strpos($str, ' ', 156));
					$str = rtrim($str, ",");
					$str = rtrim($str, ":");
					$str = rtrim($str, ";");
					$str = rtrim($str, "(");
					$str = rtrim($str, ")");
				}

				update_post_meta($result->ID, '_yoast_wpseo_focuskw', $post_title);
				update_post_meta($result->ID, '_yoast_wpseo_metadesc', $str);
				update_post_meta($result->ID, '_yoast_wpseo_content_score', 90);
				update_post_meta($result->ID, '_yoast_wpseo_linkdex', 42);

				// Обновляем краткое описание, очищая от всякого мусоря
				if(strstr($result->post_excerpt, 'data-bem=') !== false){
					$new_post_excerpt = trim(strip_tags($result->post_excerpt, '<ul><li>'));
					$data['table'] = 'prefix_posts';
					$data['primary'] = array('id' => $result->ID, 'name' => 'ID');
					$data['post_excerpt'] = $new_post_excerpt;
					self::update_data($data);
				}

				// Добавляем Alt и Заголовок основному изображениютовара
				/*$_sql = "SELECT ID FROM prefix_posts WHERE post_parent = ".$result->ID." AND post_type = 'attachment'";
				$attachment_post_id = self::get_var($_sql);
				if($attachment_post_id){
					$_sql = "SELECT pm.meta_value FROM prefix_postmeta pm WHERE pm.post_id = ".$attachment_post_id." AND pm.meta_key = '_wp_attachment_image_alt' AND pm.meta_value = ''";
					$wp_attachment_image_alt = self::get_var($_sql);
					if(is_null($wp_attachment_image_alt)){
						WCPL_Helper::log('[function '.__FUNCTION__.'] attachment_post_id = '.$attachment_post_id);
						update_post_meta($attachment_post_id, '_wp_attachment_image_alt', $result->post_title);
						$data['table']      = 'prefix_posts';
						$data['primary']    = array('id' => intval($attachment_post_id), 'name' => 'ID');
						$data['post_title'] = $result->post_title;
						self::update_data($data);
					}
				}*/
			}

			if(!empty($ids)){
				self::query("UPDATE mod_products SET yoast_seo = 1 WHERE products_id IN(".implode(',', $ids).")");
			}
		}

		$_sql = "SELECT COUNT(*) AS count FROM mod_products WHERE yoast_seo = 0 AND products_status != 'trash'";
		$count = self::get_var($_sql);
		WCPL_Helper::log('- remaining products count = '.$count);
	}

	public static function cron__generate_product_thumb_fields(){
		WCPL_Helper::log(__METHOD__);

		if($GLOBALS['PINLOADER_STOP_CRON']) return;

		$results = self::update_products_thumb_id();

		if($results['applied'] == 0 && $results['skipped'] == 0){
			self::update_products_thumb_alt();
		}

		$_sql = "SELECT COUNT(*) AS count FROM mod_products WHERE image_alt = 0 AND products_status != 'trash'";
		$count = self::get_var($_sql);
		WCPL_Helper::log('- remaining products image count = '.$count);
	}

	public static function cron__checking_for_fix_products_popupmaker(){
		WCPL_Helper::log(__METHOD__);

		if($GLOBALS['PINLOADER_STOP_CRON']) return;

		$_sql = "SELECT pm.meta_value AS popup_id
				 FROM ml_postmeta pm
				 LEFT JOIN mod_fixation_options fo ON fo.source_id = pm.meta_value
				 WHERE (fo.is_fixed IS NULL OR fo.is_fixed = 0) AND (fo.source_name IS NULL OR fo.source_name = 'popupmaker') AND pm.meta_value != '' AND pm.meta_key='products_promotion_popup_window'
				 GROUP BY pm.meta_value
				 ORDER BY pm.meta_value ASC LIMIT 1";
		$popupmaker_id = self::get_var($_sql);

		if(empty($popupmaker_id)){
			WCPL_Helper::log('- NO popupmaker ID');
			self::query("UPDATE mod_fixation_options SET is_fixed = 0 WHERE is_fixed = 1 AND source_name = 'popupmaker'");
			WCPL_Helper::log('- Reset table mod_fixation_options for popupmaker source records');
			self::cron__checking_for_fix_products_popupmaker();
			return;
		}

		$_sql = "SELECT meta_id, post_id AS product_id, meta_value AS popup_id
				 FROM prefix_postmeta
				 WHERE meta_key = 'products_promotion_popup_window' AND meta_value = ".$popupmaker_id."
				 ORDER BY post_id ASC";
		$products_result = self::get_results($_sql);

		if(empty($products_result)){
			WCPL_Helper::log('- No product ids');
			return;

			#self::query("UPDATE mod_products SET yoast_seo = 0 WHERE products_id IN(".implode(',', $ids).")");
		}

		$popup_settings = get_post_meta($popupmaker_id, 'popup_settings', true);
		#WCPL_Helper::log($popup_settings);

		if(empty($popup_settings) || is_null($popup_settings)){
			WCPL_Helper::log('- Popup setting is empty');
			return;
		}

		if(!isset($popup_settings['conditions'])){
			WCPL_Helper::log('- Popup setting has no "conditions" key');
			return;
		}

		$conditions = [];
		foreach($products_result as $item){
			$conditions[0][] = [
				'target' => 'product_ID',
				'settings' => ['selected' => $item->product_id],
			];
		}
		$popup_settings['conditions'] = $conditions;
		#WCPL_Helper::log($popup_settings);
		update_post_meta($popupmaker_id, 'popup_settings', $popup_settings);

		$count = self::get_var("SELECT COUNT(*) FROM mod_fixation_options WHERE source_id = $popupmaker_id AND source_name = 'popupmaker'");
		if($count){
			self::query("UPDATE mod_fixation_options SET is_fixed = 1 WHERE source_id = $popupmaker_id AND source_name = 'popupmaker'");
		}else{
			self::query("INSERT INTO mod_fixation_options (source_id, source_name, is_fixed) VALUES ($popupmaker_id, 'popupmaker', 1)");
		}

		WCPL_Helper::log('- Fixed popupmaker ID '.$popupmaker_id.' for '.count($products_result).' products.');
	}

}
