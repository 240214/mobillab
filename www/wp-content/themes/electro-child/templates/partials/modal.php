<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 25.02.2019
 * Time: 11:13
 */

use Digidez\Functions;

//Functions::_debug($modals_data);
?>
<?php if(!empty($modals_data)):?>
	<?php foreach($modals_data as $k => $data):?>
	<div class="modal fade <?=$k;?> policy" id="<?=$k;?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog" role="document">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" opacity="1">
						<g transform="translate(-2.000000, -2.000000)" fill="#000" fill-rule="nonzero">
							<g transform="translate(2.000000, 2.000000)">
								<polygon points="23 3.22357143 20.7764286 1 12 9.77642857 3.22357143 1 1 3.22357143 9.77642857 12 1 20.7764286 3.22357143 23 12 14.2235714 20.7764286 23 23 20.7764286 14.2235714 12"></polygon>
							</g>
						</g>
					</g>
				</svg>
			</button>
			<div class="modal-content">
				<div class="modal-body">
					<h1 class="modal-title"><?=$data['title'];?></h1>
					<?=$data['content'];?>
				</div>
			</div>
		</div>
	</div>
	<?php endforeach;?>
<?php endif;?>
