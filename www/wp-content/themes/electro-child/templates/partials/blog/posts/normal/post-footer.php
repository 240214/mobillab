<?php
/**
 * Post content footer items
 *
 * @package Lambda
 * @subpackage Admin
 * @since 0.1
 *
 * @copyright (c) 2015 Oxygenna.com
 * @license **LICENSE**
 * @version 1.59.3
 */

$author_id = get_the_author_meta('ID');
$author_url = get_author_posts_url( $author_id );
?>

<?php if(is_single()):?>
<div class="post-share">
	<?php if(!in_array('none', oxy_get_option('blog_social_networks'))):?>
		<?php
		echo oxy_shortcode_sharing(array(
			'social_networks' => implode(',', oxy_get_option('blog_social_networks')),
			'icon_size'       => 'social-icon semi-round rectangle bordered branded-text',
			'background_show' => 'simple',
			'margin_top'      => 0,
			'margin_bottom'   => 0,
		));
		?>
	<?php endif; ?>
</div>
<?php endif; ?>
