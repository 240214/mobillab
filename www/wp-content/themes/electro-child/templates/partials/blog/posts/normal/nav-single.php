<?php
/**
 * Adds navigation for single post
 *
 * @package Lambda
 * @subpackage Admin
 * @since 0.1
 *
 * @copyright (c) 2015 Oxygenna.com
 * @license **LICENSE**
 * @version 1.59.3
 */


$args = array(
	'prev_text'          => '%title',
	'next_text'          => '%title',
	'in_same_term'       => false,
	'excluded_terms'     => '',
	'taxonomy'           => 'category'
);

$previous = get_previous_post_link(
	'%link',
	$args['prev_text'],
	$args['in_same_term'],
	$args['excluded_terms'],
	$args['taxonomy']
);
if( $previous ) {
	$previous = str_replace( '<a', '<a class="prev"', $previous );
}

$next = get_next_post_link(
	'%link',
	$args['next_text'],
	$args['in_same_term'],
	$args['excluded_terms'],
	$args['taxonomy']
);
if( $next ) {
	$next = str_replace( '<a', '<a class="next"', $next );
}
?>
<div class="post-nav">
	<nav class="navigation post-navigation">
		<?php echo $previous . $next ?>
	</nav>
</div>
