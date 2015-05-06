<?php

require_once(get_stylesheet_directory() . '/inc/cart.php';);

class CartTest extends PHPUnit_Framework_TestCase {

    public function setUp(){ }
    public function tearDown(){ }
    
    public function testCartMenuIsValid() {
        $_SESSION['cart'][19][0]['quantity'] = 2;
        $_SESSION['cart'][19][0]['filename'] = 'mycanvas.png';
        $_SESSION['cart'][19][1]['quantity'] = 1;
        $_SESSION['cart'][19][1]['filename'] = 'anothercanvas.png';
        $this->assertTrue(show_cart_menu() != NULL);
    }
}

?>
