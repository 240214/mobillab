<?php

namespace Digidez;


class Ajax_Actions{

	public static function initialise(){
		$self = new self();

		add_action('wp_ajax_checkout_payment_request', array($self, 'checkout_payment_request'));
		add_action('wp_ajax_nopriv_checkout_payment_request', array($self, 'checkout_payment_request'));
	}

	public function checkout_payment_request(){
		$ret = array('error' => 0, 'apply_points' => 1);

		$wc_payment_methods = $_REQUEST['wc_payment_methods'];

		if($wc_payment_methods == 'cheque'){
			WC()->cart->remove_coupons();
			$ret['apply_points'] = 0;
		}elseif($wc_payment_methods == 'cod'){
			WC()->session->set('chosen_payment_method', 'cod');
		}

		die(json_encode($ret));
	}


}
