<?php

/**
 * TODO
 */

use Pinloader\WcPinLoader;
use Pinloader\WCPL_Data_Source;
use Pinloader\WCPL_Helper;



$products1 = array();
$_sql = "SELECT COUNT(p.ID) AS count
				FROM ml_posts p
				LEFT JOIN ml_postmeta pm ON pm.post_id = p.ID
				WHERE 1=1
				AND p.ID NOT IN(SELECT tr.object_id FROM ml_term_relationships tr LEFT JOIN ml_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'suppliers')
				AND p.post_type = 'product'
				-- AND p.post_status = 'publish'
				AND (pm.meta_key = 'products_model' AND pm.meta_value NOT IN ('ML', 'ML-AG', 'ML-ME', 'ML-GR'))";
$p_count = WCPL_Data_Source::get_var($_sql);
if(intval($p_count) > 0){
	$_sql = "SELECT p.ID, pm.meta_value AS products_model, p.post_title, p.post_status 
				FROM ml_posts p 
				LEFT JOIN ml_postmeta pm ON pm.post_id = p.ID
				WHERE p.post_type = 'product'
				  -- AND p.post_status = 'publish'
				  AND (pm.meta_key = 'products_model' AND pm.meta_value NOT IN ('ML', 'ML-AG', 'ML-ME', 'ML-GR'))
				  AND p.ID NOT IN(SELECT tr.object_id FROM ml_term_relationships tr LEFT JOIN ml_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'suppliers') 
				  $where1";
	$products1 = WCPL_Data_Source::get_results($_sql);
}

?>


<div class="bootstrap form-table" data-example-id="hoverable-table">
	<div class="js_ajax_message loader"><?=esc_attr__('Sending request...', PINLOADER_TEXT_DOMAIN);?></div>

	<div>
		<?php if(count($products1)):?>
		<table id="products_without_supplier" class="table table-hover table-striped">
			<thead>
				<tr>
					<th class="column-pid">ID (Т)</th>
					<th class="column-title">Название</th>
					<th class="column-supplier">Поставщик</th>
					<th class="column-model">Код</th>
					<th class="column-status">Статус</th>
					<th class="column-actions">Действия</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($products1 as $val):
				$products_ids_to_switch_off[] = intval($val->ID);
				$color = intval($val->pricediff) < 0 ? "red" : "blue";
			?>
				<tr>
					<td><?=$val->ID;?></td>
					<td><?=$val->post_title;?></td>
					<td>Нет</td>
					<td><?=$val->products_model;?></td>
					<td><?=esc_attr__($val->post_status, PINLOADER_TEXT_DOMAIN);?></td>
					<td>
						<div class="btn-group">
							<?php if($val->post_status == 'trash'):?>
								<a href="<?=WCPL_Helper::product_trash_link($val->post_title);?>" type="button" role="button" class="btn btn-success btn-sm" target="_blank"><?=esc_attr__('View in trash', PINLOADER_TEXT_DOMAIN);?></a>
							<?php else:?>
								<a href="<?=WCPL_Helper::product_edit_link($val->ID);?>" type="button" role="button" class="btn btn-success btn-sm"><?=esc_attr__('Edit product', PINLOADER_TEXT_DOMAIN);?></a>
								<button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="<?=get_permalink($val->ID);?>" target="_blank"><?=esc_attr__('View', PINLOADER_TEXT_DOMAIN);?></a></li>
									<li><a href="<?=WCPL_Helper::yandex_search_link($val->post_title);?>" target="_blank"><?=esc_attr__('Search in Yandex', PINLOADER_TEXT_DOMAIN);?></a></li>
								</ul>
							<?php endif;?>
						</div>
					</td>
				</tr>
			<?php endforeach;?>
			</tbody>
		</table>
		<?php endif;?>
	</div>
</div>
