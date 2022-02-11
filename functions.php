<?php

function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    ///Replace white spaces with +
    $searchString = " ";
    $replaceString = "";
    $data = str_replace($searchString, $replaceString, $data);

    return $data;
}

///this is a function to store a file, accepts filename, temporary filename and the destination directory
/// it then returns the file location
function storeFiles($fileName,$tempFileName, $fileDir){
    $fileLocation = $fileDir . $fileName;

    move_uploaded_file($tempFileName, $fileLocation);

    return $fileLocation;
}

///Function to maintain aspect ratio of an image, accepts target height and width and the original image height and width as paramaters
function keepAspectRatio($width, $height, $oldWidth, $oldHeight){
    if (!$width == '')        //height missing
    {
        $factor = (float)$width / (float)$oldWidth;
        $height = $factor * $oldHeight;
        return $height;
    }
    else if (!$height == '')      //width missing
    {
        $factor = (float)$height / (float)$oldHeight;
        $width = $factor * $oldWidth;
        return $width;
    }
}

//this function handles form fields being incorrectly filled out
function emptyFieldHandler($width, $height, $keepAspectRatio, $file){
    if (!$file){
        echo "<img class='img-fluid rounded mb-4 mb-lg-0' src='https://support.apple.com/library/content/dam/edam/applecare/images/en_US/social/supportapphero/camera-modes-hero.jpg' width='750' height='600' alt='...' />";
        echo "<h2>File was not chosen</h2>";
        return false;
    } elseif ($width == '' && $height == ''){
        echo "<img class='img-fluid rounded mb-4 mb-lg-0' src='https://support.apple.com/library/content/dam/edam/applecare/images/en_US/social/supportapphero/camera-modes-hero.jpg' width='750' height='600' alt='...' />";
        echo "<h2>Please fill out both fields or one with the keep aspect ratio box ticked</h2>";
        return false;
    } elseif ($width == '' && $keepAspectRatio == 'off') {
        echo "<img class='img-fluid rounded mb-4 mb-lg-0' src='https://support.apple.com/library/content/dam/edam/applecare/images/en_US/social/supportapphero/camera-modes-hero.jpg' width='750' height='600' alt='...' />";
        echo "<h2>Please fill out both fields or one with the keep aspect ratio box ticked</h2>";
        return false;
    } elseif ($width == '' && $keepAspectRatio == 'off') {
        echo "<img class='img-fluid rounded mb-4 mb-lg-0' src='https://support.apple.com/library/content/dam/edam/applecare/images/en_US/social/supportapphero/camera-modes-hero.jpg' width='750' height='600' alt='...' />";
        echo "<h2>Please fill out both fields or one with the keep aspect ratio box ticked</h2>";
        return false;
    } elseif ($width != '' && $height != '' && $keepAspectRatio == 'on') {
        echo "<img class='img-fluid rounded mb-4 mb-lg-0' src='https://support.apple.com/library/content/dam/edam/applecare/images/en_US/social/supportapphero/camera-modes-hero.jpg' width='750' height='600' alt='...' />";
        echo "<h2>If you want to keep aspect ratio you must provide either height or width, not both</h2>";
        return false;
    } else {
        return true;
    }
}

///function to check uploaded file extension, accepts a filename as a parameter
function checkFileExtension($fileName){
    $src_file_name = $fileName;

    $ext = strtolower(pathinfo($src_file_name, PATHINFO_EXTENSION));

    if ($ext == 'jpg' || $ext == 'jpeg')
    {
        return true;
    }

    elseif ($ext == 'png')
    {
        return true;
    }

    else {
        return false;
    }
}

// this file checks the inputs from the reshaped image form and gives back the reshaped image
function imageHandler(){
    if($_POST) {

        echo "<h1>{$_FILES['image']['name']}</h1>";


        if(is_array($_FILES)) {
            // check for keep aspect ratio and image quality inputs
            if (isset($_POST['aspect'])){
                $keepAspectRatio = 'on';
            } else {
                $keepAspectRatio = 'off';
            }

            if (isset($_POST['imageQuality'])){
                $imageQuality = $_POST['imageQuality'];
            } else {
                $imageQuality = 100;
            }

            //check fields and file upload was not empty or missing data or too large
            $fieldsNeeded = emptyFieldHandler($_POST['width'], $_POST['height'], $keepAspectRatio, $_FILES['image']['name']);
            if(!$fieldsNeeded){
                return;
            } elseif ($_FILES['image']['size'] >= 2097152){
                echo "<h1>File too large must be kept under 2MB</h1>";
                return;
            }


            //extract image from FILES array and width, height and file type
            $file = $_FILES['image']['tmp_name'];
            $source_properties = getimagesize($file);

            //check is file genuine image
            if (!$source_properties){
                echo "<h1>This file is not a genuine image</h1>";
                return;
            }
            $image_type = $source_properties[2];

            //Branch for JPG images
            if( $image_type == IMAGETYPE_JPEG ) {
                $image_resource_id = imagecreatefromjpeg($file);
                $target_layer = imageResize($image_resource_id, $source_properties[0], $source_properties[1],$keepAspectRatio);
                imagejpeg($target_layer,'images/'.$_FILES['image']['name'],$imageQuality);
                echo "<img class='img-fluid rounded mb-4 mb-lg-0'  src='images/{$_FILES['image']['name']}' />";

            }
            // Branch for PNG images
            elseif( $image_type == IMAGETYPE_PNG ) {
                $image_resource_id = imagecreatefrompng($file);
                $target_layer = imageResize($image_resource_id, $source_properties[0], $source_properties[1],$keepAspectRatio);
                imagejpeg($target_layer, $_FILES['image']['name'],$imageQuality);
                echo "<img class='img-fluid rounded mb-4 mb-lg-0'  src='{$_FILES['image']['name']}' />";
                }
            else{
                echo "<h1>Please upload only jpeg or png files</h1>";
            }
        }
    } else {
        echo "<img class='img-fluid rounded mb-4 mb-lg-0' src='https://support.apple.com/library/content/dam/edam/applecare/images/en_US/social/supportapphero/camera-modes-hero.jpg' width='750' height='600' alt='...' />";
    }
}

//This function is passed the original image, its width and height and the keep aspect ratio option and gives back the resized image
function imageResize($image_resource_id,$width,$height,$keepAspectRatio) {
    $targetWidth = $_POST['width'];
    $targetHeight = $_POST['height'];

    // clean input
    $targetHeight = cleanInput($targetHeight);
    $targetWidth = cleanInput($targetWidth);

    echo "<h2>Height:$targetHeight</h2>". ' ' . "<h2>Width:$targetWidth</h2>";

    //perform keep aspect ratio function if selceted
    if ($keepAspectRatio == 'on' && $targetWidth != ''){
        $targetHeight = keepAspectRatio($targetWidth, $targetHeight, $width, $height);
    } elseif ($keepAspectRatio == 'on' && $targetHeight != ''){
        $targetWidth = keepAspectRatio($targetWidth, $targetHeight, $width, $height);
    }

    //create resized image and paint old image over it
    $target_layer=imagecreatetruecolor($targetWidth,$targetHeight);
    imagecopyresampled($target_layer,$image_resource_id,0,0,0,0,$targetWidth,$targetHeight, $width,$height);
    return $target_layer;
}
?>