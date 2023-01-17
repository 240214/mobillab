<?php

$logos = get_field('logos', 'option');
$small_logo = $logos['small_logo'];
?>

<a href="<?=site_url('/');?>" class="nav-logo vam">
	<span class="d-cell">
		<img class="logo-img" src="<?=$small_logo;?>">
	</span>
</a>

