<?php
/**
 * Post content footer items
 * @package Lambda
 * @subpackage Admin
 * @since 0.1
 * @copyright (c) 2015 Oxygenna.com
 * @license **LICENSE**
 * @version 1.59.3
 */
?>
<header>
	<div class="post-info">
		<?php if(oxy_get_option('blog_date') === 'on') : ?>
			<span class="post-date">
				<a href="<?php the_permalink();?>"><i class="icon-clock"></i> <?php the_time(get_option('date_format'));?></a>
			</span>
		<?php endif; ?>
		<?php if(oxy_get_option('blog_author') === 'on') : ?>
			<span class="post-author">
				<i class="icon-head"></i>
				<a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php the_author(); ?></a>
			</span>
		<?php endif; ?>
		<?php if(has_category() && oxy_get_option('blog_categories') === 'on') : ?>
			<span class="post-category">
				<i class="icon-clipboard"></i>
				<?php the_category(', '); ?>
			</span>
		<?php endif; ?>
		<?php if(comments_open() && !post_password_required() && oxy_get_option('blog_comment_count') === 'on') : ?>
			<span class="post-link">
				<i class="icon-speech-bubble"></i>
				<?php comments_popup_link(_x('No comments', 'comments number', 'lambda-td'), _x('1 comment', 'comments number', 'lambda-td'), _x('% comments', 'comments number', 'lambda-td')); ?>
			</span>
		<?php endif; ?>
	</div>
</header>