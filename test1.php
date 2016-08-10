<?php 
echo date('Y-m-d H:i:s').'<br>';
// $url = escapeshellarg('http://www.google.com');
$url = escapeshellarg('http://localhost/temp/pdf-to-html/output/57a2213c7f9a9/file2-10.html');
$image = '/var/www/html/temp/pdf-to-html/output/test5.jpeg';
$command = "/var/www/html/temp/pdf-to-html/vendor/h4cc/wkhtmltoimage-i386/bin/wkhtmltoimage-i386  --quality 100 --zoom 5 $url $image";
passthru($command, $status);
if ($status != 0) {
    echo "There was an error executing the command. Died with exit code: $status";
}

/* $stamp = imagecreatefrompng('images/watermark.png');
$im = imagecreatefromjpeg('images/picture.jpg');

// Set the margins for the stamp and get the height/width of the stamp image
$marge_right = 10;
$marge_bottom = 10;
$sx = imagesx($stamp);
$sy = imagesy($stamp);

// Copy the stamp image onto our photo using the margin offsets and the photo 
// width to calculate positioning of the stamp. 
imagecopy($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));

// Output and free memory
header('Content-type: image/png');
imagepng($im);
imagedestroy($im);
 */
//Set the Content Type


echo date('Y-m-d H:i:s').'<br>';

?>