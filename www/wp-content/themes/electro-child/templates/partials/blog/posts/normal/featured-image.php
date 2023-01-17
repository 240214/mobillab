<?php
/**
 * Shows a posts featured image
 * @package Lambda
 * @subpackage Admin
 * @since 0.1
 * @copyright (c) 2015 Oxygenna.com
 * @license **LICENSE**
 * @version 1.59.0
 */

global $post;

$image_link          = '';
$open_magnific_class = '';

if(is_single()){
	// image should link to magnific on single post
	$image      = get_post_thumbnail_id($post->ID);
	$image_size = oxy_get_post_image_size();
	$src        = wp_get_attachment_image_src($image, $image_size);
	if(false !== $src && is_array($src)){
		$image_link = $src[0];
	}
	$icon                = 'plus';
	$open_magnific_class = 'magnific';
}else{
	$blog_image_linkable = oxy_get_option('blog_image_linkable');
	// image should link to single post in lists
	$image_link = get_permalink($post->ID);
	$icon       = 'link';
}
$category      = get_the_category($post->ID);
$firstCategory = $category[0]->cat_name;
$category_id   = $category[0]->term_id;
?>
<div class="aspect-ratio-container" style="max-width: 765px; max-height: 310px;">
	<?php if(!is_single() && 'off' === $blog_image_linkable):?>
		<?php if(has_post_thumbnail()):?>
			<?php the_post_thumbnail('full', array('alt' => get_the_title($post->ID)));?>
		<?php endif;?>
	<?php else:?>
		<?php if(has_post_thumbnail()):?>
			<div class="aspect-ratio-fill padding-added progressive-image--is-loaded" style="padding-bottom: 40.5229%;">
				<?php the_post_thumbnail('full', array('alt' => get_the_title($post->ID), 'class' => 'attachment-rella-thumbnail-post size-rella-thumbnail-post progressive__img progressive--is-loaded'));?>
			</div>
		<?php endif;?>
		<div class="tags">
			<span class="a" rel="category" href="<?=get_category_link($category[0]->term_id);?>"><?=$firstCategory;?></span>
		</div>
	<?php endif; ?>
</div>
