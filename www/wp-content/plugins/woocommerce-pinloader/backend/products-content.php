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


$where = array();
$s_products_name = '';
if(isset($_GET['products_name']) && !empty($_GET['products_name'])){
	$s_products_name = $_GET['products_name'];
	$where[] = "products_name LIKE '%".WCPL_Data_Source::db_input($_GET['products_name'])."%' ";
}
$s_products_model = '';
if(isset($_GET['products_model']) && !empty($_GET['products_model'])){
	$s_products_model = $_GET['products_model'];
	$where[] = "products_model LIKE '%".WCPL_Data_Source::db_input($_GET['products_model'])."%' ";
}

if(isset($_SESSION['get']['id_supplier']) && intval($_SESSION['get']['id_supplier'])){
	$where[] = "id_supplier = ".intval($_SESSION['get']['id_supplier'])." ";
}

if(!isset($_GET['view_mode'])){
	$_GET['view_mode'] = 'unfilled';
}
$filled_selected = $unfilled_selected = '';
if($_GET['view_mode'] == 'filled'){
	$where[] = "products_filled = '1'";
	$filled_selected = 'selected="selected"';
}else{
	$where[] = "(isnull(products_filled) OR products_filled != '1')";
	$unfilled_selected = 'selected="selected"';
}

if(!isset($_GET['products_status'])){
	$_GET['products_status'] = 'published';
}
$published_selected = $drafted_selected = '';
if($_GET['products_status'] == 'published'){
	$where[] = "products_status = 'publish'";
	$published_selected = 'selected="selected"';
}else{
	$where[] = "products_status = 'draft'";
	$drafted_selected = 'selected="selected"';
}

if(!empty($where)){
	$where = implode(' AND ', $where);
}

$offset = 0;
$limit = 10;
$offset = $_SESSION['get']['paged'] * $limit - $limit;
if($offset < 0){
	$offset = 0;
}


$_sql = "SELECT SQL_CALC_FOUND_ROWS * FROM mod_products WHERE $where ORDER BY products_name ASC LIMIT ".$offset.", ".$limit;
#WCPL_Helper::_debug($_sql);
$products = WCPL_Data_Source::get_results($_sql);
#PAGINATION
$products_found = WCPL_Data_Source::get_var("SELECT FOUND_ROWS()");
if(isset($_SESSION['get']['paged'])){
	if(intval($_SESSION['get']['paged']) * $limit > $products_found){
		$_SESSION['get']['paged'] = ceil($products_found / $limit);
	}
}
//$navi = new WCPL_Paginate_Navigation_Builder("/wp-admin/admin.php?page=".$_REQUEST['page']);
$navi = new WCPL_Paginate_Navigation_Builder($_SERVER['REQUEST_URI']);
$pagination_template = $navi->build($limit, $products_found, $_SESSION['get']['paged']);

#WCPL_Helper::_debug($groups);
#WCPL_Helper::_debug($products);
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
					<input type="text" name="products_name" value="<?=$s_products_name;?>" placeholder="<?=esc_attr__('Product name', PINLOADER_TEXT_DOMAIN);?>"/>
				</div>
				<div class="form-group">
					<input type="text" name="products_model" value="<?=$s_products_model;?>" placeholder="<?=esc_attr__('Code', PINLOADER_TEXT_DOMAIN);?>"/>
				</div>
				<div class="form-group">
					<select name="id_supplier">
						<option value=0><?=esc_attr__('All', PINLOADER_TEXT_DOMAIN);?></option>
						<optgroup label="<?=esc_attr__('- Select supplier -', PINLOADER_TEXT_DOMAIN);?>">
						<?=implode('', $suppliers_options);?>
						</optgroup>
					</select>
				</div>
				<div class="form-group">
					<select name="products_status" title="<?=esc_attr__('Products', 'woocommerce');?>">
						<option value="published" <?=$published_selected;?>><?=esc_attr__('Published', PINLOADER_TEXT_DOMAIN);?></option>
						<option value="drafted" <?=$drafted_selected;?>><?=esc_attr__('Drafted', PINLOADER_TEXT_DOMAIN);?></option>
					</select>
				</div>
				<div class="form-group">
					<select name="view_mode">
						<option value="unfilled" <?=$unfilled_selected;?>><?=esc_attr__('Unfilled', PINLOADER_TEXT_DOMAIN);?></option>
						<option value="filled" <?=$filled_selected;?>><?=esc_attr__('Filled', PINLOADER_TEXT_DOMAIN);?></option>
					</select>
				</div>
				<button type="submit" class="btn btn-info btn-sm"><?=esc_attr__('Filter', PINLOADER_TEXT_DOMAIN);?></button>
			</form>
		</div>

		<nav aria-label="Page navigation">
			<?=$pagination_template;?>
		</nav>

		<table id="duplicate_products" class="table table-hover table-striped">
			<thead>
				<tr>
					<th><?=esc_attr__('ID', PINLOADER_TEXT_DOMAIN);?></th>
					<th><?=esc_attr__('Product', PINLOADER_TEXT_DOMAIN);?></th>
					<th><?=esc_attr__('Col Code', PINLOADER_TEXT_DOMAIN);?></th>
					<th><?=esc_attr__('Supplier', PINLOADER_TEXT_DOMAIN);?></th>
					<th><?=esc_attr__('Col price', PINLOADER_TEXT_DOMAIN);?></th>
					<th><?=esc_attr__('Actions', PINLOADER_TEXT_DOMAIN);?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($products as $product):
					$product->products_price = money_format('%!.0n', $product->products_price);?>
					<tr id="tr_<?=$product->products_id;?>">
						<td><?=$product->products_id;?></td>
						<td><?=$product->products_name;?></td>
						<td><?=$product->products_model;?></td>
						<td><?=$product->name_supplier;?></td>
						<td><?=$product->products_price;?></td>
						<td class="text-nowrap">
							<div class="btn-group">
								<button type="button" role="button" data-toggle="modal" data-target="#ymModal" data-source="get_ym_products" data-action="js_ajax" data-pid="<?=$product->products_id;?>" data-parent="#tr_<?=$product->products_id;?>" class="btn btn-warning btn-sm"><?=esc_attr__('Search in Yandex', PINLOADER_TEXT_DOMAIN);?></button>
								<button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="<?=WCPL_Helper::product_edit_link($product->products_id);?>" target="_blank"><?=esc_attr__('Edit', PINLOADER_TEXT_DOMAIN);?></a></li>
									<li><a href="<?=get_permalink($product->products_id);?>" target="_blank"><?=esc_attr__('View', PINLOADER_TEXT_DOMAIN);?></a></li>
								</ul>
							</div>
						</td>
					</tr>
				<?php endforeach;?>
			</tbody>
		</table>
	</div>

	<div class="modal fade" id="ymModal" tabindex="-1" role="dialog" aria-labelledby="ymModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="ymModalLabel"><?=esc_attr__('Choosing a product from Yandex Market', PINLOADER_TEXT_DOMAIN);?></h4>
				</div>
				<div class="modal-body">
					<iframe id="ym_site" src="" width="100%" height="500" frameborder="0"></iframe>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?=esc_attr__('Close');?></button>
				</div>
			</div>
		</div>
	</div>
</div>
