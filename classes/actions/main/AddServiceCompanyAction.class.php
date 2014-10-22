<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyDealersManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class AddServiceCompanyAction extends GuestAction {

    public function service() {
        $serviceCompanyManager = new ServiceCompanyManager($this->config, $this->args);
        $serviceCompanyDealersManager = new ServiceCompanyDealersManager($this->config, $this->args);
        $access_key = $this->secure($_REQUEST["access_key"]);
        $service_company_id = $this->secure($_REQUEST["service_company_id"]);
        $serviceCompany = $serviceCompanyManager->selectByPK($service_company_id);
        if (!isset($serviceCompany)) {
            $this->error(array("errText" => "System error! Company doesn't exists."));
        }
        if (strcasecmp($access_key, $serviceCompany->getAccessKey()) != 0) {
            $this->error(array("errText" => "Wrong access key!"));
        }

        $userId = $this->getUserId();
        $companyDealerDto = $serviceCompanyDealersManager->getByCompanyIdAndUserId($userId, $service_company_id);

        if (!isset($companyDealerDto)) {
            $serviceCompanyDealersManager->addUserToCompany($userId, $service_company_id);
            $message = $this->getPhrase(437) . ' ' . $serviceCompany->getName() . "'! \n";
            $this->ok(array("message" => $message));
        } else {
            $this->error(array("errText" => "You already have '" . $serviceCompany->getName() . "' company in your list!"));
        }
    }

    public function getRequestGroup() {
        return RequestGroups::$userRequest;
    }

}
?>