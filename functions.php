<?php


///this is a function to store a file
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
    ///reshape image and output it
    $source = imagecreatefromjpeg($image);
    $output = imagecreatetruecolor($width, $height);
    imagecopyresized($output, $source, 0, 0, 0, 0, $width, $height, $oldWidth, $oldHeight);
    return $output;
}

function photoEdit(){
if (!$_POST) {
echo "<img class='img-fluid rounded mb-4 mb-lg-0' src='https://support.apple.com/library/content/dam/edam/applecare/images/en_US/social/supportapphero/camera-modes-hero.jpg' alt='...' />";
} elseif ($_POST) {
print_r($_POST);
$width = $_POST['width'];
$height = $_POST['height'];
if (isset($_POST['aspect'])){
$keepAspectRatio = 'on';
} else {
    $keepAspectRatio = 'off';
}
$post_image = $_FILES['image']['name'];
$post_image_temp = $_FILES['image']['tmp_name'];

echo "<h1>$post_image</h1>";
echo "<h2>Height:$height</h2>". ' ' . "<h2>Width:$width</h2>";


$original = storeFiles($post_image,$post_image_temp, 'images/');
echo $original;

$reshapedImage = imageReshaper($original, $height, $width, $keepAspectRatio);
$thumb = 'images/resized' . $_FILES['image']['name'];
imagejpeg($reshapedImage, $thumb, 100);

unlink($original);
echo "<img class='img-fluid rounded mb-4 mb-lg-0'  src='$thumb' height='$height' width='$width'/>";
///unlink($thumb);
}
}
?>