<?php
    //if the page is a subpage of catalog, then display catalog
    if($post->post_parent == '47') {
        get_template_part('page-catalog');
    }
    //else display main page (temporary)
    else {
        get_template_part('index');
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
    <body>
        
    </body>
</html>
