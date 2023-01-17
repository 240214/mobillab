<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 25.02.2019
 * Time: 20:20
 */

use Digidez\Functions;

/*wp_nav_menu(array(
	'fallback_cb'    => '__return_empty_string',
	'theme_location' => 'main',
	'menu_class'     => 'clear nav-list',
	'menu_id'        => 'main-menu',
	'container'      => 'ul',
));*/

$menu = Functions::get_menu('user-menu');
?>
<?php if(!empty($menu) && is_user_logged_in()):?>
	<ul id="user-menu" class="clear nav-list">
		<?php $i=0; foreach($menu as $k => $item): $i++;?>
		<li id="menu-item-<?=$item['id'];?>" class="sm-fade delay-<?=$i;?> <?=$item['classes'];?> menu-item menu-item-home sm-animation-lg fade-animation <?=$item['active_class'];?>">
			<a class="nav-link <?=$item['active_class'];?>" href="<?=$item['url'];?>" target="<?=$item['target'];?>"><span><?=$item['name'];?></span></a>
		</li>
		<?php endforeach;?>
		<?php do_action('wpml_add_language_selector');;?>
	</ul>
<?php endif;?>
