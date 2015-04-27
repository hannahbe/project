<?php
    
if ('POST' == $_SERVER['REQUEST_METHOD'])
{
    global $wpdb;

    //image upload
    $upload = wp_upload_bits($_FILES["new_background"]["name"], null, file_get_contents($_FILES["new_background"]["tmp_name"]));

    $uploadedurl = $upload['url'];

    //add to database
    $wpdb->query("INSERT INTO wp_backgrounds (guid) VALUES ('$uploadedurl')");

    ?>
    
    <h2>You have successfully added a new background</h2>
    <br>
    <img src="<?php echo $uploadedurl ?>" alt="Your new background">

<?php
}

else { ?>

<form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
    <h2>Select a background: </h2>
    <input type="file" name="new_background" id="new_background" accept="image/*"/>
    <br>
    <br>
    <input type="submit" id="add_background" value="Add background" disabled/>
</form>

<script type="text/javascript">
    document.getElementById("new_background").onchange = function() {
    if(this.value) {
        document.getElementById("add_background").disabled = false; 
    }  
}
</script>

<?php
}
?>
