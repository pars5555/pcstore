<?php

class ImageDiff {

    // not bigger 20
    private static $matrix = 20;

    private static function getImageInfo($image_path) {
        list($width, $height, $type, $attr) = getimagesize($image_path);
        $image_type = '';
        switch ($type) {
            case IMAGETYPE_JPEG:
                $image_type = 'jpeg';
                break;
            case IMAGETYPE_GIF:
                $image_type = 'gif';
                break;
            case IMAGETYPE_PNG:
                $image_type = 'png';
                break;
            case IMAGETYPE_BMP:
                $image_type = 'bmp';
                break;
            default:
                $image_type = '';
        }
        return array('width' => $width, 'height' => $height, 'type' => $image_type);
    }

    private static function generateArray($image_path) {
        $image_info = self::getImageInfo($image_path);

        $func = 'imagecreatefrom' . $image_info['type'];
        if (function_exists($func)) {
            $main_img = $func($image_path);
            $tmp_img = imagecreatetruecolor(self::$matrix, self::$matrix);
            imagecopyresampled($tmp_img, $main_img, 0, 0, 0, 0, self::$matrix, self::$matrix, $image_info['width'], $image_info['height']);

            $pixelmap = array();
            $average_pixel = 0;
            for ($x = 0; $x < self::$matrix; $x++) {
                for ($y = 0; $y < self::$matrix; $y++) {
                    $color = imagecolorat($tmp_img, $x, $y);
                    $color = imagecolorsforindex($tmp_img, $color);
                    $pixelmap[$x][$y] = 0.299 * $color['red'] + 0.587 * $color['green'] + 0.114 * $color['blue'];
                    $average_pixel += $pixelmap[$x][$y];
                }
            }

            $average_pixel = $average_pixel / (self::$matrix * self::$matrix);

            imagedestroy($main_img);
            imagedestroy($tmp_img);

            for ($x = 0; $x < self::$matrix; $x++) {
                for ($y = 0; $y < self::$matrix; $y++) {

                    $row = ($pixelmap[$x][$y] == 0) ? 0 : (round(2 * (($pixelmap[$x][$y] > $average_pixel) ? $pixelmap[$x][$y] / $average_pixel : ($average_pixel / $pixelmap[$x][$y]) * -1)));
                    $row = ($x + 10) . ($y + 10) . (255 + $row);
                    $result[] = intval($row);
                }
            }
            return $result;
        } else {
            //raise exception
            throw new Exception('File type  not supported!');
        }
    }

    public static function diffImages($image_path1, $image_path2) {
        if (!file_exists($image_path1) || !file_exists($image_path2)) {
            return false;
        }
        $array1 = self::generateArray($image_path1);
        $array2 = self::generateArray($image_path2);
        $result = count(array_intersect($array1, $array2));
        return round($result / ( self::$matrix * self::$matrix ), 6);
    }

}

?>