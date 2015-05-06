<?php
/*
Template Name: Print Service Window
*/
?>

<?php
    require ("../../../wp-blog-header.php");    //so we can use wordpress' database
    $pid = $_GET['id'];
    $pname = get_product_name($pid);
    $pprice = get_product_price($pid);
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title><?php echo $pname ?></title>
        <script src="http://code.jquery.com/jquery-1.10.0.min.js"></script>
        <script type="text/javascript" src="js/window-print-service.js"></script>
    </head>

    <body>

        <?php
        if ('POST' == $_SERVER['REQUEST_METHOD']) {

            //if the user already clicked on "Add to cart"
            if(isset($_POST['submit'])){
                $allFilesEmpty = TRUE;
                foreach ($_POST['input_quantity'] as $key=>$arr) {

                    if ($_FILES['input_file']['size'][$key] != 0) {

                        $allFilesEmpty = FALSE;
                        $fileName = $_FILES['input_file']['name'][$key];
                        $filePath = uploadToTemp($_FILES['input_file'],$key);   //upload file to a temporary directory

                        if (!is_null($filePath)) {
                            //if the value 'cart' doesn't exist yet in session:
                            if(!array_key_exists('cart', $_SESSION))
                                $_SESSION['cart'] = array();
                            //if the user didn't enter any product of this kind in the cart yet:
                            if (!array_key_exists($pid, $_SESSION['cart'])) {
                                $_SESSION['cart'][$pid] = array();
                                //$curr_product_in_cart = 0; 
                            }  
                            /*else
                                $curr_product_in_cart = count($_SESSION['cart'][$pid]);

                            $_SESSION['cart'][$pid][$curr_product_in_cart]['quantity'] = $arr;
                            $_SESSION['cart'][$pid][$curr_product_in_cart]['filepath'] = $filePath;
                            $_SESSION['cart'][$pid][$curr_product_in_cart]['filename'] = $fileName;*/
                            $neworder = array (
                                'quantity' => $arr,
                                'filepath' => $filePath,
                                'filename' => $fileName
                            );
                            array_push($_SESSION['cart'][$pid], $neworder);
                            echo $pid . ' ' . count($_SESSION['cart'][$pid]);
                            //$_SESSION['cart'][$pid][] = $neworder;

                            echo '<br/>Uploaded file ' .  $fileName . ' added ' . $arr . ' time(s) to your cart.<br/>';
                        }  
                    }
                }

                if ($allFilesEmpty == TRUE) {
                    echo "You didn't selected any file.<br/><br/>";
                    ?><input type="button" onclick="javascript:history.back()" value="Go back">
                    <?php
                }

                //else {
                    $bla = show_cart_menu();
                    if($bla == NULL)
                        echo 'null';
                    else echo $bla;
                //}
            }
        }

        //if the user didn't clicked yet on "Add to cart"
        else {
        ?>

        <h2><?php echo $pname ?></h2>
        <h2><?php echo $pprice ?> shekels</h2>

        <form name="upload_files" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <p>
                <!-- accept images, pdf, powerpoint, excel, word -->
                <label class="input_file"><input type="file" id="input_file[0]" name="input_file[0]" accept="image/*, application/pdf, application/vnd.ms-powerpoint, application/vnd.ms-excel, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document"></label>
                <label class="input_quantity">Quantity: <input type="number" id="input_quantity[0]" name="input_quantity[0]" min="1" step="1" value="1"></label>
            </p>
            <p>
                <input type="button" value="Add file">
                <input type="button" id="remove_file" value="Remove file" disabled="disabled">
                <br/><br/>
                <input type="submit" id="submit_file" name="submit" value="Add to cart">
            </p>
        </form>

        <?php } ?>

    </body>
</html>
