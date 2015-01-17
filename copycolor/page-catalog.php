 <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri() ?>/js/newfunctions.js"></script>

<?php
get_header();
?>

<div class="catalog-content">

    <div class="catalog-section">
        <?php
            $page_object = get_queried_object();
            $page_id = get_queried_object_id();
            $title;
            $query_post;
            switch ($page_id) {
                case '47':  $title = 'Print Products';
                            $query_post = 'category_name=catalogprint';
                            break;
                case '128': $title = 'Print Products';
                            $query_post = 'category_name=catalogprint';
                            break;
                case '130': $title = 'Design Products';
                            $query_post = 'category_name=catalogdesign';
                            break;
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
