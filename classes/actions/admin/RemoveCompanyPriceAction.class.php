<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/AdminManager.class.php");
require_once (CLASSES_PATH . "/managers/CompaniesPriceListManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class RemoveCompanyPriceAction extends AdminAction {

    public function service() {
        $companiesPriceListManager = CompaniesPriceListManager::getInstance($this->config, $this->args);
        $price_id = $this->secure($_REQUEST["price_id"]);
        $companiesPriceListManager->removeCompanyPrice($price_id);
        $jsonArr = array('status' => "ok");
        echo json_encode($jsonArr);
        return true;
    }

}

?>