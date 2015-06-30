<?php
/*
Template Name: Print Service Page
*/
?>

<?php
get_header();
const PAGE_ID = 190;
$SIDES = 1;
$WEIGHT = 2;
$PAPER = 3;
$COLOR = 4;
$SIZE = 5;
?>

<div class="print-service">

    <?php
    echoServiceBar("Print");
    $step = get_query_var('step', 1);

    if ($step == 1) {           // 1st step 
        $cats = get_all_print_categories();
        echoServiceTable($cats, PAGE_ID);
    }
 
    else if ($step == 2) { // 2nd sublimation-service screen

        $cid = get_query_var('id', 0);
        $cat = get_print_category($cid);
        $print_prods = get_print_products($cid);

        if (is_null($print_prods) || count($print_prods) == 0)
            echo "<br/><br/><h1>Error, product doesn't exists</h1>";

        else {

            if ('POST' != $_SERVER['REQUEST_METHOD']) {     // screen 2-a
                initializePrintCategory($cid);
                ?>
                <div class="print-step2">
                    <img src="<?php echo $cat->icon; ?>" alt="<?php echo $cat->name; ?>"></br>
                    <label><?php echo $cat->name; ?></label></br>
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/buttons/arrow.png" alt="arrow-top" id="arrow"></br>
                    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                        <input type="submit" value="להעלות קבצים"/>
                    </form>
                </div>
                <?php
            }

            else if ('POST' == $_SERVER['REQUEST_METHOD']  && (!isset($_POST['action']) || $_POST['action'] == 'עוד' || $_POST['action'] == 'להמשיך' || isset($_POST['delete']))) {
                
                if (isset($_POST['delete'])) {
                    $total_files = getUploadNumber($cid);
                    if (intval($_POST['delete'] < $total_files))
                        removeFile($cid, intval($_POST['delete']));
                }

                else if (isset($_POST['action'])) {     // the user wants to add another file
                    $total_files = getUploadNumber($cid);
                    if ((count($_FILES['uploaded_file']['size']) < $total_files+1 || $_FILES['uploaded_file']['size'][$total_files] == 0) && $_POST['action'] != 'להמשיך')
                        echo '<script language="javascript">alert("אנא העלה קובץ")</script>';
                    else {
                        for ($i = 0; $i <= $total_files; $i++) {
                            if ($_FILES['uploaded_file']['size'][$i] != 0) {
                                if ($i != $total_files) {
                                    unlink(url_to_path(getFilepath($cid, $i)));
                                }
                                $filepath = uploadFile($_FILES['uploaded_file'], $i);
                                if (is_null($filepath))
                                    echo '<script language="javascript">alert("Sorry, an error occured while uploading the file, please try again(max size is ' . ini_get("upload_max_filesize") . ')")</script>';
                                else if ($i == $total_files)
                                    addFile($cid, $_FILES['uploaded_file']['name'][$i], $filepath);
                                else
                                    editFile($cid, $i, $_FILES['uploaded_file']['name'][$i], $filepath);
                            }
                        }
                    }
                    if ($_POST['action'] == 'להמשיך') {
                        die("<script>location.href = '" . add_query_arg (array('step' => '3', 'id' => $cid), get_page_link(PAGE_ID)) . "'</script>");
                }
            }

                ?>

                <div class="print-step2">
                    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" id="upload-form">
                        <?php echoPrintTable($cid, TRUE); ?>
                        <div class="print-uploads-next">
                            <input type="submit" name="action" value="עוד"/>
                            <input type="submit" name="action" value="להמשיך"/>
                        </div>
                    </form>
                </div> <!-- .print-step2 -->
                <?php
            }
        }
    }

    else if ($step == 3) {

        $cid = get_query_var('id', 0);
        $cat = get_print_category($cid);
        $print_prods = get_print_products($cid);
        $first_populate = get_first_populate_option($cid);
        
        if (is_null($print_prods) || count($print_prods) == 0)
            echo "<br/><br/><h1>Error, product doesn't exists</h1>";

        else if ($first_populate == '') {
            $query = "SELECT * FROM wp_print_products WHERE category = '$cid'";
            $pid = getrow_copycolor($query)->id;
            if ($pid > 0) {
                for ($i = 0; $i < getUploadNumber($cid); $i++)
                    setPrintPid($cid, $i, $pid);
            }
            die("<script>location.href = '" . add_query_arg (array('step' => '4', 'id' => $cid), get_page_link(PAGE_ID)) . "'</script>");
        }

        else {
            
            if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['go-on'])) {

                if (!allOptionsFilled(getUploadNumber($cid), $cat->sides, $cat->weight, $cat->paper, $cat->color, $cat->size))
                    echo '<script language="javascript">alert("בחר אופציות הדפסה לכל קובץ")</script>';
                    
                else {
                    for ($i = 0; $i < getUploadNumber($cid); $i++) {
                        $query = "SELECT * FROM wp_print_products WHERE category = '$cid' ";
                        if ($cat->sides) {
                            $sides_input = $_POST['sides-' . $i];
                            $query = $query . "AND sides = '$sides_input'";
                        }
                        if ($cat->weight) {
                            $weight_input = $_POST['weight-' . $i];
                            $query = $query . "AND weight = '$weight_input'";
                        }
                            
                        if ($cat->paper) {
                            $paper_input = $_POST['paper-' . $i];
                            $query = $query . "AND paper = '$paper_input'";
                        }
                            
                        if ($cat->color) {
                            $color_input = $_POST['color-' . $i];
                            $query = $query . "AND color = '$color_input'";
                        }
                            
                        if ($cat->size) {
                            $size_input = $_POST['size-' . $i];
                            $query = $query . "AND size = '$size_input'";
                        }
                        $pid = getrow_copycolor($query)->id;
                        if ($pid > 0)
                            setPrintPid($cid, $i, $pid);
                    }
                    
                    die("<script>location.href = '" . add_query_arg (array('step' => '4', 'id' => $cid), get_page_link(PAGE_ID)) . "'</script>");
                }
            }
              
            ?>
            <div class="print-step2">
                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="category" value="<?php echo $cid; ?>">
                    <input type="hidden" id="current_index" value="-1">
                    <h2>:בחר אופציות הדפסה לכל קובץ</h2>
                    <?php
                    echoPrintTable($cid, FALSE); 

                    for ($i = 0; $i < getUploadNumber($cid); $i++) {

                        echo '<div class="print-options" id="print-options-' . $i . '" hidden>';
                        echo '<table>';
                        echo '<tr>';
                        $index = 1;
                        if ($cat->sides)
                            echoSelectOption ($i, $cid, 'sides', $first_populate == $SIDES, $index++);
                        if ($cat->weight)
                            echoSelectOption ($i, $cid, 'weight', $first_populate == $WEIGHT, $index++);
                        if ($cat->paper)
                            echoSelectOption ($i, $cid, 'paper', $first_populate == $PAPER, $index++);
                        if ($cat->color)
                            echoSelectOption ($i, $cid, 'color', $first_populate == $COLOR, $index++);
                        if ($cat->size)
                            echoSelectOption ($i, $cid, 'size', $first_populate == $SIZE, $index++);
                        echo '</tr>';
                        echo '<tr>';
                        if ($cat->sides)
                            echo '<td>צדים</td>';
                        if ($cat->weight)
                            echo '<td>משקל</td>';
                        if ($cat->paper)
                            echo '<td>נייר</td>';
                        if ($cat->color)
                            echo '<td>צבע</td>';
                        if ($cat->size)
                            echo '<td>גודל</td>';
                        echo '</tr>'; 
                        echo '</table>';
                        echo '</div>';
                    }
                    ?>
                    <div class="print-uploads-next">
                        <input type="submit" name="go-on" value="להמשיך"/>
                    </div>
                </form>
            </div> <!-- .print-step2 -->
        <?php

        }
    }

    else if ($step == 4) {
        $cid = get_query_var('id', 0);
        $print_prods = get_print_products($cid);

        if (is_null($print_prods))
            echo "<br/><br/><h1>Error, product doesn't exists</h1>";

        else {
            if ('POST' == $_SERVER['REQUEST_METHOD']) {      // after submit

                if (!isset($_POST['complete-name']) || !isset($_POST['mail']) || !isset($_POST['address']) || !isset($_POST['phone'])
                    || $_POST['complete-name'] == 'שם מלא' || $_POST['mail'] == 'מייל' || $_POST['address'] == 'כתובת' || $_POST['phone'] == 'נייד')
                    echo '<script language="javascript">alert("אנא מלא את כל הפרטים")</script>';

                else {
                    setSessionName($_POST['complete-name']);
                    setSessionMail($_POST['mail']);
                    setSessionAddress($_POST['address']);
                    setSessionPhone($_POST['phone']);

                    if (getClientName() == NULL || getClientAddress() == NULL || getClientMail() == NULL || getClientPhone() == NULL)
                        echo '<script language="javascript">alert("אנא מלא את כל הפרטים")</script>';

                    else {
                        $quantities = $_POST['p_quantity'];
                        $cat = get_print_category($cid);
                        if ($cat->pages && isset($_POST['page']))
                            $pages = $_POST['page'];
                    
                        for ($i = 0; $i < getUploadNumber($cid); $i++) {
                            setPrintQuantity($cid, $i, $quantities[$i]);
                            if ($cat->pages && isset($_POST['page']))
                                setPrintPages($cid, $i, $pages[$i]);
                        }
                        if (isset($_POST['save-order']) || cartIsEmpty())    // view order
                            die("<script>location.href = '" . add_query_arg (array('sent' => 'false'), get_page_link(CART_ID)) . "'</script>");
                        else if (isset($_POST['send-order'])) {  // send and view order
                            $order_id = sendOrder();
                            if ($order_id == 'empty')
                                echo '<h2>Empty order</h2>';
                            else
                                die("<script>location.href = '" . add_query_arg (array('sent' => 'true', 'id' => $order_id), get_page_link(CART_ID)) . "'</script>");
                        }
                    }
                }
            }

            ?>
            <div id="order-container">

                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" id="save-order-form">

                <div id="save-or-send" class="order-content">
                    <input type="submit" class="copycolor-button" name="save-order" value="לשמור">
                    <br/>
                    <input type="submit" class="copycolor-button" name="send-order" value="לשלוח">
                </div> <!-- #save-or-send -->

                <div id="choose-quantity" class="order-content">
                    <?php echoPrint4Table($cid); ?>
                </div> <!-- #choose-quantity -->

                <div id="client-data" class="order-content">
                    <?php echoClientDetails(FALSE); ?>
                </div> <!-- #client-data -->

                </form>

            </div> <!-- #order-container -->

            <div id="order-price">
                <h5><span id="total_price"><?php echo calcTotalPrintPrice($cid); ?>₪</span></h5>
            </div> <!-- #order-price -->

        <div style="clear: both"></div>

            <?php
        }
    }

    else {
        echo "<h1>Error</h1>";
    }
    ?>
</div> <!-- .sublimation-service -->

<?php
echoBeforeFooter();
get_footer();
?>