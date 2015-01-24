 <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri() ?>/js/newfunctions.js"></script>

<?php
get_header();
?>

<!--<div class="bla">

<div class="back-to-section">
    <img src="<?php //echo get_stylesheet_directory_uri() ?>/images/left_arrow.png" alt="Back">
</div>
-->

<div class="catalog-content">

    <div class="catalog-section">
        <?php
            $page_object = get_queried_object();
            $page_id = get_queried_object_id();
            $title;         //will hold the title of the subpage
            $query_post;    //will hold the query we will use to display the posts that are relevants to the subpage
            switch ($page_id) {
                //catalog page (by default, must show the print products)
                case '47':  $title = 'Print Products';
                            $query_post = 'category_name=catalogprint';
                            break;
                //catalog -> print subpage
                case '128': $title = 'Print Products';
                            $query_post = 'category_name=catalogprint';
                            break;
                //catalog -> design subpage
                case '130': $title = 'Design Products';
                            $query_post = 'category_name=catalogdesign';
                            break;
                //catalog -> sublimation subpage
                case '132': $title = 'Sublimation Products';
                            $query_post = 'category_name=catalogsublimation';
                            break;
            }
        ?>
        <h1><?php echo $title ?></h1>
    </div><!-- .catalog-section -->
    <div class="catalog-posts">
            <?php
            query_posts($query_post);
            $i = 0;
            while (have_posts()) : the_post();
                if ($i == 0):
            ?>
                    <div class="catalog-posts-row">
            <?php
                endif;
            ?>
        <div class="single-catalog-post">
            <?php
            the_title( '<h2>', '</h2>' );
            the_content();
            $i++;
            if ($i == 3):
            ?>
                </div><!-- .single-catalog-post -->
            <?php
                $i = 0;
                endif;
            ?>
        </div><!-- .catalog-post-row -->
        <?php
            endwhile;
        ?>
    </div><!-- .catalog-posts -->

</div><!-- .catalog-content -->

<!--<div class="forward-to-section">
    <img src="<?php //echo get_stylesheet_directory_uri() ?>/images/right_arrow.png" alt="Forward">
</div>

</div>
-->

<?php
get_footer();
?>