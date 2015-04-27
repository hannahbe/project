<?php
    
 add_action ('admin_menu', 'register_sublimation_menu');

 function register_sublimation_menu() {
     add_menu_page('Sublimation', 'Sublimation', 'manage_options', 'view-products');
     add_submenu_page('view-products', 'Products', 'Products', 'read', 'view-products', 'view_products_function');
     add_submenu_page('view-products', 'Add Product', 'Add Product', 'read', 'add-product', 'add_product_function');
     add_submenu_page('view-products', 'Backgrounds', 'Backgrounds', 'read', 'view-backgrounds', 'view_backgrounds_function');
     add_submenu_page('view-products', 'Add Background', 'Add Background', 'read', 'add-background', 'add_background_function');
 }

 function view_products_function() {
     require get_stylesheet_directory() . '/inc/view-products.php';
 }

 function add_product_function() {
     require get_stylesheet_directory() . '/inc/new-product.php';
 }

 function view_backgrounds_function() {
     
 }

 function add_background_function() {
     require get_stylesheet_directory() . '/inc/new-background.php';
 }

?>
