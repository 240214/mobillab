<?php

namespace Pinloader;

class WCPL_Cron {

	public $options;
	public $slug = 'pinloader';
	private $cron_task_name = "pinloader_data_receiver";

	public function __construct(){
		add_action('init', array($this, 'init'), 0);
		add_action($this->cron_task_name, array($this, 'pinloader_cron_task_exec'));
		add_filter('cron_schedules', array($this, 'pinloader_cron_add_schedule'));
	}

	public function init() {
		$this->slug = WcPinLoader::instance()->plugin_slug;
		$this->options = WcPinLoader::instance()->settings->get_options();
		$this->run();
	}

	function pinloader_cron_add_schedule(){
		$schedules['pinloader_cron_interval'] = array('interval' => (intval($this->options['pinloader_interval']) * 60), 'display' => 'WcPinLoader Cron Worker');
		return $schedules;
	}

	public function run(){
		if(wp_next_scheduled($this->cron_task_name) === false){
			wp_schedule_event(time()+$this->options['pinloader_interval'], 'pinloader_cron_interval', $this->cron_task_name);
		}
	}

	public function stop(){
		wp_clear_scheduled_hook($this->cron_task_name);
	}

	public function pinloader_cron_task_exec(){
		if(class_exists('WcPinLoader')){
			WcPinLoader::instance()->log('function '.__FUNCTION__.' is called');
			WcPinLoader::instance()->api_ui->run();
			$this->removeExpiredCssFromCachedir();
		}else{
			echo 'class WcPinLoader not exist';
		}
	}


	public function removeExpiredCssFromCachedir(){
		$cache_dir = WcPinLoader::instance()->cache_dir;
		$files = array_diff(scandir($cache_dir), array('..', '.'));

		foreach($files as $k => $v){
			$type = explode('.', $v);
			$type = array_reverse($type);
			if($type[0] == 'css'){
				@unlink($cache_dir.'/'.$v);
			}
		}
	}

}
