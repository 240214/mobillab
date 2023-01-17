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
}*/
WCPL_Helper::set_session_data();

if(empty($_POST)) {
	$_POST = $_SESSION['post'];
} else {
	$_SESSION['post'] = $_POST;
}

$suppliers_options = $order_options = $paged_arr = $diapasons = $ids_to_purge = $ids_to_purge_arr = array();

WCPL_Data_Source::query("DELETE FROM mod_products_new WHERE products_id AND products_id NOT IN (SELECT ID FROM ml_posts)");
WCPL_Data_Source::query("INSERT IGNORE INTO  mod_products_price (products_id, new_price) SELECT products_id, price FROM mod_products_new WHERE products_id AND price != 0 ON DUPLICATE KEY UPDATE new_price = mod_products_new.price");
WCPL_Data_Source::query("UPDATE mod_products_price SET price = new_price WHERE NOT price");

$results = WCPL_Data_Source::get_results("SELECT COUNT(*) AS cnt, p.id, p.products_id FROM mod_products_new p GROUP BY products_id ORDER BY COUNT(*) DESC");
#WCPL_Helper::_debug($results);
foreach($results as $result){
	if($result->cnt == 1){
		break;
	}elseif($result->cnt > 10){
		continue;
	}
	$ids_to_purge[$result->products_id] = $result->products_id;
}
#WCPL_Helper::_debug($ids_to_purge);

if(!empty($ids_to_purge)){
	$results = WCPL_Data_Source::get_results("SELECT  p.id, p.products_id FROM mod_products_new p WHERE products_id IN ( ".implode(",", $ids_to_purge).")");
	#WCPL_Helper::_debug($results);
	foreach($results as $result){
		$ids_to_purge_arr[$result->products_id][] = $result->id;
	}
	foreach($ids_to_purge_arr as $key => $val) {
		rsort($val);
		$ids_to_purge_arr[$key] = $val;
		$ids_to_purge_query = WCPL_Data_Source::query("DELETE FROM mod_products_new WHERE products_id = $key AND id != {$val[0]}");
	}
}



#FOR SEARCHING
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
	7 => "Коэффициенту &darr;",
	8 => "Коэффициенту &uarr;",
	9 => "Дате &darr;",
	10 => "Фирме &darr;",
	11 => "Заливке &darr;",
);
foreach($order_options_arr as $i => $v) {
	$sel = ($i == $_GET['sort_order']) ? "selected" : "";
	$order_options[] = '<option value="'.$i.'" '.$sel.'>'.$v.'</option>';
}
$sort_order = "m.products_id ASC";
switch($_GET['sort_order']) {
	case 1:
		$sort_order = "m.products_name ASC";
		break;
	case 2:
		$sort_order = "m.products_name DESC";
		break;
	case 3:
		$sort_order = "m.products_name_new ASC";
		break;
	case 4:
		$sort_order = "m.products_name_new DESC";
		break;
	case 5:
		$sort_order = "m.nacenka ASC";
		break;
	case 6:
		$sort_order = "m.nacenka DESC";
		break;
	case 7:
		$sort_order = "m.coefficient ASC";
		break;
	case 8:
		$sort_order = "m.coefficient DESC";
		break;
	case 9:
		$sort_order = "STR_TO_DATE(m.date, '%d.%m.%y') DESC";
		break;
	case 10:
		$sort_order = "lpad(m.firm, 4, '0') DESC";
		break;
	case 11:
		$sort_order = "color DESC";
		break;
	default:
		break;
}
#END

$where = $where1 = $where2 = "";
if(!empty($s_products_name)){
	$where .= " AND m.products_name LIKE '%".WCPL_Data_Source::db_input($s_products_name)."%' ";
	$where1 .= " AND products_name LIKE '%".WCPL_Data_Source::db_input($s_products_name)."%' ";
}
if(!empty($s_products_name_new)){
	$where .= " AND m.products_name_new LIKE '%".WCPL_Data_Source::db_input($s_products_name_new)."%' ";
}
if(!empty($s_products_model)){
	$where .= " AND m.products_model LIKE '%".WCPL_Data_Source::db_input($s_products_model)."%' ";
}
if(isset($_SESSION['get']['id_supplier']) && intval($_SESSION['get']['id_supplier'])){
	$where .= " AND m.id_supplier = ".intval($_SESSION['get']['id_supplier'])." ";
	$where1 .= " AND id_supplier = ".intval($_SESSION['get']['id_supplier'])." ";
}

/*$_sql = "
	SELECT COUNT(*) AS cnt 
	FROM ml_posts p 
	LEFT JOIN ml_postmeta pm ON pm.post_id = p.ID 
	LEFT JOIN ml_term_relationships tr ON tr.object_id = p.ID
	LEFT JOIN ml_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
	LEFT JOIN mod_suppliers s ON s.term_id = tt.term_id
	WHERE p.post_type = 'product' 
	  AND post_status IN ('publish', 'draft', 'pending') 
	  AND tt.taxonomy = 'suppliers' $where1
	UNION 
	SELECT COUNT(*) AS cnt 
	FROM ml_posts p 
	LEFT JOIN ml_postmeta pm ON pm.post_id = p.ID 
	LEFT JOIN ml_term_relationships tr ON tr.object_id = p.ID
	LEFT JOIN ml_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
	LEFT JOIN mod_suppliers s ON s.term_id = tt.term_id
	WHERE p.post_type = 'product' 
	  AND post_status = 'publish' 
	  AND tt.taxonomy = 'suppliers' $where1
";*/
$_sql = "
SELECT COUNT(*) AS cnt FROM mod_products WHERE products_status != 'trash' $where1
UNION 
SELECT COUNT(*) AS cnt FROM mod_products WHERE products_status = 'publish' $where1
";
$results = WCPL_Data_Source::get_results($_sql);
foreach($results as $result){
	$productStats[] = $result->cnt;
}
if(empty($productStats[1])) {
	$productStats[1] = $productStats[0];
}
#WCPL_Helper::_debug($productStats);

#PAGINATION
$offset = 0;
$limit = 10;
$products_found = WCPL_Data_Source::get_var("SELECT COUNT(*) AS cnt FROM mod_products_monitor_price m WHERE 1 = 1 $where ");
if(isset($_SESSION['get']['paged'])){
	if(intval($_SESSION['get']['paged']) * $limit > $products_found){
		$_SESSION['get']['paged'] = ceil($products_found / $limit);
	}
	$offset = $_SESSION['get']['paged'] * $limit - $limit;
	if($offset < 0){
		$offset = 0;
	}
}
$navi = new WCPL_Paginate_Navigation_Builder($_SERVER['REQUEST_URI']);
$pagination_template = $navi->build($limit, $products_found, $_SESSION['get']['paged']);

#DATA
$_sql = "SELECT m.* FROM mod_products_monitor_price m  WHERE 1 = 1 $where ORDER BY $sort_order LIMIT ".$offset.", ".$limit;
#WCPL_Helper::_debug($_sql);
$products = WCPL_Data_Source::get_results($_sql, ARRAY_A);

?>


<div class="bootstrap form-table" data-example-id="hoverable-table">
	<div class="js_ajax_message loader"><?=esc_attr__('Sending request...', PINLOADER_TEXT_DOMAIN);?></div>

	<div class="bg-warning p-5 brr-5 text-right">
		Всего товаров <?=$productStats[0];?>, в том числе включенных <?=$productStats[1];?>
	</div>

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

		<?php if(!empty($products)):?>
			<table id="product_monitoring" class="table table-hover table-striped price-monitoring">
				<thead>
					<tr>
						<th class="column-pid">ID (Т)</th>
						<th class="column-title">Название товара (На сайте)</th>
						<th class="column-title">Название товара (В прайсе)</th>
						<th class="column-supplier">Поставщик</th>
						<th class="column-model">Код</th>
						<th class="column-price">Цена на сайте</th>
						<th class="column-price">Новая цена</th>
						<th class="column-price">Наценка</th>
						<th class="column-price">Коэф.</th>
						<th class="column-price">Мин. цена</th>
						<th class="column-firm">Фирма</th>
						<th class="column-different">Разница</th>
						<th class="column-date">Дата</th>
						<th class="column-actions-sm">Действия</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($products as $key => $val):
					$val['new_price'] = money_format('%!.0n', $val['new_price']);
					$val['nacenka'] = money_format('%!.0n', $val['nacenka']);
					if(!empty($val['raznica'])) {
						$val['raznica'] = money_format('%!.0n', $val['raznica']);
					}
					$colorNacenka = intval($val['nacenka']) < 0 ? "red" : "inherit";
					$colorRaznica = intval($val['raznica']) < 0 ? "red" : "inherit";
					$highlightBgColor = $val['color'] ? "yellow" : "inherit";
					#WCPL_Helper::_debug(date("d.m.y", strtotime($val['date'])));
					#WCPL_Helper::_debug(date("d.m.y"));
					$dateColor = ($val['date'] == date("d.m.y")) ? "blue" : "inherit";

					?>
					<tr style="color: <?=$dateColor;?>;">
						<td style="background-color: <?=$highlightBgColor;?>" data-source="monitor_change_color" data-action="js_ajax" data-pid="<?=$val['products_id'];?>"><?=$val['products_id'];?></td>
						<td style="color: <?=$dateColor;?>;"><?=str_replace("  ", "&nbsp;&nbsp;", $val['products_name']);?></td>
						<td style="color: <?=$dateColor;?>;"><?=str_replace("  ", "&nbsp;&nbsp;", $val['products_name_new']);?></td>
						<td><?=$val['name_supplier'];?></td>
						<td><?=str_replace("  ", "&nbsp;&nbsp;",$val['products_model']);?></td>
						<td><input type="text" name="product_price" value="<?=$val['products_price'];?>" placeholder="Цена на сайте" class="input-small-1 product_price" style="color: <?=$dateColor;?>;" data-source="change_price2" data-action="js_change" data-pid="<?=$val['products_id'];?>"/></td>
						<td align="right"><span class="new_price"><?=$val['new_price'];?></span></td>
						<td align="right" style="color: <?=$colorNacenka;?>;"><span class="nacenka"><?=$val['nacenka'];?></span></td>
						<td align="right"><span class="coefficient"><?=$val['coefficient'];?></span></td>
						<td><input type="text" name="minprice" value="<?=$val['minprice'];?>" placeholder="Мин. цена" class="input-small-1 minprice" style="color: <?=$dateColor;?>;" data-source="change_minprice" data-action="js_change" data-pid="<?=$val['products_id'];?>"/></td>
						<td><input type="text" name="firm" value="<?=$val['firm'];?>" placeholder="Фирма" class="input-small-1" style="color: <?=$dateColor;?>;" /></td>
						<td align="right" style="color: <?=$colorRaznica;?>;"><span class="raznica"><?=$val['raznica'];?></span></td>
						<td><span class="date"><?=$val['date'];?></span></td>
						<td>
							<div class="btn-group">
								<button type="button" role="button" data-source="change_price" data-action="js_ajax" data-pid="<?=$val['products_id'];?>" class="btn btn-danger btn-sm"><?=esc_attr__('Save');?></button>
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
		<?php endif;?>

	</div>
</div>
