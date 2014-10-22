<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");

/**
 * @author Vahagn Sookiasian
 * @site http://naghashyan.com
 * @mail vahagnsookaisyan@gmail.com
 * @year 2010-2012
 */
class GetImageAction extends GuestAction {

    public function service() {
        if ($this->args[0] == "big_logo") {
            $cover = IMAGE_ROOT_DIR . "/company_logo/company_" . $this->args[1] . "_logo_120_75.png";
        } else if ($this->args[0] == "service_big_logo") {
            $cover = IMAGE_ROOT_DIR . "/service_company_logo/service_company_" . $this->args[1] . "_logo_55_30.png";
        } else if ($this->args[0] == "small_logo") {
            $cover = IMAGE_ROOT_DIR . "/company_logo/company_" . $this->args[1] . "_logo_55_30.png";
        } else if ($this->args[0] == "sc_big_logo") {
            $cover = IMAGE_ROOT_DIR . "/service_company_logo/service_company_" . $this->args[1] . "_logo_120_75.png";
        } else if ($this->args[0] == "sc_small_logo") {
            $cover = IMAGE_ROOT_DIR . "/service_company_logo/service_company_" . $this->args[1] . "_logo_55_30.png";
        } else if ($this->args[0] == "item_30_30") {
            $cover = IMAGE_ROOT_DIR . "/items/" . $this->args[1] . '_' . $this->args[2] . "_30_30" . ".jpg";
        } else if ($this->args[0] == "item_60_60") {
            $cover = IMAGE_ROOT_DIR . "/items/" . $this->args[1] . '_' . $this->args[2] . "_60_60" . ".jpg";
        } else if ($this->args[0] == "item_150_150") {
            $cover = IMAGE_ROOT_DIR . "/items/" . $this->args[1] . '_' . $this->args[2] . "_150_150" . ".jpg";
            $this->showImage($cover, true);
            exit;
        } else if ($this->args[0] == "item_400_400") {
            $cover = IMAGE_ROOT_DIR . "/items/" . $this->args[1] . '_' . $this->args[2] . "_400_400" . ".jpg";
            $this->showImage($cover, true);
            exit;
        } else if ($this->args[0] == "item_800_800") {
            $cover = IMAGE_ROOT_DIR . "/items/" . $this->args[1] . '_' . $this->args[2] . "_800_800" . ".jpg";
            if (!file_exists($cover)) {
                $cover = IMAGE_ROOT_DIR . "/items/" . $this->args[1] . '_' . $this->args[2] . "_400_400" . ".jpg";
            }
            $this->showImage($cover, true);
            exit;
        }
        $this->showImage($cover);
        exit;
    }

    private function showImage($picture, $watermark = false) {

        if (file_exists($picture)) {
            $extension = explode('.', $picture);
            $extension = end($extension);
            $im = null;
            switch (strtolower($extension)) {
                case 'jpg':
                    $im = imagecreatefromjpeg($picture);
                    $itemPictureWidth = imagesx($im);
                    $itemPictureHeight = imagesy($im);

                    $stamp = imagecreatefrompng(IMG_ROOT_DIR . "/pcstorestamp.png");
                    // Set the margins for the stamp and get the height/width of the stamp image                    
                    $sx = imagesx($stamp);
                    $sy = imagesy($stamp);
                    $height = (($sy * $itemPictureWidth) / $sx);

                    imagecopyresized($im, $stamp, 0, $itemPictureHeight / 2 - $height / 2, 0, 0, $itemPictureWidth, $height, $sx, $sy);
                    header('Content-type: image/jpg');
                    imagejpeg($im);
                    break;
                case 'png':
                    $im = imagecreatefrompng($picture);
                    header('Content-type: image/png');
                    imagepng($im);
                    break;
                default:
                    break;
            }



            /*

              $last_modified_time = filemtime($cover);
              $size = getimagesize($cover);
              $mime = $size['mime'];
              header("Content-type: " . $mime);
              header("Last-Modified: " . gmdate("D, d M Y H:i:s", $last_modified_time) . " GMT");

              if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time) {
              header("HTTP/1.1 304 Not Modified");
              return true;
              }

              header("Cache-Control: private, max-age=10800, pre-check=10800");
              header("Pragma: private");

              header("Content-Length: " . filesize($cover));
              // imagejpeg($cover);
              // imagedestroy($im);
              $fh = fopen($cover, "rb");

              while (!feof($fh)) {
              print(fread($fh, 4094));
              }
              fclose($fh); */
        }
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

    protected function logRequest() {
        return false;
    }

}

?>