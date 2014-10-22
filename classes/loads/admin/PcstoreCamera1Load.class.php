<?php

require_once (CLASSES_PATH . "/loads/admin/AdminLoad.class.php");
require_once (CLASSES_PATH . "/managers/OnlineUsersManager.class.php");
require_once (CLASSES_PATH . "/util/ImageDiff.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class PcstoreCamera1Load extends AdminLoad {

    public function load() {        
      /*  $i1 = DATA_DIR . '/images/1.jpg';
        $i2 = DATA_DIR . '/images/2.jpg';
        $t = microtime(true);        
        $ret = ImageDiff::diffImages($i1, $i2);
        
        var_dump($ret, microtime(true)-$t);exit;*/
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/admin/pcstore_camera_1.tpl";
    }

    /**
     * Image Comparing Function (C)2011 Robert Lerner, All Rights Reserved
     * $image1                     STRING/RESOURCE          Filepath and name to PNG or passed image resource handle
     * $image2                    STRING/RESOURCE          Filepath and name to PNG or passed image resource handle
     * $RTolerance               INTEGER (0-/+255)     Red Integer Color Deviation before channel flag thrown
     * $GTolerance               INTEGER (0-/+255)     Green Integer Color Deviation before channel flag thrown
     * $BTolerance               INTEGER (0-/+255)     Blue Integer Color Deviation before channel flag thrown
     * $WarningTolerance     INTEGER (0-100)          Percentage of channel differences before warning returned
     * $ErrorTolerance          INTEGER (0-100)          Percentage of channel difference before error returned

     */
    private function imageCompare($image1, $image2, $RTolerance = 0, $GTolerance = 0, $BTolerance = 0, $WarningTolerance = 1, $ErrorTolerance = 5) {
        if (is_resource($image1)) {
            $im = $image1;
        } else {
            if (!$im = imagecreatefromjpeg($image1)) {
                trigger_error("Image 1 could not be opened", E_USER_ERROR);
            }
        }
        if (is_resource($image2)) {
            $im2 = $image2;
        } else {
            if (!$im2 = imagecreatefromjpeg($image2)) {
                trigger_error("Image 2 could not be opened", E_USER_ERROR);
            }
        }

        $OutOfSpec = 0;
        if (imagesx($im) != imagesx($im2)) {
            die("Width does not match.");
        }
        if (imagesy($im) != imagesy($im2)) {
            die("Height does not match.");
        }

        //By columns
        for ($width = 0; $width <= imagesx($im) - 1; $width++) {
            for ($height = 0; $height <= imagesy($im) - 1; $height++) {
                $rgb1 = imagecolorat($im, $width, $height);
                $r1 = ($rgb1 >> 16) & 0xFF;
                $g1 = ($rgb1 >> 8) & 0xFF;
                $b1 = $rgb1 & 0xFF;

                $rgb2 = imagecolorat($im2, $width, $height);
                $r2 = ($rgb2 >> 16) & 0xFF;
                $g2 = ($rgb2 >> 8) & 0xFF;
                $b2 = $rgb2 & 0xFF;

                if (!($r1 >= $r2 - $RTolerance && $r1 <= $r2 + $RTolerance)) {
                    $OutOfSpec++;
                }

                if (!($g1 >= $g2 - $GTolerance && $g1 <= $g2 + $GTolerance)) {
                    $OutOfSpec++;
                }

                if (!($b1 >= $b2 - $BTolerance && $b1 <= $b2 + $BTolerance)) {
                    $OutOfSpec++;
                }
            }
        }
        $TotalPixelsWithColors = (imagesx($im) * imagesy($im)) * 3;

        $RET['PixelsByColors'] = $TotalPixelsWithColors;
        $RET['PixelsOutOfSpec'] = $OutOfSpec;

        if ($OutOfSpec != 0 && $TotalPixelsWithColors != 0) {
            $PercentOut = ($OutOfSpec / $TotalPixelsWithColors) * 100;
            $RET['PercentDifference'] = $PercentOut;
            if ($PercentOut >= $WarningTolerance) {//difference triggers WARNINGTOLERANCE%
                $RET['WarningLevel'] = TRUE;
            }
            if ($PercentOut >= $ErrorTolerance) { //difference triggers ERRORTOLERANCE%
                $RET['ErrorLevel'] = TRUE;
            }
        }

        RETURN $RET;
    }

}

?>