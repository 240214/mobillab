<?php
/**
 * Shows a simple single post
 *
 * @package Lambda
 * @subpackage Frontend
 * @since 1.0
 *
 * @copyright (c) 2015 Oxygenna.com
 * @license http://wiki.envato.com/support/legal-terms/licensing-terms/
 * @version 1.59.0
 */
global $post, $extra_article_class;
$media_position = oxy_get_option('blog_post_media_position');
$extra_article_class[] = 'blog-post';
$related_posts_enabled = oxy_get_option('related_posts');
$allow_comments = oxy_get_option('site_comments');
#\Digidez\Functions::_debug($related_posts_enabled);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $extra_article_class ); ?> itemscope="itemscope" itemtype="http://schema.org/BlogPosting">
    <?php if ($media_position === 'below'): ?>
        <?php get_template_part( PARTIALS_PATH.'/blog/posts/normal/post', 'header' ); ?>
    <?php endif ?>

    <?php if( has_post_thumbnail() && !is_search()) : ?>
        <figure class="post-image hmedia">
            <?php get_template_part( PARTIALS_PATH.'/blog/posts/normal/featured-image' ); ?>
        </figure>
    <?php endif; ?>

    <?php if ($media_position === 'above'): ?>
        <?php get_template_part( PARTIALS_PATH.'/blog/posts/normal/post', 'header' ); ?>
    <?php endif ?>

	<div class="post-contents">
		<?php if (oxy_get_option('blog_post_header') === 'details') : ?>
			<?php get_template_part( PARTIALS_PATH.'/blog/posts/normal/post', 'details' ); ?>
		<?php endif ?>
		<div class="entry-content">
        <?php the_content( '', oxy_get_option('blog_stripteaser') === 'on' ); ?>
		</div>
		<?php get_template_part( PARTIALS_PATH.'/blog/posts/normal/post', 'footer' );?>
		<?php get_template_part(PARTIALS_PATH.'/blog/posts/normal/nav', 'single');?>
		<?php
		if($related_posts_enabled == 'on'){
			get_template_part(PARTIALS_PATH.'/blog/posts/normal/post', 'related');
		}
		if($allow_comments == 'posts' || $allow_comments == 'all'){
			comments_template('', true);
		}
		?>
    </div>

	<?php
    if( !is_single() && oxy_get_option('blog_show_readmore') === 'on' ) {
        // show up to readmore tag and conditionally render the readmore
        oxy_read_more_link();
    } ?>
</article>