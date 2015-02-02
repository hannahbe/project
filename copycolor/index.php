<?php
get_header();
?>

<div id="main-content" class="main-content">

    <div id="primary" class="content-area">

        <!-- the banner: -->
		<div id="content" class="site-content" role="main">
            <img src="<?php echo get_stylesheet_directory_uri() ?>/images/mainbanner.png" alt="A mug with your own design" width="100%">
        </div><!-- #content -->

        <!-- the 3 images: (the color of this div is the same as the header) -->
        <div id="content2" class="site-content" role="main" style="background-color: <?php echo get_theme_mod('header_color'); ?>">

            <div id="print" class="pds">
                <img src="<?php echo get_stylesheet_directory_uri() ?>/images/print.png" alt="Print" width="60%">
            </div><!-- #print -->

            <div id="design" class="pds">
                <img src="<?php echo get_stylesheet_directory_uri() ?>/images/design.png" alt="Design" width="60%">
            </div><!-- #design -->

            <div id="sublimation" class="pds">
                <img src="<?php echo get_stylesheet_directory_uri() ?>/images/sublimation.png" alt="Sublimation" width="60%">
            </div><!-- #sublimation -->

        </div><!-- #content2 -->

	</div><!-- #primary -->

</div><!-- #main-content -->

<?php
get_footer();
?>