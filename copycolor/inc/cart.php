<?php
    
function cart_enqueue() {
    global $post;    //retrieves the current menu slug
    $CART_ID = 208;
    if ($post->ID == $CART_ID) {
        wp_enqueue_script('ext-jquery-script', 'http://code.jquery.com/jquery-1.10.0.min.js');
        wp_enqueue_script('service-final-step-js', get_stylesheet_directory_uri() . '/js/service-final-step.js');
    }
}
add_action('wp_enqueue_scripts', 'cart_enqueue');

//SESSION
add_action('init', 'myStartSession', 1);
add_action('wp_logout', 'myEndSession');
add_action('wp_login', 'myEndSession');

function myStartSession() {
    if(!session_id())
        session_start();
}

function myEndSession() {
    $sub_cart = getSubCart();
    $print_cart = getPrintCart();
    if (!is_null($sub_cart) || !is_null($print_cart)) {
        if ($sub_cart != NULL) {
            foreach ($sub_cart as $pid => $sub_cart_product) {
                for ($i = 0; $i < getDesignNumber($pid); $i++) {
                    if (array_key_exists('url', $prod) && !is_null($prod['url']))
                        unlink(url_to_path($prod['url']));
                }  
            }
        }
    
        if ($print_cart != NULL) {
            foreach ($print_cart as $cid => $print_cart_cat) {
                for ($i = 0; $i < getUploadNumber($cid); $i++) {
                    if (array_key_exists('filepath', $prod) && !is_null($prod['filepath']))
                        unlink(url_to_path($prod['filepath']));
                }  
            }
        }
    }
    session_destroy ();
}

function setSessionName($name) {
    $_SESSION['name'] = $name;
}

function setSessionAddress($address) {
    $_SESSION['address'] = $address;
}

function setSessionMail($mail) {
    $_SESSION['mail'] = $mail;
}

function setSessionPhone($phone) {
    $_SESSION['phone'] = $phone;
}

function getClientName() {
    return (isset($_SESSION['name']) ? $_SESSION['name'] : NULL);
}

function getClientAddress() {
    return (isset($_SESSION['address']) ? $_SESSION['address'] : NULL);
}

function getClientMail() {
    return (isset($_SESSION['mail']) ? $_SESSION['mail'] : NULL);
}

function getClientPhone() {
    return (isset($_SESSION['phone']) ? $_SESSION['phone'] : NULL);
}

/*****     $_SESSION['orders']     *****/
// holds the ids of the already sent orders

function checkSentOrders() {
    if(!array_key_exists('orders', $_SESSION))                        
        $_SESSION['orders'] = array();  
}

function addSentOrder($id) {
    checkSentOrders();
    if (!in_array($id, $_SESSION['orders']))
        array_push($_SESSION['orders'], $id);
}

function getSentOrders() {
    if(!array_key_exists('orders', $_SESSION) || empty($_SESSION['orders']))
        return NULL;
    return $_SESSION['orders'];
}

/*****     $_SESSION['cart']     *****/

function cartIsEmpty() {
    $sub_cart = getSubCart();
    $print_cart = getPrintCart();
    if (!is_null($sub_cart) || !is_null($print_cart)) {
        if ($sub_cart != NULL) {
            foreach ($sub_cart as $pid => $sub_cart_product) {
                foreach ($_SESSION['cart']['sublimation'][$pid] as $prod) {
                    if (array_key_exists('quantity', $prod) && $prod['quantity'] > 0)
                        return FALSE;
                }
            }
        }
    
        if ($print_cart != NULL) {
            foreach ($print_cart as $cid => $print_cart_cat) {
                foreach ($_SESSION['cart']['print'][$cid] as $prod) {
                    if (array_key_exists('quantity', $prod) && $prod['quantity'] > 0)
                        return FALSE;
                }
            }
            unset($_SESSION['cart']['print']);
        }
    }
    return TRUE;        
}

function emptyCart() {
    $sub_cart = getSubCart();
    $print_cart = getPrintCart();
    if (!is_null($sub_cart) || !is_null($print_cart)) {
        if ($sub_cart != NULL) {
            foreach ($sub_cart as $pid => $sub_cart_product) {
                $dlt_count = 0;
                foreach ($_SESSION['cart']['sublimation'][$pid] as $key=>$prod) {
                    array_splice($_SESSION['cart']['sublimation'][$pid], $key- $dlt_count, 1);
                    $dlt_count++;
                }
            }
            unset($_SESSION['cart']['sublimation']);
        }
    
        if ($print_cart != NULL) {
            foreach ($print_cart as $cid => $print_cart_cat) {
                $dlt_count = 0;
                foreach ($_SESSION['cart']['print'][$cid] as $key=>$prod) {
                    array_splice($_SESSION['cart']['print'][$cid], $key - $dlt_count, 1);
                    $dlt_count++;
                }
            }
            unset($_SESSION['cart']['print']);
        }
    }                    
    unset($_SESSION['cart']);
}

function checkService($service, $id) {
    if(!array_key_exists('cart', $_SESSION))                        //if the cart doesn't exist yet
        $_SESSION['cart'] = array();                   
    if (!array_key_exists($service, $_SESSION['cart']))             //if the user didn't enter any service product in the cart yet
        $_SESSION['cart'][$service] = array();
    if (!array_key_exists($id, $_SESSION['cart'][$service]))        //if the user didn't enter any service product of this kind yet 
        $_SESSION['cart'][$service][$id] = array();
}

/*****     $_SESSION['cart']['sublimation']     *****/
/*
 * $_SESSION['cart']['sublimation'][product-id][index] = (design-url, quantity)
 */

function initializeSubProduct($pid) {
    checkService('sublimation', $pid);
    if (is_null(get_sub_product($pid)) || count(get_sub_product($pid)) == 0 || empty($_SESSION['cart']['sublimation'][$pid])) {
        unset($_SESSION['cart']['sublimation'][$pid]);
        return;
    }
    $dlt_count = 0;
    foreach ($_SESSION['cart']['sublimation'][$pid] as $key=>$prod) {
        if (!array_key_exists('quantity', $prod) || $prod['quantity'] == -1) {
            if (array_key_exists('quantity', $prod))
                unlink(url_to_path($prod['url']));
            array_splice($_SESSION['cart']['sublimation'][$pid], $key- $dlt_count, 1);
            $dlt_count++;
        }
    }
}

function checkSubProduct($pid) {
    checkService('sublimation', $pid);
}

function getSubCart() {
    if(!array_key_exists('cart', $_SESSION) || !array_key_exists('sublimation', $_SESSION['cart']))
        return NULL;
    if (empty($_SESSION['cart']['sublimation']) || count($_SESSION['cart']['sublimation']) == 0)
        return NULL;
    foreach ($_SESSION['cart']['sublimation'] as $pid=>$sub_product)
        initializeSubProduct($pid);
    return $_SESSION['cart']['sublimation'];   
}

function addDesign($pid, $url) {
    checkSubProduct($pid);
    $new_design = array (
        'url' => $url,
        'quantity' => -1
    );
    array_push($_SESSION['cart']['sublimation'][$pid], $new_design);
}

function setDesignQuantity($pid, $index, $quantity) {
    if ($index >= count($_SESSION['cart']['sublimation'][$pid]))
			return;
    $_SESSION['cart']['sublimation'][$pid][$index]['quantity'] = $quantity;
}

function getDesignNumber($pid) {
    checkSubProduct($pid);
    return count($_SESSION['cart']['sublimation'][$pid]);
}

function getSublimationField($pid, $index, $field) {
    checkSubProduct($pid);
    if ($index >= count($_SESSION['cart']['sublimation'][$pid]))
        return NULL;
    return $_SESSION['cart']['sublimation'][$pid][$index][$field];
}

function getSublimationDesign($pid, $index) { return getSublimationField($pid, $index, 'url'); }

function getSublimationQuantity($pid, $index) { return getSublimationField($pid, $index, 'quantity'); }

/*****     $_SESSION['cart']['print']     *****/
/*
 * $_SESSION['cart']['print'][category-id][index] = (filename, file-url, product-id, quantity, pages)
 */

function initializePrintCategory($cid) {
    checkService('print', $cid);
    if (is_null(get_print_category($cid)) || count(get_print_category($cid)) == 0 || empty($_SESSION['cart']['print'][$cid])) {
        unset($_SESSION['cart']['print'][$cid]);
        return;
    }
    $dlt_count = 0;
    foreach ($_SESSION['cart']['print'][$cid] as $key=>$prod) {
        if (!array_key_exists('quantity', $prod) || $prod['quantity'] == -1) {
            if (array_key_exists('filepath', $prod))
                unlink(url_to_path($prod['filepath']));
            array_splice($_SESSION['cart']['print'][$cid], $key - $dlt_count, 1);
            $dlt_count++;
        }
    }
}

function checkPrintCategory($cid) {
    checkService('print', $cid);
}

function getPrintCart() {
    if(!array_key_exists('cart', $_SESSION) || !array_key_exists('print', $_SESSION['cart']))
        return NULL;
    if (empty($_SESSION['cart']['print']) || count($_SESSION['cart']['print']) == 0)
        return NULL;
    foreach ($_SESSION['cart']['print'] as $cid=>$print_product)
        initializePrintCategory($cid);
    return count($_SESSION['cart']['print']) == 0 ? NULL : $_SESSION['cart']['print'];   
}

function addFile($cid, $filename, $filepath) {
    checkPrintCategory($cid);
    $new_file = array (
        'filename' => $filename,
        'filepath' => $filepath,
        'quantity' => -1,
        'pages' => 1,
        'pid' => 0
    );
    array_push($_SESSION['cart']['print'][$cid], $new_file);
}

function removeFile($cid, $index) {
    checkPrintCategory($cid);
    if ($index >= count($_SESSION['cart']['print'][$cid]))
			return;
    unlink(url_to_path($_SESSION['cart']['print'][$cid][$index]['filepath']));
    array_splice($_SESSION['cart']['print'][$cid], $index, 1);
}

function editFile($cid, $index, $filename, $filepath) {
    checkPrintCategory($cid);
    $new_file = array (
        'filename' => $filename,
        'filepath' => $filepath,
        'quantity' => -1,
        'pid' => 0,
        'pages' => 1
    );
    $_SESSION['cart']['print'][$cid][$index] = $new_file;
}

function getUploadNumber($cid) {
    checkPrintCategory($cid);
    return count($_SESSION['cart']['print'][$cid]);
}

function getPrintField($cid, $index, $field) {
    checkSubProduct($cid);
    if ($index >= count($_SESSION['cart']['print'][$cid]))
        return NULL;
    return $_SESSION['cart']['print'][$cid][$index][$field];
}

function getPid($cid, $index) { return getPrintField($cid, $index, 'pid'); }

function getFilename($cid, $index) { return getPrintField($cid, $index, 'filename'); }

function getFilepath($cid, $index) { return getPrintField($cid, $index, 'filepath'); }

function getPrintQuantity($cid, $index) { return getPrintField($cid, $index, 'quantity'); }

function getNumPages($cid, $index) { return getPrintField($cid, $index, 'pages'); }

function setPrintField($cid, $index, $field, $value) {
    checkSubProduct($cid);
    if ($index >= count($_SESSION['cart']['print'][$cid]))
        return;
    $_SESSION['cart']['print'][$cid][$index][$field] = $value;
}

function setPrintPid($cid, $index, $pid) { setPrintField($cid, $index, 'pid', $pid); }

function setPrintQuantity($cid, $index, $q) { setPrintField($cid, $index, 'quantity', $q); }

function setPrintPages($cid, $index, $pagesno) { setPrintField($cid, $index, 'pages', $pagesno); }

/*****     TABLES     *****/

function echoSubCartTable($sent) {
    $sub_cart = getSubCart();
    if ($sub_cart != NULL) {
        echo '<table class="cart-table">';
        echo '<thead><tr><th colspan="4">כמות</th></tr></thead>';
        echo '<tbody>';
        $index = 0;
        foreach ($sub_cart as $pid => $sub_cart_product) {
            echoSubTable($pid, $index, $sent);
            $index += getDesignNumber($pid);
            //for ($i = $index - getDesignNumber($pid); $i < $index; $i++)
                echo '<input type="hidden" name="sub_prod[]" value="' . $pid . '">';
        }
        echo '</tbody>';
        echo '</table>';
    }
}

function echoPrintCartTable($sent) {
    $print_cart = getPrintCart();
    if ($print_cart != NULL) {
        echo '<table class="cart-table">';
        echo '<thead><tr>';
        echo '<th>מחיר</th>';
        echo '<th>כמות</th>';
        echo '<th>עמודים</th>';
        echo '<th>צדים</th>';
        echo '<th>משקל</th>';
        echo '<th>נייר</th>';
        echo '<th>צבע</th>';
        echo '<th>גודל</th>';
        echo '<th></th>';
        echo '</tr></thead>';
        echo '<tbody>';
        $index = 0;
        foreach ($print_cart as $cid => $print_cart_cat) {
            echoPrint4Category($cid, TRUE, $index, $sent);
            $index += getUploadNumber($cid);
            //for ($i = $index - getUploadNumber($cid); $i < $index; $i++)
                echo '<input type="hidden" name="print_prod[]" value="' . $cid . '">';
        }
        echo '</tbody>';
        echo '</table>';
    }
}

function calcTotalPrice () {
    $total = 0;
    $sub_cart = getSubCart();
    if ($sub_cart != NULL) {
        foreach ($sub_cart as $pid => $sub_cart_product)
            $total += calcTotalSubPrice($pid);;
    }
    $print_cart = getPrintCart();
    if ($print_cart != NULL) {
        foreach ($print_cart as $cid => $print_cart_cat)
            $total += calcTotalPrintPrice($cid);
    }
    return $total;
}

function sendOrder () {
    $sub_cart = getSubCart();
    $print_cart = getPrintCart();
    if (is_null($sub_cart) && is_null($print_cart))
        return 'empty';

    date_default_timezone_set('Asia/Jerusalem');
    $now = getdate();
    $mysqldate = $now['year'] . '-' . $now['mon'] . '-' . $now['mday'];
    $total_price = calcTotalPrice();
    $order_id = add_new_order($_SESSION['name'], $_SESSION['mail'], $_SESSION['phone'], $_SESSION['address'], $mysqldate, $total_price);

    if ($sub_cart != NULL) {
        foreach ($sub_cart as $pid => $sub_cart_product) {
            $sub_product = get_sub_product($pid);
            $unit_price = $sub_product->price;
            for ($i = 0; $i < getDesignNumber($pid); $i++) {
                $file = getSublimationDesign($pid, $i);
                $quantity = getSublimationQuantity($pid, $i);
                $sub_price = $quantity * $unit_price;
                if ($quantity > 0)
                    add_new_order_detail($order_id, 1, $pid, $file, $quantity, $sub_price, 1);
            }  
        }
    }
    
    if ($print_cart != NULL) {
        foreach ($print_cart as $cid => $print_cart_cat) {
            $cat = get_print_category($cid);
            for ($i = 0; $i < getUploadNumber($cid); $i++) {
                $pid = getPid($cid, $i);
                $print_product = get_print_product($pid);
                $unit_price = $print_product->price;
                $file = getFilePath($cid, $i);
                $quantity = getPrintQuantity($cid, $i);
                $pagesno = getNumPages($cid, $i);
                $sub_price = $pagesno * $quantity * $unit_price;
                if ($quantity > 0)
                    add_new_order_detail($order_id, 0, $pid, $file, $quantity, $sub_price, $pagesno);
            }  
        }
    }

    writeInvoice($order_id);
    return $order_id;
}

?>
