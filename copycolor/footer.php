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
                <?php 
                if (get_theme_mod('company_address') != ''):
                ?>
                    <h6 class="company_address">
				    <?php echo get_theme_mod('company_address'); ?>
                    </h6>
                    <?php
                 endif;

                 // display company phone:
                 if (get_theme_mod('company_phone') != ''):
                 ?>
                    <h6 class="company_phone">
                    <?php echo get_theme_mod('company_phone');?>
                    </h6>
                    <?php
                 endif;

                 // display company email:
                 if (get_theme_mod('company_email') != ''):
                 ?>
                    <h6 class="company_email">
                    <?php echo get_theme_mod('company_email');?>
                    </h6>
                    <?php
                 endif;

                // display link to facebook page:
                $visibility = ( TRUE == get_theme_mod('display_facebook_page') ) ? '' : 'hidden';
                ?>
                    <br>
                    <br>
                    <div id="fblink" class="<?php echo $visibility; ?>">
                        <a href="<?php echo get_theme_mod('url_facebook_page'); ?>">
                            <img src="<?php echo get_stylesheet_directory_uri() ?>/images/facebook.jpg" alt="Print">
			            </a>
                    </div>
                
            </div><!-- .site-info -->
		</footer><!-- #colophon -->

	</div><!-- #page -->

	<?php wp_footer(); ?>
</body>
</html>