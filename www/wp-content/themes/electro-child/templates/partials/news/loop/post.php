<?php

use Digidez\Functions;

global $post;
//Functions::_debug($post->cf['gallery_item_options']['products_collection']);

$link = get_permalink($post->ID);
$categories = get_the_category($post->ID);
#Functions::_debug($categories);
$cats = array();
foreach($categories as $cat){
	$cats[] = $cat->name;
}
?>
<article id="post-<?=$post->ID;?>" <?php post_class('col-sm-12'); ?>>
	<div class="news-item">

		<?php if(has_post_thumbnail()):?>
			<?php if(Functions::$device == 'desktop'):?>
				<figure style="background-image: url(<?=Functions::get_the_post_thumbnail($post->ID, 'relative-event', array(), false); ?>);"></figure>
			<?php else:?>
				<figure><?=Functions::get_the_post_thumbnail($post->ID, 'relative-event', array('alt' => get_the_title()));?></figure>
			<?php endif; ?>
		<?php else:?>
			<?php if(Functions::$device == 'desktop'):?>
				<figure style="background-image: url(/wp-content/uploads/2019/03/default_thumb.jpg);"></figure>
			<?php else:?>
				<figure><img src="/wp-content/uploads/2019/03/default_thumb.jpg"></figure>
			<?php endif; ?>
		<?php endif; ?>
		<div class="content">
			<div class="row">
				<div class="col-md-8 info">
					<a class="font-hc2 title fs40 md-fs29 sm-fs37" href="<?=$link;?>"><?php the_title();?></a>
					<div class="excerpt fs25 md-fs17 sm-fs20"><?=Functions::create_excerpt($post->post_content, 300);?></div>
				</div>
				<div class="col-md-4">
					<div class="attrs fs17 md-fs15 sm-fs17">
						<div class="date"><?php the_time('d/m/y');?></div>
						<div class="author">By <?php the_author();?></div>
						<div class="comment">Comments are off</div>
						<div class="category"><?=implode(', ', $cats);?></div>
					</div>
					<a class="share button font-hn indigo fs23 md-fs20" href="#">Share</a>
					<a class="more button font-hn indigo fs23 md-fs20" href="<?=$link;?>">Read</a>
				</div>
			</div>
		</div>

	</div>
</article>