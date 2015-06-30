<?php
/*
Template Name: Gallery Page
*/
?>

<?php
get_header();
?>

<div id="main-content" class="main-content">

<div id="primary" class="content-area">
	<div id="gallery-content" class="site-content" role="main">

        <?php
            $query_post = 'category_name=gallery';
            query_posts($query_post);
            $i = 0;
            if ( have_posts() ) : while (have_posts()) : the_post();
                if ($i == 0):
        ?>
                    <div class="gallery-pictures-row">
            <?php
                endif;
                if (get_first_image(get_the_ID()) != NULL) {
            ?>
                <div class="single-gallery-picture <?php gallery_hover_color(get_the_ID()); ?>">

                    <a href="<?php echo get_permalink(); ?>">
                        <figure>
                            <img src="<?php echo get_first_image(get_the_ID())?>" alt="<?php echo get_the_title(get_the_ID()); ?>"></img>
                            <figcaption><p><?php echo get_the_title(get_the_ID()); ?></p></figcaption>
                        </figure>
                    </a>

                </div><!-- .single-gallery-picture -->
            <?php
                }
                $i++;
                if ($i == 3):
                ?>
                    </div><!-- .gallery-pictures-row -->
                <?php
                    $i = 0;
                endif;
            endwhile; endif;
            if ($i != 0):
            ?>
                </div><!-- .gallery-pictures-row -->
            <?php
            endif;
        ?>

</div> <!-- #gallery-content -->
            </div><!-- #primary -->
	<?php get_sidebar( 'content' ); ?>
</div><!-- #main-content -->

<script type="text/javascript">
    window.onload = function (e) {
        var cell_width = 0.23 * window.innerWidth;
        var cells = document.getElementsByClassName("single-gallery-picture");
        for (var i = 0; i < cells.length; i++) {
            cells[i].getElementsByTagName("img")[0].style.width = (cell_width + "px");
            cells[i].getElementsByTagName("figcaption")[0].style.height = cells[i].getElementsByTagName("img")[0].height + "px";
            cells[i].getElementsByTagName("p")[0].style.width = (cell_width + "px");
        }
    }
</script>

<?php
get_footer();
?>
