<?php

function makeThumb($imgSrc, $thumbSrc, $thumbnailWidth, $thumbnailHeight) {

    $fileNewName = $thumbSrc;
    //getting the image dimensions
    list($width_orig, $height_orig) = getimagesize($imgSrc);
    $imgType = exif_imagetype($imgSrc);
    if ($imgType == IMAGETYPE_GIF) {
        $myImage = imagecreatefromgif($imgSrc);
    }
    if ($imgType == IMAGETYPE_JPEG) {

        $myImage = imagecreatefromjpeg($imgSrc);
    }
    if ($imgType == IMAGETYPE_PNG) {

        $myImage = imagecreatefrompng($imgSrc);
    }

    $ratio_orig = $width_orig / $height_orig;

    if ($thumbnailWidth / $thumbnailHeight > $ratio_orig) {
        $new_height = $thumbnailWidth / $ratio_orig;
        $new_width = $thumbnailWidth;
    } else {
        $new_width = $thumbnailHeight * $ratio_orig;
        $new_height = $thumbnailHeight;
    }

    $x_mid = $new_width / 2;
    //horizontal middle
    $y_mid = $new_height / 2;
    //vertical middle

    $newImg = imagecreatetruecolor(round($new_width), round($new_height));

    $imgInfo = getimagesize($imgSrc);
    if (($imgInfo[2] == 1) OR ($imgInfo[2] == 3)) {

        imagealphablending($newImg, false);

        imagesavealpha($newImg, true);

        $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);

        imagefilledrectangle($newImg, 0, 0, round($new_width), round($new_height), $transparent);
    }

    imagecopyresampled($newImg, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
    $thumb = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);
    imagecopyresampled($thumb, $newImg, 0, 0, ($x_mid - ($thumbnailWidth / 2)), ($y_mid - ($thumbnailHeight / 2)), $thumbnailWidth, $thumbnailHeight, $thumbnailWidth, $thumbnailHeight);

    imagedestroy($newImg);
    imagedestroy($myImage);

    imagepng($thumb, $fileNewName, 0, PNG_ALL_FILTERS);

    // Free up memory
    imagedestroy($thumb);
    return true;
}

function resizeImageToGivenType($img, $newfilename, $w, $h, $type) {

    //Check if GD extension is loaded
    if (!extension_loaded('gd') && !extension_loaded('gd2')) {
        trigger_error("GD is not loaded", E_USER_WARNING);
        return false;
    }

    //Get Image size info
    $imgInfo = getimagesize($img);
    switch ($imgInfo[2]) {
        case IMAGETYPE_GIF :
            $im = imagecreatefromgif($img);
            break;
        case IMAGETYPE_JPEG :
            $im = imagecreatefromjpeg($img);
            break;
        case IMAGETYPE_PNG :
            $im = imagecreatefrompng($img);
            break;
        default :
            trigger_error('Unsupported filetype!', E_USER_WARNING);
            break;
    }

    //If image dimension is smaller, do not resize
    if ($imgInfo[0] <= $w && $imgInfo[1] <= $h) {
        $nHeight = $imgInfo[1];
        $nWidth = $imgInfo[0];
    } else {
        //yeah, resize it, but keep it proportional
        if ($w / $imgInfo[0] < $h / $imgInfo[1]) {
            $nWidth = $w;
            $nHeight = $imgInfo[1] * ($w / $imgInfo[0]);
        } else {
            $nWidth = $imgInfo[0] * ($h / $imgInfo[1]);
            $nHeight = $h;
        }
    }

    $nWidth = round($nWidth);
    $nHeight = round($nHeight);
    
    $newImg = imagecreatetruecolor($nWidth, $nHeight);
    $backgroundColor = imagecolorallocate($newImg, 255, 255, 255);
    imagefill($newImg, 0, 0, $backgroundColor);   

    imagecopyresampled($newImg, $im, 0, 0, 0, 0, $nWidth, $nHeight, $imgInfo[0], $imgInfo[1]);

    if (strtolower($type) === 'png') {
        imagepng($newImg, $newfilename);
    } else if (strtolower($type) === 'gif') {
        imagegif($newImg, $newfilename);
    } else if (strtolower($type) === 'jpg') {
        imagejpeg($newImg, $newfilename);
    } else {
        trigger_error('Failed resize image!', E_USER_WARNING);
    }
    return $newfilename;
}

function resizeImage($img, $newfilename, $w, $h) {

    //Check if GD extension is loaded
    if (!extension_loaded('gd') && !extension_loaded('gd2')) {
        trigger_error("GD is not loaded", E_USER_WARNING);
        return false;
    }

    //Get Image size info
    $imgInfo = getimagesize($img);
    switch ($imgInfo[2]) {
        case 1 :
            $im = imagecreatefromgif($img);
            break;
        case 2 :
            $im = imagecreatefromjpeg($img);
            break;
        case 3 :
            $im = imagecreatefrompng($img);
            break;
        default :
            trigger_error('Unsupported filetype!', E_USER_WARNING);
            break;
    }

    //If image dimension is smaller, do not resize
    if ($imgInfo[0] <= $w && $imgInfo[1] <= $h) {
        $nHeight = $imgInfo[1];
        $nWidth = $imgInfo[0];
    } else {
        //yeah, resize it, but keep it proportional
        if ($w / $imgInfo[0] < $h / $imgInfo[1]) {
            $nWidth = $w;
            $nHeight = $imgInfo[1] * ($w / $imgInfo[0]);
        } else {
            $nWidth = $imgInfo[0] * ($h / $imgInfo[1]);
            $nHeight = $h;
        }
    }
    $nWidth = round($nWidth);
    $nHeight = round($nHeight);

    $newImg = imagecreatetruecolor($nWidth, $nHeight);

    /* Check if this image is PNG or GIF, then set if Transparent */
    if (($imgInfo[2] == 1) OR ($imgInfo[2] == 3)) {
        imagealphablending($newImg, false);
        imagesavealpha($newImg, true);
        $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
        imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
    }
    imagecopyresampled($newImg, $im, 0, 0, 0, 0, $nWidth, $nHeight, $imgInfo[0], $imgInfo[1]);

    //Generate the file, and rename it to $newfilename
    switch ($imgInfo[2]) {
        case 1 :
            imagegif($newImg, $newfilename);
            break;
        case 2 :
            imagejpeg($newImg, $newfilename);
            break;
        case 3 :
            imagepng($newImg, $newfilename);
            break;
        default :
            trigger_error('Failed resize image!', E_USER_WARNING);
            break;
    }

    return $newfilename;
}

?>