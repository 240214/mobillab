<?php


namespace Pinloader;



class WCPL_Core{

	public static $sql_structure = array();

	public static function initialise(){
		$self = new self();

	}

	public static function load_textdomain(){
		load_plugin_textdomain(PINLOADER_TEXT_DOMAIN, false, PINLOADER_PLUGIN_DIR_SHORT.'/languages/');
	}

	public static function create_tables(){
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		#WCPL_Helper::_debug(self::$sql_structure);

		//$wpdb->show_errors();
		$charset_collate = '';

		if($wpdb->has_cap('collation')){
			$charset_collate = $wpdb->get_charset_collate();
		}

		self::$sql_structure = include_once PINLOADER_PLUGIN_DIR."/inc/sql-structure.php";

		$sql_tables = self::$sql_structure['tables'];
		$sql_views = self::$sql_structure['views'];

		if(!empty($sql_tables)){
			$sql_tables = str_replace(array('{charset_collate}', '{prefix}'), array($charset_collate, $wpdb->prefix), $sql_tables);
			dbDelta($sql_tables);
		}
		if(!empty($sql_views)){
			$sql_views = str_replace(array('{db_name}', '{prefix}'), array(DB_NAME, $wpdb->prefix), $sql_views);
			dbDelta($sql_views);
		}
	}

	public static function destroy(){
		global $wpdb;

		if($cron){
			$cron = new WCPL_Cron();
			$cron->stop();
		}
	}

	public static function get_blog_ids(){
		global $wpdb;

		return $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs WHERE archived = '0' AND spam = '0' AND deleted = '0'");
	}

}