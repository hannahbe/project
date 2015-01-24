<?php
/**
 * The template for displaying the footer
 * Contains footer content and the closing of the #main and #page div elements.
 */
?>

		</div><!-- #main -->

		<footer id="colophon" class="site-footer" role="contentinfo">

			<?php get_sidebar( 'footer' ); ?>

			<div class="site-info">

                <!-- display company address: -->
				<?php echo get_theme_mod('company_address'); ?>

                <!-- display company phone: -->
                <?php 
                    if (get_theme_mod('company_phone') != ''):
                ?>
                    <br>
                    <?php echo get_theme_mod('company_phone');
                    endif;
                ?>

                <!-- display company email: -->
                <?php 
                    if (get_theme_mod('company_email') != ''):
                ?>
                    <br>
                    <?php echo get_theme_mod('company_email');
                    endif;
                ?>

                <!-- display link to facebook page: -->
                <?php 
                    if (get_theme_mod('display_facebook_page') == TRUE):
                ?>
                    <br>
                    <br>
                    <a href="<?php echo get_theme_mod('url_facebook_page'); ?>">
                        <img src="<?php echo get_stylesheet_directory_uri() ?>/images/facebook.jpg" alt="Print" width="30px">
			        </a>
                <?php 
                    endif;
                ?>
            </div><!-- .site-info -->
		</footer><!-- #colophon -->

	</div><!-- #page -->

	<?php wp_footer(); ?>
</body>
</html>