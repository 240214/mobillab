<?php
use Digidez\Functions;

global $post;

$loop = Functions::get_related_news($post->ID, true);

$categories = get_the_category($post->ID);
#Functions::_debug($categories);
$cats = array();
foreach($categories as $cat){
	$cats[] = $cat->name;
}

$excerpt_length = (Functions::$device == 'desktop') ? 300 : 400;
?>

<?php if($loop->have_posts()):?>
	<section class="related-news" style="background-image: url(/wp-content/uploads/2019/03/related_news_bg.jpg);">
		<div class="cloud-s">
			<h2 class="section-title fs100 xs-fs41 text-center">Related News</h2>
			<div class="section-content">
				<div class="related-slider related-news-slider">
					<?php while($loop->have_posts()): $loop->the_post();?>
					<?php $link = get_permalink();?>
					<div class="item">
						<div class="col l">
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
						</div>
						<div class="col r">
							<div class="row">
								<div class="col-lg-9 col-md-12 info">
									<a class="font-hc2 title fs40 md-fs29" href="<?=$link;?>"><?php the_title();?></a>
									<div class="excerpt fs25 md-fs17 sm-fs19"><?=Functions::create_excerpt($post->post_content, $excerpt_length);?></div>
								</div>
								<div class="col-lg-3 col-md-12">
									<div class="attrs fs17">
										<div class="date"><?php the_time('d/m/y');?></div>
										<div class="author">By <?php the_author();?></div>
										<div class="comment">Comments are off</div>
										<div class="category"><?=implode(', ', $cats);?></div>
									</div>
									<a class="share btn-primary indigo fs23 md-fs20" href="<?=$link;?>">Share</a>
									<a class="more btn-primary indigo fs23 md-fs20" href="<?=$link;?>">Read</a>
								</div>
							</div>
						</div>
					</div>
					<?php endwhile;?>
				</div>
				<div id="slider-navs" class="slider-navs"></div>
			</div>
		</div>
	</section>
	<?php wp_reset_postdata();?>
<?php endif;?>
