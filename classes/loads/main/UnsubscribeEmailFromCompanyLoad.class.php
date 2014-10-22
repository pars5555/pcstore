<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/CompanyExtendedProfileManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class UnsubscribeEmailFromCompanyLoad extends GuestLoad {

    public function load() {
        if (!isset($this->args[0])) {
            exit;
        }
        if (!isset($this->args[1])) {
            exit;
        }
        if (!isset($_REQUEST['md_email'])) {
            exit;
        }
        $companyId = intval($this->args[1]);
        $serviceCompanyParam = $this->secure($this->args[0]);
        switch ($serviceCompanyParam) {
            case 'sc':
                $isServiceCompany = true;
                break;
            case 's':
                $isServiceCompany = false;
                break;
            default:
                exit;
        }
        if ($isServiceCompany) {
            $companyExtendedProfileManager = ServiceCompanyExtendedProfileManager::getInstance($this->config, $this->args);
        } else {
            $companyExtendedProfileManager = CompanyExtendedProfileManager::getInstance($this->config, $this->args);
        }
        $md_email = $this->secure($_REQUEST['md_email']);
        $companyExtendedProfileManager->addUnsubscribeEmailForCompany($companyId, $md_email);
    }

    public function getDefaultLoads($args) {
        $loads = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/unsubscribe_email_from_company.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

    protected function logRequest() {
        return false;
    }

}

?>