<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 23.02.2019
 * Time: 20:20
 */

use Digidez\Functions;
/*
?>
<nav id="main-nav" class="site-nav">
	<?php wp_nav_menu(array(
		'fallback_cb'    => '__return_empty_string',
		'theme_location' => 'primary',
		'menu_class'     => 'clear topmenu',
		'menu_id'        => 'primary-menu',
		'container'      => 'ul',
		'item_spacing' => 'discard',
	));?>
</nav>

<?php
*/
$menu = Functions::get_menu_tree('primary-menu');
#Functions::_debug($menu);
?>
<?php if(!empty($menu)):?>
	<ul id="primary-menu" class="clear nav-list trans_all">
		<?php $i=0; foreach($menu as $k => $item): $i++;?>
			<li id="menu-item-<?=$item['id'];?>" class="sm-fade delay-<?=$i;?> <?=$item['classes'];?> menu-item sm-animation-lg fade-animation <?=$item['active_class'];?>">
				<a class="nav-link <?=$item['active_class'];?>" href="<?=$item['url'];?>" target="<?=$item['target'];?>"><span><?=$item['name'];?></span></a>
				<?php if(isset($item['items'])):?>
				<?php if(count($item['items']) > 0):?>
					<ul class="submenu <?=$item['active_child'];?>">
						<?php foreach($item['items'] as $subitem):?>
							<li><a href="<?=$subitem['url'];?>" class="nav-link child-item <?=$subitem['classes'];?> <?=$subitem['active_class'];?>"><?=$subitem['name'];?></a></li>
						<?php endforeach;?>
					</ul>
				<?php endif;?>
				<?php endif;?>
			</li>
		<?php endforeach;?>
		<?php do_action('get_menu_bar_widget');;?>
		<?php do_action('wpml_add_language_selector');;?>
	</ul>
<?php endif;?>
