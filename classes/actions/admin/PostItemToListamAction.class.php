<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class PostItemToListamAction extends AdminAction {

    public function service() {
        $item_id = intval($this->secure($_REQUEST['item_id']));
        $itemManager = new ItemManager($this->config, $this->args);
        $itemsForOrder = $itemManager->getItemsForOrder($item_id, $this->getUserId(), $this->getUserLevel(), true);

        $header = array("Content-type: multipart/form-data");
        /* $filePath = IMAGE_ROOT_DIR . "/items/" . $item_id . "_1_800_800.jpg";
          $post = array('file' => '@' . $filePath, "size" => filesize($filePath), "name" => $item_id . "_1_800_800.jpg");
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, 'http://www.list.am/img/upload.php');
          curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
          $result = curl_exec($ch);
          curl_close($ch);
         */

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:25.0) Gecko/20100101 Firefox/25.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'price' => $itemManager->exchangeFromUsdToAMD($itemsForOrder->getCustomerItemPrice()),
            'location' => $this->getCmsVar('listam_post_location_id'), //arabkir
            'your_email' => $this->getCmsVar('listam_post_email'),
            'password' => $this->getCmsVar('listam_account_password'),
            'phone_numbers' => $this->getCmsVar('listam_post_phone_number_1'),
            'phone_numbers__2' => $this->getCmsVar('listam_post_phone_number_2'),
            'title' => 'New ' . substr($itemsForOrder->getDisplayName(), 0, 75) . ' Warranty ' . $itemsForOrder->getWarranty() . ' Months',
            'agree' => 1,
            '_form_confirm' => 1,
            'verification_numberetoken' => 'S1ZOxa9+DvSOVrtE5fP9K0WlK6241Q2XQ+UL3em9AW0=',
            'verification_number' => '75183',
            'currency' => '0',
            'ad_type' => '0',
            '_form_action' => 'Post',
            'post_form_visited' => '1',
            'ufiles[]' => $item_id . "_1_800_800.jpg",
            'description' => substr($itemsForOrder->getDisplayName() . '<br><br>' . $itemsForOrder->getFullDescription(), 0, 16383)));
        curl_setopt($ch, CURLOPT_URL, 'http://www.list.am/add/8');

        $result = curl_exec($ch);
        var_dump($result);
        exit;
        curl_close($ch);
        //$captchaImgSrcStartPos = strpos($result, '<img')+10;
        //$captchaImgSrcEndPos = strpos($result, '"', $captchaImgSrcStartPos + 1);
        //echo substr($result, $captchaImgSrcStartPos, $captchaImgSrcEndPos-$captchaImgSrcStartPos);
        if (strpos($result, 'Հայտարարությունը ավելացված է') !== false) {
            $this->ok();
        } else {
            $this->error();
        }
    }

}

?>