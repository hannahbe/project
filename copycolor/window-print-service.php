<?php
/*
Template Name: Print Service Window
*/
?>

<?php
    require ("../../../wp-blog-header.php");    //so we can use wordpress' database
    global $wpdb;
    $product =  $wpdb->get_row('SELECT * FROM wp_products WHERE id = ' . $_GET['id']);
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title><?php echo $product->name ?></title>
    </head>

    <body>

        <script src="http://code.jquery.com/jquery-1.10.0.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {

                // display another upload and quantity fields on click on "Add file"
                $("input[value='Add file']").click(function (event) {
                    event.preventDefault();
                    var count = $(".input_file").length;
                    $("label.input_file:last").clone().find('input').attr({
                        'id' : 'input_file['+count+']',
                        'name' : 'input_file['+count+']'
                    }).val("").end().insertAfter("label.input_quantity:last");
                    $("label.input_quantity:last").clone().find('input').attr({
                        'id' : 'input_quantity['+count+']',
                        'name'  :'input_quantity['+count+']'
                    }).val("1").end().insertAfter("label.input_file:last");
                    $('#remove_file').removeAttr('disabled');
                });

                // remove last upload and quantity fields on click on "Remove file"
                $("input[value='Remove file']").click(function (event) {
                    event.preventDefault();
                    $("label.input_quantity:last").remove();
                    $("label.input_file:last").remove();
                    if (!canRemove())
                        $('#remove_file').attr('disabled', true);
                });

                //return true if we can remove last upload and quantity fields (= if there are at least 2 upload fields)
                function canRemove() {
                    var remove = false;
                    var e = document.getElementsByClassName("input_file");
                    if (e.length > 1)
                        remove = true;
                    return remove;
                }
            });
        </script>

        <?php
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            if(isset($_POST['submit'])){
                foreach ($_POST['input_quantity'] as $key=>$arr) {
                    //upload file:
                    $upload = wp_upload_bits($_FILES['input_file']['name'][$key], null, file_get_contents($_FILES['input_file']['tmp_name'][$key]));
                    echo "Upload: " . $_FILES['input_file']["name"][$key] . "<br>";
                }
            }
        }

        else {
        ?>

        <h2><?php echo $product->name ?></h2>
        <h2><?php echo $product->price ?> shekels</h2>

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
                <input type="submit" name="submit" value="Send order">
            </p>
        </form>

        <?php } ?>

    </body>
</html>
