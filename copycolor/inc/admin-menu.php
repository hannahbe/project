<?php
    
/***** RESTRICTIONS *****/

// remove tags menu and metaboxes
function remove_tags() {
    remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' );   //Tags Menu
    remove_meta_box( 'tagsdiv-post_tag','post','normal' );                  // Tags Metabox
}
add_action( 'admin_menu', 'remove_tags', 999 );

// remove slider's submenus htat are useless to Copy Color's workers
function remove_slider_submenus() {
    remove_submenu_page( 'sliders_wds', 'featured_plugins_wds' );   //Featured Plugins
    remove_submenu_page( 'sliders_wds', 'featured_themes_wds' );    //Featured Themes
    remove_submenu_page( 'sliders_wds', 'uninstall_wds' );          //Uninstall
}
add_action( 'admin_menu', 'remove_slider_submenus',10 );

// hide the delete button completely and the delete link only for the row that matches the slider number 1 si it is impossible to delete it as it is used on the home page
function hide_slider_delete(){
    ?>
    <style type="text/css">
        input.button-secondary[value="Delete"] {
            display: none;
        }
        #tr_1 td:last-child a{
            display:none;
        }
    </style>
    <?php
}
add_action( "admin_head", "hide_slider_delete");

// we hide these categories from the category menu so that the user cannot delete them or change their slugs, as we need them in gallery and catalog pages
function hide_the_category(){
    /* the categories ids:
     * catalog = 22
     * catalog-design = 23
     * catalog-print = 24
     * catalog-sublimation = 25
     * gallery = 14
     * gallery-design = 15
     * gallery-print = 16
     * gallery-sublimation = 17
     */
    ?>
    <style type="text/css">
        #tag-22, #tag-23, #tag-24, #tag-25, #tag-14, #tag-15, #tag-16, #tag-17{
            display:none;
        }
    </style>
    <?php
}
add_action( "admin_head", "hide_the_category" );

// we prevent the user from deleting those pages as they are essential to the website
function restrict_page_deletion($page_ID){
    $P_ABOUT = 74;
    $P_CART = 208;
    $P_CATALOG = 47;
    $P_CATALOG_DESIGN = 130;
    $P_CATALOG_PRINT = 128;
    $P_CATALOG_SUBLIMATION = 132;
    $P_CONTACT = 76;
    $P_GALLERY = 56;
    $P_SERVICE = 2;
    $P_SERVICE_PRINT = 190;
    $P_SERVICE_SUBLIMATION = 193;
    $restricted_pages = array($P_ABOUT, $P_CART, $P_CATALOG, $P_CATALOG_DESIGN, $P_CATALOG_PRINT, $P_CATALOG_SUBLIMATION, $P_CONTACT, $P_GALLERY, $P_SERVICE, $P_SERVICE_PRINT, $P_SERVICE_SUBLIMATION);
    if(in_array($page_ID, $restricted_pages)){
        wp_die ("You are not authorized to delete this page.");
        exit;
    }
}
add_action('wp_trash_post', 'restrict_page_deletion');
add_action('before_delete_post', 'restrict_page_deletion');

/***** END OF RESTRICTIONS *****/

/*******************************************************************************************************/

function copycolor_admin_enqueue()
{
    global $plugin_page;    //retrieves the current menu slug
    if ($plugin_page == 'view-main-article' || $plugin_page == 'view-workers'
    || $plugin_page == 'view-backgrounds' || $plugin_page == 'view-sub-products' || $plugin_page == 'add-sub-product'
    || $plugin_page == 'view-print-categories' || $plugin_page == 'view-print-products' || $plugin_page == 'add-print-product'
    || $plugin_page == 'view-all-orders' || $plugin_page == 'view-todo-orders')
        wp_enqueue_script('ext-jquery-script', 'http://code.jquery.com/jquery-1.10.0.min.js');
    if ($plugin_page == 'add-sub-product')
        wp_enqueue_script('add-sub-js', get_stylesheet_directory_uri() . '/js/add-sub.js');
}
add_action('admin_enqueue_scripts', 'copycolor_admin_enqueue');

function echoForm ($id, $hidden_name) {
    $form = '<form action="' . $_SERVER['REQUEST_URI'] . '" method="POST" enctype="multipart/form-data">';
    $form = $form . '<input type="submit" name="action" value="Edit"/>';
    $form = $form . '<input type="submit" name="action" value="Delete"/>';
    $form = $form . '<input type="hidden" name="' . $hidden_name . '" value="' . $id . '"/>';
    $form = $form . '</form>';
    return $form;
}
    
/**********     ABOUT MENU     **********/

add_action ('admin_menu', 'register_about_menu');

function register_about_menu() {
     add_menu_page('About', 'About', 'manage_options', 'view-main-article');
     add_submenu_page('view-main-article', 'Main article', 'Main article', 'read', 'view-main-article', 'view_main_article');
     add_submenu_page('view-main-article', 'Workers', 'Workers', 'read', 'view-workers', 'view_workers');
}

function view_main_article() {

    $main_article = get_main_article();

    if ('POST' == $_SERVER['REQUEST_METHOD'] && $_POST['action'] == 'edit1') {

        ?> <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data"> <?php
                                                                                                          
        echo '<table style="width: 100%;">';
            echo "<tr>";
                echo '<td style="width: 30%;">Title : </td>';
                echo '<td style="width: 70%;"><input type="text" id="article-title" name="article-title" value="' . $main_article->title . '"/></td>';
            echo "</tr>";
            echo '<tr>';
                echo '<td>Content : </td>';
                echo '<td><textarea id="article-content" name="article-content" rows="10" style="width: 95%;">' . $main_article->content . '</textarea></td>';
            echo "</tr>";
            echo "<tr>";
                echo '<td><input type="file" id="article-url" name="article-url" accept=".jpg, .jpeg, .png, .gif, .bmp, .pdf"/></td>';
                echo '<td><img src="';
                if ($main_article->image != NULL)
                    echo $main_article->image;
                else
                    echo '#';
                echo '" id="article-image" alt="article illustration" width="500" height="auto"/></td>';
            echo "</tr>";
        echo "</table>";
        echo "";

        ?>
            <input type="submit" id="edit-article" value="Edit"/>
            <input type="hidden" name="action" value="edit2"/>
        </form>
        <?php
    }

    else {

        if ('POST' == $_SERVER['REQUEST_METHOD'] && $_POST['action'] == 'edit2') {
            $title = $_POST["article-title"];
            $content = $_POST["article-content"];
            $image_url = $_FILES["article-url"];
            if ($image_url == NULL || empty($image_url) || $image_url['size'] == 0)
                $image_url = $main_article->image;

            if ($title == NULL || empty($title) || $content == NULL || empty($content) || $image_url == NULL || empty($image_url))
                echo '<h1>Changes have not been saved, all fields must be filled</h1></br></br>';

                else {
                    if ($image_url != $main_article->image) {
                        $image_url = uploadImage($image_url);
                    }
                    edit_main_article ($title, $content, $image_url);
                    $main_article = get_main_article();
                }
        }
        

        echo "<h1>" . $main_article->title . "</h1>";
        echo "<p>" . $main_article->content . "</p>";
        if ($main_article->image != NULL)
            echo '<img src="' . $main_article->image . '" alt="article illustration" width="500" height="auto"></img>';
       
        ?>
        </br></br>
        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <input type="submit" value="Edit"/>
            <input type="hidden" name="action" value="edit1"/>
        </form>
        <?php
    }
    
    ?>
    <script type="text/javascript">
    $(function () {
        $("#article-url").change(function () {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#article-image')
                    .attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    </script>
    <?php
}

function view_workers() { 

    if ('POST' == $_SERVER['REQUEST_METHOD'] && ($_POST['action'] == 'New worker' || $_POST['action'] == 'Edit')) {

        if ($_POST['action'] == 'Edit')
             $worker = get_worker($_POST['worker-id']);

        ?> <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data"> <?php
            echo '<table>';
                echo '<tr>';
                    echo '<td>Name</td>';
                    echo '<td><input type="text" name="name"';
                    if ($_POST['action'] == 'Edit')
                        echo ' value="' . $worker->title . '"';
                    echo '/></td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<td>Job</td>';
                    echo '<td><input type="text" name="job"';
                    if ($_POST['action'] == 'Edit')
                        echo ' value="' . $worker->content . '"';
                    echo '/></td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<td><input type="file" name="new-worker-url" id="new-worker-url" accept=".jpg, .jpeg, .png, .gif, .bmp, .pdf"/></td>';
                    echo '<td><img src="';
                    if ($_POST['action'] == 'Edit')
                        echo $worker->image;
                    else echo '#';
                    echo '" alt="photo of the worker" id="new-worker-photo" width="500" height="auto"/></td>';
                echo '</tr>';
            echo '</table>';
        ?>
            </br></br>
            <input type="submit" name="action" value="Add" <?php if ($_POST['action'] == 'Edit') echo 'hidden'; ?>/>
            <input type="submit" name="action" value="Edit worker" <?php if ($_POST['action'] == 'New worker') echo 'hidden'; ?>/>
            <?php if ($_POST['action'] == 'Edit') { ?>
                <input type="hidden" name="worker-id" value="<?php echo $worker->id; ?>"/>
            <?php } ?>
            <input type="hidden" name="cropped-img" id="cropped-img" value=""/>
        </form>
        <?php 
    }

    else {

        if ('POST' == $_SERVER['REQUEST_METHOD'] && $_POST['action'] == 'Delete')
            delete_worker($_POST['worker-id']);

        if ('POST' == $_SERVER['REQUEST_METHOD'] && ($_POST['action'] == 'Add' || $_POST['action'] == 'Edit worker')) {

            if ($_POST['action'] == 'Edit worker')
                $worker = get_worker($_POST['worker-id']);

            $name = $_POST["name"];
            $job = $_POST["job"];

            $cropped = $_POST['cropped-img'];
            if ($cropped == NULL || empty($cropped)) {
                if ($_POST['action'] == 'Add') {
                    echo '<h1>Worker was not added, all fields must be filled</h1></br></br>';
                    return;
                }
                else
                    $cropped = $worker->image;
            }
            if ($name == NULL || empty($name) || $job == NULL || empty($job))
                echo '<h1>Worker was not added/edited, all fields must be filled</h1></br></br>';

            else {
                if ($_POST['action'] == 'Add' && $cropped != NULL) {
                    $cropped = uploadCanvasImg($cropped, 'images');
                    add_new_worker($name, $job, $cropped);
                }
                    
                else if ($_POST['action'] == 'Edit worker') {
                    if ($cropped != $worker->image)
                        $cropped = uploadCanvasImg($cropped, 'images');
                    edit_worker ($worker->id, $name, $job, $cropped);
                }
            }
        }

        $workers = get_all_workers();

        if ($workers == NULL || empty($workers))
            echo "<h1>No workers yet</h1>";

        else {
            echo '</br><table style="width: 100%; border: 2px solid black; text-align: center;">';
            echo '<thead style="background-color: #f3eeea; font-size: 150%;">';
                echo '<th>Name</th>';
                echo '<th>Job</th>';
                echo '<th>Photo</th>';
                echo '<th>Edit - Delete</th>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($workers as $worker) {
                echo '<tr>';
                    echo '<td>' . $worker->title . '</td>';
                    echo '<td>' . $worker->content . '</td>';
                    echo '<td><img src="' . $worker->image . '" alt="photo" width="100"/></td>';
                    echo '<td>' . echoForm($worker->id, "worker-id") . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }

        ?>
        </br></br>
        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <input type="submit" name="action" value="New worker"/>
        </form>
        <?php 
    }

    ?>
    <script type="text/javascript">
    $(function () {
        $("#new-worker-url").change(function () {
            if (this.files && this.files[0]) {
                var canvas = document.createElement('canvas');
                var context = canvas.getContext("2d");
                var reader = new FileReader();
                var photo = new Image();

                photo.onload = function () {
                    var dim = (this.width > this.height ? this.height : this.width);
                    canvas.width = dim;
                    canvas.height = dim;
                    var sourceX = (this.width > this.height ? (this.width - this.height) / 2 : 0);
                    var sourceY = (this.width > this.height ? 0 : (this.height - this.width) / 2);
                    context.drawImage(photo, sourceX, sourceY, dim, dim, 0, 0, dim, dim);
                    $('#new-worker-photo').attr('src', canvas.toDataURL());
                    $('#cropped-img').val(canvas.toDataURL().replace(/^data:image\/(png|jpg);base64,/, ""));
                }

                reader.onload = function (e) {
                    photo.src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    </script>
    <?php
}

/**********     END OF ABOUT MENU     **********/

/*******************************************************************************************************/

/**********     SUBLIMATION MENU     **********/

add_action ('admin_menu', 'register_sublimation_menu');

function register_sublimation_menu() {
     add_menu_page('Sublimation', 'Sublimation', 'manage_options', 'view-backgrounds');
     add_submenu_page('view-backgrounds', 'Backgrounds', 'Backgrounds', 'read', 'view-backgrounds', 'view_backgrounds');
     add_submenu_page('view-backgrounds', 'Products', 'Products', 'read', 'view-sub-products', 'view_sub_products');
     add_submenu_page('view-backgrounds', 'Add Product', 'Add Product', 'read', 'add-sub-product', 'add_sub_product');
}

function view_backgrounds() {

    if ('POST' == $_SERVER['REQUEST_METHOD'] && $_POST['action'] == 'Add new background') { ?>
        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <br>
            <h2>Select a background: </h2>
            <input type="file" name="new_background" id="new_background" accept=".jpg, .jpeg, .png, .gif, .bmp, .pdf"/>
            <br><br>
            <input type="submit" id="add_background" name="action" value="Add" disabled/>
        </form>
        <script type="text/javascript">
            document.getElementById("new_background").onchange = function() {
                if(this.value)
                    document.getElementById("add_background").disabled = false; 
            }
        </script>
        <?php return;
    }

    if ('POST' == $_SERVER['REQUEST_METHOD'] && $_POST['action'] == 'Add') {
        $bg_url = uploadBackground($_FILES["new_background"]);  //upload background to backgrounds directory
        add_new_background($bg_url);                            //add to database
    }

    if ('POST' == $_SERVER['REQUEST_METHOD'] && $_POST['action'] == 'Delete') {
        if (!empty($_POST['remove-bg'])) {
            foreach ($_POST['remove-bg'] as $selected)
                delete_background($selected);
        }
    }

    $bgs = get_all_backgrounds();
    if (empty($bgs))
        echo '</br><h1>No backgrounds to show yet</h1>';

    else {
        
        echo '<form action="' . $_SERVER['REQUEST_URI'] . '" method="POST">';
        echo '</br><table style="width: 100%; border: 2px solid black; text-align: center;">';
        echo '<thead style="background-color: #f2e6ac; font-size: 150%;">';
        echo '<th colspan="3">Backgrounds</th>';
        echo '</thead>';
        echo '<tbody>';

        $i = 0;
        foreach ($bgs as $bg) {
            if ($i == 0)
                echo '<tr>';
            echo '<td><img src="' . $bg->guid . '" width="150px" alt="background"></br>
            <input type="checkbox" name="remove-bg[]" class="remove-bg" value="' . $bg->id . '"></input>Select to delete</td>';
            $i++;
            if ($i == 3) {
                echo '</tr>';
                $i = 0;
            }
        }

        echo '</tbody>';
        echo '</table></br>';
        echo '<input type="submit" id="dlt-bg" name="action" value="Delete" disabled="disabled">';
        echo '</form>';
        echo '</br><form action="' . $_SERVER['REQUEST_URI'] . '" method="POST">
        <input type="submit" name="action" value="Add new background"></form>';

        ?> <script type="text/javascript">
            $(function() {
                $(".remove-bg").click(function(){
                    $('#dlt-bg').prop('disabled',$('input.remove-bg:checked').length == 0);
                });
            });
        </script> <?php 
    }
}

function view_sub_products() {

    if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['delete-prod'])) {
        if (!empty($_POST['delete-prod'])) {
            foreach($_POST['delete-prod'] as $selected)
                remove_sub_product($selected);
        }
    }

    $subs = get_all_sub_products();
    if ($subs == NULL || empty($subs))
        echo "<h1>No products to show</h1>";

    else {

        echo '<form action="' . $_SERVER['REQUEST_URI'] . '" method="POST">';
        echo '</br><table style="width: 100%; border: 2px solid black; text-align: center;">';

        echo '<thead style="background-color: #f2e6ac; font-size:150%;">';
            echo '<th>ID</th>';
            echo '<th>Icon</th>';
            echo '<th>Name</th>';
            echo '<th>Price</th>';
            echo '<th>Photo</th>';
            echo '<th>Delete</th>';
        echo '</thead>';

        echo '<tbody>';
        foreach ($subs as $sub) {
            echo '<tr>';
                echo '<td>' . $sub->id . '</td>';
                echo '<td><img src="' . $sub->icon . '" alt="icon" width="100px"></td>';
                echo '<td>' . $sub->name . '</td>';
                echo '<td>' . $sub->price . '</td>';
                echo '<td><img src="' . $sub->image . '" alt="photo" width="150px"></td>';
                echo '<td><input type="checkbox" class="dlt-prod" name="delete-prod[]" value="' . $sub->id . '">Select to delete</input></td>'; 
            echo '</tr>';   
        }
        echo '</tbody>';
        echo '</table></br>';
        echo '<input type="submit" value="Delete" disabled="disabled"/>';
        echo '</form>';
    }

    ?>
    <script type="text/javascript">
        $(function() {
            $(".dlt-prod").click(function(){
                $('input[type="submit"]').prop('disabled',$('input.dlt-prod:checked').length == 0);
            });
        });
    </script>
    <?php
}

function add_sub_product() {

    if ('POST' == $_SERVER['REQUEST_METHOD']) { //if the admin already filled the form

        $pname = $_POST['product_name'];
        $pprice = $_POST['product_price'];
        $pheight = 0;
        $pwidth = 0;
        $pheight = $_POST['product_height'];
        $pwidth = $_POST['product_width'];
        $px = isset($_POST['isMug']) ? 0 : $_POST['x_pos'];
        $py = isset($_POST['isMug']) ? 0 : $_POST['y_pos'];
        $pwidth_pos = isset($_POST['isMug']) ? 0 : $_POST['width_pos'];

        if (empty($pname) || empty($pprice) || !is_numeric($pprice))
            exit ("Unvalid field(s)");
        else if ($isSublimationProduct && (empty($pheight) || !is_numeric($pheight) || empty($pwidth) || !is_numeric($pwidth)))
            exit ("Unvalid field(s)");
        else if ($isSublimationProduct && isset($_POST['isMug']) && $_POST['isMug'] == 'Yes' && (empty($px) || !is_numeric($px) || empty($py) || !is_numeric($py) || empty($pwidth_pos) || !is_numeric($pwidth_pos)))
            exit ("Unvalid field(s)");
        $proportion = $pwidth / $pheight;

        if($_FILES['icon_file']['error'] != 0)              //if the user didn't upload an icon for the product
            $icon_url = NULL;
        else  
            $icon_url = uploadIcon($_FILES['icon_file']);   //image upload
        if (is_null($icon_url))
            exit("Couldn't upload the icon");
        
        if($_FILES['image_file']['error'] != 0)             //if the user didn't upload an image for the product 
            $image_url = NULL;
        else  
            $image_url = uploadImage($_FILES['image_file']);//image upload
        if (is_null($image_url))
            exit("Couldn't upload the image");

        insert_sub_product($pname, $icon_url, $image_url, $proportion, $px, $py, $pwidth_pos, $pprice);

        ?>
        <h2>You have successfully added <?php echo $pname ?> to your <? echo $typeOfProduct ?> products</h2>
        <br>
        <img src="<?php echo $image_url ?>" alt="Your new product" width="300px" height="auto">
        <br/><br/>
        <a href="admin.php?page=add-sub-product"><button>Add another sublimation product</button></a> <!--sublimation product-->
        <?php
    }

    
    else { //if the admin didn't filled the form yet ?>

        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <br>
            Product Name:<br/>
            <input type="text" class="required" id="product_name" name="product_name"/></br>
            Product price:<br/>
            <input type="number" class="required" id="product_price" step="0.01" min="0" name="product_price"/></br></br>
            Product icon:</br>
            <input type="file" id="icon_file" name="icon_file" accept="image/*" class="required"/></br></br>
            <img id="icon_file_preview" src="#" alt="product icon" hidden/><br/><br/>
            Check if the product is a mug <input type="checkbox" id="isMug" name="isMug"></br></br>
            Sublimation area's width in centimeter:</br>
            <input type="number" class="required dimension" id="product_width" step="0.01" min="0.01" name="product_width"/></br>
            Sublimation area's height in centimeter:</br>
            <input type="number" class="required dimension" id="product_height" step="0.01" min="0.01" name="product_height"/></br></br>
            <input type="hidden" id="x_pos" name="x_pos" value=""> <!--value="set_x()"-->
            <input type="hidden" id="y_pos" name="y_pos" value=""> <!--value="set_y()"-->
            <input type="hidden" id="width_pos" name="width_pos" value=""> <!--value="set_width()"-->
            Product image:</br>
            <input type="file" id="image_file" name="image_file" accept="image/*" class="required"/>
            <br>

            <canvas id="myCanvas" width="300" height="300" hidden>Your browser doesn't support html5 canvas</canvas>

            </br></br>
            <input type="submit" id="add_product" value="Add product" disabled/>
        </form>

    <?php
    }
}



/**********     END OF SUBLIMATION MENU     **********/

/*******************************************************************************************************/

/**********     PRINT MENU     **********/

add_action ('admin_menu', 'register_print_menu');

function register_print_menu() {
     add_menu_page('Print', 'Print', 'manage_options', 'view-print-categories');
     add_submenu_page('view-print-categories', 'Categories', 'Categories', 'read', 'view-print-categories', 'view_print_categories');
     add_submenu_page('view-print-categories', 'Products', 'Products', 'read', 'view-print-products', 'view_print_products');
     add_submenu_page('view-print-categories', 'Add Product', 'Add Product', 'read', 'add-print-product', 'add_print_product');
}

function view_print_categories() { 

    if ('POST' == $_SERVER['REQUEST_METHOD'] && ($_POST['action'] == 'New category' || $_POST['action'] == 'Edit')) {

        if ($_POST['action'] == 'Edit')
             $cat = get_print_category($_POST['cat-id']);

        ?> <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data"> <?php
            echo '<table>';
                echo '<tr>';
                    echo '<td>Name</td>';
                    echo '<td><input type="text" name="name"';
                    if ($_POST['action'] == 'Edit')
                        echo ' value="' . $cat->name . '"';
                    echo '/></td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<td><input type="file" name="new-cat-url" id="new-cat-url" accept=".jpg, .jpeg, .png, .gif, .bmp, .pdf"/></td>';
                    echo '<td><img src="';
                    if ($_POST['action'] == 'Edit')
                        echo $cat->icon;
                    else echo '#';
                    echo '" alt="category icon" id="new-cat-icon" width="150" height="auto"/></td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<td colspan="2"><input type="checkbox" name="size"';
                    if ($_POST['action'] == 'Edit' && $cat->size)
                        echo ' checked';
                    echo '>Size option</td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<td colspan="2"><input type="checkbox" name="color"';
                    if ($_POST['action'] == 'Edit' && $cat->color)
                        echo ' checked';
                    echo '>Color option</td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<td colspan="2"><input type="checkbox" name="paper"';
                    if ($_POST['action'] == 'Edit' && $cat->paper)
                        echo ' checked';
                    echo '>Paper type option</td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<td colspan="2"><input type="checkbox" name="weight"';
                    if ($_POST['action'] == 'Edit' && $cat->weight)
                        echo ' checked';
                    echo '>Weight option</td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<td colspan="2"><input type="checkbox" name="sides"';
                    if ($_POST['action'] == 'Edit' && $cat->sides)
                        echo ' checked';
                    echo '>Two-sided option</td>';
                echo '</tr>';
                echo '<tr>';
                    echo '<td colspan="2"><input type="checkbox" name="pages"';
                    if ($_POST['action'] == 'Edit' && $cat->pages)
                        echo ' checked';
                    echo '>Number of pages option</td>';
                echo '</tr>';
            echo '</table>';
        ?>
            </br></br>
            <input type="submit" name="action" value="Add" <?php if ($_POST['action'] == 'Edit') echo 'hidden'; ?>/>
            <input type="submit" name="action" value="Edit category" <?php if ($_POST['action'] == 'New category') echo 'hidden'; ?>/>
            <?php if ($_POST['action'] == 'Edit') { ?>
                <input type="hidden" name="cat-id" value="<?php echo $cat->id; ?>"/>
            <?php } ?>
        </form>
        <?php 
    }

    else {

        if ('POST' == $_SERVER['REQUEST_METHOD'] && $_POST['action'] == 'Delete')
            delete_print_category($_POST['cat-id']);

        if ('POST' == $_SERVER['REQUEST_METHOD'] && ($_POST['action'] == 'Add' || $_POST['action'] == 'Edit category')) {

            if ($_POST['action'] == 'Edit category')
                $cat = get_print_category($_POST['cat-id']);

            $name = $_POST["name"];
            $icon = $_FILES["new-cat-url"];
            $size = isset($_POST['size']);
            $color = isset($_POST['color']);
            $paper = isset($_POST['paper']);
            $weight = isset($_POST['weight']);
            $sides = isset($_POST['sides']);
            $pages = isset($_POST['pages']);

            if ($icon['error'] != 0) {
                if ($_POST['action'] == 'Add') {
                    echo '<h1>Category was not added, all fields must be filled</h1></br></br>';
                    return;
                }
                else
                    $icon = $cat->icon;
            }
            if ($name == NULL || empty($name))
                echo '<h1>Category was not added/edited, all fields must be filled</h1></br></br>';

            else {
                if ($_POST['action'] == 'Add' && $icon != NULL) {
                    $icon = uploadIcon($icon);
                    add_new_print_category($name, $icon, $size, $color, $paper, $weight, $sides, $pages);
                }
                    
                else if ($_POST['action'] == 'Edit category') {
                    if ($icon != $cat->icon)
                        $icon = uploadIcon($icon);
                    edit_print_category ($cat->id, $name, $icon, $size, $color, $paper, $weight, $sides, $pages);
                }
            }
        }

        $cats = get_all_print_categories();

        if ($cats == NULL || empty($cats))
            echo "<h1>No categories yet</h1>";

        else {
            echo '</br><table style="width: 100%; border: 2px solid black; text-align: center; table-layout: fixed;">';
            echo '<thead style="background-color: #e2b3e9; font-size:150%;">';
            echo '<th>Name</th>';
            echo '<th>Icon</th>';
            echo '<th>Size option</th>';
            echo '<th>Color option</th>';
            echo '<th>Paper option</th>';
            echo '<th>Weight option</th>';
            echo '<th>Two-sided option</th>';
            echo '<th>Number of pages option</th>';
            echo '<th>Edit - Delete</th>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($cats as $cat) {
                echo '<tr>';
                    echo '<td>' . $cat->name . '</td>';
                    echo '<td><img src="' . $cat->icon . '" alt="icon" width="100"/></td>';
                    echo '<td>' . ($cat->size ? "yes":"no") . '</td>';
                    echo '<td>' . ($cat->color ? "yes":"no") . '</td>';
                    echo '<td>' . ($cat->paper ? "yes":"no") . '</td>';
                    echo '<td>' . ($cat->weight ? "yes":"no") . '</td>';
                    echo '<td>' . ($cat->sides ? "yes":"no") . '</td>';
                    echo '<td>' . ($cat->pages ? "yes":"no") . '</td>';
                    echo '<td>' . echoForm($cat->id, "cat-id") . '</td>'; 
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }

        ?>
        </br></br>
        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <input type="submit" name="action" value="New category"/>
        </form>
        <?php 
    }

    ?>
    <script type="text/javascript">
    $(function () {
        $("#new-cat-url").change(function () {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                var icon = new Image();
                icon.onload = function () {
                    $('#new-cat-icon').attr('src', icon.src);
                };
                reader.onload = function (e) {
                    icon.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    </script>
    <?php
}

function view_print_products() {

    if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['delete-prod'])) {
        if (!empty($_POST['delete-prod'])) {
            foreach($_POST['delete-prod'] as $selected)
                delete_print_product($selected);
        }
    }

    $all_prods = get_all_print_products();
    if ($all_prods == NULL || empty($all_prods))
        echo "<h1>No products to show</h1>";

    else {
        $cats = get_all_print_categories();
        echo '<form action="' . $_SERVER['REQUEST_URI'] . '" method="POST">';
        foreach ($cats as $cat) {

            $prods = get_print_products($cat->id);
            if ($prods != NULL && !empty($prods)) {

            echo '</br><table style="width: 100%; border: 2px solid black; text-align: center; table-layout: fixed;">';
            echo '<thead style="background-color: #e2b3e9; font-size:150%;">';
            echo '<th>' . $cat->name . '</th>';
            echo '<th>ID</th>';
            if ($cat->size)
                echo '<th>Size</th>';
            if ($cat->color)
                echo '<th>Color</th>';
            if ($cat->paper)
                echo '<th>Paper</th>';
            if ($cat->weight)
                echo '<th>Weight</th>';
            if ($cat->sides)
                echo '<th>Two-sided</th>';
            echo '<th>Price</th>';
            echo '<th>Delete</th>';
            echo '</thead>';

            echo '<tbody>';
            echo '<tr>';
            echo '<td rowspan="' . count($prods) . '"><img src="' . $cat->icon . '" alt="icon" width="100"/></td>';

            foreach ($prods as $prod) {
                echo '<td>' . $prod->id . '</td>';
                if ($cat->size)
                    echo '<td>' . $prod->size . '</td>';
                if ($cat->color)
                    echo '<td>' . ($prod->color ? "yes" : "no") . '</td>';
                if ($cat->paper)
                    echo '<td>' . $prod->paper . '</td>';
                if ($cat->weight)
                    echo '<td>' . $prod->weight . '</td>';
                if ($cat->sides)
                    echo '<td>' . ($prod->sides ? "yes" : "no") . '</td>';
                echo '<td>' . $prod->price . '</td>';
                echo '<td><input type="checkbox" class="dlt-prod" name="delete-prod[]" value="' . $prod->id . '">Select to delete</input></td>'; 
                echo '</tr>';
                echo '<tr>';
            }
            echo '</tr>';
            echo '</tbody>';
            echo '</table></br>';
            }
        }
        echo '<input type="submit" value="Delete"  disabled="disabled"/>';
        echo '</form>';
    }

    ?>
    <script type="text/javascript">
        $(function() {
            $(".dlt-prod").click(function(){
                $('input[type="submit"]').prop('disabled',$('input.dlt-prod:checked').length == 0);
            });
        });
    </script>
    <?php

}

function add_print_product() {

    if ('POST' == $_SERVER['REQUEST_METHOD']) {
        $category = $_POST['print-category'];
        $size = (isset($_POST['size']) ? $_POST['size'] : NULL);
        $color = (isset($_POST['color']) ? $_POST['color'] : NULL);
        $paper = (isset($_POST['paper']) ? $_POST['paper'] : NULL);
        $weight = (isset($_POST['weight']) ? $_POST['weight'] : NULL);
        $sides = (isset($_POST['sides']) ? $_POST['sides'] : NULL);
        $price = $_POST['price'];

        if (($weight != NULL && (!is_numeric($weight) || intval($weight) == 0))
            || ($price != NULL && (!is_numeric($price) || floatval($price) == 0))) {
                echo '<h1>Incorrect input(s), the product was not added</h1>';
                return;
        }

        else {
            add_new_print_product($category, $size, $color, $paper, $weight, $sides, $price);
            echo '<h1>You have successfully added a new print product</h1>';
            ?> <a href="admin.php?page=add-print-product"><button>Add another product</button></a> <?php
        }
    }
    
    else {
        $cats = get_all_print_categories();
        if ($cats == NULL || empty($cats)) {
            echo '</br><h1>There are no print category to display</h1>';
            return;
        }
        ?>
        </br>
        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <label>Category: </label>
            <select name="print-category" onchange="showOptions(this.value);" class="mandatory mandatory-select">
                <option disabled selected value=""> -- select a category -- </option>
                <?php
                    foreach ($cats as $cat)
                        echo '<option value="' . $cat->id . '">' . $cat->name . '</option>';
                ?>
            </select>
            </br></br>
            <div id="print-product-inputs"></div>
            <label>Unit Price: </label><input type="number" min="0.01" step="0.01" name="price" class="mandatory">
            </br></br>
            <input type="submit" value="Add" disabled></input>
        </form>

        <script type="text/javascript">

            $(document).ready(function () {
                $('.mandatory-select').bind('change', function () {
                    $('input[type=submit]').attr('disabled', true);
                });
            });

            function allFilled() {
                var filled = true;
                $('.mandatory').each(function () {
                    if (!$(this).val() || $(this).val().trim() == "")
                        filled = false;
                });
                if ($('input:radio[name="color"]').length && $('input:radio[name="color"]:checked').length <= 0)
                    filled = false;
                if ($('input:radio[name="sides"]').length && $('input:radio[name="sides"]:checked').length <= 0)
                    filled = false;
                return filled;
            }

            function showOptions(str) {
                if (str == "") {
                    document.getElementById("print-product-inputs").innerHTML = "";
                    return;
                }
                else {
                    var data = {
                        'action': 'set_inputs',
                        'cat-id': str
                    };
                    $.post(ajaxurl, data, function (response) {
                        document.getElementById("print-product-inputs").innerHTML = response;

                        $('.mandatory').bind('keyup', function () {
                            if (allFilled())
                                $('input[type=submit]').removeAttr('disabled');
                            else
                                $('input[type=submit]').attr('disabled', true);
                        });

                        $('.mandatory-radio').bind('change', function () {
                            if (allFilled())
                                $('input[type=submit]').removeAttr('disabled');
                            else
                                $('input[type=submit]').attr('disabled', true);
                        });
                    });
                }
            }
        </script>
        <?php
    }
}

add_action( 'wp_ajax_set_inputs', 'set_print_product_inputs' );

function set_print_product_inputs() {

	$id = intval( $_POST['cat-id'] );
    $cat = get_print_category($id);
    $response = '';

    if ($cat != NULL && !empty($cat)) {
        if ($cat->size)
            $response = $response . '<label>Size: </label><input type="text" name="size" class="mandatory"></input>
            </br></br>';
        if ($cat->color)
            $response = $response . '<input type="radio" name="color" value="1" class="mandatory-radio">Color
            <input type="radio" name="color" value="0" class="mandatory-radio">Black & White
            </br></br>';
        if ($cat->paper)
            $response = $response . '<label>Paper: </label><input type="text" name="paper" class="mandatory"></input>
            </br></br>';
        if ($cat->weight)
            $response = $response . '<label>Weight: </label><input type="number" min="1" step="1" name="weight" class="mandatory"></input>
            </br></br>';
        if ($cat->sides)
            $response = $response . 'Two-sided:
            <input type="radio" name="sides" value="1" class="mandatory-radio">Yes
            <input type="radio" name="sides" value="0" class="mandatory-radio">No
            </br></br>';
    }

    echo $response;
	wp_die(); // this is required to terminate immediately and return a proper response
}

/*****     END OF PRINT MENU     *****/

/*******************************************************************************************************/

/*****     ORDER MENU     *****/

add_action ('admin_menu', 'register_order_menu');

function register_order_menu() {
     add_menu_page('Orders', 'Orders', 'manage_options', 'view-all-orders');
     add_submenu_page('view-all-orders', 'View All', 'View All', 'read', 'view-all-orders', 'view_all_orders');
     add_submenu_page('view-all-orders', 'To Do', 'To Do', 'read', 'view-todo-orders', 'view_todo_orders');
}

function view_all_orders() {
    
    if ('POST' == $_SERVER['REQUEST_METHOD']) {
        if (isset($_POST['delete_order'])) {
            $id = $_POST['delete_order'];
            remove_order($id);
        }
    }

    $all_orders = get_all_orders();
    if ($all_orders == NULL || empty($all_orders))
        echo "</br><h1>No orders to show</h1>";

    else {
        $clients = get_all_clients();
        $dates = get_all_dates();
        $prices = get_all_prices();
        ?>
        </br>
        <select class="dropdown" id="client" style="margin-right: 20px;">
            <option selected value="no value"> -- All clients -- </option>
            <?php
            if ($clients != NULL && !empty($clients)) {
                foreach($clients as $c)
                    echo '<option value="' . $c->mail . '">' . $c->name . ', ' . $c->mail . '</option>';
            }
            ?>
        </select>
        <select class="dropdown" id="date" style="margin-right: 20px;">
            <option selected value="no value"> -- All dates -- </option>
            <?php
            if ($dates != NULL && !empty($dates)) {
                foreach($dates as $d)
                    echo '<option value="' . $d . '">' . $d . '</option>';
            }
            ?>
        </select>
        <select class="dropdown" id="price" style="margin-right: 20px;">
            <option selected value="no value"> -- All prices -- </option>
            <?php
            if ($prices != NULL && !empty($prices)) {
                foreach($prices as $p)
                    echo '<option value="' . $p . '">' . $p . ' NIS</option>';
            }
            ?>
        </select>
        </br>
        <?php
        echo '<div id="orders-table">';
        echo '</br><table style="width: 100%; border: 2px solid black; text-align: center;">';
        echo '<thead style="background-color: gray; font-size: 150%;">';
        echo '<th>ID</th>';
        echo '<th>Name</th>';
        echo '<th>Mail</th>';
        echo '<th>Phone</th>';
        echo '<th>Address</th>';
        echo '<th>Price</th>';
        echo '<th>Date</th>';
        echo '<th>Invoice</th>';
        echo '<th>Status</th>';
        echo '<th>Delete</th>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($all_orders as $order) {
            echo '<tr>';
            echo '<td class="order ' . $order->id . '">' . $order->id . '</td>';
            echo '<td class="order ' . $order->id . '">' . $order->name . '</td>';
            echo '<td class="order ' . $order->id . '">' . $order->mail . '</td>';
            echo '<td class="order ' . $order->id . '">' . $order->phone . '</td>';
            echo '<td class="order ' . $order->id . '">' . $order->address . '</td>';
            echo '<td class="order ' . $order->id . '">' . $order->price . ' NIS</td>';
            echo '<td class="order ' . $order->id . '">' . $order->date . '</td>';
            echo '<td><a href="' . WP_CONTENT_URL . '/uploads/invoices/invoice-number-' . $order->id . '.pdf">Invoice n°' . $order->id . '</a></td>';
            echo '<td class="order ' . $order->id . '">' .get_order_status($order->id) . '</td>';
            $form = '<form action="' . $_SERVER['REQUEST_URI'] . '" method="POST" enctype="multipart/form-data">
                        <input type="submit" name="action" value="Delete"/>
                        <input type="hidden" name="delete_order" value="' . $order->id . '"/></form>';
            echo '<td>' . $form . '</td>';
            echo '</tr>';

            $order_details = get_order_details($order->id);
            if ($order_details != NULL && !empty($order_details)) {
                $table = 'none';
                echo '<tr><td colspan="10" id="details-' . $order->id . '" hidden>';
                foreach($order_details as $detail) {
                    if ($table == 'none' && $detail->sublimation == 0) {
                        echo '<table style="width: 100%; border: 2px solid black; text-align: center; table-layout: fixed;">';
                        echo '<thead style="background-color: #e2b3e9;">';
                        echo '<th>Product ID</th><th>File</th><th>Pages</th><th>Quantity</th><th>Price</th><th>Status</th>';
                        echo '</thead>';
                        echo '<tbody>';
                        $table = 'print';
                    }
                    else if ($table != 'sublimation' && $detail->sublimation == 1) {
                        if ($table == 'print') {
                            echo '</tbody>';
                            echo '</table>';
                        }
                        echo '<table style="width: 100%; border: 2px solid black; text-align: center; table-layout: fixed;">';
                        echo '<thead style="background-color: #f2e6ac;">';
                        echo '<th>Product ID</th><th>Design</th><th>Pages</th><th>Quantity</th><th>Price</th><th>Status</th>';
                        echo '</thead>';
                        echo '<tbody>';
                        $table = 'sublimation';
                    }
                    echo '<tr>';
                    if ($detail->sublimation == 0)
                        echo '<td>' . $detail->pid. ' - ' . get_print_product_category($detail->pid)->name . '</td>';
                    else 
                        echo '<td>' . $detail->pid. ' - ' . get_sub_product($detail->pid)->name . '</td>';
                    echo '<td><a href="' . $detail->file . '">';
                    echo ($detail->sublimation == 0 ? "file" : "design");
                    echo '</a></td>';
                    echo '<td>' . ($detail->sublimation == 0 ? $detail->pages : "-") . '</td>';
                    echo '<td>' . $detail->quantity . '</td>';
                    echo '<td>' . $detail->sub_price . ' NIS</td>';
                    echo '<td>' . ($detail->todo == 1 ? 'To do' : 'Done') . '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
                echo '</td></tr>';
            }

        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
        

    ?>
    <script type="text/javascript">

        $(document).ready(function () {

            $('.order').on("click", function () {
                var classList = $(this).attr('class').split(/\s+/);
                if (classList.length != 2)
                    return;
                var id = classList[1];
                if (id == 'order') id = classList[0];
                $('#details-' + id).toggle(250);
            });

            $('.dropdown').on("change", function () {
                var select_by = $(this).attr('id');
                var value = $(this).val();
                var data = {
                    'action': 'show_orders',
                    'select_by': select_by,
                    'value': value
                };
                $.post(ajaxurl, data, function (response) {
                    if (select_by != 'client')
                        document.getElementById("client").selectedIndex = "0";
                    if (select_by != 'date')
                        document.getElementById("date").selectedIndex = "0";
                    if (select_by != 'price')
                        document.getElementById("price").selectedIndex = "0";
                    $("#orders-table").html(response);
                });
            });

        });

    </script>
    <?php
}

function ViewOrdersScript() {
    return "$('.order').on('click', function () {
                var classList = $(this).attr('class').split(/\s+/);
                if (classList.length != 2)
                    return;
                var id = classList[1];
                if (id == 'order') id = classList[0];
                $('#details-' + id).toggle(250);
            });";
}

add_action( 'wp_ajax_show_orders', 'show_selected_orders' );

function show_selected_orders() {

	$select_by = $_POST['select_by'];
    $value = $_POST['value'];

    if ($value == 'no value')
        $all_orders = get_all_orders();
    
    else {   
        $query = "SELECT * FROM wp_orders WHERE ";
        if ($select_by == 'client')
            $query = $query . "mail = ";
        else if ($select_by == 'date')
            $query = $query . "date = ";
        else if ($select_by == 'price')
            $query = $query . "price = ";
        $query = $query . "'" . $value . "'";
        $all_orders = getresults_copycolor($query);
    }

    $response = '';

    $response = $response . '</br><table style="width: 100%; border: 2px solid black; text-align: center;">';
    $response = $response . '<thead style="background-color: gray; font-size: 150%;">';
    $response = $response . '<th>ID</th>';
    $response = $response . '<th>Name</th>';
    $response = $response . '<th>Mail</th>';
    $response = $response . '<th>Phone</th>';
    $response = $response . '<th>Address</th>';
    $response = $response . '<th>Price</th>';
    $response = $response . '<th>Date</th>';
    $response = $response . '<th>Invoice</th>';
    $response = $response . '<th>Status</th>';
    $response = $response . '<th>Delete</th>';
    $response = $response . '</thead>';
    $response = $response . '<tbody>';
        foreach ($all_orders as $order) {
            $response = $response . '<tr>';
            $response = $response . '<td class="order ' . $order->id . '">' . $order->id . '</td>';
            $response = $response . '<td class="order ' . $order->id . '">' . $order->name . '</td>';
            $response = $response . '<td class="order ' . $order->id . '">' . $order->mail . '</td>';
            $response = $response . '<td class="order ' . $order->id . '">' . $order->phone . '</td>';
            $response = $response . '<td class="order ' . $order->id . '">' . $order->address . '</td>';
            $response = $response . '<td class="order ' . $order->id . '">' . $order->price . ' NIS</td>';
            $response = $response . '<td class="order ' . $order->id . '">' . $order->date . '</td>';
            $response = $response . '<td><a href="' . WP_CONTENT_URL . '/uploads/invoices/invoice-number-' . $order->id . '.pdf">Invoice n°' . $order->id . '</a></td>';
            $response = $response . '<td class="order ' . $order->id . '">' .get_order_status($order->id) . '</td>';
            $form = '<form action="' . $_SERVER['REQUEST_URI'] . '" method="POST" enctype="multipart/form-data">
                        <input type="submit" name="action" value="Delete"/>
                        <input type="hidden" name="delete_order" value="' . $order->id . '"/></form>';
            $response = $response . '<td>' . $form . '</td>';
            $response = $response . '</tr>';

            $order_details = get_order_details($order->id);
            if ($order_details != NULL && !empty($order_details)) {
                $table = 'none';
                $response = $response . '<tr><td colspan="10" id="details-' . $order->id . '" hidden>';
                foreach($order_details as $detail) {
                    if ($table == 'none' && $detail->sublimation == 0) {
                        $response = $response . '<table style="width: 100%; border: 2px solid black; text-align: center; table-layout: fixed;">';
                        $response = $response . '<thead style="background-color: #e2b3e9;">';
                        $response = $response . '<th>Product ID</th><th>File</th><th>Pages</th><th>Quantity</th><th>Price</th><th>Status</th>';
                        $response = $response . '</thead>';
                        $response = $response . '<tbody>';
                        $table = 'print';
                    }
                    else if ($table != 'sublimation' && $detail->sublimation == 1) {
                        if ($table == 'print') {
                            $response = $response . '</tbody>';
                            $response = $response . '</table>';
                        }
                        $response = $response . '<table style="width: 100%; border: 2px solid black; text-align: center; table-layout: fixed;">';
                        $response = $response . '<thead style="background-color: #f2e6ac;">';
                        $response = $response . '<th>Product ID</th><th>Design</th><th>Pages</th><th>Quantity</th><th>Price</th><th>Status</th>';
                        $response = $response . '</thead>';
                        $response = $response . '<tbody>';
                        $table = 'sublimation';
                    }
                    $response = $response . '<tr>';
                    if ($detail->sublimation == 0)
                        $response = $response . '<td>' . $detail->pid. ' - ' . get_print_product_category($detail->pid)->name . '</td>';
                    else 
                        $response = $response . '<td>' . $detail->pid. ' - ' . get_sub_product($detail->pid)->name . '</td>';
                    $response = $response . '<td><a href="' . $detail->file . '">';
                    $response = $response . ($detail->sublimation == 0 ? "file" : "design");
                    $response = $response . '</a></td>';
                    $response = $response . '<td>' . ($detail->sublimation == 0 ? $detail->pages : "-") . '</td>';
                    $response = $response . '<td>' . $detail->quantity . '</td>';
                    $response = $response . '<td>' . $detail->sub_price . ' NIS</td>';
                    $response = $response . '<td>' . ($detail->todo == 1 ? 'To do' : 'Done') . '</td>';
                    $response = $response . '</tr>';
                }
                $response = $response . '</tbody>';
                $response = $response . '</table>';
                $response = $response . '</td></tr>';
            }

        }
        $response = $response . '</tbody>';
        $response = $response . '</table>';

    echo $response . '<script type="text/javascript">' . ViewOrdersScript() . '</script>';
	wp_die(); // this is required to terminate immediately and return a proper response
}

function view_todo_orders() {

    if ('POST' == $_SERVER['REQUEST_METHOD']) {
        if(!empty($_POST['done_orders'])) {
            foreach($_POST['done_orders'] as $order_file)
                set_orders_details_done($order_file);
        }
    }

    $todo_orders = get_orders_details_todo();
    if ($todo_orders == NULL || empty($todo_orders))
        echo "</br><h1>No orders to show</h1>";

    else {
        $orders_ids = get_orders_details_todo_ids();
        $dates = get_orders_details_todo_dates();
        $print_products = get_orders_details_todo_print_products();
        $sub_products = get_orders_details_todo_sub_products();
        echo '<form action="' . $_SERVER['REQUEST_URI'] . '" method="POST" enctype="multipart/form-data">';
        ?>
        </br>
        <select class="todo-dropdown" id="order_id" style="margin-right: 20px;">
            <option selected value="no value"> -- All Orders -- </option>
            <?php
            if ($orders_ids != NULL && !empty($orders_ids)) {
                foreach($orders_ids as $o)
                    echo '<option value="' . $o . '">' . $o . '</option>';
            }
            ?>
        </select>
        <select class="todo-dropdown" id="date" style="margin-right: 20px;">
            <option selected value="no value"> -- All dates -- </option>
            <?php
            if ($dates != NULL && !empty($dates)) {
                foreach($dates as $d)
                    echo '<option value="' . $d . '">' . $d . '</option>';
            }
            ?>
        </select>
        <select class="todo-dropdown" id="product" style="margin-right: 20px;">
            <option selected value="no value"> -- All products -- </option>
            <?php
            if ($print_products != NULL && !empty($print_products)) {
                foreach($print_products as $p)
                    echo '<option value="p-' . $p->pid . '">Print - ' . $p->name . ' - ' . $p->pid . '</option>';
            }
            if ($sub_products != NULL && !empty($sub_products)) {
                foreach($sub_products as $p)
                    echo '<option value="s-' . $p->pid . '">Sublimation - ' . $p->name . '</option>';
            }
            ?>
        </select>
        <input type="submit" value="Done" style="float: right; margin-right: 20px;">
        </br>
        <?php
        echo '<div id="todo-orders-table">';
        echo '</br><table style="width: 100%; border: 2px solid black; text-align: center;">';
        echo '<thead style="background-color: gray; font-size: 150%;">';
        echo '<th>ID</th>';
        echo '<th>Service</th>';
        echo '<th>Product</th>';
        echo '<th>File / Design</th>';
        echo '<th>Pages</th>';
        echo '<th>Quantity</th>';
        echo '<th>Date</th>';
        echo '<th>Done</th>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($todo_orders as $key=>$detail) {
            $trclass = ($detail->sublimation == 0 ? 'print' : 'sublimation');
            $tdclass = ($detail->sublimation == 0 ? 'detail' : '');
            if ($detail->sublimation == 0) {
                $cat = get_print_product_category($detail->pid);
                $prod = get_print_product($detail->pid);
            }
            echo '<tr class="' . $trclass . '">';
            echo '<td class="' . $tdclass . ' ' . $key . '">' . $detail->id . '</td>';
            echo '<td class="' . $tdclass . ' ' . $key . '">' . ($detail->sublimation == 0 ? "Print" : "Sublimation") . '</td>';
            if ($detail->sublimation == 0)
                echo '<td class="' . $tdclass . ' ' . $key . '">' . $detail->pid. ' - ' . get_print_product_category($detail->pid)->name . '</td>';
            else 
                echo '<td>' . $detail->pid. ' - ' . get_sub_product($detail->pid)->name . '</td>';
            echo '<td class="' . $tdclass . ' ' . $key . '"><a href="' . $detail->file . '">';
            echo ($detail->sublimation == 0 ? "file" : "design");
            echo '</a></td>';
            echo '<td class="' . $tdclass . ' ' . $key . '">' . ($detail->sublimation == 0 ? $detail->pages : "-") . '</td>';
            echo '<td class="' . $tdclass . ' ' . $key . '">' . $detail->quantity . '</td>';
            echo '<td class="' . $tdclass . ' ' . $key . '">' . get_order($detail->id)->date . '</td>';
            echo '<td><input type="checkbox" name="done_orders[]" value="' . $detail->file . '"><label>Select if done</label></td>';
            echo '</tr>';
            if ($detail->sublimation == 0) {
                echo '<tr hidden id="print-' . $key . '">';
                echo '<td colspan="8">';
                echo '<table style="width: 100%; border: 2px solid black; text-align: center; table-layout: fixed;">';
                echo '<thead style="background-color: #e2b3e9;"><th>Size</th><th>Color</th><th>Paper</th><th>Weight</th><th>Two-sided</th></thead>';
                echo '<td>' . ($cat->size ? $prod->size : '-'). '</td>';
                echo '<td>' . ($cat->color ? ($prod->color ? 'yes' : 'no') : '-'). '</td>';
                echo '<td>' . ($cat->paper ? $prod->paper : '-'). '</td>';
                echo '<td>' . ($cat->weight ? $prod->weight : '-'). '</td>';
                echo '<td>' . ($cat->sides ? ($prod->sides ? 'yes' : 'no') : '-'). '</td>';
                echo '</table>';
                echo '<td>';
                echo '<tr>';
            }
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
        

    ?>
    <script type="text/javascript">

        $(document).ready(function () {

            $('.print').hover(colorMagenta, colorTransparent);
            $('.sublimation').hover(colorYellow, colorTransparent);

            $('.detail').on("click", function () {
                var classList = $(this).attr('class').split(/\s+/);
                if (classList.length != 2)
                    return;
                var key = classList[1];
                if (key == 'detail') key = classList[0];
                $('#print-' + key).toggle(250);
            });

            $('.todo-dropdown').on("change", function () {
                var select_by = $(this).attr('id');
                var value = $(this).val();
                var data = {
                    'action': 'show_todo_orders',
                    'select_by': select_by,
                    'value': value
                };
                $.post(ajaxurl, data, function (response) {
                    if (select_by != 'order_id')
                        document.getElementById("order_id").selectedIndex = "0";
                    if (select_by != 'date')
                        document.getElementById("date").selectedIndex = "0";
                    if (select_by != 'product')
                        document.getElementById("product").selectedIndex = "0";
                    $("#todo-orders-table").html(response);
                });
            });

        });

        var colorMagenta = function () { $(this).css('background-color', '#e2b3e9'); };
        var colorYellow = function () { $(this).css('background-color', '#f2e6ac'); };
        var colorTransparent = function () { $(this).css('background-color', 'transparent'); };

    </script>
    <?php
}

function todoOrdersScript() {
    return "$('.print').hover(colorMagenta, colorTransparent);
            $('.sublimation').hover(colorYellow, colorTransparent);

            $('.detail').on('click', function () {
                var classList = $(this).attr('class').split(/\s+/);
                if (classList.length != 2)
                    return;
                var key = classList[1];
                if (key == 'detail') key = classList[0];
                $('#print-' + key).toggle(250);
            });

            var colorMagenta = function () { $(this).css('background-color', '#e2b3e9'); };
            var colorYellow = function () { $(this).css('background-color', '#f2e6ac'); };
            var colorTransparent = function () { $(this).css('background-color', 'transparent'); };";
}

add_action( 'wp_ajax_show_todo_orders', 'show_todo_selected_orders' );

function show_todo_selected_orders() {

	$select_by = $_POST['select_by'];
    $value = $_POST['value'];

    if ($value == 'no value')
        $all_todo_orders = get_orders_details_todo();
    
    else {   
        if ($select_by == 'order_id')
            $query = "SELECT * FROM wp_orders_details WHERE todo = 1 AND id = '$value'";
        else if ($select_by == 'date')
            $query = "SELECT * FROM wp_orders_details INNER JOIN wp_orders ON wp_orders.id=wp_orders_details.id WHERE wp_orders_details.todo = 1 AND wp_orders.date = '$value'";
        else if ($select_by == 'product') {
            $res = explode("-", $value);
            $service = $res[0];
            $pid = $res[1];
            if ($service == 'p')    //print
                $query = "SELECT * FROM wp_orders_details WHERE todo = 1 AND sublimation = 0 AND pid = '$pid'";
            else if ($service == 's')   //sublimation
                $query = "SELECT * FROM wp_orders_details WHERE todo = 1 AND sublimation = 1 AND pid = '$pid'";
        }

        $all_todo_orders = getresults_copycolor($query);
    }

    $response = '';

    $response = $response . '</br><table style="width: 100%; border: 2px solid black; text-align: center;">';
    $response = $response . '<thead style="background-color: gray; font-size: 150%;">';
    $response = $response . '<th>ID</th>';
    $response = $response . '<th>Service</th>';
    $response = $response . '<th>Product</th>';
    $response = $response . '<th>File / Design</th>';
    $response = $response . '<th>Pages</th>';
    $response = $response . '<th>Quantity</th>';
    $response = $response . '<th>Date</th>';
    $response = $response . '<th>Done</th>';
    $response = $response . '</thead>';
    $response = $response . '<tbody>';
    foreach ($all_todo_orders as $key=>$detail) {
        $trclass = ($detail->sublimation == 0 ? 'print' : 'sublimation');
        $tdclass = ($detail->sublimation == 0 ? 'detail' : '');
        if ($detail->sublimation == 0) {
            $cat = get_print_product_category($detail->pid);
            $prod = get_print_product($detail->pid);
        }
        $response = $response . '<tr class="' . $trclass . '">';
        $response = $response . '<td class="' . $tdclass . ' ' . $key . '">' . $detail->id . '</td>';
        $response = $response . '<td class="' . $tdclass . ' ' . $key . '">' . ($detail->sublimation == 0 ? "Print" : "Sublimation") . '</td>';
        if ($detail->sublimation == 0)
            $response = $response . '<td class="' . $tdclass . ' ' . $key . '">' . $detail->pid. ' - ' . get_print_product_category($detail->pid)->name . '</td>';
        else 
            $response = $response . '<td>' . $detail->pid. ' - ' . get_sub_product($detail->pid)->name . '</td>';
        $response = $response . '<td class="' . $tdclass . ' ' . $key . '"><a href="' . $detail->file . '">';
        $response = $response . ($detail->sublimation == 0 ? "file" : "design");
        $response = $response . '</a></td>';
        $response = $response . '<td class="' . $tdclass . ' ' . $key . '">' . ($detail->sublimation == 0 ? $detail->pages : "-") . '</td>';
        $response = $response . '<td class="' . $tdclass . ' ' . $key . '">' . $detail->quantity . '</td>';
        $response = $response . '<td class="' . $tdclass . ' ' . $key . '">' . get_order($detail->id)->date . '</td>';
        $response = $response . '<td><input type="checkbox" name="done_orders[]" value="' . $detail->file . '"><label>Select if done</label></td>';
        $response = $response . '</tr>';
        if ($detail->sublimation == 0) {
            $response = $response . '<tr hidden id="print-' . $key . '">';
            $response = $response . '<td colspan="8">';
            $response = $response . '<table style="width: 100%; border: 2px solid black; text-align: center; table-layout: fixed;">';
            $response = $response . '<thead style="background-color: #e2b3e9;"><th>Size</th><th>Color</th><th>Paper</th><th>Weight</th><th>Two-sided</th></thead>';
            $response = $response . '<td>' . ($cat->size ? $prod->size : '-'). '</td>';
            $response = $response . '<td>' . ($cat->color ? ($prod->color ? 'yes' : 'no') : '-'). '</td>';
            $response = $response . '<td>' . ($cat->paper ? $prod->paper : '-'). '</td>';
            $response = $response . '<td>' . ($cat->weight ? $prod->weight : '-'). '</td>';
            $response = $response . '<td>' . ($cat->sides ? ($prod->sides ? 'yes' : 'no') : '-'). '</td>';
            $response = $response . '</table>';
            $response = $response . '<td>';
            $response = $response . '<tr>';
        }
    }
    $response = $response . '</tbody>';
    $response = $response . '</table>';

    echo $response . '<script type="text/javascript">' . todoOrdersScript() . '</script>';
	wp_die(); // this is required to terminate immediately and return a proper response
}

/*****     END OF ORDER MENU     *****/

?>