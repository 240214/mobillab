<?php
namespace Pinloader;

//require_once('../vendor/autoload.php');
use GeoIp2\Database\Reader;

class WCPL_GEO {

	private $geo_db_dir = "";

	public function __construct(){
		add_action('init', array($this, 'init'), 0);
	}

	public function init(){

	}

	public function get_city(){
		$reader = new Reader(PINLOADER_PLUGIN_DIR.'/geo_db/GeoLite2-City.mmdb');
		return $reader->city($this->get_ip());
	}

	public function get_country(){
		$reader = new Reader(PINLOADER_PLUGIN_DIR.'/geo_db/GeoLite2-Country.mmdb');
		return $reader->country($this->get_ip());

	}

	private function get_ip(){
		$ip_address = '';

		if(isset($_SERVER['HTTP_CF_CONNECTING_IP']) && !empty($_SERVER['HTTP_CF_CONNECTING_IP']) ){
			$ip_address = $_SERVER['HTTP_CF_CONNECTING_IP'];
		}elseif ( isset($_SERVER['HTTP_X_SUCURI_CLIENTIP']) && !empty($_SERVER['HTTP_X_SUCURI_CLIENTIP']) ) {
			$ip_address = $_SERVER['HTTP_X_SUCURI_CLIENTIP'];
		}elseif ( isset($_SERVER['HTTP_INCAP_CLIENT_IP']) && !empty($_SERVER['HTTP_INCAP_CLIENT_IP']) ) {
			$ip_address = $_SERVER['HTTP_INCAP_CLIENT_IP'];
		}elseif ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
			$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}elseif ( isset($_SERVER['HTTP_X_FORWARDED']) && !empty($_SERVER['HTTP_X_FORWARDED']) ) {
			$ip_address = $_SERVER['HTTP_X_FORWARDED'];
		}elseif ( isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) ) {
			$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		}elseif ( isset($_SERVER['HTTP_X_REAL_IP']) && !empty($_SERVER['HTTP_X_REAL_IP']) ) {
			$ip_address = $_SERVER['HTTP_X_REAL_IP'];
		}elseif ( isset($_SERVER['HTTP_FORWARDED']) && !empty($_SERVER['HTTP_FORWARDED']) ) {
			$ip_address = $_SERVER['HTTP_FORWARDED'];
		}elseif ( isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])){
			$ip_address = $_SERVER['REMOTE_ADDR'];
		}

		// Get first ip if ip_address contains multiple addresses
		$ips = explode(',', $ip_address);
		$ip_address = trim($ips[0]);

		return $ip_address;
	}

}