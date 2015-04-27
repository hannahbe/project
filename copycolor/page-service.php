<?php
/*
Template Name: Service Page
*/
?>

<?php
get_header();
?>

<div class="products-area">
    <?php
        $i = 0;
        if ( have_posts() ) : while (have_posts()) : the_post();
        if ($i == 0):
    ?>
</div>

<?php
get_footer();
?>
