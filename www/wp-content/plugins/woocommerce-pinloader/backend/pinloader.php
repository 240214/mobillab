<?php

use Pinloader\WcPinLoader;

?>
<div class="js_ajax_message loader"><?=esc_attr__('Sending request...', PINLOADER_TEXT_DOMAIN);?></div>
<div class="row">
	<div class="col-lg-4 col-md-6">
		<form method="post" action="options.php" class="options">
			<?php
			settings_fields('pinloader-options');
			do_settings_sections(WcPinLoader::$plugin_slug);
			submit_button();
			?>
		</form>
	</div>
	<div class="col-md-1"></div>
	<div class="col-lg-4 col-md-6">
		<h2><?=__('Yandex Market', PINLOADER_TEXT_DOMAIN);?></h2>
		<table class="form-table">
			<tr>
				<th><?=__('Update Yandex Market feed manually', PINLOADER_TEXT_DOMAIN);?></th>
				<td>
					<a role="button"
					   class="button button-primary"
					   href="javascript:;"
					   data-target="#result_info"
					   data-source="update_ymf_prices"
					   data-action="js_ajax"><?=__('Update');?></a>
				</td>
			</tr>
		</table>
		<hr>
		<h2><?=__('Images', PINLOADER_TEXT_DOMAIN);?></h2>
		<table class="form-table">
			<tr>
				<th><?=__('Update Images Alt attributes', PINLOADER_TEXT_DOMAIN);?></th>
				<td>
					<a role="button"
					   class="button button-primary"
					   href="javascript:;"
					   data-target="#result_info"
					   data-source="update_images_alt"
					   data-action="js_ajax"><?=__('Update');?></a>
				</td>
			</tr>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div id="result_info" class="result-info"></div>
	</div>
</div>