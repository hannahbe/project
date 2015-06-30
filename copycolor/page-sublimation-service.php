<?php
/*
Template Name: Sublimation Service Page
*/
?>

<?php
get_header();
const PAGE_ID = 193;
?>

<div class="sublimation-service">

    <?php
    echoServiceBar("Sublimation");
    $step = get_query_var('step', 1);

    if ($step == 1) {                               // 1st step 
        $sub_services = get_all_sub_products();
        echoServiceTable($sub_services, PAGE_ID);
    }
 
    else if ($step == 2) { // 2nd sublimation-service screen

        $pid = get_query_var('id', 0);
        $sub_prod = get_sub_product($pid);

        if (is_null($sub_prod) || count($sub_prod) == 0)
            echo "<br/><br/><h1>Error, product doesn't exist</h1>";

        else {
            if ('POST' != $_SERVER['REQUEST_METHOD']) {
                initializeSubProduct($pid);
            }
            else if ('POST' == $_SERVER['REQUEST_METHOD']) {
                $canvas_url = $_POST['canvas_url'];
                if ($canvas_url == NULL || empty($canvas_url))
                    echo '<h1>canvas was not saved</h1>';
                else {
                    $canvas_url = uploadCanvasImg($canvas_url, 'orders');
                    addDesign($pid, $canvas_url);
                    if (isset($_POST['submit-and-redirect'])) {
                        die("<script>location.href = '" . add_query_arg (array('step' => '3', 'id' => $pid), get_page_link(PAGE_ID)) . "'</script>");
                    }
   
                }
            }

            $picon = $sub_prod->icon;
            echo "<input type='hidden' id='pimage' value='" . $sub_prod->image . "'>";
            echo "<input type='hidden' id='pproportion' value='" . $sub_prod->proportion . "'>";
            echo "<input type='hidden' id='px' value='" . $sub_prod->x_position . "'>";
            echo "<input type='hidden' id='py' value='" . $sub_prod->y_position . "'>";
            echo "<input type='hidden' id='pwidth' value='" . $sub_prod->width_position . "'>";

        ?>
        <div id="designer-area">

            <div id="background-screen"> <!-- choice of special backgrounds -->
                <div id="background-screen-bar">
                    <div id="background-screen-bar-left"><h2>Background</h2></div>
                    <div id="background-screen-bar-right"><a href="#"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/buttons/x-button.png" alt="close"></a></div>
                </div>
                
                <?php
                    $bgs = get_all_backgrounds();
                    if (empty($bgs))
                        echo "<h1>Sorry, service unavailable</h1>";
                    else {
                        $i = 0;
                        echo "<div id='bg-table-div'>";
                        echo "<table id='backgrounds-designer-table'>";
                        foreach ($bgs as $bg) {
                            if ($i == 0)
                                echo "<tr>";
                            echo "<td><label><input type='radio' name='bg' onclick='setDesignerBackground(this.value, true);' value='". $bg->guid . "'/><img src='" . $bg->guid . "' alt='special background'><label></td>";
                            $i++;
                            if ($i == 5) {
                                echo "</tr>";
                                $i = 0;
                            }
                        }
                        if ($i == 0)
                            echo "<tr>";
                        echo "<td><label><input type='radio' name='bg' onclick='setDesignerBackground(this.value, true);' value='transparent'/><img src='" . get_stylesheet_directory_uri() . "/images/transparent.png' alt='transparent'><label></td>";
                        echo "</tr>";
                        echo "</table>";
                        echo "</div>";
                    }
                ?>
            </div> <!-- #background-screen -->

            <div id="cover"></div>

            <div id="designer-toolbar">
                <table><tr>
                <td><img src="<?php echo get_stylesheet_directory_uri() ?>/images/toolbar/undo.png" alt="undo" title="Undo" onmouseover="" style="cursor: pointer;" onclick="undo();"></td>
                <td><img src="<?php echo get_stylesheet_directory_uri() ?>/images/toolbar/redo.png" alt="redo" title="Redo" onmouseover="" style="cursor: pointer;" onclick="redo();"></td>
                <td><img src="<?php echo get_stylesheet_directory_uri() ?>/images/toolbar/text.png" alt="add text" title="Add text" onmouseover="" style="cursor: pointer;" onclick="showHideTextForm();"></td>
                
                <td>
                    <select id="font-family" title="Select font" onmouseover="" style="cursor: pointer;" onchange="setFont(this);">
                        <option value="Aharoni,monospace" style="font-family: Aharoni,monospace;" selected="selected">Aharoni אבגד הוז</option>
                        <option value="Arial,Helvetica Neue,Helvetica,sans-serif" style="font-family: Arial,Helvetica Neue,Helvetica,sans-serif;">Arial אבגד הוז</option>
                        <option value="Arial Black,Arial Bold,Gadget,sans-serif" style="font-family: Arial Black,Arial Bold,Gadget,sans-serif;">Arial Black</option>
                        <option value="Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif" style="font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif;">Calibri</option>
                        <option value="Consolas,monaco,monospace" style="font-family: Consolas,monaco,monospace;">Consolas</option>
                        <option value="Copperplate,Copperplate Gothic Light,fantasy" style="font-family: Copperplate,Copperplate Gothic Light,fantasy;">Copperplate</option>
                        <option value="Courier New,Courier,monospace" style="font-family: Courier New,Courier,monospace;">Courier New אבגד הוז</option>
                        <option value="FrankRuehl,serif" style="font-family: FrankRuehl,serif;">FrankRuehl אבגד הוז</option>
                        <option value="Garamond,Baskerville,Baskerville Old Face,Hoefler Text,Times New Roman,serif" style="font-family: Garamond,Baskerville,Baskerville Old Face,Hoefler Text,Times New Roman,serif;">Garamond</option>
                        <option value="Georgia,Times,Times New Roman,serif" style="font-family: Georgia,Times,Times New Roman,serif;">Georgia</option>
                        <option value="Helvetica Neue,Helvetica,Arial,sans-serif" style="font-family: Helvetica Neue,Helvetica,Arial,sans-serif;">Helvetica</option>
                        <option value="Impact,Haettenschweiler,Franklin Gothic Bold,Charcoal,Helvetica Inserat,Bitstream Vera Sans Bold,Arial Black,sans serif" style="font-family: Impact,Haettenschweiler,Franklin Gothic Bold,Charcoal,Helvetica Inserat,Bitstream Vera Sans Bold,Arial Black,sans serif;">Impact</option>
                        <option value="Lucida Console,Lucida Sans Typewriter,monaco,Bitstream Vera Sans Mono,monospace" style="font-family: Lucida Console,Lucida Sans Typewriter,monaco,Bitstream Vera Sans Mono,monospace;">Lucida Console</option>
                        <option value="Miriam,sans-serif" style="font-family: Miriam,sans-serif;">Miriam אבגד הוז</option>
                        <option value="monaco,Consolas,Lucida Console,monospace" style="font-family: monaco,Consolas,Lucida Console,monospace;">monaco</option>
                        <option value="Narkisim,sans-serif" style="font-family: Narkisim,sans-serif;">Narkisim אבגד הוז</option>
                        <option value="Tahoma,Verdana,Segoe,sans-serif" style="font-family: Tahoma,Verdana,Segoe,sans-serif;">Tahoma אבגד הוז</option>
                        <option value="TimesNewRoman,Times New Roman,Times,Baskerville,serif" style="font-family: TimesNewRoman,Times New Roman,Times,Baskerville,serif;">Times New Roman אבגד הוז</option>
                        <option value="Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif" style="font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;">Trebuchet MS</option>
                        <!--<option value="'Ubuntu',sans-serif" style="font-family: 'Ubuntu',sans-serif;">Ubuntu</option>-->
                        <option value="Verdana,Geneva,sans-serif" style="font-family: Verdana,Geneva,sans-serif;">Verdana</option>
                    </select>
                </td>

                <td><input id="font-size" type="number" min="1" step="1" value="15" title="Select font size" onmouseover="" style="cursor: pointer;" onchange="setSize(this);"></td>

                <td><input type="color" value="#000000" id="txt_color" class="hidden_input" oninput="showTxtColorPreview(this)" onchange='setColorText(this, true);'/></td>
                <td><img src="<?php echo get_stylesheet_directory_uri() ?>/images/toolbar/textcolor.png" alt="text color" title="Text color" onmouseover="" style="cursor: pointer;" onclick="chooseTxtColor();"></td>

                <td><img src="<?php echo get_stylesheet_directory_uri() ?>/images/toolbar/bold.png" alt="bold" title="Bold" onmouseover="" style="cursor: pointer;" onclick="emphasize('bold');"></td>
                <td><img src="<?php echo get_stylesheet_directory_uri() ?>/images/toolbar/italic.png" alt="italic" title="Italic" onmouseover="" style="cursor: pointer;" onclick="emphasize('italic');"></td>
                <td><img src="<?php echo get_stylesheet_directory_uri() ?>/images/toolbar/underline.png" alt="underline" title="Underline" onmouseover="" style="cursor: pointer;" onclick="emphasize('underline');"></td>
                
                <td><img src="<?php echo get_stylesheet_directory_uri() ?>/images/toolbar/textleft.png" alt="text left" title="Align left" onmouseover="" style="cursor: pointer;" onclick="alignText('left');"></td>
                <td><img src="<?php echo get_stylesheet_directory_uri() ?>/images/toolbar/textright.png" alt="text right" title="Align right" onmouseover="" style="cursor: pointer;" onclick="alignText('right');"></td>
                <td><img src="<?php echo get_stylesheet_directory_uri() ?>/images/toolbar/textcenter.png" alt="text center" title="Center" onmouseover="" style="cursor: pointer;" onclick="alignText('center');"></td>
                
                <!-- image upload: input[type="file] + customized button -->
                <td><input id="image_file" class="hidden_input" type="file" accept=".jpg, .jpeg, .png, .gif, .bmp, .pdf" onchange="readImg(this);"></td>
                <td><img src="<?php echo get_stylesheet_directory_uri() ?>/images/toolbar/image.png" alt="upload image" id="image_btn" title="Upload Image" onmouseover="" style="cursor: pointer;" onclick="uploadImage();"></td>
                
                <!-- background choice: button opens overlay div -->
                <td><img src="<?php echo get_stylesheet_directory_uri() ?>/images/toolbar/bg.png" alt="choose background" title="Upload one of our backgrounds" onmouseover="" style="cursor: pointer;" onclick="location.href='#background-screen'"></td>
                
                 <!-- background color: input[type="color"] + customized button -->
                <td><input type="color" value="#ffffff" id="bg_color" class="hidden_input" oninput="showColorPreview(this);" onchange='setColorBackground(this, true);'/></td>
                <td><img src="<?php echo get_stylesheet_directory_uri() ?>/images/toolbar/bgcolor.png" alt="choose background color" title="Background color" onmouseover="" style="cursor: pointer;" onclick="chooseBgColor();"></td>
                </tr></table>
            </div> <!-- #designer-toolbar -->

            <!-- the input to add text, appears when the add text button is clicked -->
            <div class="add-edit-text" id="add-text">
                <input type="text" id="txt_input" value="Write your text here" style="color:grey;" onfocus="inputTextFocus(this)" onblur="inputTextBlur(this)">
                <input type="button" onclick="addText()" value="Add text">
            </div> <!-- #add-text -->

            <!-- the input to edit text, appears when an existing text in the design is selected -->
            <div class="add-edit-text" id="edit-text">
                <input type="text" id="txt_edit">
                <input type="button" onclick="editText()" value="Edit text">
            </div> <!-- #edit-text -->

            <div id="designer-container">

                <div class="designer-content" id="designer-sidebar">
                    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" id="save-canvas-form" onsubmit="return confirm('לא תוכל לעשות יותר שינויים בעיצוב זה');">
                        <input type="hidden" id="canvas_url" name="canvas_url"/>
                        <table>
                            <?php
                            for ($i = 0; $i < getDesignNumber($pid) + 1; $i++) { ?>
                                <tr><td>#<?php echo ($i+1); ?></td></tr>
                                <tr><td class="sidebar-icon"><img src="<?php echo $picon; ?>" alt="icon" class="icon" onclick="showMatchingCanvas('<?php echo getSublimationDesign($pid, $i); ?>');" onmouseover="" style="cursor: pointer;"></td></tr>
                                <tr class='spacer'><td></td></tr>
                            <?php } ?>
                                <tr><td><input type="image"  class="add-design" title="Add another design" src="<?php echo get_stylesheet_directory_uri(); ?>/images/buttons/add-button.png"></td></tr>
                                <tr class='spacer'><td></td></tr>
                                <tr><td></br></td></tr>
                                <tr><td><input type="submit" class="copycolor-button" name="submit-and-redirect" value="המשך"></td></tr>
                        </table>
                    </form>
                </div> <!-- #designer-sidebar -->

                <div class="designer-content" id="designer-online">
                    <canvas id="designer-canvas">>Your browser doesn't support html5 canvas</canvas>
                    <canvas id="tooltip-canvas" width=150 height=70>Your browser doesn't support html5 canvas</canvas>
                </div>

                <div class="designer-content" id="designer-preview">
                    <canvas id="preview-canvas">>Your browser doesn't support html5 canvas</canvas>
                </div>
            </div> <!-- #designer-container -->

        </div> <!-- #designer-area -->
        <?php
        }
    }
     
    else if ($step == 3) {
        $pid = get_query_var('id', 0);
        $sub_prod = get_sub_product($pid);

        if (is_null($sub_prod) || count($sub_prod) == 0)
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
                        $quantities = $_POST['s_quantity'];
                        for ($i = 0; $i < getDesignNumber($pid); $i++)
                            setDesignQuantity($pid, $i, $quantities[$i]);
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
                    <?php 
                    echo '<table class="cart-table">';
                    echo '<thead><tr><th colspan="4">כמות</th></tr></thead>';
                    echo '<tbody>'; 
                    echoSubTable($pid, 0, FALSE);
                    echo '</tbody>';
                    echo '</table>';
                    ?>
                </div> <!-- #choose-quantity -->

                <div id="client-data" class="order-content">
                    <?php echoClientDetails(FALSE); ?>
                </div> <!-- #client-data -->

                </form>

            </div> <!-- #order-container -->

            <div id="order-price">
                <h5><span id="total_price"><?php echo calcTotalSubPrice($pid); ?>₪</span></h5>
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
