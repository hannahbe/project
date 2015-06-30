<?php
/*
Template Name: Contact Page
*/
?>

<?php get_header();
$article = get_main_article();
?>


<div id="about-main">

    <div id="about-us">
        <div id="about-article">
            <h2>Contact us</h2>
            <p><?php echo get_theme_mod('company_address'); ?></p>
            <p><?php echo get_theme_mod('company_phone'); ?></p>
            <p><?php echo get_theme_mod('company_email'); ?></p>
        </div>
        <div id="about-us-img"><img src="<?php echo $article->image; ?>" alt="copy color"></div>
        <div style="clear: both"></div>
    </div>

</div>

<?php get_footer(); ?>
