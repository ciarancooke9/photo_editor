<?php

function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}

///this is a function to store a file, accepts filename, temporary filename and the destination directory
/// it then returns the file location
function storeFiles($fileName, $tempFileName){
    $fileLocation = 'images' . $fileName;

    move_uploaded_file($tempFileName, $fileLocation);

    return $fileLocation;
}

///Function to maintain aspect ratio of an image, accepts target height and width and the original image height and width as parameters
function keepAspectRatio($width, $height, $oldWidth, $oldHeight){
    if (!$width == '')        //height missing, return new height
    {
        $factor = (float)$width / (float)$oldWidth;
        $height = $factor * $oldHeight;
        return $height;
    }
    else if (!$height == '')      //width missing, return new width
    {
        $factor = (float)$height / (float)$oldHeight;
        $width = $factor * $oldWidth;
        return $width;
    }
    return false;
}

//this function handles form fields being incorrectly filled out, it also checks for a valid file size
function emptyFieldHandler($width, $height, $keepAspectRatio, $file){ // TODO refactor to return an array with not template code
    $replacementImage = "<img class='img-fluid rounded mb-4 mb-lg-0' src='https://support.apple.com/library/content/dam/edam/applecare/images/en_US/social/supportapphero/camera-modes-hero.jpg' width='750' height='600' alt='...' />";
    $defaultErrorMessage = "<h2>Please fill out both fields or one with the keep aspect ratio box ticked</h2>";
    if (!$file){ //No file uploaded
        $message = 'Please choose a file to upload!';
        $valid = false;
    } elseif ($width == '' && $height == ''){ //image & height missing
        $message = $defaultErrorMessage;
        $valid = false;
    } elseif ($width == '' && !$keepAspectRatio) { //width missing without keep aspect ratio checked
        $message = $defaultErrorMessage;
        $valid = false;
    } elseif ($height == '' && !$keepAspectRatio) { //height missing without keep aspect ratio checked
        $message = $defaultErrorMessage;
        $valid = false;
    } elseif ($width != '' && $height != '' && $keepAspectRatio) { //both height & width missing but aspect ratio checked
        $message = 'If you want to keep aspect ratio you must provide either height or width, not both';
        $valid = false;
    } elseif ($_FILES['image']['size'] >= 2097152){ ///image too large
        $message = "File too large must be kept under 2MB";
        $valid = false;
    } else {
        $message = null;
        $valid = true;
    }

    return ['valid_field_input' => $valid, 'message' => $message];

}

// this function makes sure the image reshaping process & form validation only begins once a form & image is being submitted
function validateFormUpload(){
    if (!$_POST){
        $message = "<img class='img-fluid rounded mb-4 mb-lg-0' src='https://support.apple.com/library/content/dam/edam/applecare/images/en_US/social/supportapphero/camera-modes-hero.jpg' width='750' height='600' alt='...' />";
        return ['validation' => false, 'output' => $message];
    }

    if (!is_array($_FILES)){
        $message = "<h1>Please upload only jpeg or png files</h1>";
        return ['validation' => false, 'output' => $message];
    }
    return ['validation' => true, 'output' => ''];
}

// this file checks the inputs from form, validates them and returns an array with the attributes needed to reshape the image
function formHandler()
{
    //Validating form has been submitted with file
    $validForm = validateFormUpload();
    if (!$validForm['validation']) {
        return $validForm['output'];
    }
    //Ends Validation

    // check for keep aspect ratio option
    $keepAspectRatio = isset($_POST['aspect']);

    //check for image quality parameter, keep 100% photo quality if not found
    $imageQuality = isset($_POST['imageQuality']) && ($_POST['imageQuality'] != '') ? $_POST['imageQuality'] : 100;

    //check fields and file upload was not empty or missing data or too large
    $fieldsNeeded = emptyFieldHandler($_POST['width'], $_POST['height'], $keepAspectRatio, $_FILES['image']['name']);

    if (!$fieldsNeeded['valid_field_input']) {
        return $fieldsNeeded['message'];
    }

    //extract image from FILES array
    $file = $_FILES['image']['tmp_name'];

    //$source properties array = width, height and file type
    $source_properties = getimagesize($file);
    //check is file genuine image
    if (!$source_properties) {
        return "<h1>This file is not a genuine image</h1>";
    }

    return ['image' => $file, 'aspect_ratio' => $keepAspectRatio, 'image_quality' => $imageQuality,
        'target_width' => $_POST['width'], 'target_height' => $_POST['height'],
        'watermark' => $_POST['watermark'], 'watermark_color' => $_POST['color'], 'watermark_position' => $_POST['position']];
}


//function which accepts an image
function imageEditor($imageFile ,$keepAspectRatio, $imageQuality, $targetWidth, $targetHeight){
    $sourceImageProperties = getimagesize($imageFile);
    if( $sourceImageProperties[2] == IMAGETYPE_JPEG ) {
        $image_resource_id = imagecreatefromjpeg($imageFile);
        $target_layer = imageResize($image_resource_id, $sourceImageProperties[0], $sourceImageProperties[1], $keepAspectRatio, $targetWidth, $targetHeight);
        $target_layer = !$_POST['watermark'] == '' ? watermarkImage($target_layer) : $target_layer;
        move_uploaded_file(imagejpeg($target_layer, 'images/' . $_FILES['image']['name'], $imageQuality), 'images/' . $_FILES['image']['name']);

        echo "<img class='img-fluid rounded mb-4 mb-lg-0'  src='images/{$_FILES['image']['name']}' />";
    }
    // Branch for PNG images
    elseif( $sourceImageProperties[2] == IMAGETYPE_PNG ) {
        $image_resource_id = imagecreatefrompng($imageFile);
        $target_layer = imageResize($image_resource_id, $sourceImageProperties[0], $sourceImageProperties[1],$keepAspectRatio, $targetWidth, $targetHeight);
        $target_layer = !$_POST['watermark'] == '' ? watermarkImage($target_layer) : $target_layer;
        move_uploaded_file(imagejpeg($target_layer, 'images/' . $_FILES['image']['name'], $imageQuality), 'images/' . $_FILES['image']['name']);
        echo "<img class='img-fluid rounded mb-4 mb-lg-0'  src='images/{$_FILES['image']['name']}'/>";
        }
}

//This function is passed the original image, its width and height and the keep aspect ratio option and gives back the resized image
function imageResize($image_resource_id,$width,$height,$keepAspectRatio, $targetWidth, $targetHeight) {
    // clean input
    $targetHeight = cleanInput($targetHeight);
    $targetWidth = cleanInput($targetWidth);

    echo "<h2>Height:$targetHeight</h2>". ' ' . "<h2>Width:$targetWidth</h2>";

    //perform keep aspect ratio function if selected
    if ($keepAspectRatio && $targetWidth != ''){
        $targetHeight = keepAspectRatio($targetWidth, $targetHeight, $width, $height);
    } elseif ($keepAspectRatio && $targetHeight != ''){
        $targetWidth = keepAspectRatio($targetWidth, $targetHeight, $width, $height);
    }

    //create resized image and paint old image over it
    $target_layer=imagecreatetruecolor($targetWidth,$targetHeight);
    imagecopyresampled($target_layer,$image_resource_id,0,0,0,0,$targetWidth,$targetHeight, $width,$height);
    return $target_layer;
}
//this function will add a watermark to the image
function watermarkImage($image){
    $watermarkParameters = formHandler(); //get watermark form inputs

    $text = $watermarkParameters['watermark'];
    $text = cleanInput($text);
    $font = "var/www/html/git/photo_editor/DejaVuSans.ttf"; //select font -- windows font file C:\Windows\Fonts\arial.ttf

    //assign watermark color
    $fontColor = hexColorAllocate($image, $watermarkParameters['watermark_color']);

    //assign watermark position
    $sizeAndPositionArray = watermarkPositionAndSize($watermarkParameters['target_width'], $watermarkParameters['target_height'], $watermarkParameters['watermark_position']);

    imagettftext($image, $sizeAndPositionArray[0], $sizeAndPositionArray[1],$sizeAndPositionArray[2], $sizeAndPositionArray[3], $fontColor, $font, $text); //choose watermark position

    return $image;
}

//this function will accept an image and an option from form data and return an array with the size,angle, x coordinate, y coordinate
function watermarkPositionAndSize($targetWidth, $targetHeight, $position){

    //switch statement to handle different options
    $size = ($targetWidth + $targetHeight) / 100; //statement to keep image size consistent with different image sizes and shapes
    $sizeAndPositionArray = array($size, 0, 28, 54);
    switch ($position){
        case "topLeft":
            break;
        case "topRight":
            $firstLetterPosition = $targetWidth - ($size * 7); //to keep watermark within borders of image
            $sizeAndPositionArray = array($size, 0, $firstLetterPosition, 54);
            break;
        case "bottomLeft":
            $watermarkHeight = $targetHeight - ($size); //to keep watermark within borders of image
            $sizeAndPositionArray = array($size, 0, 28, $watermarkHeight);
            break;
        case "bottomRight":
            $firstLetterPosition = $targetWidth - ($size * 7);
            $watermarkHeight = $targetHeight - ($size);
            $sizeAndPositionArray = array($size, 0, $firstLetterPosition, $watermarkHeight);
            break;
        case "centreDiagonal":
            $firstLetterPosition = $targetWidth / 2;
            $watermarkHeight = $targetHeight / 2;
            $sizeAndPositionArray = array($size, 315, $firstLetterPosition, $watermarkHeight);
            break;
        case "centreStraight":
            $firstLetterPosition = $targetWidth / 2;
            $watermarkHeight = $targetHeight / 2;
            $sizeAndPositionArray = array($size, 0, $firstLetterPosition, $watermarkHeight);
            break;
    }
    return $sizeAndPositionArray;
}

//function to convert hex color to rgb, accepts image and hex code from form input,
//Returns a color identifier representing the color composed of the given RGB components.
function hexColorAllocate($image,$hex){
    $hex = ltrim($hex,'#');
    $red = hexdec(substr($hex,0,2));
    $green = hexdec(substr($hex,2,2));
    $blue = hexdec(substr($hex,4,2));
    return imagecolorallocatealpha($image, $red, $green, $blue, 75);
}
?>