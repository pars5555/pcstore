<?php

require_once (CLASSES_PATH . "/actions/company/CompanyAction.class.php");
require_once (CLASSES_PATH . "/managers/CompaniesPriceListManager.class.php");
require_once (CLASSES_PATH . "/managers/RequestHistoryManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class RevertCompanyLastPriceAction extends CompanyAction {

    public function service() {
        $company_id = intval($_REQUEST['company_id']);

        if ($this->getUserLevel() === UserGroups::$COMPANY) {
            $requestHistoryManager = RequestHistoryManager::getInstance($this->config, $this->args);
            $customerGivenRequestRecentCountByHours = $requestHistoryManager->getCustomerGivenRequestRecentCountByHours($this->getCustomerLogin(), 24, get_class());
            if ($customerGivenRequestRecentCountByHours > intval($this->getCmsVar('company_revert_price_limit'))) {
                $jsonArr = array('status' => "err", "errText" => $this->getPhrase(557) . ' ' . intval($this->getCmsVar('company_revert_price_limit')));
                echo json_encode($jsonArr);
                return false;
            }
        }


        $companiesPriceListManager = CompaniesPriceListManager::getInstance($this->config, $this->args);
        $ret = $companiesPriceListManager->removeCompanyLastPrice($company_id);


        if ($ret > 0) {
            $jsonArr = array('status' => "ok");
            echo json_encode($jsonArr);
            return true;
        } else {
            switch ($ret) {
                case -1:
                    $errMessage = "Price doesn't exist!";
                    break;

                default:
                    $errMessage = "You can not revert the price file! You have only 1 price file on pcstore.";
                    break;
            }

            $jsonArr = array('status' => "err", "errText" => "System Error: " . $errMessage);
            echo json_encode($jsonArr);
            return false;
        }
    }

}

?>