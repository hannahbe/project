<?php
/*
Template Name: Cart
*/
?>


<?php

function show_cart_menu() {
    
    if (session_status() == PHP_SESSION_NONE || session_id() == '' || !is_array($_SESSION['cart']))
        return NULL;

    $menu = '<ul class="cart_menu"><li>My cart';

    $menu = $menu . '<ul class="cart_menu_products">';

    foreach ($_SESSION['cart'] as $pid=>$pdetail) {

        $pname = get_product_name($pid);
        if (is_null($pname))
            return NULL;
        $menu = $menu . '<li>' . $pid . ' ' . $pname;
        $menu = $menu . '<ul class="cart_menu_product_orders>';

        for ($i = 0; $i < count($_SESSION['cart'][$pid]); $i++) {
            print_r ($_SESSION['cart'][$pid][$i]);
            //$current_order = $_SESSION['cart'][$pid][$i];
            //$menu = $menu . '<li>pid: ' . $pid . ' i:' . $i . ' ' . $current_order['filename'] . ', quantity: ' . $current_order['quantity'] . '</li>';
        }

        /*foreach ($_SESSION['cart'][$pid] as $porder) {
            
            $menu = $menu . '<li>' . $porder['filename'] . ', quantity: ' . $porder['quantity'] . '</li>';

        }*/

        $menu = $menu . '</ul></li>';

    }


    $menu = $menu . '</ul>';

    $menu = $menu . '</li></ul>';

    return $menu;
}


?>
