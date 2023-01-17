<?php

/**
 * TODO
 */

use Pinloader\WcPinLoader;
use Pinloader\WCPL_Data_Source;
use Pinloader\WCPL_Helper;
use Pinloader\WCPL_Paginate_Navigation_Builder;


/*if(empty($_GET)){
	$_GET = $_SESSION['get'];
}else{
	$_SESSION['get'] = $_GET;
}

if(!isset($_SESSION['get']['paged'])){
	$_SESSION['get']['paged'] = 0;
}*/

if(empty($_POST)) {
	$_POST = isset($_SESSION['post']) ? $_SESSION['post'] : [];
} else {
	$_SESSION['post'] = $_POST;
}

WCPL_Helper::set_session_data();

$suppliers_options = $order_options = $paged_arr = $diapasons = $zero_products_ids = $zero_auto_products_ids = $zero_auto_products_prices = array();
$offset = 0;
$limit = 15;

/*
WCPL_Data_Source::query("DELETE FROM mod_products_new WHERE products_id AND products_id NOT IN (SELECT ID FROM ml_posts)");
WCPL_Data_Source::query("INSERT IGNORE INTO mod_products_price (products_id, new_price) SELECT products_id, price FROM mod_products_new WHERE products_id AND price != 0 ON DUPLICATE KEY UPDATE new_price = mod_products_new.price");
WCPL_Data_Source::query("UPDATE mod_products_price SET price = new_price WHERE NOT price");
*/
//WCPL_Data_Source::sync_products_prices();

/*if(isset($_GET['switchoffall']) && intval($_GET['switchoffall']) == 1) {
	WCPL_Data_Source::query("DELETE FROM mod_temp WHERE date < DATE_SUB(NOW(), INTERVAL 4 HOUR) ");

	$result = WCPL_Data_Source::get_row("SELECT * FROM mod_temp WHERE sid = '".session_id()."' AND type = 'switchoffall'");
	if($result){
		$products_ids_to_switch_off = unserialize($result->data);
		if(is_array($products_ids_to_switch_off)){
			WCPL_Data_Source::query("UPDATE products SET products_status = 0 WHERE products_id IN (".implode(",", $products_ids_to_switch_off).")");
		}
	}
}*/

/*if(isset($_GET['action'])){
	switch($_GET['action']){
		case "change":
			$_sql = "UPDATE mod_products_price SET price = new_price WHERE products_id IN (".intval($_GET['products_id']).")";
			WCPL_Data_Source::query($_sql);
			break;
		case "switchon":
			$_sql = "UPDATE products SET products_status = 1 WHERE products_id IN (".intval($_GET['products_id']).")";
			WCPL_Data_Source::query($_sql);
			break;
		case "switchonallzero":
			$_sql = "UPDATE products SET products_status = 1 WHERE products_id IN (".WCPL_Data_Source::db_input(implode(",", explode("-", $_GET['ids']))).")";
			WCPL_Data_Source::query($_sql);
			break;
		default:
			break;
	}
}*/

$results = WCPL_Data_Source::get_results("SELECT s.diapason, s.name_supplier, d.* FROM mod_suppliers s LEFT JOIN mod_diapasons d ON d.id_supplier = s.id_supplier", ARRAY_A);
foreach($results as $result){
	if(!empty($result['id']) && $result['diapason'] == "1") {
		$diapasons[$result['id_supplier']][$result['id']] = $result;
	}
}

$s_products_name = '';
if(isset($_GET['products_name']) && !empty($_GET['products_name'])){
	$s_products_name = $_GET['products_name'];
}
$s_products_name_new = '';
if(isset($_GET['products_name_new']) && !empty($_GET['products_name_new'])){
	$s_products_name_new = $_GET['products_name_new'];
}
$s_products_model = '';
if(isset($_GET['products_model']) && !empty($_GET['products_model'])){
	$s_products_model = $_GET['products_model'];
}

$results = WCPL_Data_Source::get_results("SELECT * FROM `mod_suppliers` WHERE `id_supplier` ORDER BY `name_supplier` ");
foreach($results as $result){
	$suppliers[$result->id_supplier] = $result->name_supplier;
}
foreach($suppliers as $key => $val){
	$sel = '';
	if(isset($_SESSION['get']['id_supplier']) && $key == $_SESSION['get']['id_supplier']){
		$sel = "selected";
	}
	$suppliers_options[] = '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
}

if(!isset($_GET['sort_order'])){
	$_GET['sort_order'] = 3;
}
$order_options_arr = array(
	1 => "Названию на сайте &darr;",
	2 => "Названию на сайте &uarr;",
	3 => "Названию в прайсе &darr;",
	4 => "Названию в прайсе &uarr;",
	5 => "Наценке &darr;",
	6 => "Наценке &uarr;",
	7 => "Разнице в цене &darr;",
	8 => "Разнице в цене &uarr;",
);
foreach($order_options_arr as $i => $v) {
	$sel = (isset($_GET['sort_order']) && $i == $_GET['sort_order']) ? "selected" : "";
	$order_options[] = '<option value="'.$i.'" '.$sel.'>'.$v.'</option>';
}
//if(isset($_GET['sort_order']) && intval($_GET['sort_order'])){
	switch($_GET['sort_order']) {
		case 1:
			$sort_order = " m.products_name ASC";
			break;
		case 2:
			$sort_order = " m.products_name DESC";
			break;
		case 3:
			$sort_order = " m.products_name_new ASC";
			break;
		case 4:
			$sort_order = " m.products_name_new DESC";
			break;
		case 5:
			$sort_order = " m.nacenka ASC";
			break;
		case 6:
			$sort_order = " m.nacenka DESC";
			break;
		case 7:
			$sort_order = " m.pricediff ASC";
			break;
		case 8:
			$sort_order = " m.pricediff DESC";
			break;
		default:
			$sort_order = " m.products_id ASC";
			break;
	}
//}

$where = "";
if(!empty($s_products_name)){
	$where .= " AND products_name LIKE '%".WCPL_Data_Source::db_input($s_products_name)."%' ";
}
if(!empty($s_products_name_new)){
	$where .= " AND products_name_new LIKE '%".WCPL_Data_Source::db_input($s_products_name_new)."%' ";
}
if(!empty($s_products_model)){
	$where .= " AND products_model LIKE '%".WCPL_Data_Source::db_input($s_products_model)."%' ";
}
if(isset($_SESSION['get']['id_supplier']) && intval($_SESSION['get']['id_supplier'])){
	$where .= " AND id_supplier = ".intval($_SESSION['get']['id_supplier'])." ";
}

/*$_sql = "UPDATE mod_products_new m, ml_posts p, ml_postmeta pm, ml_term_taxonomy tt, ml_term_relationships tr, mod_suppliers s
				SET m.products_id = p.ID 
				WHERE pm.post_id = p.ID
				  AND (pm.meta_key = 'products_model' AND pm.meta_value = m.products_model)
				  AND (tt.taxonomy = 'suppliers' AND tt.term_id = s.term_id AND tr.term_taxonomy_id = tt.term_taxonomy_id AND p.ID = tr.object_id)
				  AND m.id_supplier = s.id_supplier 
				  AND NOT m.products_id";
WCPL_Data_Source::query($_sql);*/
//WCPL_Data_Source::update_mod_products_new_ids();

$products_found = WCPL_Data_Source::get_var("SELECT COUNT(*) FROM mod_products_to_switch_on m WHERE 1=1 $where");
if(empty($products_found)){
	$products2 = WCPL_Data_Source::get_results("SELECT SQL_CALC_FOUND_ROWS m.* FROM mod_products_to_switch_on m WHERE 1=1 $where");
	$products_found = WCPL_Data_Source::get_found_rows();
}
if(isset($_SESSION['get']['paged'])){
	if(intval($_SESSION['get']['paged']) * $limit > $products_found){
		$_SESSION['get']['paged'] = ceil($products_found / $limit);
	}
	$offset = $_SESSION['get']['paged'] * $limit - $limit;
	if($offset < 0){
		$offset = 0;
	}
}
$_sql = "SELECT m.* FROM mod_products_to_switch_on m WHERE 1=1 $where ORDER BY $sort_order LIMIT ".$offset.", ".$limit;
$products2 = WCPL_Data_Source::get_results($_sql, ARRAY_A);

$products_quantity_changed = array();
if(isset($_SESSION['get']['id_supplier']) && in_array(intval($_SESSION['get']['id_supplier']), array(8, 9))){
	$_sql = "SELECT c.products_id, (c.ostatok > p.ostatok), (c.roznica > p.roznica) 
			 FROM mod_soho_current c 
			 LEFT JOIN mod_soho_prev p ON (c.products_id = p.products_id) 
			 WHERE ((c.ostatok > p.ostatok OR c.roznica > p.roznica) OR (c.ostatok  IS NOT NULL AND p.ostatok IS NULL)) AND c.id_supplier = ".intval($_SESSION['get']['id_supplier']);
	$product_query = WCPL_Data_Source::get_results($_sql, ARRAY_A);
	if(!empty($product_query)){
		foreach($product_query as $product){
			$products_quantity_changed[$product['products_id']] = $product['products_id'];
		}
	}
}
#WCPL_Helper::_debug($products_quantity_changed);

//$navi = new WCPL_Paginate_Navigation_Builder("/wp-admin/admin.php?page=".$_REQUEST['page']);
$navi = new WCPL_Paginate_Navigation_Builder($_SERVER['REQUEST_URI']);
$pagination_template = $navi->build($limit, $products_found, $_SESSION['get']['paged']);

$_sql = "SELECT m.* FROM mod_products_to_switch_on m WHERE 1=1 $where ORDER BY $sort_order";
$products_all = WCPL_Data_Source::get_results($_sql, ARRAY_A);

?>


<div class="bootstrap form-table" data-example-id="hoverable-table">
	<div class="js_ajax_message loader"><?=esc_attr__('Sending request...', PINLOADER_TEXT_DOMAIN);?></div>

	<div class="bg-info p-5 brr-5">
		<form method="get" action="<?=$_SERVER['PHP_SELF'];?>" class="form-inline">
			<input type="hidden" name="page" value="<?=$_GET['page'];?>">
			<div class="form-group">
				<div class="btn btn-success btn-sm"><?=esc_attr__('Products', PINLOADER_TEXT_DOMAIN);?> <span class="badge"><?=$products_found;?></span></div>
			</div>
			<div class="form-group">
				<input type="text" name="products_name" value="<?=$s_products_name;?>" placeholder="Название на сайте"/>
			</div>
			<div class="form-group">
				<input type="text" name="products_name_new" value="<?=$s_products_name_new;?>" placeholder="Название в прайсе"/>
			</div>
			<div class="form-group">
				<input type="text" name="products_model" value="<?=$s_products_model;?>" placeholder="Код"/>
			</div>
			<div class="form-group">
				<select name="id_supplier">
					<option value=0>- Выберите поставщика -</option>
					<?=implode('', $suppliers_options);?>
				</select>
			</div>
			<div class="form-group">
				<select name="sort_order">
					<option value=0>- Сортировка -</option>
					<?=implode('', $order_options);?>
				</select>
			</div>
			<button type="submit" class="btn btn-info btn-sm">Фильтр</button>
		</form>
	</div>

	<nav aria-label="Page navigation">
		<?=$pagination_template;?>
	</nav>

	<div>

		<?php if(is_array($products2)):?>
			<table id="products_off" class="table table-hover table-striped">
				<thead>
					<tr>
						<th class="column-pid">ID (Т)</th>
						<th class="column-title">Название товара (На сайте)</th>
						<th class="column-title">Название товара (В прайсе)</th>
						<th class="column-supplier">Поставщик</th>
						<th class="column-model">Код</th>
						<th class="column-price">Цена на сайте</th>
						<th class="column-price">Старая цена</th>
						<th class="column-price">Новая цена</th>
						<th class="column-price">Разница в цене</th>
						<th class="column-price">Наценка</th>
						<th class="column-actions">Действия</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($products2 as $key => $val):
					$row_class = "";
					if(is_array($products_quantity_changed) && in_array($val['products_id'], $products_quantity_changed)){
						unset($products_quantity_changed[$val['products_id']]);
						$row_class = "bg-warning";
					}
					$color = intval($val['pricediff']) < 0 ? "red" : "blue";
					$color1 = intval($val['nacenka']) < 0 ? "red" : "inherit";
					$val['price']     = money_format('%!.0n', $val['price']);
					$val['new_price'] = money_format('%!.0n', $val['new_price']);
					$val['pricediff'] = money_format('%!.0n', $val['pricediff']);
					$val['nacenka']   = money_format('%!.0n', $val['nacenka']);
					if(is_array($diapasons[$val['id_supplier']])){
						$inputReadonly = "readonly";
						$oldPrice      = "<br />{$val['products_price']}";
						foreach($diapasons[$val['id_supplier']] as $kk => $vv){
							if($val['new_price'] >= $vv['price_low'] && $val['new_price'] <= $vv['price_high']){
								if(intval($vv['floor']) <= 0){
									$vv['floor'] = 1;
								}
								$val['products_price']       = floor(($val['new_price'] * $vv['coefficient'] + $vv['delivery']) / $vv['floor']) * $vv['floor'] - $vv['minus'];
								$zero_auto_products_ids[]    = $val['products_id'];
								$zero_auto_products_prices[] = $val['products_price'];
								break;
							}
						}
					}else{
						$inputReadonly = "";
						$oldPrice      = "";
						if(ceil($val['pricediff']) == 0){
							$zero_products_ids[] = $val['products_id'];
						}
					}?>
					<tr class="<?=$row_class;?>" id="tr_nacenka_<?=$val['products_id'];?>">
						<td><?=$val['products_id'];?></td>
						<td><?=$val['products_name'];?></td>
						<td><?=$val['products_name_new'];?></td>
						<td><?=$val['name_supplier'];?></td>
						<td><?=$val['products_model'];?></td>
						<td><input type="text" name="product_price" class="input-small-1" value="<?=$val['products_price'];?>" data-source="change_price" data-action="js_change" data-pid="<?=$val['products_id'];?>" <?=$inputReadonly;?> /><?=$oldPrice;?></td>
						<td align="right"><?=$val['price'];?></td>
						<td align="right"><span class="new_price"><?=$val['new_price'];?></span></td>
						<td style="color: <?=$color;?>"  align="right"><?=$val['pricediff'];?></td>
						<td id="td_nacenka_<?=$val['products_id'];?>" style="color: <?=$color1;?>" align="right"><?=floor($val['products_price'] - $val['new_price']).( $inputReadonly ? "<br />{$val['nacenka']}" : "");?></td>
						<td>
							<div class="btn-group">
								<button type="button" role="button" data-source="switchon_one" data-action="js_ajax" data-pid="<?=$val['products_id'];?>" class="btn btn-danger btn-sm"><?=esc_attr__('Switch On', PINLOADER_TEXT_DOMAIN);?></button>
								<button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="<?=get_permalink($val['products_id']);?>" target="_blank"><?=esc_attr__('View', PINLOADER_TEXT_DOMAIN);?></a></li>
									<li><a href="<?=WCPL_Helper::product_edit_link($val['products_id']);?>" target="_blank"><?=esc_attr__('Edit', PINLOADER_TEXT_DOMAIN);?></a></li>
									<li><a href="<?=WCPL_Helper::yandex_search_link($val['products_name']);?>" target="_blank"><?=esc_attr__('Search in Yandex', PINLOADER_TEXT_DOMAIN);?></a></li>
								</ul>
							</div>
						</td>
					</tr>
				<?php endforeach;?>
				</tbody>
			</table>
			<?php
			foreach($products_all as $key => $val){
				if(is_array($diapasons[$val['id_supplier']])){
					foreach($diapasons[$val['id_supplier']] as $kk => $vv){
						if($val['new_price'] >= $vv['price_low'] && $val['new_price'] <= $vv['price_high']){
							if(intval($vv['floor']) <= 0){
								$vv['floor'] = 1;
							}
							$val['products_price']       = floor(($val['new_price'] * $vv['coefficient'] + $vv['delivery']) / $vv['floor']) * $vv['floor'] - $vv['minus'];
							$zero_auto_products_ids[]    = $val['products_id'];
							$zero_auto_products_prices[] = $val['products_price'];
							break;
						}
					}
				}else{
					if(ceil($val['pricediff']) == 0){
						$zero_products_ids[] = $val['products_id'];
					}
				}
			}
			if(count($zero_products_ids) == 1) {
				$zero_products_ids[] = 0;
			}
			if(count($zero_auto_products_ids) == 1) {
				$zero_auto_products_ids[] = 0;
				$zero_auto_products_prices[] = 0;
			}
			?>
			<?php if(is_array($zero_products_ids) && !empty($zero_products_ids)):?>
				<div class="text-center">
					<a href="javascript:;" role="button" class="btn btn-danger btn-lg"
					   data-source="switchon_all_manual"
					   data-action="js_ajax"
					   data-rowprefix="tr_nacenka_"
					   data-ids="<?=implode(",", $zero_products_ids)?>">Включить все без изменения цены</a>
				</div>
			<?php endif;?>
			<?php if(is_array($zero_auto_products_ids) && !empty($zero_auto_products_ids)):?>
				<div class="text-center">
					<a href="javascript:;" role="button" class="btn btn-danger btn-lg"
					   data-source="switchon_all_auto"
					   data-action="js_ajax"
					   data-rowprefix="tr_nacenka_"
					   data-ids="<?=implode(",", $zero_auto_products_ids)?>"
					   data-prices="<?=implode(",", $zero_auto_products_prices)?>">Включить все автоматом</a>
				</div>
			<?php endif;?>
		<?php endif;?>

	</div>
</div>
