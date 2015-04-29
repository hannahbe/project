<?php

/**********     SUBLIMATION MENU     **********/

add_action ('admin_menu', 'register_sublimation_menu');

function register_sublimation_menu() {
     add_menu_page('Sublimation', 'Sublimation', 'manage_options', 'view-sub-products');
     add_submenu_page('view-sub-products', 'Products', 'Products', 'read', 'view-sub-products', 'view_sub_products');
     add_submenu_page('view-sub-products', 'Add Product', 'Add Product', 'read', 'add-sub-product', 'add_sub_product');
     add_submenu_page('view-sub-products', 'Backgrounds', 'Backgrounds', 'read', 'view-backgrounds', 'view_backgrounds');
     add_submenu_page('view-sub-products', 'Add Background', 'Add Background', 'read', 'add-background', 'add_background');
     add_submenu_page('view-sub-products', 'Orders To do', 'Orders To do', 'read', 'view-sub-orders-todo', 'view_sub_orders_todo');
     add_submenu_page('view-sub-products', 'Orders Already done', 'Orders Already done', 'read', 'view-sub-orders-done', 'view_sub_orders_done');
}

function view_sub_products() {
     view_items('sublimation');
}

function add_sub_product() {
     new_product('sublimation');
}

function view_backgrounds() {
     view_items('background');
}

function add_background() {

    if ('POST' == $_SERVER['REQUEST_METHOD']) {
        global $wpdb;

        //image upload
        $upload = wp_upload_bits($_FILES["new_background"]["name"], null, file_get_contents($_FILES["new_background"]["tmp_name"]));

        $uploadedurl = $upload['url'];

        //add to database
        $wpdb->query("INSERT INTO wp_backgrounds (guid) VALUES ('$uploadedurl')");
        ?>
    
        <h2>You have successfully added a new background</h2>
        <br>
        <img src="<?php echo $uploadedurl ?>" alt="Your new background">

        <?php
    }

    else { ?>

        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <h2>Select a background: </h2>
            <input type="file" name="new_background" id="new_background" accept="image/*"/>
            <br><br>
            <input type="submit" id="add_background" value="Add background" disabled/>
        </form>

        <script type="text/javascript">
            document.getElementById("new_background").onchange = function() {
            if(this.value) {
                document.getElementById("add_background").disabled = false; 
            }  
            }
        </script>

    <?php
    }
}

function view_sub_orders_todo() {
    
}

function view_sub_orders_done() {
    
}

/**********     PRINT MENU     **********/

add_action ('admin_menu', 'register_print_menu');

function register_print_menu() {
     add_menu_page('Print', 'Print', 'manage_options', 'view-print-products');
     add_submenu_page('view-print-products', 'Products', 'Products', 'read', 'view-print-products', 'view_print_products');
     add_submenu_page('view-print-products', 'Add Product', 'Add Product', 'read', 'add-print-product', 'add_print_product');
     add_submenu_page('view-print-products', 'Orders To do', 'Orders To do', 'read', 'view-print-orders-todo', 'view_print_orders_todo');
     add_submenu_page('view-print-products', 'Orders Already done', 'Orders Already done', 'read', 'view-print-orders-done', 'view_print_orders_done');
}

function view_print_products() {
     view_items('print');
}

function add_print_product() {
     new_product('print');
}

function view_print_orders_todo() {
     
}

function view_print_orders_done() {
     
}

/**********     GENERAL FUNCTIONS     **********/

function new_product ($typeOfProduct) {

    $isSublimationProduct = 0;
    if ($typeOfProduct == 'sublimation') {
        $isSublimationProduct = 1;
    }

    if ('POST' == $_SERVER['REQUEST_METHOD']) {

        global $wpdb;

        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];

        $product_height = 0;
        $product_width = 0;
        if ($isSublimationProduct == 1) {
            $product_height = $_POST['product_height'];
            $product_width = $_POST['product_width'];
        }

        $uploadedurl = NULL;

        //if the user didn't upload an image for the product, add it to the database without image's url
        if($_FILES['image_file']['error'] != 0){
            $wpdb->query("INSERT INTO wp_products (name, height, width, price, sublimation) VALUES ('$product_name', '$product_height', '$product_width', '$product_price', '$isSublimationProduct')");
        }

        else {
            //image upload
            $upload = wp_upload_bits($_FILES['image_file']['name'], null, file_get_contents($_FILES['image_file']['tmp_name']));

            $uploadedurl = $upload['url'];

            //add to database
            $wpdb->query("INSERT INTO wp_products (name, height, width, guid, price, sublimation) VALUES ('$product_name', '$product_height', '$product_width', '$uploadedurl', '$product_price', '$isSublimationProduct')");
        }
        ?>

        <h2>You have successfully added <?php echo $product_name ?> to your <? echo $typeOfProduct ?> products</h2>
        <?php
        if ($uploadedurl != NULL) {
        ?>
            <br>
            <img src="<?php echo $uploadedurl ?>" alt="Your new product" width="300px" height="auto">
        <?php
        }
    }

    //if the admin didn't filled the form yet
    else { ?>

        <script src="http://code.jquery.com/jquery-1.10.0.min.js"></script>
        <script type="text/javascript">

            $(document).ready(function () {
                $('#product_name, #product_price, #product_height, #product_width, #image_file').bind('keyup', function () {
                    if (allFilled())
                        $('#add_product').removeAttr('disabled');
                    else
                        $('#add_product').attr('disabled', true);
                });

                function allFilled() {
                    var filled = true;
                    $('.required').each(function () {
                        if ($(this).val() == '') filled = false;
                    });
                    return filled;
                } 
            });
        </script>

        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            Product Name:<br/>
            <input type="text" class="required" id="product_name" name="product_name"/></br>
            Product price:<br/>
            <input type="number" class="required" id="product_price" step="0.01" min="0" name="product_price"/></br>
            <?php if ($isSublimationProduct == 1) { ?>
                Sublimation area's height:</br>
                <input type="number" class="required" id="product_height" step="0.01" min="0" name="product_height"/></br>
                Sublimation area's width:</br>
                <input type="number" class="required" id="product_width" step="0.01" min="0" name="product_width"/></br>
            <?php } ?>
            <input type="file" id="image_file" name="image_file" accept="image/*"/>
            </br></br>
            <input type="submit" id="add_product" value="Add product" disabled/>
        </form>

    <?php
    }
}

function view_items ($typeOfService) {

     global $wpdb;

     switch($typeOfService) {
        case 'sublimation': $table = 'wp_products';
                            $query = 'SELECT * FROM wp_products WHERE sublimation = 1';
                            $max_i = 4;
                            $item_type = 'product';
                            break;
        case 'print':       $table = 'wp_products';
                            $query = 'SELECT * FROM wp_products WHERE sublimation = 0';
                            $max_i = 4;
                            $item_type = 'product';
                            break;
        case 'background':  $table = 'wp_backgrounds';
                            $query = 'SELECT * FROM wp_backgrounds';
                            $max_i = 3;
                            $item_type = 'background';
                            break;
        default:            return;
     }

    if ('POST' == $_SERVER['REQUEST_METHOD']) {
        $remove = $_POST['item_to_remove'];
        if (!empty($remove)) {
            $N = count($remove);
            for ($i = 0; $i < $N; $i++) {
                //delete attached file/image if exists
                $current = $wpdb->get_row("SELECT * FROM " .$table . " WHERE id = $remove[$i]");
                $current_guid = $current->guid;
                if ($current_guid != NULL) {
                    unlink(url_to_path_test($current_guid));
                }
                //remove from database
                $wpdb->query("DELETE FROM " . $table . " WHERE id = $remove[$i]");
            }
        }
    }

    $all_items = $wpdb->get_results ($query);
    $i = 0;

    if (!empty($all_items)) {?>

        <!-- script to disable/enable the button when checkboxes are unchecked/checked -->
        <script src="http://code.jquery.com/jquery-1.10.0.min.js"></script>
        <script type="text/javascript">
            $(function() {
                $(".remove_checkbox").click(function(){
                $('#remove_item').prop('disabled',$('input.remove_checkbox:checked').length == 0);
            });
        });
        </script>
        
        <br/>
        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
            <table>

            <?php
            foreach ($all_items as $item) {
                if ($i == 0) {
                    ?><tr><?php   
                }?>
                <td>
                <table 
                    <?php if ($typeOfService == 'sublimation' || $typeOfService == 'print') { ?>
                       style="border: 1px solid black; border-spacing: 10px"
                    <?php } ?>>
                <?php
                if ($typeOfService == 'sublimation' || $typeOfService == 'print') {
                ?>
                    <tr>
                        <td><?php echo $item->name; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $item->price; ?> shekels</td>
                    </tr>
                    <tr>
                        <td>
                        <?php
                        if ($item->guid != NULL) {
                        ?>
                            <img src="<?php echo $item->guid; ?>" alt="<?php echo $item->name; ?>'s image" height="200px" width="auto">
                        <?php
                        }
                        else {
                        ?>No image for this product<?php
                        }
                        ?>
                        </td>
                    </tr>
                <?php
                }
                else if ($typeOfService == 'background'){
                ?>
                    <tr>
                        <td>
                            <img src="<?php echo $item->guid; ?>" alt="background <?php echo $item->id ?>" height="200px" width="auto">
                        </td>
                    </tr>
                <?php
                }
                ?>
                <tr>
                    <td>
                        <input type="checkbox" class="remove_checkbox" name="item_to_remove[]" value="<?php echo $item->id ?>">Select to remove the <?php echo $item_type ?>
                    </td>
                </tr>
            </table>
            </td>
    <?php
    $i++;
    if ($i == $max_i) {
        ?></tr><?php
        $i = 0; 
        }
    }?>
        </table>
        <br/>
        <br/>
        <input type="submit" id="remove_item" value="Remove <?php echo $item_type ?>s" disabled="disabled">
    </form>

    <?php
    }

    else {?>
        <h2>There are no <?php echo $item_type ?>s to display</h2><?php    
    }

}

?>