<?php
/*
Template Name: Cart Page
*/
?>

<?php
get_header();
$sent = get_query_var('sent', FALSE);
if ($sent == 'true') $sent = TRUE;
else $sent = FALSE;

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
            if (isset($_POST['sub_prod']) && isset($_POST['s_quantity'])) {
                $pids = $_POST['sub_prod'];
                $sub_quantities = $_POST['s_quantity'];
                $from_index = 0;
                foreach ($pids as $pid) {
                    for ($i = 0; $i < getDesignNumber($pid); $i++)
                        setDesignQuantity($pid, $i, $sub_quantities[$from_index + $i]);
                    $from_index += $i;
                }
            }

            if (isset($_POST['print_prod']) && isset($_POST['p_quantity'])) {
                $cids = $_POST['print_prod'];
                $print_quantities = $_POST['p_quantity'];
                if (isset($_POST['page']))
                    $pages = $_POST['page'];
                $quantity_from_index = 0;
                $pages_from_index = 0;
                foreach ($cids as $cid) {
                    $cat = get_print_category($cid);
                    for ($i = 0; $i < getUploadNumber($cid); $i++) {
                        setPrintQuantity($cid, $i, $print_quantities[$quantity_from_index + $i]);
                        if ($cat->pages && isset($_POST['page']))
                            setPrintPages($cid, $i, $pages[$pages_from_index + $i]);
                    }
                    $quantity_from_index += $i;
                    if ($cat->pages && isset($_POST['page']))
                        $pages_from_index += $i;
                }            
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

<div id="cart-div">

    <div id="order-container">

        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" id="save-order-form">

        <div id="save-or-send" class="order-content">
             <?php if (!$sent && !(is_null(getSubCart()) && is_null(getPrintCart()))) { ?>
                <input type="submit" class="copycolor-button" name="save-order" value="לשמור">
                <br/>
                <input type="submit" class="copycolor-button" name="send-order" value="לשלוח">
                <br/>
            <?php } 
            else if ($sent) {
                $order_id = get_query_var('id', 0);
                if ($order_id != 0)
                    addSentOrder($order_id);
            }
            $sent_orders = getSentOrders();
            if (!is_null($sent_orders)) {
                foreach($sent_orders as $oid) {
                    $pdf_url = WP_CONTENT_URL . '/uploads/invoices/invoice-number-' . $oid . '.pdf';
                    $db_order = get_order($oid);
                    if ($db_order != NULL && !empty($db_order))  
                        echo '<input class="copycolor-button order" type="button" onClick="location.href=\'' . $pdf_url . '\'" value="' . $oid . ' \'הזמנה מס"><br/>';
                }
            }
            ?>
        </div> <!-- #save-or-send -->
        
        <div id="choose-quantity" class="order-content">
            <?php if ($sent) echo '<div style="text-align:center;"><h2>הזמנה שלך נשלחה בהצלחה</h2><h2>' . $oid . ': מספר אישור</h2></br></div>'; ?>

            <div id="sub-cart">
                <?php echoSubCartTable($sent); ?>
            </div>

            <div id="print-cart">
                <?php echoPrintCartTable($sent); ?>
            </div>
        </div> <!-- #choose-quantity -->

        <div id="client-data" class="order-content">
            <?php echoClientDetails($sent); ?>
        </div> <!-- #client-data -->

        </form>

    </div> <!-- #order-container -->

    <div id="order-price">
        <h5><span id="total_price"><?php echo calcTotalPrice(); ?>₪</span></h5>
    </div> <!-- #order-price -->

<div style="clear: both"></div>

<?php if ($sent) emptyCart(); ?>    

</div>

<?php get_footer(); ?>