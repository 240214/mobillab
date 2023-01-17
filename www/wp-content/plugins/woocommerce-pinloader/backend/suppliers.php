<?php

use Pinloader\WcPinLoader;
use Pinloader\WCPL_Helper;
use Pinloader\WCPL_Data_Source;

do_settings_sections(WcPinLoader::$plugin_slug);

$suppliers = WCPL_Data_Source::get_suppliers();

#WCPL_Helper::_debug($suppliers);

$add_link = '/wp-admin/edit-tags.php?taxonomy=suppliers&post_type=product';
?>

<div class="of mb-10">
	<a href="<?=$add_link;?>" class="btn btn-info btn-add"><i class="glyphicon glyphicon-plus"></i> <b><?=esc_attr__('Add new supplier', PINLOADER_TEXT_DOMAIN);?></b></a>
</div>

<div class="-bs-example form-table" data-example-id="hoverable-table">
	<div class="js_ajax_message loader"><?=esc_attr__('Sending request...', PINLOADER_TEXT_DOMAIN);?></div>
	<?php if(!empty($suppliers)):?>
	<table class="table table-hover table-striped">
		<thead>
		<tr>
			<th><?=esc_attr__('TID', PINLOADER_TEXT_DOMAIN);?></th>
			<th><?=esc_attr__('SID', PINLOADER_TEXT_DOMAIN);?></th>
			<th><?=esc_attr__('Supplier', PINLOADER_TEXT_DOMAIN);?></th>
			<th><?=esc_attr__('Sku suffix', PINLOADER_TEXT_DOMAIN);?></th>
			<th><?=esc_attr__('Col Code', PINLOADER_TEXT_DOMAIN);?></th>
			<th><?=esc_attr__('Col name', PINLOADER_TEXT_DOMAIN);?></th>
			<th><?=esc_attr__('Col price', PINLOADER_TEXT_DOMAIN);?></th>
			<th><?=esc_attr__('Col coefficient', PINLOADER_TEXT_DOMAIN);?></th>
			<th class="column-actions"><?=esc_attr__('Actions');?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach($suppliers as $supplier):?>
		<tr id="row_<?=$supplier->params->id_supplier;?>" class="form-group">
			<th scope="row"><input type="hidden" name="term_id" value="<?=$supplier->term_id;?>"><?=$supplier->term_id;?></th>
			<td><input type="hidden" name="id_supplier" value="<?=$supplier->params->id_supplier;?>"><span class="id_supplier"><?=$supplier->params->id_supplier;?></span></td>
			<td><input type="hidden" name="name_supplier" value="<?=$supplier->name;?>"><b><?=$supplier->name;?></b></td>
			<td><input type="text" name="sku_suffix" value="<?=$supplier->params->sku_suffix;?>" class="form-control input-sm"></td>
			<td><input type="text" name="code" value="<?=$supplier->params->code;?>" class="form-control input-sm"></td>
			<td><input type="text" name="name" value="<?=$supplier->params->name;?>" class="form-control input-sm"></td>
			<td><input type="text" name="price" value="<?=$supplier->params->price;?>" class="form-control input-sm"></td>
			<td><input type="text" name="coefficient" value="<?=$supplier->params->coefficient;?>" class="form-control input-sm"></td>
			<td>
				<div class="btn-group">
					<button type="button" role="button" data-source="supplier" data-action="js_ajax" data-supplier="<?=$supplier->params->id_supplier;?>" class="btn btn-success btn-sm"><i class="glyphicon glyphicon-save"></i> <?=esc_attr__('Save');?></button>
					<button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="caret"></span>
						<span class="sr-only">Toggle Dropdown</span>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
						<li><a href="<?=get_edit_term_link($supplier->term_id, $supplier->taxonomy);?>"><i class="glyphicon glyphicon-link"></i> <?=esc_attr__('Edit');?></a></li>
					</ul>
				</div>
			</td>
		</tr>
		<?php endforeach;?>
		</tbody>
	</table>
	<?php else:?>
	<div class="text-center text-muted">
		<p class="bg-dangers"><?=esc_attr__("You do not have any suppliers.<br>Click the button 'Add new supplier' to add the first supplier.", PINLOADER_TEXT_DOMAIN);?></p>
	</div>
	<?php endif;?>
</div>

<div class="of mt-10">
	<a href="<?=$add_link;?>" class="btn btn-info btn-add"><i class="glyphicon glyphicon-plus"></i> <b><?=esc_attr__('Add new supplier', PINLOADER_TEXT_DOMAIN);?></b></a>
</div>
