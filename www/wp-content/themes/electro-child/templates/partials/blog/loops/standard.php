<?php
/**
 * Main Blog loop
 *
 * @package Lambda
 * @subpackage Frontend
 * @since 1.4
 *
 * @copyright (c) 2015 Oxygenna.com
 * @license **LICENSE**
 * @version 1.59.3
 */

if (have_posts()) {

    $name = oxy_get_option('blog_style');
    $extra_article_class = $name === 'no-sidebar-normal' ? 'post-nosidebar' : '';

    $post_count = 1;
    $page = get_query_var('paged') ? get_query_var('paged') : 1;
    if ($page > 1) {
        $post_count = ((int) ($page - 1) * (int) get_query_var('posts_per_page')) + 1;
    }

    while (have_posts()) {
        the_post();
        $name = is_search() ? 'search' : get_post_format();
        // add - if not empty to match get_template_part functionality
        if (!empty($name)) {
            $name = '-' . $name;
        }
        include(locate_template(PARTIALS_PATH.'/blog/posts/normal/post' . $name . '.php'));
        $post_count++;
    }
    oxy_pagination($wp_query->max_num_pages);
} else {
    get_template_part(PARTIALS_PATH.'/blog/posts/normal/post', 'no-posts');
}

/*
if (is_single()) {
    // show post navigation at the bottom of single post
    get_template_part(PARTIALS_PATH.'/blog/posts/normal/nav', 'single');
    // add related posts
    $related_posts_enabled = oxy_get_option('related_posts');
    if ($related_posts_enabled == 'on') {
        get_template_part(PARTIALS_PATH.'/blog/posts/normal/post', 'related');
    }
    // show comments below that
    $allow_comments = oxy_get_option('site_comments');
    if ($allow_comments == 'posts' || $allow_comments == 'all') {
        comments_template('', true);
    }
}
*/