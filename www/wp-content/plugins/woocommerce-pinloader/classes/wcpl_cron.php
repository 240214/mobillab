<?php

namespace Pinloader;
use Digidez\Actions;

class WCPL_Cron {

	public static $options;
	public static $slug = 'pinloader';
	private static $cron_task_name = "pinloader_data_updater";

	public static function initialise(){
		$self = new self();

		add_action('init', array($self, 'init'), 0);
		add_action(self::$cron_task_name, array($self, 'pinloader_cron_task_exec'));
		add_filter('cron_schedules', array($self, 'pinloader_cron_add_schedule'));
	}

	public function init() {
		self::$options = WCPL_Admin::get_options();
		self::run();
	}

	function pinloader_cron_add_schedule(){
		$interval = isset(self::$options['pinloader_interval']) ? intval(self::$options['pinloader_interval']) : 50;
		$schedules['pinloader_cron_interval'] =
			[
				'interval' => ($interval * 60),
				'display' => 'Every '.$interval.' minute(s)'
			];

		return $schedules;
	}

	public static function run(){
		#WCPL_Helper::log('[function '.__FUNCTION__.'] is called');
		if(wp_next_scheduled(self::$cron_task_name) === false){
			WCPL_Helper::log('[function '.__FUNCTION__.'], wp_next_scheduled = false');
			wp_schedule_event(time()+self::$options['pinloader_interval'] * 60, 'pinloader_cron_interval', self::$cron_task_name);
		}else{
			/*$timestamp = wp_next_scheduled(self::$cron_task_name);
			if($timestamp > time()+self::$options['pinloader_interval']*60){
				WCPL_Helper::log('[function '.__FUNCTION__.'], schedule restart, timestamp = '.$timestamp);
				self::stop();
				//self::run();
			}*/
		}
	}

	public static function stop(){
		#WCPL_Helper::log('[function '.__FUNCTION__.'] is called');
		$timestamp = wp_next_scheduled(self::$cron_task_name);
		wp_unschedule_event($timestamp, self::$cron_task_name);
		wp_clear_scheduled_hook(self::$cron_task_name);
	}

	public function pinloader_cron_task_exec(){
		WCPL_Helper::log('------------------- START CRON -------------------');
		WCPL_Helper::log(__METHOD__);

		if($GLOBALS['PINLOADER_STOP_CRON'] === false){
			WCPL_Data_Source::cron__checking_for_fix_products_popupmaker();
			WCPL_Data_Source::cron__restore_broken_products();
			WCPL_Data_Source::cron__sync_mod_products();
			WCPL_Data_Source::cron__update_mod_products_new_ids();
			WCPL_Data_Source::cron__checking_for_fix_seo_fields();
			WCPL_Data_Source::cron__generate_product_seo_fields();
			WCPL_Data_Source::cron__generate_product_thumb_fields();
			WCPL_Data_Source::wc_update_product_lookup_tables_column();

			if(intval(self::$options['pinloader_update_yml_feed']) == 1){
				WCPL_Data_Source::fix_yml_prices();
			}

			#Actions::wc_update_product_lookup_tables_column('min_max_price');
		}

		WCPL_Helper::log('------------------- END CRON -------------------');
	}

}
