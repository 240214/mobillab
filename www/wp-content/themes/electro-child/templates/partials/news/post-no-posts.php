<article id="post-0" class="post no-results not-found text-center">
    <header class="entry-header">
        <h1 class="section-title fs100"><?php _e( 'Nothing Found', THEME_TD ); ?></h1>
    </header>

    <div class="section-content fs25">
    <?php
        if( is_category() ) {
            $message = esc_html__('Sorry, no posts were found for this category.', THEME_TD);
        }
        else if( is_date() ) {
            $message = esc_html__('Sorry, no posts found in that timeframe', THEME_TD);
        }
        else if( is_author() ) {
            $message = esc_html__('Sorry, no posts from that author were found', THEME_TD);
        }
        else if( is_tag() ) {
            $message = sprintf( esc_html__('Sorry, no posts were tagged with  "%1$s"', THEME_TD), single_tag_title( '', false ) );
        }
        else if( is_search() ) {
            $message = sprintf( esc_html__('Sorry, no search results were found for  "%1$s"', THEME_TD), get_search_query() );
        }
        else {
            $message = esc_html__( 'Sorry, nothing found', THEME_TD );
        }
    ?>
        <p class="lead"><?php echo esc_html($message); ?></p>
    </div>
</article>