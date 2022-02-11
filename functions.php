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

//function will accept a file and check if it is a png or a jpeg, raising an error if it is not
/*function checkFileIsImage($file){
    $fileName = $file;
    $error = '';
    $type = exif_imagetype( $fileName );
    switch( $type ) {
        case 1:
            $isImage = @imagecreatefromjpeg( $fileName );
            $error .= ( !$isImage ) ? "extn - jpg, but not a valid jpg" : '  valid jpg';
            return $error;
        case 2:
            echo "png : ";
            $isImage = @imagecreatefrompng( $fileName );
            $error .= ( !$isImage ) ? "extn - png, but not a valid png" : ' valid png';
            return $error;
        default: //if there is no exif data
            $error .= "Not an image" ;
    }
    return $error;
}*/

/*function checkInputIsInteger($input){

    $int_value = ctype_digit($input) ? intval($input) : null;
    if ($int_value === null)
    {
        // $value wasn't all numeric
        return "<h2>for height and width enter numbers only!</h2>";
    } else {
        // else assign user input

        if ($input < 0)
        {
        return  "<h2>Enter a positive numbers only!</h2>";
        } else {
            return $input;
        }
    }
}*/

///this is a function to store a file, accepts filename, temporary filename and the destination directory
/// it then returns the file location
function storeFiles($fileName,$tempFileName, $fileDir){
    $fileLocation = $fileDir . $fileName;

    move_uploaded_file($tempFileName, $fileLocation);

    return $fileLocation;
}

///Function to maintain aspect ratio of an image
function keepAspectRatio($width, $height, $oldWidth, $oldHeight){
    if (!$width == '')
    {
        $factor = (float)$width / (float)$oldWidth;
        $height = $factor * $oldHeight;
        return $height;
    }
    else if (!$height == '')
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
        return $ext;
    }

    elseif ($ext == 'png')
    {
        return $ext;
    }

    else {
        return false;
    }
}

function resizeImage($image,$width,$height,$oldWidth, $oldHeight) {
    $target_layer=imagecreatetruecolor($width,$height);
    imagecopyresampled($target_layer,$image,0,0,0,0,$width,$height, $oldWidth,$oldHeight);
    return $target_layer;
}


///this function will accept an image file location & parameters to reshape the file (width & height)
/// it also checks if the file is a genuine image, if it is not it returns an error message string
function jpgReshaper($image,$height, $width, $keepAspectRatio){
    ///get height and width dimensions of original image
    list($oldHeight, $oldWidth) = getimagesize($image);

    ///check for "keep aspect ratio" feature
    if ($keepAspectRatio == 'on' && $width != ''){
        $height = keepAspectRatio($width, $height, $oldWidth, $oldHeight);
    } elseif ($keepAspectRatio == 'on' && $height != ''){
    $width = keepAspectRatio($width, $height, $oldWidth, $oldHeight);
        }

    $source = @imagecreatefromjpeg($image);
    //check if file is an image
    if (!$source){
        return "Error: This file is not an image";
    } else {
        $output = imagecreatetruecolor($width, $height);
        imagecopyresampled($output, $source, 0, 0, 0, 0, $width, $height, $oldWidth, $oldHeight);
        //resizeImage($image,$width,$height,$oldWidth, $oldHeight);
        echo $width ."<br>". $height ."<br>". $oldWidth."<br>". $oldHeight;

        //check if file is image and then output error message or image depending on outcome
        if (is_string($output)){
            echo "<h2>{$output}</h2>";
        } else {
            $thumb = 'images/resized' . $_FILES['image']['name'];
            imagejpeg($output, $thumb, 100);

            //unlink($image);
            echo "<img class='img-fluid rounded mb-4 mb-lg-0'  src='$thumb' />";
        }

    }
}

function imageHandler(){
    if($_POST) {
        if(is_array($_FILES)) {

            if (isset($_POST['aspect'])){
                $keepAspectRatio = 'on';
            } else {
                $keepAspectRatio = 'off';
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
            if (!$source_properties){
                echo "<h1>This file is not a genuine image</h1>";
                return;
            }
            $image_type = $source_properties[2];
            if( $image_type == IMAGETYPE_JPEG ) {
                $image_resource_id = imagecreatefromjpeg($file);
                $target_layer = imageResize($image_resource_id, $source_properties[0], $source_properties[1]);
                imagejpeg($target_layer, $_FILES['image']['name']);
                echo "<img class='img-fluid rounded mb-4 mb-lg-0'  src='{$_FILES['image']['name']}' />";

            }
            elseif( $image_type == IMAGETYPE_PNG ) {
                $image_resource_id = imagecreatefrompng($file);
                if (!$image_resource_id) {
                    echo "<h1>Error: This file is not an image</h1>";
                } else {

                    $target_layer = imageResize($image_resource_id, $source_properties[0], $source_properties[1]);
                    imagejpeg($target_layer, $_FILES['image']['name']);
                    echo "<img class='img-fluid rounded mb-4 mb-lg-0'  src='{$_FILES['image']['name']}' />";
                }
            }
        }
    } else {
        echo "<img class='img-fluid rounded mb-4 mb-lg-0' src='https://support.apple.com/library/content/dam/edam/applecare/images/en_US/social/supportapphero/camera-modes-hero.jpg' width='750' height='600' alt='...' />";
    }
}

function imageResize($image_resource_id,$width,$height) {
    $targetWidth = $_POST['width'];
    $targetHeight = $_POST['height'];
    // clean input
    $targetHeight = cleanInput($targetHeight);
    $targetWidth = cleanInput($targetWidth);

    //create resized image and paint old image over it
    $target_layer=imagecreatetruecolor($targetWidth,$targetHeight);
    imagecopyresampled($target_layer,$image_resource_id,0,0,0,0,$targetWidth,$targetHeight, $width,$height);
    return $target_layer;
}
?>