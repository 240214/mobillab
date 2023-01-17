<?php
/**
 * Примеры по DOM HTML
 * https://xdan.ru/uchimsya-parsit-saity-s-bibliotekoi-php-simple-html-dom-parser.html
 */

namespace Pinloader;

class WCPL_Yandex_Market{

	public $errorCode = 0;
	public $options;
	public $yandex_search_url_m = "https://m.market.yandex.ru/search?text=";
	public $yandex_search_url_d = "http://market.yandex.ru/search.xml?text=";

	public static function initialise(){
		$self = new self();

		add_action('init', array($self, 'init'), 0);

		//add_action('wp_ajax_get_ym_products_request', array($self, 'ajax_get_ym_products_request'));
	}

	public function init(){}

	public function get_yandex_search_url($search_text, $mode = 'm'){
		return $this->{'yandex_search_url_'.$mode}.urlencode($search_text);
	}

	public function get_ym_products($product_title){
		$url = WCPL_Helper::yandex_search_link($product_title);

		$options = array(
			'http' => array(
				'header'  => "Content-type: text/html",
				'method'  => 'GET',
				'content' => 'URL='.urlencode($url)
			)
		);
		$context = stream_context_create($options);
		$content = file_get_contents($url, false, $context);


		return $content;
	}

	public function get_ym_products_curl($product_title){
		$header['errno']   = 0;
		$header['errmsg']  = '';
		$header['content'] = '';
		$url               = $this->get_yandex_search_url($product_title, 'd');
		$user_agent        = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:70.0) Gecko/20100101 Firefox/70.0';

		$options = array(
			CURLOPT_CUSTOMREQUEST  => "GET",        //set request type post or get
			CURLOPT_POST           => false,        //set to GET
			CURLOPT_USERAGENT      => $user_agent, //set user agent
			CURLOPT_COOKIEFILE     => "cookie.txt", //set cookie file
			CURLOPT_COOKIEJAR      => "cookie.txt", //set cookie jar
			CURLOPT_RETURNTRANSFER => true,     // return web page
			CURLOPT_HEADER         => false,    // don't return headers
			CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			CURLOPT_ENCODING       => "",       // handle all encodings
			CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
			CURLOPT_TIMEOUT        => 120,      // timeout on response
			CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
			CURLOPT_PROXY          => "167.250.52.2:8080",
			CURLOPT_PROXYTYPE      => CURLPROXY_HTTP,
			CURLOPT_FAILONERROR    => true,
		);

		if($ch = curl_init($url)){
			curl_setopt_array($ch, $options);
			$content = curl_exec($ch);
			$err     = curl_errno($ch);
			$errmsg  = curl_error($ch);
			$header  = curl_getinfo($ch);
			curl_close($ch);

			$header['errno']   = $err;
			$header['errmsg']  = $errmsg;
			$header['content'] = $content;
		}

		return $header;
	}

	public function get_ym_products_iframe($product_title){
		$url = WCPL_Helper::yandex_search_link($product_title);

		$content = '<iframe src="'.$url.'" width="100%" height="100%" frameborder="0"></iframe>';

		return $content;
	}

	public function ajax_get_ym_products_request(){
		$return    = array('error' => 0, 'message' => esc_attr__('Data saved successfully', PINLOADER_TEXT_DOMAIN), 'result' => array());
		$post_data = $_REQUEST['form_data'];
		$pid       = intval($post_data['pid']);

		$product_title = WCPL_Data_Source::get_var("SELECT products_name FROM mod_products WHERE products_id = ".$pid);

		$result = $this->get_ym_products_curl($product_title);
		if($result['errno'] != 0){
			//$return['result'] = $result['errmsg'];
		}
		$return['result'] = $result;
		//$return['result'] = $this->get_yandex_search_url($product_title);

		die(json_encode($return));
	}

	public static function format_specifications_meta($html_specifications){
		$output = array();
		$enter  = chr(13).chr(10);
		$tab    = chr(9);
		$slash  = chr(92);

		if(strstr($html_specifications, 'n-product-spec-wrap__body') === false){
			return $html_specifications;
		}

		$html = str_replace(array($enter, $tab, $slash), '', $html_specifications);
		$html = str_replace('<div class="n-product-spec-wrap__body">', '', $html);
		$html = str_replace('<div class="n-hint-button i-bem n-hint-button_js_inited" data-bem="{&quot;n-hint-button&quot;:{&quot;place&quot;:&quot;&quot;}}">', '', $html);
		$html = str_replace('</div>', '', $html);
		$html = str_replace('<span class="n-product-spec__name-inner">', '', $html);
		$html = str_replace('<span class="n-product-spec__value-inner">', '', $html);
		$html = str_replace('</span>', '', $html);
		$html = str_replace(array('<dl', '</dl', '<dt', '</dt', '<dd', '</dd'), array('<tr', '</tr', '<td', '</td', '<td', '</td'), $html);
		$html = str_replace(array('<h2', '</h2>'), array('<tr><td', '</td></tr>'), $html);
		$html = '<table>'.$html.'</table>';

		#WCPL_Helper::_debug($html);

		$html_dom = str_get_html($html);

		$html                      = array();
		$prev_n_product_spec__name = null;
		$tr_i                      = 0;
		foreach($html_dom->find('tr') as $tr){
			if($tr->find('.title', 0)){
				$html[$tr_i]['title'] = trim($tr->find('.title', 0)->innertext);
			}
			if($tr->find('.n-product-spec__name', 0)){
				$html[$tr_i]['name'] = trim($tr->find('.n-product-spec__name', 0)->innertext);
			}
			if($tr->find('.n-product-spec__value', 0)){
				$html[$tr_i]['value'] = trim($tr->find('.n-product-spec__value', 0)->innertext);
			}
			$tr_i++;
		}
		$html_dom->clear();
		unset($html_dom);

		#WCPL_Helper::_debug($html);

		$prev_name = '';
		foreach($html as $k => $v){
			if(isset($v['title'])){
				$output[] = '<tr><th colspan="2" class="title">'.$v['title'].'</th></tr>';
			}else{
				if(isset($v['name']) && !isset($v['value'])){
					$prev_name = $v['name'];
				}elseif(isset($v['name']) && isset($v['value']) && empty($v['name'])){
					$output[] = '<tr><td class="name">'.$prev_name.'</td><td class="value">'.$v['value'].'</td></tr>';
				}else{
					$output[] = '<tr><td class="name">'.$v['name'].'</td><td class="value">'.$v['value'].'</td></tr>';
				}
			}
		}
		#WCPL_Helper::_debug($output);

		$html = implode('', $output);
		$html = '<table class="ym_specs">'.$html.'</table>';
		unset($output);

		#WCPL_Helper::_debug($html);
		#exit;

		return $html;
	}

	public static function format_specifications_meta2($html_specifications){
		$enter  = chr(13).chr(10);
		$tab    = chr(9);
		$slash  = chr(92);
		
		#WCPL_Helper::_debug($html_specifications);
		
		if(strstr($html_specifications, '<div') === false){
			return $html_specifications;
		}

		$html_dom = str_get_html($html_specifications);

		// Удаляем все внутренние теги div
		foreach($html_dom->find('dt') as $dt){
			if($div = $dt->find('div', 0)){
				$div->outertext = strip_tags($div->outertext);
			}
		}

		// Удаляем все внутренние теги dd из a
		foreach($html_dom->find('a') as $a){
			if($dd = $a->find('dd', 0)){
				$dd->outertext = strip_tags($dd->outertext);
			}
		}

		$html = $html_dom->innertext;
		unset($html_dom);

		$html = str_replace([$enter, $tab, $slash], '', $html);
		$html = str_replace('<div', '<table', $html);
		$html = str_replace('<div class="n-hint-button i-bem n-hint-button_js_inited" data-bem="{&quot;n-hint-button&quot;:{&quot;place&quot;:&quot;&quot;}}">', '', $html);
		$html = str_replace('</div>', '</table>', $html);
		$html = str_replace('<span class="n-product-spec__name-inner">', '', $html);
		$html = str_replace('<span class="n-product-spec__value-inner">', '', $html);
		$html = str_replace('</span>', '', $html);
		$html = str_replace(['<dl', '</dl', '<dt', '</dt', '<dd', '</dd'], ['<tr', '</tr', '<td', '</td', '<td', '</td'], $html);
		$html = str_replace(['<h2', '</h2>'], ['<tr><th colspan="2"', '</th></tr>'], $html);
		$html = str_replace('</tr><a', '<td', $html);
		$html = str_replace('</a>', '</td></tr>', $html);

		#WCPL_Helper::_debug($html); exit;

		return $html;
	}

	public static function format_specifications_meta3($html_specifications){
		return strip_tags($html_specifications, "<div><h2><dl><dt><dd>");
	}

	public static function update_yml_feed_prices($feed_file, $products){
		$ret = ['updates' => [], 'count' => 0];

		$new_feed_file = str_replace('feed-yml', time().'-feed-yml', $feed_file);

		$yml_catalog = simplexml_load_file($feed_file);

		$offers = $yml_catalog->shop->offers->offer;

		foreach($offers as $k => $offer){
			$id = intval($offer["id"]);
			if(isset($products[$id])){
				if($products[$id] != intval($offer->price)){
					$offer->price = intval($products[$id]);
					$ret['updates'][$id] = $products[$id];
				}
			}
			if(strstr($offer->url, '?post_type=product') !== false){
				wp_update_post(['ID' => $id]);
				$offer->url = get_permalink($id);
			}
		}

		$ret['count'] = count($ret['updates']);

		if($ret['count'] > 0){
			$yml_catalog->asXML($feed_file);
		}

		$ret['new_feed_file'] = $new_feed_file;

		return $ret;
	}


}
