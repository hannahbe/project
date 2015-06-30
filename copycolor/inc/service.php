<?php

//function to pass id, step and sent as parameter in url
function add_custom_query_var( $vars ) {
  $vars[] = 'id';
  $vars[] = 'step';
  $vars[] = 'sent';
  return $vars;
}
add_filter( 'query_vars', 'add_custom_query_var');

function service_enqueue() {
    global $post;    //retrieves the current menu slug
    $SUBLIMATION_SERVICE_ID = 193;
    $PRINT_SERVICE_ID = 190;
    $SERVICE_ID = 2;
    $CART_ID = 208;
    if ($post->ID == $CART_ID) {
        wp_enqueue_script('ext-jquery-script', 'http://code.jquery.com/jquery-1.10.0.min.js');
        wp_enqueue_script('service-final-step-js', get_stylesheet_directory_uri() . '/js/service-final-step.js');
    }
    if ($post->ID == $SERVICE_ID || $post->ID == $PRINT_SERVICE_ID || $post->ID == $SUBLIMATION_SERVICE_ID) {
        wp_enqueue_script('ext-jquery-script', 'http://code.jquery.com/jquery-1.10.0.min.js');
        wp_enqueue_script('service-icons-js', get_stylesheet_directory_uri() . '/js/service-icons.js');
        wp_enqueue_script('service-final-step-js', get_stylesheet_directory_uri() . '/js/service-final-step.js');
    }
    if ($post->ID == $SERVICE_ID || $post->ID == $PRINT_SERVICE_ID) {
        wp_enqueue_script('service-print-js', get_stylesheet_directory_uri() . '/js/service-print.js');
        wp_localize_script('service-print-js', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php')));
    }
    else if ($post->ID == $SUBLIMATION_SERVICE_ID) {
        wp_enqueue_script('online-designer-js', get_stylesheet_directory_uri() . '/js/online-designer.js');
        wp_enqueue_script('online-designer-classes-js', get_stylesheet_directory_uri() . '/js/online-designer-classes.js');
        wp_enqueue_script('three-min-js', get_stylesheet_directory_uri() . '/js/external-js/three.min.js');
    }
}
add_action('wp_enqueue_scripts', 'service_enqueue');

// echo the transparent black band which indicates what are the current type of service and the current step
function echoServiceBar($service) {
     echo '<div class="service-bar">';
        echo '<div class="service-bar-content">';
            echo '<h1>' . $service . '</h1>';
                $step = get_query_var('step', 1);
                echo "<img src='" . get_stylesheet_directory_uri() . "/images/step1.png' alt='step 1'";
                if ($step != 1)
                    echo " class='not-current-step'";
                echo ">";
                echo "<img src='" . get_stylesheet_directory_uri() . "/images/step2.png' alt='step 2'";
                if ($step != 2)
                    echo " class='not-current-step'";
                echo ">";
                echo "<img src='" . get_stylesheet_directory_uri() . "/images/step3.png' alt='step 3'";
                if ($step != 3)
                    echo " class='not-current-step'";
                echo ">"; 
                if ($service == 'Print') {
                    echo "<img src='" . get_stylesheet_directory_uri() . "/images/step4.png' alt='step 4'";
                    if ($step != 4)
                        echo " class='not-current-step'";
                    echo ">"; 
                }
        echo '</div><br/>'; // .service-bar-content
    echo '</div>'; // .service-bar
}

// echo the table of icons displayed in step 1 of the service pages
function echoServiceTable($services, $page_id) {
    echo '<div>';
    if (empty($services))
        echo "</br></br><h1>We're sorry, this service isn't available yet</h1></br></br>";
    else {
        $i = 0;
        $count = count($services);
        $service_bottom_row = ($count % 3 != 0 ? $count % 3 : 3);
        echo "<table id='service-table'>";
        foreach ($services as $s) {
            if ($i == 0)
                echo "<tr>";
            $service_name[$i] = $s->name;
            echoPreIcon($i, $count, $service_bottom_row); 
            echo "<td class='service-icon' colspan='2'>";
            echo "<a href='" . add_query_arg (array('step' => '2', 'id' => $s->id), get_page_link($page_id)) . "'>";
            echo "<img src='".$s->icon."' alt='".$s->name."'>";
            echo "</a>";
            echo "</td>";  
            echoPostIcon($i, $count, $service_bottom_row); 
            $i++;
            $count--;
            if  ($i == 3 || $count == 0) {
                echo "</tr>";
                echo "<tr>";
                echoPreTitle($i, $count);
                for ($j = 0; $j < $i; $j++)
                    echo "<td class='service-name' colspan='2'>".$service_name[$j]."</td>";
                echoPostTitle($i, $count);
                echo "</tr>";
                if ($count != 0)
                    echo "<tr class='spacer'><td></td></tr>";
                $i = 0;
            }
        }
        echo "</table>";
    }
    echo '</div>';
}

function echoPreIcon($index, $count, $bottom_row) {
    if ($count == 1 && $bottom_row == 1)
        echo "<td colspan='2'></td>";
    else if ($index == 0 && $bottom_row == 2 && $count < 3)
        echo "<td colspan='1'></td>";
}

function echoPostIcon($index, $count, $bottom_row) {
    if ($index == 1 && $bottom_row == 2 && $count < 3)
        echo "<td colspan='1'></td>";
    else if ($count == 1 && $bottom_row == 1)
        echo "<td colspan='2'></td>";
}

function echoPreTitle($index, $count) {
    if ($count == 0 && $index == 1)
        echo "<td colspan='2'></td>";
    else if ($count == 0 && $index == 2)
        echo "<td colspan='1'></td>";
}

function echoPostTitle($index, $count) {
    if ($count == 0 && $index == 2)
        echo "<td colspan='1'></td>";
    else if ($count == 0 && $index == 1)
        echo "<td colspan='2'></td>";
}

// echo the table of print uploads displayed in screen 2b and 3 in service print
function echoPrintTable($cid, $upload) {
    $cat = get_print_category($cid);
    $count = getUploadNumber($cid);
    if ($upload) $count++;
    $bottom_row = ($count % 3 > 0 ? $count % 3 : 3);
    echo '<table id="print-table">';
        for ($i = 0; $i < $count; $i++) { 
            if ($i % 3 == 0)
                echo '<tr>';
            echoPreIcon($i % 3, $count - $i, $bottom_row);
            echo '<td colspan="2" ';
            if ($upload)
                echo 'class="upload-icon"><input name="uploaded_file[' . $i . ']" id="uploaded_file-' . $i . '" class="hidden_input uploaded_file" 
                    type="file" accept=".jpg, .jpeg, .png, .gif, .bmp, .pdf,
                    .doc, .dot, .docx, .docm, .dotx, .dotm,
                    .ppt, .pot, .pps, .pptx, .pptm, .potx, .potm, .ppsx, .ppsm, .sldx, .sldm,
                    .xlsx, .xlsm, .xltx, .xltm, .xls, .xlt,
                    .rtf, .txt">';
            else
                echo 'title="בחר אופציות לקובץ זה" class="choose_product unchoosen" id="choose_product-' . $i . '" style="cursor: pointer;"><input type="hidden" name="upload_number"></input>';
            echo '<img src="' . $cat->icon . '" alt="' . $cat->name . '" ';
            if ($upload)
                echo 'title="העלה קובץ" class="upload_file" id="upload_file-' . $i . '" style="cursor: pointer;">';
            else
                echo '>';
            if ($upload)
                echo '<button title="מחק קובץ" name="delete" value="' . $i . '" onclick="document.getElementById(\'upload-form\').submit();"><img src="' . get_stylesheet_directory_uri() . '/images/buttons/x-button.png" alt="delete" title="Delete File"></button></td>';
            echo '</td>'; 
            echoPostIcon($i % 3, $count - $i, $bottom_row);                
            if ($i % 3 == 2 || $i == $count - 1) {
                echo '</tr>';
                echo '<tr>';
                echoPreTitle($i % 3 + 1, $count - $i - 1);
                for ($j = ($i >= 2 ? $i - ($i % 3) : 0); $j <= $i; $j++)
                    echo '<td colspan="2">' . ($j+1) . '#קובץ</td>';
                echoPostTitle($i % 3 + 1, $count - $i - 1);
                echo '</tr>';
                echo '<tr class="file-name">';
                echoPreTitle($i % 3 + 1, $count - $i - 1);
                for ($j = ($i >= 2 ? $i - ($i % 3) : 0); $j <= $i; $j++) {
                    echo '<td colspan="2"><span id="filename-' . $j . '">';
                    if ($j < getUploadNumber($cid))
                        echo getFilename($cid, $j);
                    echo '</span></td>';
                }
                echoPostTitle($i % 3 + 1, $count - $i - 1);
                echo '</tr>';
                echo '<tr class="spacer"><td></td></tr>';
            }
        }
    echo '</table>';
}

function getCountOptions ($cid) {
    $cat = get_print_category($cid);
    $count = 0;
    if ($cat->size) $count++;
    if ($cat->color) $count++;
    if ($cat->paper) $count++;
    if ($cat->weight) $count++;
    if ($cat->sides) $count++;
    return $count;
}

function echoSelectOption ($fileno, $cid, $option, $firstPopulate, $index) {
    echo '<td>';
    echo '<select name="' . $option . '-' . $fileno . '" id="dropdown-' . $fileno . '-' . $index . '" class="' . $option;
    if ($index != 1) echo ' cascade';
    echo '"';
    if (!$firstPopulate) echo ' disabled';
    echo '>';
    echo '<option disabled selected value="no value"> - בחר - </option>';
    if ($firstPopulate) {
        $options = get_options_from_category($cid, $option);
        foreach ($options as $opt) {
            if ($option == 'color' || $option == 'sizes')
                echo '<option value="' . $opt . '">' . ($opt ? "כן" : "לא") . '</option>';
            else
                echo '<option value="' . $opt . '">' . $opt . '</option>';
        }
    }
    echo '</select>';
    echo '</td>';
}

if ( is_admin() ) {
add_action( 'wp_ajax_populate_next', 'populate_next_dropdown' );
add_action( 'wp_ajax_nopriv_populate_next', 'populate_next_dropdown' );
}

function populate_next_dropdown() {
    $value = $_POST['value'];
    $cat = $_POST['cat'];
    $query = "SELECT " . $_POST['select'] . " FROM wp_print_products WHERE " . $_POST['field'] . " = '$value' AND category = '$cat' GROUP BY " . $_POST['select'];
    $options = getcol_copycolor($query);
    $response = '<option disabled selected> - בחר - </option>';
    foreach ($options as $opt) {
        if ($_POST['select'] == 'color' || $_POST['select'] == 'sides')
            $response = $response . '<option value="' . $opt . '">' . ($opt ? "כן" : "לא") . '</option>';
        else
            $response = $response . '<option value="' . $opt . '">' . $opt . '</option>';
    }
    echo $response;
	die();
}

function allOptionsFilled($count, $sides_condition, $weight_condition, $paper_condition, $color_condition, $size_condition) {
    for ($fileno = 0; $fileno < $count; $fileno ++) {
        if ($sides_condition && (!isset($_POST['sides-' . $fileno]) || $_POST['sides-' . $fileno] == "no value"))
            return FALSE;
        if ($weight_condition && (!isset($_POST['weight-' . $fileno]) || $_POST['weight-' . $fileno] == "no value"))
            return FALSE;
        if ($paper_condition && (!isset($_POST['paper-' . $fileno]) || $_POST['paper-' . $fileno] == "no value"))
            return FALSE;
        if ($color_condition && (!isset($_POST['color-' . $fileno]) || $_POST['color-' . $fileno] == "no value"))
            return FALSE;
        if ($size_condition && (!isset($_POST['size-' . $fileno]) || $_POST['size-' . $fileno] == "no value"))
            return FALSE;
    }
    return TRUE;
}

function echoSubTable($pid, $from_index, $sent) {           
    $sub_prod = get_sub_product($pid);
    for ($i = 0; $i < getDesignNumber($pid); $i++) {
        $subprice = 0;
        $quantity = getSublimationQuantity($pid, $i);
        if ($quantity > 0)
            $subprice = $quantity * $sub_prod->price;
        echo '<tr>';
        echo '<td>'; 
        echo '<span id="s_subprice-' . ($from_index + $i) . '">' . $subprice . '₪</span>';
        echo '<input type="hidden" id="s_unit_price-' . ($from_index + $i) . '" value="' . $sub_prod->price . '">';
        echo '</td>';
        if ($sent)
            echo '<td><span>' . ($quantity < 0 ? 0 : $quantity) . '</span></td>';
        else
            echo '<td><input type="number" min="0" step="1" value="' . ($quantity < 0 ? 0 : $quantity) . '" class="s_q_input" name="s_quantity[]"></td>';
        echo '<td rowspan="2" class="td-design"><img src="' . getSublimationDesign($pid, $i) . '" alt="design #' . ($i+1) . '"></td>';
        echo '<td class="td-icon"><img src="' . $sub_prod->icon . '" alt="icon"></td>';
        echo '</tr>';
        echo '<tr><td>מחיר</td><td>כמות</td><td>' . ($i+1) . '#קובץ</td></tr>';
        echo '<tr><td colspan="4" class="border"></td></tr>';
    }
}

function echoPrint4Table($cid) {
    $cols = get_num_of_options($cid) + 4;
    $cat = get_print_category($cid);
    echo '<table class="cart-table">';
    echo '<thead><tr>';
    echo '<th>מחיר</th>';
    echo '<th>כמות</th>';
    if ($cat->pages) echo '<th>עמודים</th>';
    if ($cat->sides) echo '<th>צדים</th>';
    if ($cat->weight) echo '<th>משקל</th>';
    if ($cat->paper) echo '<th>נייר</th>';
    if ($cat->color) echo '<th>צבע</th>';
    if ($cat->size) echo '<th>גודל</th>';
    echo '<th></th>';
    echo '</tr></thead>';
    echo '<tbody>';
    echoPrint4Category($cid, FALSE, 0, FALSE);
    echo '</tbody>';
    echo '</table>';
}

function echoPrint4Category($cid, $show_all_cols, $from_index, $sent) {
    $cols = $show_all_cols ? 9 : get_num_of_options($cid) + 3;
    $cat = get_print_category($cid);
    for ($i = 0; $i < getUploadNumber($cid); $i++) {
        $pid = getPid($cid, $i);
        $print_prod = get_print_product($pid);
        $subprice = 0;
        if ( getPrintQuantity($cid, $i) > 0)
            $subprice =  getNumPages($cid, $i) * getPrintQuantity($cid, $i) * $print_prod->price;
        echo '<tr>';
        echo '<td rowspan="2">'; 
        echo '<span id="p_subprice-' . ($from_index + $i) . '">' . $subprice . '₪</span>';
        echo '<input type="hidden" id="p_unit_price-' . ($from_index + $i) . '" value="' . $print_prod->price . '">';
        echo '</td>';
        if ($sent)
            echo '<td rowspan="2"><span>' . (getPrintQuantity($cid, $i) < 0 ? 0 : getPrintQuantity($cid, $i)) . '</span></td>';
        else
            echo '<td rowspan="2"><input type="number" id="p_quantity-' . ($from_index + $i) . '" min="0" step="1" value="' . (getPrintQuantity($cid, $i) < 0 ? 0 : getPrintQuantity($cid, $i)) . '" class="p_q_input" name="p_quantity[]"></td>';
        
        if ($cat->pages) {
            if ($sent)
                echo '<td rowspan="2"><span>' . getNumPages($cid, $i) . '</span></td>';
            else
                echo '<td rowspan="2"><input type="number" id="num_pages-' . ($from_index + $i) . '" min="1" step="1" value="' . getNumPages($cid, $i) . '" class="p_input" name="page[]"></td>';
        }
        else if ($show_all_cols) echo '<td rowspan="2"></td>';
        if ($cat->sides)
            echo '<td rowspan="2">' . ($print_prod->sides ? 'כן' : 'לא') . '</td>';
        else if ($show_all_cols) echo '<td rowspan="2"></td>';
        if ($cat->weight)
            echo '<td rowspan="2">' . $print_prod->weight . '</td>';
        else if ($show_all_cols) echo '<td rowspan="2"></td>';
        if ($cat->paper)
            echo '<td rowspan="2">' . $print_prod->paper . '</td>';
        else if ($show_all_cols) echo '<td rowspan="2"></td>';
        if ($cat->color)
            echo '<td rowspan="2">' . ($print_prod->color ? 'כן' : 'לא') . '</td>';
        else if ($show_all_cols) echo '<td rowspan="2"></td>';
         if ($cat->size)
            echo '<td rowspan="2">' . $print_prod->size . '</td>';
        else if ($show_all_cols) echo '<td rowspan="2"></td>';
        echo '<td class="td-icon"><img src="' . $cat->icon . '" alt="icon">' . ($i+1) . '#קובץ</td>';
        echo '<tr><td style="font-size: 1vw;">' . getFilename($cid, $i) . '</td></tr>';
        echo '<tr><td colspan="' . $cols . '" class="border"></td></tr>';
    }
}

function echoClientDetails($sent) {
    echo '<table>';
    if ($sent)
        echo '<tr><td>' . (getClientName() != NULL ? getClientName() : "שם המלא") . '</td></tr>';
    else
        echo '<tr><td><input type="text" name="complete-name" value="' . (getClientName() != NULL ? getClientName() : "שם המלא") . '" onfocus="inputStep3TextFocus(this);" onblur="inputStep3TextBlur(this, \'שם המלא\')"></td></tr>';
    echo '<tr><td class="spacer"></td></tr>';
    if ($sent)
        echo '<tr><td>' . (getClientMail() != NULL ? getClientMail() : "מייל") . '</td></tr>';
    else
        echo '<tr><td><input type="email" name="mail" value="' . (getClientMail() != NULL ? getClientMail() : "מייל") . '" onfocus="inputStep3TextFocus(this);" onblur="inputStep3TextBlur(this, \'מייל\')"></td></tr>';
    echo '<tr><td class="spacer"></td></tr>';
    if ($sent)
        echo '<tr><td>' . (getClientAddress() != NULL ? getClientAddress() : "כתובת") . '</td></tr>';
    else
        echo '<tr><td><input type="text" name="address" value="' . (getClientAddress() != NULL ? getClientAddress() : "כתובת") . '" onfocus="inputStep3TextFocus(this);" onblur="inputStep3TextBlur(this, \'כתובת\')"></td></tr>';
    echo '<tr><td class="spacer"></td></tr>';
    if ($sent)
        echo '<tr><td>' . (getClientPhone() != NULL ? getClientPhone() : "נייד") . '</td></tr>';
    else
    echo '<tr><td><input type="tel" pattern="[0]{1}[5]{1}[0-9]{8}" name="phone" value="' . (getClientPhone() != NULL ? getClientPhone() : "נייד") . '" onfocus="inputStep3TextFocus(this);" onblur="inputStep3TextBlur(this, \'נייד\')"></td></tr>';
    echo '</table>';
}

function calcTotalSubPrice($pid) {
    $sub_prod = get_sub_product($pid);
    $total = 0;
    for ($i = 0; $i < getDesignNumber($pid); $i++) {
        if (getSublimationQuantity($pid, $i) > 0)
            $total += getSublimationQuantity($pid, $i) * $sub_prod->price;
    }
    return $total;
}

function calcTotalPrintPrice($cid) {
    $cat = get_print_category($cid);
    $total = 0;
    for ($i = 0; $i < getUploadNumber($cid); $i++) {
        $pid = getPid($cid, $i);
        $print_prod = get_print_product($pid);
        if (getPrintQuantity($cid, $i) > 0)
            $total +=  getNumPages($cid, $i) * getPrintQuantity($cid, $i) * $print_prod->price;
    }
    return $total;
}
?>
