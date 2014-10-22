<?php

require_once (CLASSES_PATH . "/actions/company/CompanyAction.class.php");
require_once (CLASSES_PATH . "/managers/AdminManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/util/ImageThumber.php");

/**
 * @author Vahagn Sookiasian
 */
class AddChangeItemAction extends CompanyAction {

    public function service() {
        $itemManager = ItemManager::getInstance($this->config, $this->args);

        $companyID = $this->secure($_REQUEST["company_id"]);
        $customer = $this->sessionManager->getUser();
        $userLevel = $customer->getLevel();
        $dbCustomer = $this->getCustomer();
        if ($userLevel == UserGroups::$COMPANY) {
            if ($customer->getId() != $companyID) {
                $jsonArr = array('status' => "err", "errText" => "System Error: You can not change other companies items!");
                echo "<script>var l= new parent.ngs.AddChangeItemAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
                return false;
            }
        } else if ($userLevel != UserGroups::$ADMIN) {
            $jsonArr = array('status' => "err", "errText" => "System Error: Access denied!");
            echo "<script>var l= new parent.ngs.AddChangeItemAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return false;
        }

        $item_title = $this->secure($_REQUEST["item_title"]);
        $short_description = $this->secure($_REQUEST["short_description_for_display"]);
        $full_description = $_REQUEST["full_description"];

        $item_price = $this->secure($_REQUEST["item_price"]);
        $item_vat_price = $this->secure($_REQUEST["item_vat_price"]);
        $item_price_amd = intval($this->secure($_REQUEST["item_price_amd"]));
        if ($item_price_amd > 0) {
            $item_price = $itemManager->exchangeFromAMDToUSD($item_price_amd);
        }
        $item_vat_price_amd = intval($this->secure($_REQUEST["item_vat_price_amd"]));
        if ($item_vat_price_amd > 0) {
            $item_vat_price = $itemManager->exchangeFromAMDToUSD($item_vat_price_amd);
        }
        $warranty_period = $this->secure($_REQUEST["warranty_period"]);
        $item_root_category = $this->secure($_REQUEST["item_root_category"]);
        $item_sub_categories_ids = $this->secure($_REQUEST["selected_sub_categories_ids"]);

        $item_model = $this->secure($_REQUEST["item_model"]);
        $item_brand = $this->secure($_REQUEST["item_brand"]);
        $item_price_sort_index = intval($this->secure($_REQUEST["item_price_sort_index"]));

        $item_available_till_days_count = $this->secure($_REQUEST["item_availability"]);
        $check_availability = $this->secure($_REQUEST["check_availability"]);
        $item_removed_pictures_ids = $this->secure($_REQUEST["item_removed_pictures_ids"]);

        $item_categories_ids_array[] = $item_root_category;
        // add root category

        if ($item_sub_categories_ids && strlen($item_sub_categories_ids) > 0) {
            $item_categories_ids_array = array_merge($item_categories_ids_array, explode(',', $item_sub_categories_ids));
            //add sub categories
        }


        //if item availibility need to be checked then $item_available_till_date field value should be in past
        //if item is not available then it should be hidden with past date
        $item_available_till_date = $this->getTimeStampByNextDaysCount($check_availability == '1' ? -1 : $item_available_till_days_count);

        $add_picture = $this->secure($_REQUEST["add_picture"]);

        $editedItemId = 0;
        if ($_REQUEST["selected_item_id"]) {
            $editedItemId = $this->secure($_REQUEST["selected_item_id"]);
        }


        if ($editedItemId == 0) {
            $itemId = $itemManager->addItem($item_title, $short_description, $full_description, $warranty_period, $item_price, $item_vat_price, $item_price_amd, $item_vat_price_amd, $companyID, $item_model, $item_brand, $item_categories_ids_array, $item_available_till_date, $item_price_sort_index, $dbCustomer->getEmail());
        } else {
            $unchange_item_date = $this->secure($_REQUEST["unchange_item_date"]);
            if ($unchange_item_date == 1) {
                $item_available_till_date = null;
            }
            $itemManager->updateItem($editedItemId, $item_title, $short_description, $full_description, $warranty_period, $item_price, $item_vat_price, $item_price_amd, $item_vat_price_amd, $companyID, $item_model, $item_brand, $item_categories_ids_array, $item_available_till_date, $item_price_sort_index, $dbCustomer->getEmail());
            $itemId = $editedItemId;
        }


        if ($item_removed_pictures_ids && strlen($item_removed_pictures_ids) > 0) {
            $item_removed_pictures_ids_array = explode(',', $item_removed_pictures_ids);
            $itemManager->removeItemPicturesByPicturesIndexes($itemId, $item_removed_pictures_ids_array);
        }

        if ($add_picture) {
            $itemDto = $itemManager->selectByPK($itemId);
            $logoCheck = $this->checkInputFile('item_picture');
            if ($logoCheck != 'ok') {
                $jsonArr = array('status' => "error", "errText" => $logoCheck);
                echo "<script>var l= new parent.ngs.AddChangeItemAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
                return false;
            }
            $added = $itemManager->addPicture($itemDto);
            if ($added !== true) {
                $jsonArr = array('status' => "err", "errText" => $added);
                echo "<script>var l= new parent.ngs.AddChangeItemAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
                return false;
            }
        }
        



        $jsonArr = array('status' => "ok");
        echo "<script>var l= new parent.ngs.AddChangeItemAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
        return true;
    }

    public static function getTimeStampByNextDaysCount($days_count) {
        $d = intval($days_count);
        return date("Y-m-d", time() + $d * 24 * 3600);
    }

}

?>