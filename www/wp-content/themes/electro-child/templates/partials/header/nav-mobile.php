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

$menu = Functions::get_menu('mobile-header');
?>
<?php if(!empty($menu)):?>
	<ul id="mobile-menu" class="clear nav-list">
		<?php $i=0; foreach($menu as $k => $item): $i++;?>
		<li id="menu-item-<?=$item['id'];?>" class="sm-fade delay-<?=$i;?> <?=$item['classes'];?> menu-item menu-item-home sm-animation-lg fade-animation <?=$item['active_class'];?>">
			<a id="<?=$item['classes'];?>" class="nav-link <?=$item['active_class'];?>" href="<?=$item['url'];?>" target="<?=$item['target'];?>">
				<?php if($item['classes'] == 'icon-basket'):?>
				<span class="mini-cart-count">0</span>
				<?php else:?>
				<span><?=$item['name'];?></span>
				<?php endif;?>
			</a>
		</li>
		<?php endforeach;?>
		<?php do_action('wpml_add_language_selector');;?>
	</ul>
<?php endif;?>
