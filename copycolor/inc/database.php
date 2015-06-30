<?php

function getcol_copycolor($query) {
    global $wpdb;
    return $wpdb->get_col($query);
}

function getrow_copycolor($query) {
    global $wpdb;
    return $wpdb->get_row($query);
}

function getresults_copycolor($query) {
    global $wpdb;
    return $wpdb->get_results($query);
}

/*****     WP_ABOUT     *****/

function get_main_article() {
    global $wpdb;
    return $wpdb->get_row("SELECT * FROM wp_about WHERE worker = false");
}

function edit_main_article($title, $content, $image) {
    global $wpdb;
    $article = $wpdb->get_row("SELECT * FROM wp_about WHERE worker = false");
    if ($article->image != NULL && $article->image != $image)
        unlink(url_to_path($article->image));
    $wpdb->query("UPDATE wp_about SET title = '$title', content = '$content', image = '$image' WHERE worker = false");
}

function get_all_workers() {
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM wp_about WHERE worker = true");
}

function get_worker($id) {
    global $wpdb;
    return $wpdb->get_row("SELECT * FROM wp_about WHERE id = '$id'");
}

function add_new_worker($name, $job, $photo) {
    global $wpdb;
    $wpdb->query("INSERT INTO wp_about (worker, title, content, image) VALUES (true, '$name', '$job', '$photo')");
}

function edit_worker($id, $name, $job, $photo) {
    global $wpdb;
    $worker = $wpdb->get_row("SELECT * FROM wp_about WHERE id = '$id'");
    if ($worker->image != NULL && $worker->image != $photo)
        unlink(url_to_path($worker->image));
    $wpdb->query("UPDATE wp_about SET title = '$name', content = '$job', image = '$photo' WHERE id = $id");
}

function delete_worker($id) {
    global $wpdb;
    $worker = $wpdb->get_row("SELECT * FROM wp_about WHERE id = $id");
    $image_url = $worker->image;
    unlink(url_to_path($image_url));    //delete worker's photo
    $wpdb->query("DELETE FROM wp_about WHERE id = $id");    //delete from database
}

/*****     WP_SUB_PRODUCTS     *****/

function get_all_sub_products() {
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM wp_sub_products");
}

function get_sub_product($pid) {
    global $wpdb;
    return $wpdb->get_row("SELECT * FROM wp_sub_products WHERE id = $pid");
}

function insert_sub_product($product_name, $icon_url, $image_url, $proportion, $x, $y, $width, $product_price) {
    global $wpdb;
    $wpdb->query("INSERT INTO wp_sub_products (name, icon, image, proportion, x_position, y_position, width_position, price) VALUES ('$product_name', '$icon_url', '$image_url', '$proportion', '$x', '$y', '$width', '$product_price')");
}

function remove_sub_product($pid) {
    global $wpdb;
    // first remove orders_details whiwh product is the one we want to remove
    $details = get_orders_details_by_pid(1, $pid);
    foreach ($details as $detail)
        unlink(url_to_path($detail->file));
    $wpdb->query("DELETE FROM wp_orders_details WHERE pid = $pid");

    // then delete image and icon before removinf from database
    $product = $wpdb->get_row("SELECT * FROM wp_sub_products WHERE id = $pid");
    $icon_url = $product->icon;
    if ($icon_url != NULL)
        unlink(url_to_path($icon_url));    //delete attached icone
    $image_url = $product->image;
    if ($image_url != NULL)
        unlink(url_to_path($image_url));    //delete attached image
    $wpdb->query("DELETE FROM wp_sub_products WHERE id = $pid");    //delete from database
}

/*****     WP_PRINT_CATEGORIES     *****/

function get_all_print_categories() {
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM wp_print_categories");
}

function get_print_category($id) {
    global $wpdb;
    return $wpdb->get_row("SELECT * FROM wp_print_categories WHERE id = '$id'");
}

function add_new_print_category($name, $icon, $size, $color, $paper, $weight, $sides) {
    global $wpdb;
    $wpdb->query("INSERT INTO wp_print_categories (name, icon, size, color, paper, weight, sides) VALUES ('$name', '$icon', '$size', '$color', '$paper', '$weight', '$sides')");
}

function edit_print_category($id, $name, $icon, $size, $color, $paper, $weight, $sides) {
    global $wpdb;
    $cat = $wpdb->get_row("SELECT * FROM wp_print_categories WHERE id = '$id'");
    if ($cat->icon != NULL && $cat->icon != $icon)
        unlink(url_to_path($cat->icon));
    $wpdb->query("UPDATE wp_print_categories SET name = '$name', icon = '$icon', size = '$size', color = '$color', paper = '$paper', weight = '$weight', sides = '$sides'  WHERE id = $id");
}

function delete_print_category($id) {
    global $wpdb;
    delete_print_products($id);
    $cat = $wpdb->get_row("SELECT * FROM wp_print_categories WHERE id = $id");
    unlink(url_to_path($cat->icon));                                    //delete icon
    $wpdb->query("DELETE FROM wp_print_categories WHERE id = $id");     //delete from database
}

function get_first_populate_option($id) {
    global $wpdb;
    $cat = $wpdb->get_row("SELECT * FROM wp_print_categories WHERE id = $id");
    $SIDES = 1; $WEIGHT = 2; $PAPER = 3; $COLOR = 4; $SIZE = 5;
    if ($cat->size) return $SIZE;
    if ($cat->color) return $COLOR;
    if ($cat->paper) return $PAPER;
    if ($cat->weight) return $WEIGHT;
    if ($cat->sides) return $SIDES;
    return 0;
}

function get_num_of_options($id) {
    global $wpdb;
    $cat = $wpdb->get_row("SELECT * FROM wp_print_categories WHERE id = $id");
    $count = 0;
    if ($cat->size) $count++;
    if ($cat->color) $count++;
    if ($cat->paper) $count++;
    if ($cat->weight) $count++;
    if ($cat->sides) $count++;
    if ($cat->pages) $count++;
    return $count;
}

/*****     WP_PRINT_PRODUCTS     *****/

function get_all_print_products() {
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM wp_print_products");
}

function get_print_product($id) {
    global $wpdb;
    return $wpdb->get_row("SELECT * FROM wp_print_products WHERE id = '$id'");
}

function get_print_product_category($id) {
    global $wpdb;
    $cid = $wpdb->get_col("SELECT category FROM wp_print_products WHERE id = '$id'");
    if ($cid == NULL || empty($cid))
        return NULL;
    return get_print_category($cid[0]);
}

function get_print_products($cat) {
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM wp_print_products WHERE category = '$cat'");
}

function add_new_print_product($category, $size, $color, $paper, $weight, $sides, $price) {
    global $wpdb;
    $wpdb->query("INSERT INTO wp_print_products (category, size, color, paper, weight, sides, price) VALUES ('$category', '$size', '$color', '$paper', '$weight', '$sides', '$price')");
}

function edit_print_product($id, $size, $color, $paper, $weight, $sides, $price) {
    global $wpdb;
    $wpdb->query("UPDATE wp_print_products SET size = '$size', color = '$color', paper = '$paper', weight = '$weight', sides = '$sides', price = '$price' WHERE id = $id");
}

function delete_print_product($id) {
    global $wpdb;
    // first remove orders_details whiwh product is the one we want to remove
    $details = get_orders_details_by_pid(1, $id);
    foreach ($details as $detail)
        unlink(url_to_path($detail->file));
    $wpdb->query("DELETE FROM wp_orders_details WHERE pid = $id");                                  
    $wpdb->query("DELETE FROM wp_print_products WHERE id = $id");
}

function delete_print_products($category) {
    global $wpdb;
    $ids = $wpdb->get_col("SELECT id FROM wp_print_products WHERE category = $category");
    foreach ($ids as $id) {
        $details = get_orders_details_by_pid(1, $id);
        foreach ($details as $detail)
            unlink(url_to_path($detail->file));
        $wpdb->query("DELETE FROM wp_orders_details WHERE pid = $id"); 
    }                            
    $wpdb->query("DELETE FROM wp_print_products WHERE category = $category");     //delete all products from one category
}

function get_options_from_category($cat, $option) {
    if ($option == 'size') return get_sizes_from_category($cat);
    else if ($option == 'color') return get_colors_from_category($cat);
    else if ($option == 'paper') return get_papers_from_category($cat);
    else if ($option == 'weight') return get_weights_from_category($cat);
    else if ($option == 'sides') return get_sides_from_category($cat);
    else return NULL;
}

function get_sizes_from_category($cat) {
    global $wpdb;
    return $wpdb->get_col("SELECT size FROM wp_print_products WHERE category = '$cat' GROUP BY size");
}

function get_colors_from_category($cat) {
    global $wpdb;
    return $wpdb->get_col("SELECT color FROM wp_print_products WHERE category = '$cat' GROUP BY color");
}

function get_papers_from_category($cat) {
    global $wpdb;
    return $wpdb->get_col("SELECT paper FROM wp_print_products WHERE category = '$cat' GROUP BY paper");
}

function get_weights_from_category($cat) {
    global $wpdb;
    return $wpdb->get_col("SELECT weight FROM wp_print_products WHERE category = '$cat' GROUP BY weight");
}

function get_sides_from_category($cat) {
    global $wpdb;
    return $wpdb->get_col("SELECT sides FROM wp_print_products WHERE category = '$cat' GROUP BY sides");
}

/*****     WP_BACKGROUNDS     *****/

function get_all_backgrounds() {
    global $wpdb;
    return $wpdb->get_results('SELECT * FROM wp_backgrounds');
}

function add_new_background($background_url) {
    global $wpdb;
    $wpdb->query("INSERT INTO wp_backgrounds (guid) VALUES ('$background_url')");
}

function delete_background($id) {
    global $wpdb;
    $bg = $wpdb->get_row("SELECT * FROM wp_backgrounds WHERE id = $id");
    if ($bg->guid != NULL)
        unlink(url_to_path($bg->guid));
    $wpdb->query("DELETE FROM wp_backgrounds WHERE id = $id");
}

/*****     WP_ORDERS     *****/

function get_all_orders() {
    global $wpdb;
    return $wpdb->get_results('SELECT * FROM wp_orders');
}

function add_new_order($name, $mail, $phone, $address, $date, $price) {
    global $wpdb;
    $wpdb->query("INSERT INTO wp_orders (name, mail, phone, address, date, price) VALUES ('$name', '$mail', '$phone', '$address', '$date', '$price')");
    $lastid = $wpdb->insert_id;
    return $lastid;
}

function remove_order($id) {
    unlink(url_to_path(WP_CONTENT_URL . '/uploads/invoices/invoice-number-' . $id . '.pdf'));
    global $wpdb;
    $order_details = get_order_details($id);
    foreach ($order_details as $detail)
        unlink(url_to_path($detail->file));
    $wpdb->query("DELETE FROM wp_orders_details WHERE id = $id");
    $wpdb->query("DELETE FROM wp_orders WHERE id = $id");
}

function get_order($id) {
    global $wpdb;
    return $wpdb->get_row("SELECT * FROM wp_orders WHERE id = $id");
}

function get_order_status($id) {
    global $wpdb;
    $order_details = $wpdb->get_results("SELECT * FROM wp_orders_details WHERE id = $id");
    if ($order_details == NULL || empty($order_details))
        return 'Done';
    foreach ($order_details as $detail) {
        if ($detail->todo == 1)
            return 'To do';
    }
    return 'Done';
}

function get_all_clients() {
    global $wpdb;
    return $wpdb->get_results('SELECT name, mail FROM wp_orders GROUP BY name, mail ORDER BY name ASC');
}

function get_all_dates() {
    global $wpdb;
    return $wpdb->get_col('SELECT date FROM wp_orders GROUP BY date ORDER BY date ASC');
}

function get_all_prices() {
    global $wpdb;
    return $wpdb->get_col('SELECT price FROM wp_orders GROUP BY price ORDER BY price ASC');
}

/*****     WP_ORDERS_DETAILS     *****/

function get_all_orders_details() {
    global $wpdb;
    return $wpdb->get_results('SELECT * FROM wp_orders_details GROUP BY id');
}

function get_orders_details_by_pid($sublimation, $pid) {
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM wp_orders_details WHERE sublimation = $sublimation AND pid = $pid");
}

function add_new_order_detail($id, $sublimation, $pid, $file, $quantity, $sub_price, $pages) {
    global $wpdb;
    $wpdb->query("INSERT INTO wp_orders_details (id, sublimation, pid, file, quantity, sub_price, todo, pages) VALUES ('$id', '$sublimation', '$pid', '$file', '$quantity', '$sub_price', '1', '$pages')");
}

function get_order_details($id) {
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM wp_orders_details WHERE id = $id GROUP BY sublimation, pid");
}

function get_orders_details_todo() {
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM wp_orders_details WHERE todo = 1");
}

function set_orders_details_done($file) {
    global $wpdb;
    $wpdb->query("UPDATE wp_orders_details SET todo = 0 WHERE file = '$file'");
}

function get_orders_details_todo_ids() {
    global $wpdb;
    return $wpdb->get_col("SELECT id FROM wp_orders_details WHERE todo = 1 GROUP BY id ASC");
}

function get_orders_details_todo_dates() {
    global $wpdb;
    return $wpdb->get_col("SELECT wp_orders.date FROM wp_orders INNER JOIN wp_orders_details ON wp_orders.id=wp_orders_details.id
                            WHERE wp_orders_details.todo = 1 GROUP BY date ASC");
}

function get_orders_details_todo_print_products() {
    global $wpdb;
    return $wpdb->get_results("SELECT wp_print_categories.id, wp_print_categories.name, wp_orders_details.pid FROM wp_orders_details
                                INNER JOIN wp_print_products ON wp_orders_details.pid = wp_print_products.id
                                INNER JOIN wp_print_categories ON wp_print_products.category = wp_print_categories.id
                                WHERE wp_orders_details.todo = 1 AND wp_orders_details.sublimation = 0 GROUP BY wp_print_categories.name, wp_orders_details.pid ASC");
}

function get_orders_details_todo_sub_products() {
    global $wpdb;
    return $wpdb->get_results("SELECT wp_sub_products.name, wp_orders_details.pid 
                                FROM wp_orders_details INNER JOIN wp_sub_products ON wp_orders_details.pid = wp_sub_products.id
                                WHERE wp_orders_details.todo = 1 AND wp_orders_details.sublimation = 1 GROUP BY wp_sub_products.name ASC");
}

?>
