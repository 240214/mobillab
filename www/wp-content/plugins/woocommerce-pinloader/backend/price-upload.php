<?php
use Pinloader\WcPinLoader;
use Pinloader\WCPL_Tools;
use Pinloader\WCPL_Data_Source;
use Pinloader\WCPL_Helper;

WCPL_Helper::set_session_data();

$suppliers = WCPL_Data_Source::get_mod_suppliers_list();
$suppliers_select_options = array(0 => esc_attr__('- Select supplier -', PINLOADER_TEXT_DOMAIN)) + $suppliers;
#WCPL_Helper::_debug($suppliers_select_options);
$display_form = true;
$message = array('class' => '', 'content' => '');
$content = '';
//WCPL_Helper::_debug(session_id());

// Upload process
if(!empty($_FILES)){
	if($_FILES["file"]["error"] > 0){
		$message['class']   = 'bg-danger';
		$message['content'] = "Error: ".$_FILES["file"]["error"]."<br>";
	}elseif(intval($_FILES["file"]["size"])){
		$display_form = false;
		//  echo "Upload: " . $_FILES["file"]["name"] . "<br>";
		//  echo "Type: " . $_FILES["file"]["type"] . "<br>";
		//  echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
		//  echo "Stored in: " . $_FILES["file"]["tmp_name"];


		$data = new Spreadsheet_Excel_Reader($_FILES["file"]["tmp_name"]);
		//WCPL_Helper::_debug($data->sheets);

		$product_codes['id_supplier'] = $_POST['id_supplier'];

		$supplierFieldOrder = WCPL_Data_Source::get_mod_supplier($_POST['id_supplier']);
		#WCPL_Helper::_debug($supplierFieldOrder); exit;

		if(in_array(intval($_POST['id_supplier']), array(17, 18, 19))){ // 11 12 14
			$sheetNumber = 2;
			$sheetNumber = 0;
		}else{
			$sheetNumber = 0;
		}

		if(!is_array($data->sheets[$sheetNumber]['cells']) && $_POST['id_supplier'] == 7){ //18
			$file = file_get_contents($_FILES["file"]["tmp_name"]);
			setlocale(LC_CTYPE, 'ru_RU');
			$file = iconv('windows-1251', 'UTF-8', $file);
			$file = explode("\n", $file);
			foreach($file as $key => $val){
				unset($data);
				if(stristr($val, "<td ")){
					$data = strip_tags($val);
					if(!stristr($val, "</td>")){
						$data .= strip_tags($file[($key + 1)]);
					}
				}
				$arr[] = $data;
			}
			$arr = array_filter($arr);
			$arr = array_filter($arr);
			foreach($arr as $key => $val){
				if(trim($val) != "Название" && trim($val) != "Цена" && trim($val)){
					$ar1[] = $val;
				}
			}
			foreach($ar1 as $key => $val){
				$kk++;
				if($kk % 2 and intval(trim($ar1[($key + 1)])) > 0){
					$product_codes['data'][] = array(
						"code"  => preg_replace("/[^A-Za-z0-9 ]/", '', str_replace(" ", "", str_replace(chr(160), " ", preg_replace("/\([^)]+\)/", "", trim($val))))),
						"name"  => trim(str_replace(chr(160), " ", $val)),
						"price" => intval(trim(str_replace(" ", "", str_replace(",", "", $ar1[($key + 1)]))))
					);
				}
			}
		}

		if(is_array($data->sheets[$sheetNumber]['cells'])){
			/*if(intval($_POST['id_supplier']) == 16){
				WCPL_Helper::_debug($data->sheets[$sheetNumber]['cells']);
			}*/
			foreach($data->sheets[$sheetNumber]['cells'] as $key => $val){
				#WCPL_Helper::_debug($val);
				switch(intval($_POST['id_supplier'])){
					case 11: //2
						if(strlen(trim($val[$supplierFieldOrder->code])) == 13 || is_numeric(trim($val[$supplierFieldOrder->code])) || preg_match('#^[a-zA-Z0-9-/\s]+$#', trim($val[$supplierFieldOrder->code]))){
							$product_codes['data'][] = array("code" => trim($val[$supplierFieldOrder->code]), "name" => trim($val[$supplierFieldOrder->name]), "price" => intval(str_replace(",", "", trim($val[$supplierFieldOrder->price])) * $supplierFieldOrder->coefficient));
						}
						break;
					case 4: //3
					case 3: //19
					case 2: //20
					case 1: //21
						if((intval(preg_replace('/\D/', '', trim($val[$supplierFieldOrder->code]))) && intval(trim(str_replace(",", "", $val[$supplierFieldOrder->price])))) || preg_match('/[{Latin}]+/', trim($val[$supplierFieldOrder->code]))){
							$product_codes['data'][] = array("code" => trim($val[$supplierFieldOrder->code]), "name" => trim($val[$supplierFieldOrder->name]), "price" => intval(str_replace(",", "", trim($val[$supplierFieldOrder->price])) * $supplierFieldOrder->coefficient));
						}
						break;
					case 16: //4
						if(trim($val[$supplierFieldOrder->code]) && intval(trim(str_replace(",", "", $val[$supplierFieldOrder->price])))){
							$product_codes['data'][] = array(
								"code"  => trim($val[$supplierFieldOrder->code]),
								"name"  => trim($val[$supplierFieldOrder->name]),
								"price" => intval(str_replace(",", "", trim($val[$supplierFieldOrder->price])) * $supplierFieldOrder->coefficient)
							);
						}
						break;
					case 15: //5
						if(trim($val[$supplierFieldOrder->code]) && intval(trim(str_replace(",", "", $val[$supplierFieldOrder->price])))){
							$product_codes['data'][] = array("code" => trim($val[$supplierFieldOrder->code]), "name" => trim($val[$supplierFieldOrder->name]), "price" => intval(str_replace(",", "", trim($val[$supplierFieldOrder->price])) * $supplierFieldOrder->coefficient));
						}
						break;
					case 13: //7
						if(is_numeric(trim($val[$supplierFieldOrder->code]))){
							$product_codes['data'][] = array("code" => intval($val[$supplierFieldOrder->code]), "name" => trim($val[$supplierFieldOrder->name]), "price" => intval(str_replace(",", "", trim($val[$supplierFieldOrder->price])) * $supplierFieldOrder->coefficient));
						}
						break;
					case 12: //8
						if(is_numeric(trim($val[$supplierFieldOrder->code]))){
							$product_codes['data'][] = array("code" => intval($val[$supplierFieldOrder->code]), "name" => trim($val[$supplierFieldOrder->name]), "price" => intval(str_replace(",", "", trim($val[$supplierFieldOrder->price])) * $supplierFieldOrder->coefficient));
						}
						break;
					case 14: //1
						if(is_numeric(trim($val[$supplierFieldOrder->code]))){
							$product_codes['data'][] = array("code" => intval($val[$supplierFieldOrder->code]), "name" => trim($val[$supplierFieldOrder->name]), "price" => intval(str_replace(",", "", trim($val[$supplierFieldOrder->price])) * $supplierFieldOrder->coefficient));
						}
						break;
					case 6: //13
						if(trim($val[$supplierFieldOrder->name]) && intval($val[$supplierFieldOrder->price]) > 0 && trim($val[$supplierFieldOrder->code])){
							$product_codes['data'][] = array("code" => trim($val[$supplierFieldOrder->code]), "name" => trim($val[$supplierFieldOrder->name]), "price" => intval(intval(str_replace(",", "", $val[$supplierFieldOrder->price])) * $supplierFieldOrder->coefficient));
						}
						break;
					case 8: //16
					case 9: //17
						if(trim($val[$supplierFieldOrder->name]) && intval(str_replace(",", "", $val[$supplierFieldOrder->price])) > 0 && trim($val[$supplierFieldOrder->code])){
							$product_codes['data'][] = array(
								"code"    => trim($val[$supplierFieldOrder->code]),
								"name"    => trim($val[$supplierFieldOrder->name]),
								"price"   => intval(intval(str_replace(",", "", $val[$supplierFieldOrder->price])) * $supplierFieldOrder->coefficient),
								"ostatok" => intval(filter_var($val[5], FILTER_SANITIZE_NUMBER_INT)),
								"roznica" => intval(filter_var($val[6], FILTER_SANITIZE_NUMBER_INT))
							);
						}
						break;
					case 17: //11
					case 18: //12
					case 19: //14
						if(intval(preg_replace("/[^0-9 ]/", '', $val[$supplierFieldOrder->price - 3])) > 0 && intval(preg_replace("/[^0-9 ]/", '', $val[$supplierFieldOrder->price - 2])) > 0 && intval(str_replace(",", "", trim($val[$supplierFieldOrder->price]))) > 0){
							$product_codes['data'][] = array(
								"code"  => preg_replace("/[^A-Za-z0-9 ]/", '', str_replace(" ", "", str_replace(chr(160), " ", preg_replace("/\([^)]+\)/", "", trim($val[$supplierFieldOrder->code]))))),
								"name"  => trim(str_replace(chr(160), " ", $val[$supplierFieldOrder->name])),
								"price" => intval(str_replace(",", "", trim($val[$supplierFieldOrder->price])) * $supplierFieldOrder->coefficient)
							);
						}
						break;
					case 7: //18
						if(intval($val[$supplierFieldOrder->price]) > 0 && !empty($val[$supplierFieldOrder->name])){
							$product_codes['data'][] = array(
								"code"  => preg_replace("/[^A-Za-z0-9 ]/", '', str_replace(" ", "", str_replace(chr(160), " ", preg_replace("/\([^)]+\)/", "", trim($val[$supplierFieldOrder->name]))))),
								"name"  => trim(str_replace(chr(160), " ", $val[$supplierFieldOrder->name])),
								"price" => intval(trim(str_replace(" ", "", str_replace(",", "", $val[$supplierFieldOrder->price]))))
							);
						}
						break;
					case 5: //10
						if(is_numeric(trim(str_replace(",", "", $val[$supplierFieldOrder->price]))) && intval(trim(str_replace(",", "", $val[$supplierFieldOrder->price]))) > 0){
							$thisCode = trim($val[$supplierFieldOrder->code]);
							if(is_numeric(substr($thisCode, 0, 1)) && is_numeric(substr($thisCode, -1))){
								$thisCode = substr($thisCode, 0, strlen($thisCode) / 11);
							}
							$product_codes['data'][] = array("code" => trim($val[$supplierFieldOrder->code]), "name" => trim($val[$supplierFieldOrder->name]), "price" => intval(str_replace(",", "", trim($val[$supplierFieldOrder->price])) * $supplierFieldOrder->coefficient));
							//					$product_codes['data'][] = array("code" => trim($thisCode), "name" => trim($val[$supplierFieldOrder->name]), "price" => intval(str_replace(",", "", trim($val[$supplierFieldOrder->price])) * $supplierFieldOrder->coefficient ) );
						}
						break;
					/*case 6:
					case 9:
						if(substr(trim($val[$supplierFieldOrder->code]), 0, 2) == "39"){
							$product_codes['data'][] = array("code" => trim($val[$supplierFieldOrder->code]), "name" => trim($val[$supplierFieldOrder->name]), "price" => intval(str_replace(",", "", trim($val[$supplierFieldOrder->price])) * $supplierFieldOrder->coefficient));
						}
						break;*/
					case 10: //15
						if(intval(trim(str_replace(",", "", $val[$supplierFieldOrder->price]))) > 0 && !empty($val[$supplierFieldOrder->name]) && !empty($val[$supplierFieldOrder->code])){
							$product_codes['data'][] = array(
								"code"  => trim($val[$supplierFieldOrder->code]),
								"name"  => trim(str_replace(", , шт", "", $val[$supplierFieldOrder->name])),
								"price" => intval(str_replace(",", "", trim($val[$supplierFieldOrder->price])) * $supplierFieldOrder->coefficient)
							);
						}
						break;
					case 20: //22
					case 21:
					case 22:
					case 23:
						if(!isset($val[$supplierFieldOrder->price])){
							$tmp_price = $val[$supplierFieldOrder->price] = 0;
						}else{
							$tmp_price = intval(trim(str_replace(",", "", $val[$supplierFieldOrder->price])));
						}
						if($tmp_price > 0 && !empty($val[$supplierFieldOrder->name]) && !empty($val[$supplierFieldOrder->code])){
							$product_codes['data'][] = array(
								"code"  => trim($val[$supplierFieldOrder->code]),
								"name"  => trim(str_replace(", , шт", "", $val[$supplierFieldOrder->name])),
								"price" => intval(str_replace(",", "", trim($val[$supplierFieldOrder->price])) * $supplierFieldOrder->coefficient)
							);
						}
						break;
					default:
						break;
				}
			}
			/*if(intval($_POST['id_supplier']) == 16){
				WCPL_Helper::_debug($product_codes);
				exit;
			}*/
		}

		if(is_array($product_codes)){

			WCPL_Data_Source::query("DELETE FROM mod_temp WHERE `date` < DATE_SUB(NOW(), INTERVAL 4 HOUR) ");
			$product_query = WCPL_Data_Source::query("INSERT IGNORE INTO `mod_temp` SET `sid` = '".session_id()."', `type` = 'upload_prices', `data` = '".WCPL_Data_Source::db_input(base64_encode(serialize($product_codes)))."', `date` = now() ON DUPLICATE KEY UPDATE `data` = '".WCPL_Data_Source::db_input(base64_encode(serialize($product_codes)))."', `type` = 'upload_prices', `date` = now() ");

			$message['class']   = 'bg-success';
			$message['content'] = esc_attr__('Check supplier price list', PINLOADER_TEXT_DOMAIN).': '.$suppliers[$product_codes['id_supplier']];

			$content .= '
				<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
					<div class="panel panel-default">
						<div class="panel-heading" role="tab" id="headingOne">
							<h4 class="panel-title">
								<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
								'.esc_attr__('Display table', PINLOADER_TEXT_DOMAIN).'
								</a>
							</h4>
						</div>
						<div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
							<div class="panel-body">
				<table class="table table-hover table-striped">
					<thead>
		                <tr class="dataTableHeadingRow">
			                <th class="dataTableHeadingContent">'.esc_attr__('Col Code', PINLOADER_TEXT_DOMAIN).'</th>
			                <th class="dataTableHeadingContent">'.esc_attr__('Product', PINLOADER_TEXT_DOMAIN).'</th>
			                <th class="dataTableHeadingContent">'.esc_attr__('Col price', PINLOADER_TEXT_DOMAIN).'</th>
		                </tr>
	                </thead>
	                <tbody>';
			foreach($product_codes['data'] as $key => $val){
				$content .= '<tr><td>'.$val['code'].'</td><td>'.$val['name'].'</td><td>'.$val['price'].'</td></tr>';
			}
			$content .= '</tbody></table>
						</div>
					</div>
				</div>
			</div>';
			$content .= '<div><a href="?page=wcpl-price-upload&action=uploadfinal" role="button" class="btn btn-success btn-lg">'.esc_attr__('Upload data', PINLOADER_TEXT_DOMAIN).'</a></div>';
		}
	}
}elseif(isset($_REQUEST['action'])){
	if($_REQUEST['action'] == "uploadfinal"){
		$GLOBALS['PINLOADER_STOP_CRON'] = true;
		$display_form = false;

		$product_query = WCPL_Data_Source::get_row("SELECT * FROM mod_temp WHERE sid = '".session_id()."' AND type = 'upload_prices'");
		#WCPL_Helper::_debug("SELECT * FROM mod_temp WHERE sid = '".session_id()."' AND type = 'upload_prices'");
		#WCPL_Helper::_dd($product_query);
		$product_codes = unserialize(base64_decode($product_query->data));
		#WCPL_Helper::_dd($product_codes);
		if(is_array($product_codes)){
			$query_1 = "UPDATE mod_products_new SET current_list = 'off' WHERE id_supplier = {$product_codes['id_supplier']}";
			$res = WCPL_Data_Source::query($query_1);
			#WCPL_Helper::_dd($res);

			if(in_array($product_codes['id_supplier'], [8, 9])){
				$query_111 = "DELETE QUICK FROM mod_soho_temp WHERE id_supplier = {$product_codes['id_supplier']} ";
				#WCPL_Helper::_dd($query_111);
				WCPL_Data_Source::query($query_111);
			}

			foreach($product_codes['data'] as $key => $val){
				$query_1 = "INSERT IGNORE INTO mod_products_new 
							SET 
							    id_supplier = {$product_codes['id_supplier']}, 
							    products_model = '".WCPL_Data_Source::db_input($val['code'])."', 
							    products_name = '".WCPL_Data_Source::db_input(str_replace("'", "", $val['name']))."', 
							    price = {$val['price']}, 
							    current_list = 'on' 
							ON DUPLICATE KEY UPDATE 
								products_name = '".WCPL_Data_Source::db_input(str_replace("'", "", $val['name']))."', 
								price = {$val['price']}, 
								current_list = 'on'
							";
				#WCPL_Helper::_dd($query_1);
				WCPL_Data_Source::query($query_1);

				if(in_array($product_codes['id_supplier'], [8, 9]) && array_key_exists('ostatok', $val) && array_key_exists('roznica', $val)){
					$query_111 = "INSERT IGNORE INTO `mod_soho_temp` SET `id_supplier` = {$product_codes['id_supplier']}, `products_model` = '".WCPL_Data_Source::db_input($val['code'])."', `ostatok` = {$val['ostatok']}, `roznica` = {$val['roznica']} ON DUPLICATE KEY UPDATE `ostatok` = {$val['ostatok']}, `roznica` = {$val['roznica']}";
					WCPL_Data_Source::query($query_111);
				}
			}

			//$_sql = "UPDATE mod_products_new m, products p SET m.products_id = p.products_id WHERE p.products_model = m.products_model AND m.id_supplier = p.id_supplier AND m.id_supplier = {$product_codes['id_supplier']} AND NOT m.products_id";
			$_sql = "UPDATE mod_products_new m, ml_posts p, ml_postmeta pm, ml_term_taxonomy tt, ml_term_relationships tr, mod_suppliers s
					SET m.products_id = p.ID 
					WHERE pm.post_id = p.ID 
					  AND (pm.meta_key = 'products_model' AND pm.meta_value = m.products_model)
					  AND (s.id_supplier = {$product_codes['id_supplier']}
					           AND tt.taxonomy = 'suppliers' 
					           AND tt.term_id = s.term_id 
					           AND tr.term_taxonomy_id = tt.term_taxonomy_id 
					           AND p.ID = tr.object_id) 
					  AND m.id_supplier = {$product_codes['id_supplier']} 
					  AND NOT m.products_id";
			$result = WCPL_Data_Source::query($_sql);

			if(in_array($product_codes['id_supplier'], [8, 9])){
				$query_111 = "DELETE QUICK FROM `mod_soho_prev` WHERE `id_supplier` = {$product_codes['id_supplier']} ";
				WCPL_Data_Source::query($query_111);

				$query_111 = "REPLACE INTO `mod_soho_prev` (products_id, id_supplier, ostatok, roznica) SELECT products_id, id_supplier, ostatok, roznica FROM `mod_soho_current` WHERE `id_supplier` = {$product_codes['id_supplier']} ";
				WCPL_Data_Source::query($query_111);

				$query_111 = "DELETE QUICK FROM `mod_soho_current` WHERE `id_supplier` = {$product_codes['id_supplier']} ";
				WCPL_Data_Source::query($query_111);

				$query_111 = "REPLACE INTO `mod_soho_current` (products_id, id_supplier, ostatok, roznica) SELECT n.`products_id`, t.`id_supplier`, t.ostatok, t.roznica FROM `mod_soho_temp` `t`, `mod_products_new` `n` WHERE t.`id_supplier` = n.`id_supplier` AND t.`products_model` = n.`products_model` AND t.`id_supplier` = {$product_codes['id_supplier']} ";
				WCPL_Data_Source::query($query_111);

			}

			if($result !== false){
				wp_safe_redirect('/wp-admin/admin.php?page=wcpl-price-upload&action=uploadsuccess');
			}else{
				wp_safe_redirect('/wp-admin/admin.php?page=wcpl-price-upload&action=uploaderror');
			}
			//echo "<script>window.location = '?page=pinloader&section=upload_prices&action=uploadsuccess';</script>";
		}else{
			wp_safe_redirect('/wp-admin/admin.php?page=wcpl-price-upload&action=uploaderror');
			//echo "<script>window.location = '?page=pinloader&section=upload_prices&action=uploaderror';</script>";
		}
		$GLOBALS['PINLOADER_STOP_CRON'] = false;
	}elseif($_REQUEST['action'] == "uploadsuccess"){
		$display_form       = false;
		$message['class']   = 'bg-info';
		$message['content'] = esc_attr__('File uploaded successfully', PINLOADER_TEXT_DOMAIN);
	}elseif($_REQUEST['action'] == "uploaderror"){
		$message['class']   = 'bg-danger';
		$message['content'] = esc_attr__('File upload error', PINLOADER_TEXT_DOMAIN);
	}
}
?>
<?php if(!empty($message['content'])):?>
	<div class="bs-message <?=$message['class'];?>"><?=$message['content'];?></div>
<?php endif;?>
<?php if(!empty($content)):?>
	<div class="inner-content"><?=$content;?></div>
<?php endif;?>
<?php if($display_form):?>
	<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
		<div class="form-group">
			<label for="file" class="col-sm-1 control-label"><?=esc_attr__('File');?></label>
			<div class="col-sm-11">
				<input type="file" name="file" id="file" class="input-md">
			</div>
		</div>
		<div class="form-group">
			<label for="id_supplier" class="col-sm-1 control-label"><?=esc_attr__('Supplier', PINLOADER_TEXT_DOMAIN);?></label>
			<div class="col-sm-11">
				<?=WCPL_Tools::select_field(array('id' => 'id_supplier', 'name' => 'id_supplier',  'options' => $suppliers_select_options, 'class' => 'input-md selectpicker', 'size' => 1, 'value' => $_SESSION['get']['id_supplier']));?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-1"></div>
			<div class="col-sm-11">
				<button type="submit" name="submit" class="btn btn-success btn-md"><?=esc_attr__('Upload');?></button>
			</div>
		</div>
	</form>
<?php endif;?>
