<?php
use Pinloader\WcPinLoader;
?>

<form method="post" action="options.php">
	<?php
	settings_fields('pinloader-options');
	do_settings_sections(WcPinLoader::$plugin_slug);
	submit_button();
	?>
</form>
