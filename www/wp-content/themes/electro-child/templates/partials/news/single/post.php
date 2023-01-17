<?php
/**
 * Shows a simple single post
 */

use Digidez\Functions;

global $post;

$cf7_single_event_form_id = get_field('cf7_single_event_form', 'option');

$cf = Functions::get_cpt_custom_fields($post);
#Functions::_debug($cf);

?>
<?php while(have_posts()) : the_post(); ?>
	<article id="post-<?php the_ID(); ?>" class="news-single">
		<header class="header">
			<div class="page-title">
				<h1><? the_title();?></h1>
			</div>
			<div class="overlay"></div>
			<?php if(has_post_thumbnail() && !is_search()) : ?>
				<?=Functions::get_the_post_thumbnail($post->ID, 'full', array('alt' => get_the_title(), 'class' => 'hero-cover')); ?>
			<?php endif; ?>
		</header>

		<section class="toolbar">
			<div class="container">
				<?php get_template_part(PARTIALS_PATH.'/news/single/nav');?>
			</div>
		</section>

		<main class="section-content" id="main">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="inner fs25 xs-fs22">
							<?php the_content();?>
						</div>
					</div>
				</div>
			</div>
		</main>
	</article>

	<?php get_template_part(PARTIALS_PATH.'/news/related/posts');?>

<?php endwhile;?>
