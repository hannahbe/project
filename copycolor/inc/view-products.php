

<?php
    
global $wpdb;
    
if ('POST' == $_SERVER['REQUEST_METHOD'])
{
    $remove = $_POST['product_to_remove'];
    if (!empty($remove)) {
        $N = count($remove);
        for ($i = 0; $i < $N; $i++) {
            $wpdb->query("DELETE FROM wp_sublimation_products WHERE id = $remove[$i]");
        }
    }
}
//else {

$all_products = $wpdb->get_results ("SELECT * FROM wp_sublimation_products");

$i = 0;

?><form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
    <table>
<?php

foreach ($all_products as $product) {
    if ($i == 0) {
        ?><tr><?php   
    }
    ?>
        <td>
            <table>
                <tr>
                    <td><?php echo $product->name; ?></td>
                </tr>
                <tr>
                    <td>
                    <?php
                    if ($product->guid != NULL) {
                    ?>
                        <img src="<?php echo $product->guid; ?>" alt="<?php echo $product->name; ?>'s image" height="200px" width="auto">
                    <?php
                    }
                    else {
                    ?>
                        No image for this product
                    <?php
                    }
                    ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" name="product_to_remove[]" value="<?php echo $product->id ?>">Select to remove the product
                    </td>
                </tr>
            </table>
        </td>
    <?php
    $i++;
    if ($i == 4) {
        ?></tr><?php
        $i = 0; 
    }
}

?>
    </table>
    <br/>
    <br/>
    <input type="submit" value="Remove products" id="remove_products">
</form>

<!--<script type="text/javascript">
    function getCheckboxesValues(){
        return [].slice.apply(document.querySelectorAll("tr input")).filter(function(c){ return c.checked; }).map(function(c){ return c.value; });
}

document.getElementById("remove_products").addEventListener("click", function(){
    console.log(getCheckboxesValues());
});
</script>-->
<?php
//}
?>
