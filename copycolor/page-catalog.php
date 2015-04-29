<?php
/*
Template Name: Catalog Page
*/
?>

<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri() ?>/js/newfunctions.js"></script>
 
<?php
    
const CATALOG = '47';
const CATALOG_PRINT = '128';
const CATALOG_DESIGN = '130';
const CATALOG_SUBLIMATION = '132';

get_header();

$page_object = get_queried_object();
$page_id = get_queried_object_id();

//all next variables except $forward_image are initialized to the values on catalog -> print subpage
$title = 'Print Products';                                  //will hold the title of the subpage
$query_post = 'category_name=print-catalog';                 //will hold the query we will use to display the posts that are relevants to the subpage
$back_link = CATALOG_SUBLIMATION;                           //next subpage
$forward_link = CATALOG_DESIGN;                             //previous subpage
$forward_image = get_stylesheet_directory_uri().'/images/'; //will hold the forward arrow's url

switch ($page_id) {
    //catalog page (by default, must show the print products)
    case CATALOG:  
                $forward_image = $forward_image.'forward-to-design.png';
                break;
    //catalog -> print subpage
    case CATALOG_PRINT: 
                $forward_image= $forward_image.'forward-to-design.png';
                break;
    //catalog -> design subpage
    case CATALOG_DESIGN: 
                $title = 'Design Products';
                $query_post = 'category_name=design-catalog';
                $forward_image= $forward_image.'forward-to-sublimation.png';
                $back_link = CATALOG_PRINT;
                $forward_link = CATALOG_SUBLIMATION;
                break;
    //catalog -> sublimation subpage
    case CATALOG_SUBLIMATION: 
                $title = 'Sublimation Products';
                $query_post = 'category_name=sublimation-catalog';
                $forward_image= $forward_image.'forward-to-print.png';
                $back_link = CATALOG_DESIGN;
                $forward_link = CATALOG_PRINT;
                break;
    default:    break;
}
?>

<div class ="move-to-section" id="back-to-section">
    <a href="<?php echo get_page_link($back_link); ?>">
        <img src="<?php echo get_stylesheet_directory_uri() ?>/images/left-arrow.png" alt="Back">
    </a>
</div><!-- #back-to-section -->
 
<div class ="move-to-section" id="forward-to-section">
    <a href="<?php echo get_page_link($forward_link); ?>">
        <img src="<?php echo $forward_image ?>" alt="Forward">
    </a>
</div><!-- #forward-to-section -->
 
<div class="catalog-content">
    
    <div class="catalog-section">
        <h1><?php echo $title ?></h1>
    </div><!-- .catalog-section -->

    <div class="catalog-posts">
            <?php
            query_posts($query_post);
            $i = 0;
            if ( have_posts() ) : while (have_posts()) : the_post();
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
            ?>
                </div><!-- .single-catalog-post -->
            <?php
            $i++;
            if ($i == 3):
            ?>
                </div><!-- .catalog-post-row -->
            <?php
                $i = 0;
                endif;
            endwhile; endif;
            if ($i != 0):
            ?>
                </div><!-- .catalog-post-row -->
            <?php
            endif;
        ?>
    </div><!-- .catalog-posts -->
 
</div><!-- .catalog-content -->

<?php
get_footer();
?>

