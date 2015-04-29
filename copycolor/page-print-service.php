<?php
/*
Template Name: Print Service Page
*/
?>

<?php
get_header();
?>

<div class="products-area">
    <?php
        global $wpdb;
        $all_print_products = $wpdb->get_results('SELECT * FROM wp_products WHERE sublimation = 0');
        $i = 0;

        if (!empty($all_print_products)) { ?>
            <br/><br/><br/>
           <table>

           <?php
                foreach ($all_print_products as $product) {
                    if ($i == 0) {
                        ?><tr><?php   
                    }?>
                    <td>
                        <table style="border: 1px solid black; border-spacing: 10px">
                            <tr>
                                <td><?php echo $product->name ?></td>
                            </tr>
                            <?php if ($product->guid != NULL) {?>
                            <tr>
                                <td><img src="<?php echo $product->guid ?>" alt="<?php echo $product->name ?> image"></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td><?php echo $product->price?> shekels</td>
                            </tr>
                            <tr>
                                <td><input type="button" value="Select"
                                       <?php $url_with_parameter = add_query_arg ('id', $product->id, get_theme_root_uri().'/copycolor/window-print-service.php'); ?>
                                       onclick="window.open('<?php echo $url_with_parameter ?>', 'window-print', 'width=500, height=300, resizable=yes')"></td>
                            </tr>
                        </table>
                    </td>
                    <?php 
                    $i++;
                    if ($i == 4) {
                        $i = 0; ?>
                        </tr>
                    <?php }
                }

                if ($i != 0) {
                    ?> </tr> <?php
                }
            ?>
            </table>

        <?php } ?>
</div>

<?php
get_footer();
?>
