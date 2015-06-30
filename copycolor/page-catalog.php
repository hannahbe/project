<?php
/*
Template Name: Catalog Page
*/
?>
 
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
                <div class="single-catalog-post hide-img">
               <div class="catalog-post-text">
            <?php
            the_title( '<h2>', '</h2>' );
            the_content();
            ?>
                      
                </br>
                   </div>
                <img src="<?php echo get_first_image(get_the_ID())?>" alt="<?php echo get_the_title(get_the_ID()); ?>"></img>
  
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

<script type="text/javascript">
    $(document).ready(function () {
        var cell_width = 0.18 * window.innerWidth;
        var cells = document.getElementsByClassName("single-catalog-post");
        for (var i = 0; i < cells.length; i++) {
            cells[i].getElementsByTagName("img")[1].style.width = (cell_width + "px");
            cells[i].style.width = (cell_width + "px");
        }
    });
    window.onload = function (e) {
        var cells = document.getElementsByClassName("single-catalog-post");
        for (var i = 0; i < cells.length; i++) {
            var margin_bottom = cells[i].getElementsByTagName("img")[1].height;
            cells[i].getElementsByClassName("catalog-post-text")[0].style.marginBottom = margin_bottom + "px";
        }
    }
</script>

<?php
get_footer();
?>

