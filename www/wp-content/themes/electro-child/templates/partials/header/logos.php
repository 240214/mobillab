<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 23.02.2019
 * Time: 23:27
 */

$logos = get_field('logos', 'option');
$small_logo = $logos['small_logo'];
$logos_title = $logos['logos_title'];
$big_logo = $logos['big_logo'];
?>

<div id="header-logo" class="site-logo">
	<a href="<?=site_url('/');?>" title="<?=$logos_title;?>" class="logo-link">
		<img class="logo-img desktop-logo" src="<?=$big_logo;?>">
		<img class="logo-img mobile-logo" src="<?=$small_logo;?>">
	</a>
</div>
