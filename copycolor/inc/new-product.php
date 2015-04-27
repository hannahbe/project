<?php
    
if ('POST' == $_SERVER['REQUEST_METHOD'])
{
    global $wpdb;

    $product_name = $_POST['product_name'];
    $product_height = $_POST['product_height'];
    $product_width = $_POST['product_width'];
    $uploadedurl = NULL;

    //if the user didn't upload an image for the product, add it to the database without image's url
    if($_FILES['image_file']['error'] != 0){
        $wpdb->query("INSERT INTO wp_sublimation_products (name, height, width) VALUES ('$product_name', '$product_height', '$product_width')");
    }

    else {
        //image upload
        $upload = wp_upload_bits($_FILES['image_file']['name'], null, file_get_contents($_FILES['image_file']['tmp_name']));

        $uploadedurl = $upload['url'];

        //add to database
        $wpdb->query("INSERT INTO wp_sublimation_products (name, height, width, guid) VALUES ('$product_name', '$product_height', '$product_width', '$uploadedurl')");
    }
    ?>

    <h2>You have successfully added <?php echo $product_name ?> to your sublimation products</h2>
    <?php
    if ($uploadedurl != NULL) {
    ?>
    
    <br>
    <img src="<?php echo $uploadedurl ?>" alt="Your new product" width="300px" height="auto">

    <?php
    }
    ?>

<?php
}

else { ?>

<script src="http://code.jquery.com/jquery-1.10.0.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#product_name, #product_height, #product_width, #image_file').bind('keyup', function () {
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
    Sublimation area's height:</br>
    <input type="number" class="required" id="product_height" step="0.01" min="0" name="product_height"/></br>
    Sublimation area's width:</br>
    <input type="number" class="required" id="product_width" step="0.01" min="0" name="product_width"/></br>
    <input type="file" id="image_file" name="image_file" accept="image/*"/>
    </br>
    </br>
    <input type="submit" id="add_product" value="Add product" disabled/>
</form>



<?php
}
?>
