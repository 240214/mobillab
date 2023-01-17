<?php
use Digidez\Functions;

$delim = Functions::$device == 'mobile' ? '<br>' : ' | ';
?>
<footer id="site-footer" class="site-footer trans_all">
	<div class="container-fluid">

		<div class="row">

			<?php if(is_active_sidebar('footer_col_1')):?>
				<div class="col-lg-6 col-md-4 col-sm-4 wow animated fadeIn" data-wow-delay="200ms">
					<?php dynamic_sidebar('footer_col_1');?>
				</div>
			<?php endif;?>

			<?php if(is_active_sidebar('footer_col_2')):?>
				<div class="col-lg-3 col-md-4 col-sm-4 wow animated fadeIn" data-wow-delay="400ms">
					<?php dynamic_sidebar('footer_col_2');?>
				</div>
			<?php endif;?>

			<?php if(is_active_sidebar('footer_col_3')):?>
				<div class="col-lg-3 col-md-4 col-sm-4 wow animated fadeIn" data-wow-delay="600ms">
					<?php dynamic_sidebar('footer_col_3');?>
				</div>
			<?php endif;?>

		</div>

		<?php if(!is_null($data['nav'])):?>
		<nav class="row">
			<?php foreach($data['nav'] as $nav):?>
				<div class="col-md-<?=$data['col'];?> fs24 sm-fs20 xs-fs18">
					<?php if($nav['url'] == '#'):?>
						<div class="parent-item <?=$nav['classes'];?>"><?=$nav['name'];?></div>
					<?php else:?>
						<a href="<?=$nav['url'];?>" class="parent-item <?=$nav['classes'];?>"><?=$nav['name'];?></a>
					<?php endif;?>
					<?php if(count($nav['items']) > 0):?>
					<ul class="submenu">
						<?php foreach($nav['items'] as $item):?>
							<li><a href="<?=$item['url'];?>" class="child-item <?=$item['classes'];?>"><?=$item['name'];?></a></li>
						<?php endforeach;?>
					</ul>
					<?php endif;?>
				</div>
			<?php endforeach;?>
		</nav>
		<?php endif;?>
	</div>

	<div class="copyrights">
		<div class="container">
			<div class="row">
				<div class="col-md-12"><?=$data['copyright_text'];?><?=$delim;?><?=$data['creator_text'];?></div>
			</div>
		</div>
	</div>
</footer>
