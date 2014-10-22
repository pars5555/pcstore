<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyDealersManager.class.php");
require_once (CLASSES_PATH . "/managers/CustomerCartManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class AddCompanyAction extends GuestAction {

    public function service() {
        $userManager = new UserManager($this->config, $this->args);
        $companyManager = new CompanyManager($this->config, $this->args);
        $companyDealersManager = new CompanyDealersManager($this->config, $this->args);
        $access_key = $userManager->secure($_REQUEST["access_key"]);
        $company = $companyManager->getCompanyByAccessKey($access_key);
        if ($company) {
            $userId = $this->getUserId();
            $companyId = $company->getId();

            if (!$companyDealersManager->getByCompanyIdAndUserId($userId, $companyId)) {
                $customer = $this->getCustomer();
                $customerEmail = $customer->getEmail();
                $customerCartManager = CustomerCartManager::getInstance($this->args, $this->config);
                $items = $customerCartManager->getCustomerItemsByCompanyId($customerEmail, $companyId);
                $bundlesIds = $customerCartManager->getCustomerBundlesIdsByCompanyId($customerEmail, $companyId);
                $customerCartManager->deleteCompanyRelatedItemsFromCustomerCart($customerEmail, $companyId);
                $companyDealersManager->addUserToCompany($userId, $companyId);
                $message = $this->getPhrase(437) . ' ' . $company->getName() . "'! \n";
                if (!empty($items)) {
                    $message .= $this->getPhrase(436) . "'\n";
                }
                if (!empty($bundlesIds)) {
                    $message .= $this->getPhrase(435);
                }
                $jsonArr = array('status' => "ok", "message" => $message);
                echo json_encode($jsonArr);
                return true;
            } else {
                $jsonArr = array('status' => "err", "errText" => "You already have '" . $company->getName() . "' company in your list!");
                echo json_encode($jsonArr);
                return false;
            }
        } else {
            $jsonArr = array('status' => "err", "errText" => "Access key incorrect!");
            echo json_encode($jsonArr);
            return false;
        }
    }

    public function getRequestGroup() {
        return RequestGroups::$userRequest;
    }

}

?>