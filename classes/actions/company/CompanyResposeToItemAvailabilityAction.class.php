<?php

require_once (CLASSES_PATH . "/actions/company/CompanyAction.class.php");
require_once (CLASSES_PATH . "/managers/CompanyItemCheckListManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/actions/admin/AddChangeItemAction.class.php");

/**
 * @author Vahagn Sookiasian
 */
class CompanyResposeToItemAvailabilityAction extends CompanyAction {

    public function service() {
        $companyItemCheckListManager = CompanyItemCheckListManager::getInstance($this->config, $this->args);
        $itemId = intval($this->secure($_REQUEST['item_id']));
        $itemAvailability = intval($this->secure($_REQUEST['item_availability']));
        $companyId = $this->getUserId();
        $companyItemCheckListManager->setCompanyItemAvailability($companyId, $itemId, $itemAvailability);
        $itemManager = ItemManager::getInstance($this->config, $this->args);
        if ($itemAvailability === -1) {
            $itemManager->setItemHidden($itemId, 1);
        } elseif ($itemAvailability === 1) {
            $itemManager->setItemTillDateAttribute($itemId, date('Y-m-d', strtotime("tomorrow")));
        }
        $jsonArr = array('status' => 'ok');
        echo json_encode($jsonArr);
        return true;
    }

}

?>