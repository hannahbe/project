<?php
    
function get_product_name($product_id) {
    global $wpdb;
    $product =  $wpdb->get_row('SELECT * FROM wp_products WHERE id = ' . $product_id);
    if (is_null($product))
        return NULL;
    return $product->name;
}

function get_product_price($product_id) {
    global $wpdb;
    $product =  $wpdb->get_row('SELECT * FROM wp_products WHERE id = ' . $product_id);
    if (is_null($product))
        return NULL;
    return $product->price;
}

?>
