<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 23.02.2019
 * Time: 23:13
 */

use Digidez\Functions;

//Functions::_debug($post->cf);

?>

<header id="site-header" class="site-header">
	<div class="container-fluid">
		<div class="lead-logo-holder">
			<?php get_template_part(PARTIALS_PATH.'/header/logos');?>
		</div>

		<?php get_template_part(PARTIALS_PATH.'/header/burger');?>

		<nav id="main-nav" class="site-nav">
			<?php get_template_part(PARTIALS_PATH.'/header/logo-mob');?>
			<?php get_template_part(PARTIALS_PATH.'/header/nav-primary');?>
			<?php //get_template_part(PARTIALS_PATH.'/header/socials');?>
		</nav>

		<nav id="mobile-icons-nav" class="mobile-icons-nav">
			<?php get_template_part(PARTIALS_PATH.'/header/nav-mobile');?>
		</nav>

		<nav id="user-nav" class="user-nav">
			<?php get_template_part(PARTIALS_PATH.'/header/logo-user');?>
			<?php get_template_part(PARTIALS_PATH.'/header/nav-user');?>
		</nav>
	</div>
</header>
