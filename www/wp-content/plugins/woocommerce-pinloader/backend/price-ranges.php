<?php

use Pinloader\WcPinLoader;
use Pinloader\WCPL_Data_Source;
use Pinloader\WCPL_Helper;


$suppliers = WCPL_Data_Source::get_mod_suppliers_list('all');

#WCPL_Helper::_debug($suppliers);

if(!empty($_POST)){
	foreach($_POST as $key => $val){
		if(substr($key, 0, 3) == "dia"){
			$query_1       = "UPDATE mod_suppliers SET diapason = '".intval($val)."' WHERE id_supplier = ".intval(substr($key, 3))." ";
			$product_query = WCPL_Data_Source::query($query_1);
		}
		if(substr($key, 0, 3) == "new"){
			foreach($val as $ke => $va){
				$query_1       = "INSERT IGNORE INTO mod_diapasons SET id_supplier = ".intval(substr($key, 3)).", price_low = ".intval($va['price_low']).", price_high = ".intval($va['price_high']).", coefficient = '".floatval($va['coefficient'])."', delivery = ".intval($va['delivery']).", floor = ".intval($va['floor']).",minus = ".intval($va['minus'])." ";
				$product_query = WCPL_Data_Source::query($query_1);
			}
		}elseif(substr($key, 0, 3) == "old"){
			foreach($val as $ke => $va){
				$query_1       = "UPDATE mod_diapasons SET price_low = ".intval($va['price_low']).", price_high = ".intval($va['price_high']).", coefficient = '".floatval($va['coefficient'])."', delivery = ".intval($va['delivery']).", floor = ".intval($va['floor']).",minus = ".intval($va['minus'])." WHERE id = ".intval($ke)." AND id_supplier = ".intval(substr($key, 3))." ";
				$product_query = WCPL_Data_Source::query($query_1);
			}
		}elseif(substr($key, 0, 3) == "del"){
			$to_delete     = explode("_", $key);
			$query_1       = "DELETE FROM mod_diapasons WHERE id_supplier = ".intval($to_delete['1'])." AND id = ".intval($to_delete['2'])." ";
			$product_query = WCPL_Data_Source::query($query_1);
		}

	}
	wp_safe_redirect('/wp-admin/admin.php?page=wcpl-price-ranges&scrollToElement='.$_POST['scrollToElement']);
}
?>

<div class="bootstrap form-table" data-example-id="hoverable-table">

	<form method="post" id="diapasonform">
		<input type="hidden" id="scrollToElement" name="scrollToElement" value=""/>
		<table class="table table-hover table-striped">
			<?php
			$diapasons = WCPL_Data_Source::get_price_ranges();
			#WCPL_Helper::_debug($diapasons);

			$odd_even   = 1;
			foreach($suppliers as $val):
				if($val->diapason == "1"){
					$checked1 = "checked";
					$checked0 = "";
				}else{
					$checked0 = "checked";
					$checked1 = "";
				}
			?>
			<tr id="tblh_<?=$val->id_supplier;?>" class="tbl_sup suppl">
				<td>
					<h3 class="nomargin p-10"><?=$val->name_supplier;?></h3>
					<table id="tbl_<?=$val->id_supplier;?>" class="tbl_sup">
					<?php if(is_array($diapasons[$val->id_supplier])):
						$cntr = 0;
						foreach($diapasons[$val->id_supplier] as $k => $v):
							$cntr++;
							if($cntr == 1):?>
							<tbody>
								<tr>
									<td>Цена от:</td>
									<td>Цена до:</td>
									<td>Коэффициент</td>
									<td>Доставка</td>
									<td>Округление</td>
									<td>Минус</td>
									<td></td>
								</tr>
							</tbody>
							<?php endif;?>
							<tbody>
								<tr>
									<td><input type="text" name="old<?=$val->id_supplier;?>[<?=$v['id'];?>][price_low]" value="<?=$v['price_low'];?>"/></td>
									<td><input type="text" name="old<?=$val->id_supplier;?>[<?=$v['id'];?>][price_high]" value="<?=$v['price_high'];?>" /></td>
									<td><input type="text" name="old<?=$val->id_supplier;?>[<?=$v['id'];?>][coefficient]" value="<?=$v['coefficient'];?>" /></td>
									<td><input type="text" name="old<?=$val->id_supplier;?>[<?=$v['id'];?>][delivery]" value="<?=$v['delivery'];?>" /></td>
									<td><input type="text" name="old<?=$val->id_supplier;?>[<?=$v['id'];?>][floor]" value="<?=$v['floor'];?>" /></td>
									<td><input type="text" name="old<?=$val->id_supplier;?>[<?=$v['id'];?>][minus]" value="<?=$v['minus'];?>" /></td>
									<td><a onclick="rem(this, <?=$val->id_supplier;?>, <?=$v['id'];?>)" role="button" class="btn btn-danger btn-remove btn-sm"><i class="glyphicon glyphicon-minus"></i> Удалить</a></td>
								</tr>
							</tbody>
						<?php endforeach;?>
					<?php endif;?>
						<tfoot>
							<tr>
								<td colspan="5">
										Режим:&nbsp;
										<label><input type="radio" name="dia<?=$val->id_supplier;?>" value="1" <?=$checked1;?> /> Автоматический</label>
										<label><input type="radio" name="dia<?=$val->id_supplier;?>" value="0" <?=$checked0;?> /> Ручной</label>
										<button type="submit" class="btn btn-success btn-sm" onclick="addscrollToElement(<?=$val->id_supplier;?>);">Сохранить</button>
								</td>
								<td colspan="2" class="text-right">
									<?php if(is_array($diapasons[$val->id_supplier]) or $val->diapason == "1"):?>
										<a href="javascript:;" role="button" class="btn btn-info btn-add btn-sm btn-plus" data-sid="<?=$val->id_supplier;?>"><i class="glyphicon glyphicon-plus"></i> Добавить строку</a>
									<?php endif;?>
								</td>
							</tr>
						</tfoot>
					</table>


				</td>
			</tr>

			<?php endforeach;?>
		</table>

	</form>

</div>
<script type="text/javascript">
	jQuery(document).ready(function($){
		<?php if(!empty($_REQUEST['scrollToElement'])):?>
		$('html, body').animate({scrollTop: $('#tblh_<?=$_REQUEST['scrollToElement'];?>').offset().top-50}, 800);
		<?php endif;?>

		var cnt = 0;
		$('.btn-plus').on('click', function(){
			cnt++;
			var sid = $(this).data('sid');
			var $tr = $('<tr>');
			var $tbody = $('<tbody>');
			$tr.append('<td><input type="text" name="new'+sid+'['+cnt+'][price_low]"></td>');
			$tr.append('<td><input type="text" name="new'+sid+'['+cnt+'][price_high]"></td>');
			$tr.append('<td><input type="text" name="new'+sid+'['+cnt+'][coefficient]"></td>');
			$tr.append('<td><input type="text" name="new'+sid+'['+cnt+'][delivery]"></td>');
			$tr.append('<td><input type="text" name="new'+sid+'['+cnt+'][floor]"></td>');
			$tr.append('<td><input type="text" name="new'+sid+'['+cnt+'][minus]"></td>');
			$tr.append('<td><a onclick="rem(this, null, null)" role="button" class="btn btn-danger btn-remove btn-sm"><i class="glyphicon glyphicon-minus"></i> Удалить</a></td>');
			$tbody.append($tr);
			$('#tbl_'+sid).append($tbody);

		})

	});
	function addscrollToElement(v){
		jQuery("#scrollToElement").val(v);
	}
	function rem(obj, sid, id){
		if(sid != null){
			jQuery("#diapasonform").append('<input type="hidden" name="del_' + sid + '_' + id + '" value="1"/>');
		}
		jQuery(obj).closest("tbody").find("input").prop("disabled", true).end().hide();
	}

</script>
