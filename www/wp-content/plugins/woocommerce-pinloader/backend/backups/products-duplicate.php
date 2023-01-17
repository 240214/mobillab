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


$where = '';
$s_products_name = '';
if($_GET['view_mode'] != 'grouped'){
	if(isset($_GET['products_name']) && !empty($_GET['products_name'])){
		$s_products_name = $_GET['products_name'];
		$where           .= "AND products_name LIKE '%".WCPL_Data_Source::db_input($_GET['products_name'])."%' ";
	}
	$s_products_model = '';
	if(isset($_GET['products_model']) && !empty($_GET['products_model'])){
		$s_products_model = $_GET['products_model'];
		$where            .= "AND products_model LIKE '%".WCPL_Data_Source::db_input($_GET['products_model'])."%' ";
	}

	if(isset($_SESSION['get']['id_supplier']) && intval($_SESSION['get']['id_supplier'])){
		$where .= "AND id_supplier = ".intval($_SESSION['get']['id_supplier'])." ";
	}
}
$offset = 0;
$limit = 10;
$offset = $_SESSION['get']['paged'] * $limit - $limit;
if($offset < 0){
	$offset = 0;
}

$groups = WCPL_Data_Source::get_group_products();
if(!isset($_GET['view_mode'])){
	$_GET['view_mode'] = 'ungrouped';
}
$grouped_selected = $ungrouped_selected = '';
if($_GET['view_mode'] == 'grouped'){
	//if(!empty($groups['product_ids'])){
		$where .= "AND products_id IN (".implode(',', $groups['product_ids']).")";
	//}
	$offset = 0;
	$limit = 999999999;
	$grouped_selected = 'selected="selected"';
}else{
	if(!empty($groups['product_ids'])){
		$where .= "AND products_id NOT IN (".implode(',', $groups['product_ids']).")";
	}
	$ungrouped_selected = 'selected="selected"';
}

#WCPL_Helper::_debug($groups['product_ids']);

/*$_sql = "
	SELECT SQL_CALC_FOUND_ROWS p.ID, p.post_title AS products_name, pm.meta_key, pm.meta_value AS price, pm2.meta_key, pm2.meta_value AS products_model, t.name AS supplier
	FROM ml_posts p 
	LEFT JOIN ml_postmeta pm ON pm.post_id = p.ID 
	LEFT JOIN ml_postmeta pm2 ON pm2.post_id = p.ID 
	LEFT JOIN ml_term_relationships tr ON tr.object_id = p.ID
	LEFT JOIN ml_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
	LEFT JOIN ml_terms t ON t.term_id = tt.term_id
	LEFT JOIN mod_suppliers s ON s.term_id = tt.term_id
	WHERE p.post_type = 'product' AND post_status = 'publish' AND tt.taxonomy = 'suppliers' AND pm.meta_key = '_regular_price' AND pm2.meta_key = 'products_model' $where
	ORDER BY p.post_title ASC
	LIMIT ".$offset.", ".$limit;*/
$_sql = "SELECT SQL_CALC_FOUND_ROWS * FROM mod_products WHERE 1 = 1 $where ORDER BY products_name ASC LIMIT ".$offset.", ".$limit;
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
					<select name="view_mode">
						<optgroup label="<?=esc_attr__('- Select group type -', PINLOADER_TEXT_DOMAIN);?>">
						<option value="ungrouped" <?=$ungrouped_selected;?>><?=esc_attr__('Ungrouped', PINLOADER_TEXT_DOMAIN);?></option>
						<option value="grouped" <?=$grouped_selected;?>><?=esc_attr__('Grouped', PINLOADER_TEXT_DOMAIN);?></option>
						</optgroup>
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
					<th></th>
					<th><?=esc_attr__('ID', PINLOADER_TEXT_DOMAIN);?></th>
					<th><?=esc_attr__('Product', PINLOADER_TEXT_DOMAIN);?></th>
					<th><?=esc_attr__('Col Code', PINLOADER_TEXT_DOMAIN);?></th>
					<th><?=esc_attr__('Supplier', PINLOADER_TEXT_DOMAIN);?></th>
					<th><?=esc_attr__('Col price', PINLOADER_TEXT_DOMAIN);?></th>
					<th><?=esc_attr__('Col status', PINLOADER_TEXT_DOMAIN);?></th>
					<th><?=esc_attr__('Actions', PINLOADER_TEXT_DOMAIN);?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$prev_groupid = 0;$row_span = 1;
				foreach($products as $product):
					$product->products_price = money_format('%!.0n', $product->products_price);
					$row_color = "";
					$row_class = $product->products_status == 'draft' ? "type-product status-draft" : "";
					$group_id = 0;

					$display = true;
					if(!empty($groups['group_ids']) && in_array($product->products_id, $groups['product_ids'])){
						$row_color = "background-color:".$groups['group_ids'][$product->products_id]['color'].";";
						$row_class = "group";
						$group_id = $groups['group_ids'][$product->products_id]['group_id'];
					}
					if($prev_groupid != $group_id){
						$row_span = 1;
					}
					if($group_id > 0){
						if($row_span > 1){
							$display = false;
						}else{
							$row_span = $groups['counts'][$group_id];
						}
					}else{
						$row_span = 1;
					}
				?>
					<tr id="tr_<?=$product->products_id;?>" class="<?=$row_class;?>" style="<?=$row_color;?>">
						<td>
							<?php if($group_id == 0):?>
							<input type="checkbox" class="multiple_action" value="<?=$product->products_id;?>" data-group-id="<?=$group_id;?>"/>
							<?php endif;?>
						</td>
						<td><?=$product->products_id;?></td>
						<td><?=$product->products_name;?></td>
						<td><?=$product->products_model;?></td>
						<td><?=$product->name_supplier;?></td>
						<td><?=$product->products_price;?></td>
						<td><?=__($product->products_status.'_short', PINLOADER_TEXT_DOMAIN);?></td>
						<?php if($group_id):?>
							<?php if($display):?>
						<td rowspan="<?=$row_span;?>" class="middle"><button type="button" role="button" data-source="ungroup_products" data-action="js_ajax" class="btn btn-info btn-sm" data-gid="<?=$group_id;?>"><?=esc_attr__('Ungroup', PINLOADER_TEXT_DOMAIN);?></button></td>
							<?php endif;?>
						<?php else:?>
						<td>
							<div class="btn-group">
								<button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<span class="glyphicon glyphicon-option-vertical"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="<?=WCPL_Helper::product_edit_link($product->products_id);?>" target="_blank"><?=esc_attr__('Edit', PINLOADER_TEXT_DOMAIN);?></a></li>
									<li><a href="<?=get_permalink($product->products_id);?>" target="_blank"><?=esc_attr__('View', PINLOADER_TEXT_DOMAIN);?></a></li>
								</ul>
							</div>
						</td>
						<?php endif;?>
					</tr>
				<?php $prev_groupid = $group_id; endforeach;?>
			</tbody>
		</table>
		<?php if(!isset($_GET['view_mode']) || $_GET['view_mode'] == 'ungrouped'):?>
		<div class="text-center">
			<a href="javascript:;" role="button" data-source="group_selected" data-action="js_ajax" data-parent="#duplicate_products" class="btn btn-success btn-lg"><?=esc_attr__('Group selected', PINLOADER_TEXT_DOMAIN);?></a>
		</div>
		<?php endif;?>
	</div>
</div>
