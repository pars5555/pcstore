<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/OrdersManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class PrintInvoiceLoad extends GuestLoad {

    public function load() {
        $ordersManager = OrdersManager::getInstance($this->config, $this->args);
        $orderId = $this->secure($_REQUEST['order_id']);
        $this->addParam('order_id', $orderId);
        $orderDto = $ordersManager->selectByPK($orderId);
        $this->addParam('orderDto', $orderDto);
        if (isset($orderDto)) {
            $metadataJson = $orderDto->getMetadataJson();
            $metadata = json_decode($metadataJson);
            $this->addParam('metadata', $metadata);
        }
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/orders/invoice_print.tpl";
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function isMain() {
        return true;
    }

    public function getRequestGroup() {
        return RequestGroups::$userCompanyRequest;
    }

}

?>