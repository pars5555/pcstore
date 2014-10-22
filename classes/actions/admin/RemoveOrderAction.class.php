<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/OrdersManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class RemoveOrderAction extends AdminAction {

    public function service() {
        $order_id = $this->secure($_REQUEST['order_id']);
        $ordersManager = OrdersManager::getInstance($this->config, $this->args);
        $order = $ordersManager->selectByPK($order_id);
        if (isset($order)) {
            //TODO remove order
            $jsonArr = array('status' => "ok");
            echo json_encode($jsonArr);
            return true;
        } else {
            $jsonArr = array('status' => "err", "errText" => "System Error: Order does not exist!");
            echo json_encode($jsonArr);
            return false;
        }
    }

}

?>