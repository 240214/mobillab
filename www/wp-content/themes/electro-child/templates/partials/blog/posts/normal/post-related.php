<?php
/**
 * Shows related posts
 *
 * @package Lambda
 * @subpackage Frontend
 * @since 1.3
 *
 * @copyright (c) 2015 Oxygenna.com
 * @license **LICENSE**
 * @version 1.59.3
 */

global $post, $wp_query;
$post_id = $post->ID;
$columns    = intval(oxy_get_option('related_posts_columns'));
$span_width = $columns > 0 ? floor(12 / $columns) : 12;
$post_type = $post->post_type;
$title_tag  = oxy_get_option('related_posts_title_tag');
$text_align = oxy_get_option('related_posts_text_align');
$related_posts_style = oxy_get_option('related_posts_style');
$related_posts_count = oxy_get_option('related_posts_count');
$related_posts_count = '0' == $related_posts_count ? '-1' : $related_posts_count;
$taxonomy = 'post' === $post_type ? 'category' : $post_type . '-category';
$related_posts = \Digidez\Functions::get_post_type_related_posts($post_id, $related_posts_count, $post_type, $taxonomy );
?>
<?php if($related_posts && $related_posts->have_posts()):?>
	<div class="post-related">
		<div class="row">
			<div class="col-md-12">
				<h4><?=__('You may also like', 'lambda-td');?></h4>
			</div>
			<?php while( $related_posts->have_posts()):
				$related_posts->the_post();
				$post_link = get_the_permalink();
			?>
				<div class="col-md-6">
					<article class="blog-post hentry entry">
						<?php if(has_post_thumbnail($related_posts->ID)):?>
							<figure class="post-image hmedia">
								<a href="<?=$post_link;?>">
									<?=\Digidez\Functions::get_the_post_thumbnail($related_posts->ID, 'rella-related-post', '', false);?>
								</a>
							</figure>
						<?php endif; ?>
						<div class="post-contents">
							<header>
								<?php the_title(sprintf('<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url($post_link)), '</a></h2>');?>
								<div class="post-info">
									<span>
										<time class="entry-published updated" datetime="<?=get_the_time('Y-m-d\TH:i:sP');?>" title="<?=get_the_time(esc_html_x('l, F j, Y, g:i a', 'post time format', 'lambda-td'));?>">
											<?=get_the_date('M j');?>
										</time>
									</span>
									<span class="entry-author" itemscope="itemscope" itemtype="http://schema.org/Person"><?=\Digidez\Functions::get_author_link('before=');?></span>
								</div>
							</header>
						</div>
					</article>
				</div>
			<?php endwhile; ?>
		</div>
	</div>
<?php endif;
wp_reset_postdata();
wp_reset_query();
