<?php
    
    const CATALOG = '47';

    //if the page is a subpage of catalog, then display catalog
    if($post->post_parent == CATALOG) {
        get_template_part('page-catalog');
    }
    if($post->post_parent == 2) {
        get_template_part('page-service');
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
