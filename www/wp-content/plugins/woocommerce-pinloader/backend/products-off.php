<?php

/**
 * TODO
 * 1. Написать JS для событий switchoff_one и switchoff_all
 * 2. Написать обработчик событий switchoff_one и switchoff_all
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
WCPL_Helper::set_session_data();

if(empty($_POST)) {
	$_POST = $_SESSION['post'];
} else {
	$_SESSION['post'] = $_POST;
}

$suppliers_options = $order_options = $paged_arr = $diapasons = array();
$offset = 0;
$limit = 15;

/*
WCPL_Data_Source::query("DELETE FROM mod_products_new WHERE products_id AND products_id NOT IN (SELECT ID FROM ml_posts)");
WCPL_Data_Source::query("INSERT IGNORE INTO  mod_products_price (products_id, new_price) SELECT products_id, price FROM mod_products_new WHERE products_id AND price != 0 ON DUPLICATE KEY UPDATE new_price = mod_products_new.price");
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

$results = WCPL_Data_Source::get_results("SELECT * FROM mod_suppliers WHERE id_supplier ORDER BY name_supplier");
foreach($results as $result){
	$suppliers[$result->id_supplier] = $result->name_supplier;
}
foreach($suppliers as $key => $val){
	$sel = (isset($_SESSION['get']['id_supplier']) && $key == $_SESSION['get']['id_supplier']) ? "selected" : "";
	$suppliers_options[] = '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
}

if(!isset($_GET['sort_order'])){
	$_GET['sort_order'] = 1;
}
$order_options_arr = array(
	1 => "Названию товара &darr;",
	2 => "Названию товара &uarr;",
	3 => "Коду товара &darr;",
	4 => "Коду товара &uarr;",
	5 => "Поставщику &darr;",
	6 => "Поставщику &uarr;",
);
foreach($order_options_arr as $i => $v) {
	$sel = (isset($_GET['sort_order']) && $i == $_GET['sort_order']) ? "selected" : "";
	$order_options[] = '<option value="'.$i.'" '.$sel.'>'.$v.'</option>';
}
if(isset($_GET['sort_order']) && intval($_GET['sort_order'])){
	switch($_GET['sort_order']) {
		case 1:
			$sort_order = "products_name ASC";
			break;
		case 2:
			$sort_order = "products_name DESC";
			break;
		case 3:
			$sort_order = "products_model ASC";
			break;
		case 4:
			$sort_order = "products_model DESC";
			break;
		case 5:
			$sort_order = "id_supplier ASC";
			break;
		case 6:
			$sort_order = "id_supplier DESC";
			break;
		default:
			$sort_order = "products_id ASC";
			break;
	}
}

$where = $where1 = $where2 = "";
if(!empty($s_products_name)){
	$where .= " AND products_name LIKE '%".WCPL_Data_Source::db_input($s_products_name)."%' ";
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
				  AND (tt.taxonomy = 'suppliers'
			           AND tt.term_id = s.term_id
			           AND tr.term_taxonomy_id = tt.term_taxonomy_id
			           AND p.ID = tr.object_id)
				  AND m.id_supplier = s.id_supplier 
				  AND NOT m.products_id";
WCPL_Data_Source::query($_sql);
//WCPL_Data_Source::update_mod_products_new_ids();*/


/*$_sql = "SELECT p.ID, pm.meta_value AS products_model, p.post_title, s.name_supplier
		 FROM ml_posts p, mod_suppliers s, ml_postmeta pm, ml_term_taxonomy tt, ml_term_relationships tr
		 WHERE p.ID NOT IN (SELECT products_id FROM mod_products_new WHERE products_id AND `ignore` = 'no' AND current_list = 'on') 
		  AND p.post_type = 'product'
		  AND p.post_status = 'publish'
		  AND pm.post_id = p.ID
		  AND (pm.meta_key = 'products_model' AND pm.meta_value NOT IN ('ML', 'ML-AG', 'ML-ME', 'ML-GR'))
		  AND (tt.taxonomy = 'suppliers' AND tt.term_id = s.term_id AND tr.term_taxonomy_id = tt.term_taxonomy_id AND p.ID = tr.object_id) $where2 
		 LIMIT ".$offset.", ".$limit;*/

$_sql = "SELECT COUNT(*) AS cnt FROM mod_products WHERE (products_status = 'publish' OR products_status = 'pending') AND products_id NOT IN (SELECT products_id FROM mod_products_new WHERE products_id AND `ignore` = 'no' AND current_list = 'on') ".$where;
$products_found = WCPL_Data_Source::get_var($_sql);
if(isset($_SESSION['get']['paged'])){
	if(intval($_SESSION['get']['paged']) * $limit > $products_found){
		$_SESSION['get']['paged'] = ceil($products_found / $limit);
	}
	$offset = $_SESSION['get']['paged'] * $limit - $limit;
	if($offset < 0){
		$offset = 0;
	}
}


$_sql = "SELECT products_id 
		 FROM mod_products 
		 WHERE (products_status = 'publish' OR products_status = 'pending' OR products_status = 'draft') 
		   AND products_id NOT IN (SELECT products_id FROM mod_products_new WHERE products_id AND `ignore` = 'no' AND current_list = 'on') 
		   ".$where;
$results = WCPL_Data_Source::get_results($_sql);
if($results){
	foreach($results as $result){
		$products_ids_to_switch_off[] = intval($result->products_id);
	}
}
WCPL_Data_Source::query("INSERT IGNORE INTO mod_temp SET sid = '".session_id()."', type = 'switchoffall', data = '".WCPL_Data_Source::db_input(serialize($products_ids_to_switch_off))."', date = NOW() ON DUPLICATE KEY UPDATE data = '".WCPL_Data_Source::db_input(serialize($products_ids_to_switch_off))."', type = 'switchoffall', date = NOW()");


$_sql = "SELECT * FROM mod_products 
		 WHERE (products_status = 'publish' OR products_status = 'pending') 
		   AND products_id NOT IN (SELECT products_id FROM mod_products_new WHERE products_id AND `ignore` = 'no' AND current_list = 'on') 
		   ".$where."
    	 ORDER BY ".$sort_order." 
    	 LIMIT ".$offset.", ".$limit;
$results = WCPL_Data_Source::get_results($_sql);
$products1 = array();
if($results){
	foreach($results as $result){
		$products1[] = $result;
	}
}
#WCPL_Helper::_debug($results);
//$navi = new WCPL_Paginate_Navigation_Builder("/wp-admin/admin.php?page=".$_REQUEST['page']);
$navi = new WCPL_Paginate_Navigation_Builder($_SERVER['REQUEST_URI']);
$pagination_template = $navi->build($limit, $products_found, $_SESSION['get']['paged']);

?>


<div class="bootstrap form-table" data-example-id="hoverable-table">
	<div class="js_ajax_message loader"><?=esc_attr__('Sending request...', PINLOADER_TEXT_DOMAIN);?></div>

	<div class="bg-info p-5 brr-5">
		<form id="form_ctrl" method="get" action="<?=$_SERVER['PHP_SELF'];?>" class="form-inline">
			<input type="hidden" name="page" value="<?=$_GET['page'];?>">
			<div class="form-group">
				<div class="btn btn-success btn-sm"><?=esc_attr__('Products', PINLOADER_TEXT_DOMAIN);?> <span class="badge"><?=$products_found;?></span></div>
			</div>
			<div class="form-group">
				<input type="text" name="products_name" value="<?=$s_products_name;?>" placeholder="Название товара"/>
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

		<?php if(!empty($products1)):?>
			<table id="new_products" class="table table-hover table-striped">
				<thead>
					<tr>
						<th class="column-pid">ID (Т)</th>
						<th class="column-title">Название</th>
						<th class="column-supplier">Поставщик</th>
						<th class="column-model-md">Код</th>
						<th class="column-actions-sm">Действия</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($products1 as $key => $val):
					//$products_ids_to_switch_off[] = intval($val->products_id);
				?>
					<tr>
						<td><?=$val->products_id;?></td>
						<td><?=$val->products_name;?></td>
						<td><?=$val->name_supplier;?></td>
						<td><?=$val->products_model;?></td>
						<td><a href="javascript:;" role="button" data-source="switchoff_one" data-action="js_ajax" data-pid="<?=$val->products_id;?>" class="btn btn-danger btn-sm"><?=esc_attr__('Switch Off', PINLOADER_TEXT_DOMAIN);?></a></td>
					</tr>
				<?php endforeach;?>
				</tbody>
			</table>
			<div class="text-center"><a href="javascript:;" role="button" data-source="switchoff_all" data-action="js_ajax" class="btn btn-danger btn-lg"><?=esc_attr__('Switch Off All', PINLOADER_TEXT_DOMAIN);?></a></div>
			<?php //WCPL_Data_Source::query("INSERT IGNORE INTO mod_temp SET sid = '".session_id()."', type = 'switchoffall', data = '".WCPL_Data_Source::db_input(serialize($products_ids_to_switch_off))."', date = NOW() ON DUPLICATE KEY UPDATE data = '".WCPL_Data_Source::db_input(serialize($products_ids_to_switch_off))."', type = 'switchoffall', date = NOW()");?>
		<?php endif;?>

	</div>
</div>
