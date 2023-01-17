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
$offset = 0;
$limit = 15;

WCPL_Data_Source::query("DELETE FROM mod_products_new WHERE products_id AND products_id NOT IN (SELECT ID FROM ml_posts WHERE post_type = 'product')");
//WCPL_Data_Source::query("UPDATE ml_postmeta SET meta_value = TRIM(LEADING '0' FROM meta_value) WHERE meta_key = 'products_model'");
WCPL_Data_Source::query("UPDATE ml_postmeta SET meta_value = REPLACE(meta_value, CHAR(0xc2a0), ' ') WHERE meta_key = 'products_model' AND meta_value LIKE CONCAT('%',CHAR(0xc2a0),'%')");

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
	$sel = '';
	if(isset($_GET['sort_order']) && $i == $_GET['sort_order']){
		$sel = "selected";
	}
	$order_options[] = '<option value="'.$i.'" '.$sel.'>Названию</option>';
}

$where = '';
$s_products_name = '';
if(isset($_GET['products_name']) && !empty($_GET['products_name'])){
	$s_products_name = $_GET['products_name'];
	$where .= "AND m.products_name LIKE '%".WCPL_Data_Source::db_input($_GET['products_name'])."%'";
}
$s_products_model = '';
if(isset($_GET['products_model']) && !empty($_GET['products_model'])){
	$s_products_model = $_GET['products_model'];
	$where .= "AND m.products_model LIKE '%".WCPL_Data_Source::db_input($_GET['products_model'])."%' ";
}

if(isset($_SESSION['get']['id_supplier']) && intval($_SESSION['get']['id_supplier'])){
	$where .= "AND m.id_supplier = ".intval($_SESSION['get']['id_supplier'])." ";
}
$_sql = "SELECT COUNT(*) AS cnt FROM mod_products_new m WHERE NOT m.products_id AND m.ignore = 'no' AND m.current_list = 'on' $where ";
#WCPL_Helper::_debug($_sql);
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

//$navi = new WCPL_Paginate_Navigation_Builder("/wp-admin/admin.php?page=".$_REQUEST['page']);
$navi = new WCPL_Paginate_Navigation_Builder($_SERVER['REQUEST_URI']);
$pagination_template = $navi->build($limit, $products_found, $_SESSION['get']['paged']);
#WCPL_Helper::_debug($template);

$_sql = "
	SELECT m.id, m.products_id, m.id_supplier, m.price, m.products_model, m.products_name, s.name_supplier
	FROM mod_products_new m, mod_suppliers s
	WHERE m.id_supplier = s.id_supplier AND NOT m.products_id AND m.ignore = 'no' AND m.current_list = 'on' ".$where."
	ORDER BY m.products_name ASC
	LIMIT ".$offset.", ".$limit;
#WCPL_Helper::_debug($_sql);
$results = WCPL_Data_Source::get_results($_sql);
#WCPL_Helper::_debug($results);


/*
if(!is_null($action)){
	switch($action){
		case 'add':
			if(!intval($_GET['newId']) or !intval($_GET['id'])){
				$message = "<div style='color: red; text-align: center;'>Введите номер товара</div>";
			}else{
				$products_name = WCPL_Data_Source::get_var("SELECT products_name FROM mod_products_new WHERE id = ".intval($_GET['id']));
				$message = "<div style='text-align: center;'>Добавить <span style='color: green;'>{$products_name}</span> как <span style='color: blue;'>".WCPL_Data_Source::get_products_name(intval($_GET['newId']))."</span> ?<br />
				<a href='?pagetype={$_SESSION['adminPageName']}&action=addfinal&id=".intval($_GET['id'])."&newId=".intval($_GET['newId'])."' style='color: green;font-size: large;'>ДА</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href='?pagetype={$_SESSION['adminPageName']}' style='color: red;font-size: large;'>НЕТ</a></div>
				";
			}
			break;
		case 'addfinal':
			if(!intval($_GET['newId']) or !intval($_GET['id'])){
				$message = "<div style='color: red; text-align: center;'>Введите номер товара</div>";
			}else{
				$product_query = WCPL_Data_Source::get_var("UPDATE mod_products_new SET products_id = ".intval($_GET['newId'])." WHERE id = ".intval($_GET['id']));
				WCPL_Data_Source::update_product();
				$product_query = WCPL_Data_Source::get_var("UPDATE products SET products_model = (SELECT products_model FROM mod_products_new WHERE id = ".intval($_GET['id'])."), id_supplier = (SELECT  id_supplier FROM  mod_products_new WHERE id = ".intval($_GET['id']).") WHERE products_id = ".intval($_GET['newId']));
				$products_name = WCPL_Data_Source::get_var("SELECT  products_name FROM mod_products_new  WHERE id = ".intval($_GET['id']));
				$message       = "<div style='text-align: center;'><span style='color: green;'>{$products_name}</span> добавлен как <span style='color: blue;'>".WCPL_Data_Source::get_products_name(intval($_GET['newId']))."</span></div>";
			}
			break;
		case 'ignore':
			if(!intval($_GET['id'])){
				$message = "<div style='color: red;'>Введите номер товара</div>";
			}else{
				$product_query = vam_db_query("SELECT  products_name FROM mod_products_new  WHERE id = ".intval($_GET['id']));
				$product       = vam_db_fetch_array($product_query);
				$message       = "<div>Игнорировать <span style='color: red;'>{$product['products_name']}</span> ?<br />
				<a href='?pagetype={$_SESSION['adminPageName']}&action=ignorefinal&id=".intval($_GET['id'])."' style='color: green;font-size: large;'>ДА</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href='?pagetype={$_SESSION['adminPageName']}' style='color: red;font-size: large;'>НЕТ</a></div>
				";
			}
			break;
		case 'ignorefinal':
			if(!intval($_GET['id'])){
				$message = "<div style='color: red;'>Введите номер товара</div>";
			}else{
				$product_query = vam_db_query("UPDATE mod_products_new SET ignore = 'yes' WHERE id = ".intval($_GET['id']));
				$product_query = vam_db_query("SELECT  products_name FROM mod_products_new  WHERE id = ".intval($_GET['id']));
				$product       = vam_db_fetch_array($product_query);
				$message       = "<div><span style='color: red;'>{$product['products_name']}</span> игнорируется.</div>";
			}
			break;
		case 'multipleignorefinal':
			if(empty($_GET['ids'])){
				$message = "<div style='color: red;'>Введите номер товара</div>";
			}else{
				$query_text    = "UPDATE mod_products_new SET ignore = 'yes' WHERE id IN (".vam_db_input(implode(",", explode("-", $_GET['ids']))).") ";
				$product_query = vam_db_query($query_text);

				$query_text    = "SELECT  products_name FROM mod_products_new  WHERE id IN (".vam_db_input(implode(",", explode("-", $_GET['ids']))).") ";
				$product_query = vam_db_query($query_text);
				$message       = "<div><span style='color: red;'>";
				while($product = vam_db_fetch_array($product_query)){
					$message .= "{$product['products_name']}<br />";
				}
				$message .= "</span> игнорируется.</div>";
			}
			break;

		default:
			break;
	}
}
*/
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
					<th>Цена в прайсе</th>
					<th>Цена для сайта</th>
					<th>Клонировать от ID</th>
					<th>Связать</th>
					<th colspan=2>Действия</th>
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
						$row_class = "bg-danger";
					}

					$_sql = "SELECT * FROM mod_diapasons WHERE id_supplier = ".$pin_res->id_supplier." AND (price_low <= ".$pin_res->price." AND price_high >= ".$pin_res->price.")";
					$diapasons = WCPL_Data_Source::get_row($_sql);
					#WCPL_Helper::_debug($diapasons);
					$product_price = $pin_res->price;
					if($diapasons){
						if($diapasons->floor <= 0){
							$diapasons->floor = 1;
						}
						$product_price = floor(($pin_res->price * $diapasons->coefficient + $diapasons->delivery) / $diapasons->floor) * $diapasons->floor - $diapasons->minus;
					}
					?>
					<tr class="<?=$row_class;?>" id="tr_<?=$pin_res->id;?>">
						<td><input type="checkbox" class="multiple_action" value="<?=$pin_res->id;?>"/></td>
						<td>
							<?php if($modelAlreadyInDB):?>
								<a href="<?=get_permalink($pin_res->products_id);?>"><?=$pin_res->products_name;?></a>
							<?php else:?>
								<?=$pin_res->products_name;?>
							<?php endif;?>
						</td>
						<td><?=$pin_res->products_model;?></td>
						<td><?=$pin_res->name_supplier;?></td>
						<td><?=$pin_res->price;?></td>
						<td><input type="text" name="product_price" placeholder="Итоговая цена" class="input-small-1" value="<?=$product_price;?>" /></td>
						<td><input type="text" name="clone_product_id" placeholder="ID товара-источника" class="autocomplete" autocomplete="off"/></td>
						<td class="text-center"><input type="checkbox" name="group_product_id" class="group-check hide" value="<?=$pin_res->id;?>"/></td>
						<td>
							<?php if($modelAlreadyInDB):?>
							<input type="text" id="id_product_<?=$pin_res->id;?>" value="<?=$modelAlreadyInDB;?>" />
							<?php endif;?>
							<a href="javascript:;" role="button" data-source="add_product" data-action="js_ajax" class="btn btn-success btn-sm js_hide_after_action" data-pid="<?=$pin_res->id;?>"><?=esc_attr__('Add to shop', PINLOADER_TEXT_DOMAIN);?></a>
							<a href="javascript:;" role="button" target="_blank" data-action="edit" class="btn btn-info btn-sm invisible js_show_after_action"><?=esc_attr__('Edit product', PINLOADER_TEXT_DOMAIN);?></a>
						</td>
						<td><a href="javascript:;" role="button" data-source="ignore_product" data-action="js_ajax" class="btn btn-danger btn-sm js_hide_after_action" data-pid="<?=$pin_res->id;?>"><?=esc_attr__('Igonre', PINLOADER_TEXT_DOMAIN);?></a></td>
					</tr>
				<?php endforeach;?>
			</tbody>
			<tfoot>
				<tr>
					<td><input type="checkbox" class="multiple_action_toggle" /></td>
					<td colspan=100 align="center">
						<div class="text-center">
							<button data-source="add_selected_products" data-action="js_ajax" data-parent="#new_products" class="btn btn-success"><?=esc_attr__('Add selected', PINLOADER_TEXT_DOMAIN);?></button>
							<button data-source="ignore_selected_products" data-action="js_ajax" data-parent="#new_products" class="btn btn-danger"><?=esc_attr__('Igonre selected', PINLOADER_TEXT_DOMAIN);?></button>
						</div>
					</td>
					<td></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
