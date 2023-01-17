<?php

/**
 * TODO
 */

use Pinloader\WcPinLoader;
use Pinloader\WCPL_Data_Source;
use Pinloader\WCPL_Helper;
use Pinloader\WCPL_Paginate_Navigation_Builder;


$action = (isset($_REQUEST['action']) ? $_REQUEST['action'] : '');
$suppliers_options = $order_options = array();
$limit = 10;

/*if(empty($_GET)){
	$_GET = $_SESSION['get'];
}else{
	$_SESSION['get'] = $_GET;
}

if(!isset($_SESSION['get']['paged'])){
	$_SESSION['get']['paged'] = 0;
}*/
WCPL_Helper::set_session_data();

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

for($i = 1; $i < 2; $i++){
	$sel = (isset($_GET['sort_order']) && $i == $_GET['sort_order']) ? "selected" : "";
	$order_options[] = '<option value="'.$i.'" '.$sel.'>Названию</option>';
}


$where = '';
$s_products_name = '';
if(isset($_GET['products_name']) && !empty($_GET['products_name'])){
	$s_products_name = $_GET['products_name'];
	$where .= "AND m.products_name LIKE '%".WCPL_Data_Source::db_input($_GET['products_name'])."%' ";
}
$s_products_model = '';
if(isset($_GET['products_model']) && !empty($_GET['products_model'])){
	$s_products_model = $_GET['products_model'];
	$where .= "AND m.products_model LIKE '%".WCPL_Data_Source::db_input($_GET['products_model'])."%' ";
}

if(isset($_SESSION['get']['id_supplier']) && intval($_SESSION['get']['id_supplier'])){
	$where .= "AND m.id_supplier = ".intval($_SESSION['get']['id_supplier'])." ";
}
$_sql = "SELECT count(*) AS cnt FROM mod_products_new m WHERE NOT m.products_id AND m.ignore = 'yes' AND m.current_list = 'on' $where";
#WCPL_Helper::_debug($_sql);
$products_found = WCPL_Data_Source::get_var($_sql);
if(isset($_SESSION['get']['paged'])){
	if(intval($_SESSION['get']['paged']) * $limit > $products_found){
		$_SESSION['get']['paged'] = floor($products_found / $limit);
	}
}

//$navi = new WCPL_Paginate_Navigation_Builder("/wp-admin/admin.php?page=".$_REQUEST['page']);
$navi = new WCPL_Paginate_Navigation_Builder($_SERVER['REQUEST_URI']);
$pagination_template = $navi->build($limit, $products_found, $_SESSION['get']['paged']);

$_sql = "
	SELECT 1, m.id, m.products_id, m.id_supplier, m.price, m.products_model, m.products_name, s.name_supplier
	FROM mod_products_new m, mod_suppliers s
	WHERE m.id_supplier = s.id_supplier AND NOT m.products_id AND m.ignore = 'yes' AND m.current_list = 'on' ".$where."
	ORDER BY m.products_name ASC
	LIMIT ".(intval($_SESSION['get']['paged']) * $limit).", ".$limit;
#WCPL_Helper::_debug($_sql);
$results = WCPL_Data_Source::get_results($_sql);
#WCPL_Helper::_debug($results);

?>


<div class="bootstrap form-table" data-example-id="hoverable-table">
	<div class="js_ajax_message loader"><?=esc_attr__('Sending request...', PINLOADER_TEXT_DOMAIN);?></div>
	<div>

		<div class="bg-info p-5 brr-5">
			<form method="get" action="<?=$_SERVER['PHP_SELF'];?>" class="form-inline">
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
				<button type="submit" class="btn btn-info btn-sm">Фильтр</button>
			</form>
		</div>

		<nav aria-label="Page navigation">
			<?=$pagination_template;?>
		</nav>

		<table id="new_products" class="table table-hover table-striped">
			<thead>
				<tr>
					<th><input type="checkbox" class="multiple_action_toggle" /></th>
					<th>Товар</th>
					<th>Код</th>
					<th>Поставщик</th>
					<th>Прайс</th>
					<th>Действия</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach($results as $pin_res):
					$row_class = '';
					$pin_res->price = money_format('%!.0n', $pin_res->price);
					$_sql = "SELECT p.ID FROM ml_posts p LEFT JOIN ml_postmeta pm ON pm.post_id = p.ID WHERE pm.meta_key = 'products_model' AND pm.meta_value = '".WCPL_Data_Source::db_input($pin_res->products_model)."'";
					#WCPL_Helper::_debug($_sql);
					$results2 = WCPL_Data_Source::get_results($_sql);
					#WCPL_Helper::_debug($results2);
					$modelAlreadyInDB = false;
					foreach($results2 as $pin_res1){
						$modelAlreadyInDB = $pin_res1->ID;
						$row_class = "dataTableRowRedBkg";
					}
					?>
					<tr class="<?=$row_class;?>" id="tr_<?=$pin_res->id;?>">
						<td><input type="checkbox" class="multiple_action" value="<?=$pin_res->id;?>"/></td>
						<td>
							<?php if($modelAlreadyInDB):?>
								<a href="<?=get_permalink($pin_res->products_id);?>"><?=$pin_res->products_name;?></a>";
							<?php else:?>
								<?=$pin_res->products_name;?>
							<?php endif;?>
						</td>
						<td><?=$pin_res->products_model;?></td>
						<td><?=$pin_res->name_supplier;?></td>
						<td><?=$pin_res->price;?></td>
						<td><a href="javascript:;" role="button" data-source="restore_one" data-action="js_ajax" class="btn btn-danger btn-sm js_hide_after_action" data-pid="<?=$pin_res->id;?>"><?=esc_attr__('Restore', PINLOADER_TEXT_DOMAIN);?></a></td>
					</tr>
				<?php endforeach;?>
				<tr>
					<td colspan=100 align="center">
						<div class="text-center">
							<button data-source="restore_all" data-action="js_ajax" data-parent="#new_products" class="btn btn-danger"><?=esc_attr__('Restore selected', PINLOADER_TEXT_DOMAIN);?></button>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
