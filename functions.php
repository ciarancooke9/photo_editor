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
function emptyFieldHandling($width, $height, $keepAspectRatio, $file){
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
        echo "<h2>If you want to keep aspect ratio you must only provide either height or width, not both</h2>";
        return false;
    } else {
        return true;
    }
}

///this function will accept an image file location & parameters to reshape the file (width & height)
function imageReshaper($image,$height, $width, $keepAspectRatio){
    ///get height and with Dimensions of original image
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
        imagecopyresized($output, $source, 0, 0, 0, 0, $width, $height, $oldWidth, $oldHeight);
        echo $width ."<br>". $height ."<br>". $oldWidth."<br>". $oldHeight;
        return $output;
    }
}

function photoEditJPEG(){
if (!$_POST) {
    echo "<img class='img-fluid rounded mb-4 mb-lg-0' src='https://support.apple.com/library/content/dam/edam/applecare/images/en_US/social/supportapphero/camera-modes-hero.jpg' width='750' height='600' alt='...' />";
} elseif ($_POST) {
    $width = $_POST['width'];
    $height = $_POST['height'];
    //check valid input for height and width
    $height = cleanInput($height);
    $width = cleanInput($width);
    //$width = checkInputIsInteger($width);
    //$height = checkInputIsInteger($height);

    if (isset($_POST['aspect'])){
        $keepAspectRatio = 'on';
    } else {
        $keepAspectRatio = 'off';
    }

    //check fields and file upload was not empty or missing data
    $fieldsNeeded = emptyFieldHandling($width, $height, $keepAspectRatio, $_FILES['image']['name']);
    if(!$fieldsNeeded){
        return;
    }

    $post_image = $_FILES['image']['name'];
    $post_image_temp = $_FILES['image']['tmp_name'];
    echo "<h1>$post_image</h1>";
    echo "<h2>Height:$height</h2>". ' ' . "<h2>Width:$width</h2>";

    //store file
    $original = storeFiles($post_image,$post_image_temp, 'images/');

    //reshape image
    $reshapedImage = imageReshaper($original, $height, $width, $keepAspectRatio);

    //check if file is image and then output error message or image depending on outcome
    if (is_string($reshapedImage)){
        echo "<h2>{$reshapedImage}</h2>";
    } else {
        $thumb = 'images/resized' . $_FILES['image']['name'];
        imagejpeg($reshapedImage, $thumb, 100);

        unlink($original);
        echo "<img class='img-fluid rounded mb-4 mb-lg-0'  src='$thumb' height='$height' width='$width'/>";
        }
    }
}
?>