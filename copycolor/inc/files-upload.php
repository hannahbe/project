<?php

function isImage($extension) {
    $isImage = TRUE;
    switch (strtolower($extension)) {
        case 'jpg':     break;
        case 'jpeg':    break;
        case 'png':     break;
        case 'gif':     break;
        case 'bmp':     break;
        case 'pdf':     break;
        default:        $isImage = FALSE;
                        break;
    }
    return $isImage;
}

function isWordDoc($extension) {
    $isWordDoc = TRUE;
    switch (strtolower($extension)) {
        case 'doc':     break;
        case 'dot':     break;
        case 'docx':    break;
        case 'docm':    break;
        case 'dotx':    break;
        case 'dotm':    break;
        default:        $isWordDoc = FALSE;
                        break;
    }
    return $isWordDoc;
}

function isPowerpointDoc($extension) {
    $isPowerpointDoc = TRUE;
    switch (strtolower($extension)) {
        case 'ppt':     break;
        case 'pot':     break;
        case 'pps':     break;
        case 'pptx':    break;
        case 'pptm':    break;
        case 'potx':    break;
        case 'potm':    break;
        case 'ppsx':    break;
        case 'ppsm':    break;
        case 'sldx':    break;
        case 'sldm':    break;
        default:        $isPowerpointDoc = FALSE;
                        break;
    }
    return $isPowerpointDoc;
}

function isExcelDoc($extension) {
    $isExcelDoc = TRUE;
    switch (strtolower($extension)) {
        case 'xlsx':    break;
        case 'xlsm':    break;
        case 'xltx':    break;
        case 'xltm':    break;
        case 'xls':     break;
        case 'xlt':     break;
        default:        $isExcelDoc = FALSE;
                        break;
    }
    return $isExcelDoc;
}

function isTxtDoc($extension) {
    $isTxtDoc = TRUE;
    switch (strtolower($extension)) {
        case 'rtf':     break;
        case 'txt':     break;
        default:        $isTxtDoc = FALSE;
                        break;
    }
    return $isTxtDoc;
}

function uploadBackground($background) {
    return uploadImageFile($background, 'backgrounds');
}

function uploadIcon($icon) {
    return uploadImageFile($icon, 'icons');
}

function uploadImage($image) {
    return uploadImageFile($image, 'images');
}

function uploadCanvasImg($url, $folder) {

    $imgString = base64_decode($url);
    $uploadfolder =  url_to_path(WP_CONTENT_URL . '/uploads/' . $folder);  //the temp folder url
    $imageName = uniqid();

    $filePath = $uploadfolder . '/' . $imageName . '.png';
    while (file_exists($filePath)) {
        $imageName = uniqid();
        $filePath = $uploadfolder . '/' . $imageName . '.png';
    }

    if (!is_dir($uploadfolder) && !mkdir($uploadfolder, 0777)) {
        echo 'Error creating folder, please try again';
        return NULL;
    }

    if (file_put_contents($filePath, $imgString) != FALSE)
        return path_to_url($filePath);

    return NULL;
}

function uploadImageFile($image, $folder) {

    $uploadfolder =  url_to_path(WP_CONTENT_URL . '/uploads/' . $folder); 
    $imageName = uniqid();
    $ext = explode ('.', $image['name']);
    $n = count($ext) - 1;
    $ext = $ext[$n];

    if (!isImage($ext)) {
        echo 'File must be an image';
        return NULL;
    }

    $filePath = $uploadfolder . '/' . $imageName . '.' . $ext;

    //assure that we created a file name that doesn't already exist
    while (file_exists($filePath)) {
        $imageName = uniqid();
        $filePath = $uploadfolder . '/' . $imageName . '.' . $ext;
    }

    if (!is_dir($uploadfolder) && !mkdir($uploadfolder, 0777)) {
        echo 'Error creating folder, please try again';
        return NULL;
    }

    if (move_uploaded_file($image['tmp_name'], $filePath))
        return path_to_url($filePath);

    return NULL;
}

/* upload the file n° $fileNumber of $files to temp directory
 * returns the path to created file in case of success
 */
function uploadFile($files, $fileNumber) {

    $uploadfolder =  url_to_path(WP_CONTENT_URL . '/uploads/orders');  //the temp folder url
    $fileName = uniqid();
    $ext = explode ('.', $files['name'][$fileNumber]);
    $n = count($ext) - 1;
    $ext = $ext[$n];

    if (!isImage($ext) && !isWordDoc($ext) && !isPowerpointDoc($ext) && !isExcelDoc($ext) && !isTxtDoc($ext)) {
        echo 'File not supported';
        return NULL;
    }

    $filePath = $uploadfolder . '/' . $fileName . '.' . $ext;

    //assure that we created a file name that doesn't already exist
    while (file_exists($filePath)) {
        $fileName = uniqid();
        $filePath = $uploadfolder . '/' . $fileName . '.' . $ext;
    }

    if (!is_dir($uploadfolder) && !mkdir($uploadfolder, 0777)) {
        echo 'Error creating folder, please try again';
        return NULL;
    }

    if (move_uploaded_file($files['tmp_name'][$fileNumber], $filePath))
        return path_to_url($filePath);

    return NULL;
}

//function used in view_item to turn url into path
function url_to_path($url){
    $path=str_replace(rtrim(get_site_url(),'/').'/', ABSPATH, $url);
    return $path;
}

//function used in view_item to turn url into path
function path_to_url($path){
    $url=str_replace(ABSPATH, rtrim(get_site_url(),'/').'/', $path);
    return $url;
}
?>
