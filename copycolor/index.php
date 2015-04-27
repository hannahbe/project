<?php
get_header();
?>

<div id="index-content">

        <!-- the banner: -->
		<div id="content1">

             	<?php wd_slider(1); ?>

        </div><!-- #content1 -->

        <!-- the 3 images: (the color of this div is the same as the header) -->
        <div id="content2" style="background-color: <?php echo get_theme_mod('header_color'); ?>">

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

</div><!-- #index-content -->

<?php
get_footer();
?>