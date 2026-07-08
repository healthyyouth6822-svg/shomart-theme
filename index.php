<?php
/**
 * =========================================================
 *  Shomart — index.php (fallback template)
 * =========================================================
 *  WordPress uses this when no more specific template exists.
 *  Shows blog posts. You can mostly ignore this file.
 * =========================================================
 */

get_header();
?>

<main>
    <?php
    if ( have_posts() ) {
        while ( have_posts() ) {
            the_post();
            ?>
            <article>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div><?php the_excerpt(); ?></div>
            </article>
            <hr>
            <?php
        }

        // Pagination
        the_posts_pagination( array(
            'prev_text' => '← Previous',
            'next_text' => 'Next →',
        ) );

    } else {
        echo '<p>Nothing found here.</p>';
    }
    ?>
</main>

<?php
get_footer();
