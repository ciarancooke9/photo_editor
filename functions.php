<?php
function photoEdit(){
if (!$_POST) {
echo "<img class='img-fluid rounded mb-4 mb-lg-0' src='https://support.apple.com/library/content/dam/edam/applecare/images/en_US/social/supportapphero/camera-modes-hero.jpg' alt='...' />";
} elseif ($_POST) {
print_r($_POST);
$width = $_POST['width'];
$height = $_POST['height'];
$post_image = $_FILES['image']['name'];
$post_image_temp = $_FILES['image']['tmp_name'];
echo "<h1>$post_image</h1>";
echo "<h2>Height:$height</h2>". ' ' . "<h2>Width:$width</h2>";

move_uploaded_file($post_image_temp, "images/$post_image");
$original = "images/".$_FILES['image']['name'];

list($oldheight, $oldwidth) = getimagesize($original);

if (isset($_POST['aspect'])){
if (!$width == '')
{
$factor = (float)$width / (float)$oldwidth;
$height = $factor * $oldheight;
}
else if (!$height == '')
{
$factor = (float)$height / (float)$oldheight;
$width = $factor * $oldwidth;
}
}

$newfile = imagecreatefromjpeg($original);
$thumb = 'images/blabla' . $_FILES['image']['name'];
$resized = imagecreatetruecolor($width, $height);
imagecopyresampled($resized, $newfile, 0, 0, 0, 0, $width, $height, $oldwidth, $oldheight);
imagejpeg($resized, $thumb, 100);
unlink($original);
echo "<img class='img-fluid rounded mb-4 mb-lg-0'  src='$thumb' height='$height' width='$width' />";
///unlink($thumb);
}
}
?>