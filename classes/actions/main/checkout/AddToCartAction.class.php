<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");

require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/CustomerCartManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class AddToCartAction extends GuestAction {

    public function service() {
        $customer = $this->getCustomer();
        $customerEmail = strtolower($customer->getEmail());
        $customerCartManager = CustomerCartManager::getInstance($this->config, $this->args);
        if (isset($_REQUEST['update_basket_count'])) {
            $totalCount = $customerCartManager->getCustomerCartTotalCount($customerEmail);
            $jsonArr = array('status' => "ok", "cart_items_count" => $totalCount);
            echo json_encode($jsonArr);
            return true;
        }

        $add_count = 1;
        if (isset($_REQUEST['add_count'])) {
            $add_count = $this->secure($_REQUEST['add_count']);
        }

        if (isset($_REQUEST['item_id'])) {
            $item_id = $this->secure($_REQUEST['item_id']);
        }

        $itemManager = ItemManager::getInstance($this->config, $this->args);
        if (isset($item_id)) {
            $itemDto = $itemManager->selectByPK($item_id);
            if (!isset($itemDto)) {
                $jsonArr = array('status' => "err", "errText" => "Item is no more available!");
                echo json_encode($jsonArr);
                return false;
            }
            $last_dealer_price = $itemDto->getDealerPrice();
        }
        $customerCartManager->addToCart($customerEmail, $item_id, 0, $last_dealer_price, $add_count);

        $totalCount = $customerCartManager->getCustomerCartTotalCount($customerEmail);
        $jsonArr = array('status' => "ok", "cart_items_count" => $totalCount);
        echo json_encode($jsonArr);
        return true;
    }

    public function getRequestGroup() {
        return RequestGroups::$userCompanyRequest;
    }

}

?>