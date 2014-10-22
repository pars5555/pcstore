<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/ItemWarrantiesManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class AddChangeItemWarrantyAction extends GuestAction {

    public function service() {
        $itemWarrantiesManager = new ItemWarrantiesManager($this->config, $this->args);

        $action_type = $this->secure($_REQUEST["action_type"]);

        if (isset($_REQUEST["warranty_item_id"])) {
            $warranty_item_id = $this->secure($_REQUEST["warranty_item_id"]);
            $wDto = $itemWarrantiesManager->selectByPK($warranty_item_id);
            if ($wDto->getCompanyId() != $this->sessionManager->getUser()->getId()) {
                $jsonArr = array('status' => "err", "errText" => "System Error: Permission denied!");
                echo json_encode($jsonArr);
                return false;
            }
        }
        //todo check company id and item warranty company_id should be the same
        if ($action_type === "delete") {
            $warranty_item_id = $this->secure($_REQUEST["warranty_item_id"]);
            $itemWarrantiesManager->deleteByPK($warranty_item_id);
        } else {

            $serial_number = $this->secure($_REQUEST["serial_number"]);
            $item_buyer = $this->secure($_REQUEST["item_buyer"]);
            $customer_warranty_start_date = $this->secure($_REQUEST["customer_warranty_start_date"]);
            $item_category = $this->secure($_REQUEST["item_category"]);
            $warranty_period = $this->secure($_REQUEST["warranty_period"]);
            $supplier = $this->secure($_REQUEST["supplier"]);
            $supplier_warranty_start_date = $this->secure($_REQUEST["supplier_warranty_start_date"]);
            $supplier_warranty_period = $this->secure($_REQUEST["supplier_warranty_period"]);



            $companyId = $this->sessionManager->getUser()->getId();
            $valFields = $this->validateFields($serial_number, $item_buyer, $item_category, $warranty_period);
            if ($valFields !== 'ok') {
                $jsonArr = array('status' => "err", "errText" => $valFields);
                echo json_encode($jsonArr);
                return false;
            }
            if ($action_type === "add") {
                $itemWarrantiesManager->addItemWarranty($companyId, $serial_number, $item_buyer, $item_category, $warranty_period, $customer_warranty_start_date, $supplier, $supplier_warranty_start_date, $supplier_warranty_period);
            } elseif ($action_type === "edit") {
                $warranty_item_id = $this->secure($_REQUEST["warranty_item_id"]);
                $itemWarrantiesManager->editItemWarranty($warranty_item_id, $companyId, $serial_number, $item_buyer, $item_category, $warranty_period, $customer_warranty_start_date, $supplier, $supplier_warranty_start_date, $supplier_warranty_period);
            }
        }
        $jsonArr = array('status' => "ok");
        echo json_encode($jsonArr);
        return true;
    }

    public function getRequestGroup() {
        return RequestGroups::$companyRequest;
    }

    public function validateFields($serial_number, $item_buyer, $item_category, $warranty_period) {
        if (!$serial_number || strlen($this->secure($serial_number)) < 6) {
            return "Serial Number must have at least 6 charecter!";
        }
        if (!isset($warranty_period) || strlen($this->secure($warranty_period)) === 0) {
            return "Warranty period error!";
        }
        return 'ok';
    }

}

?>