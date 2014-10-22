<?php

require_once (CLASSES_PATH . "/loads/cms/CmsLoad.class.php");
require_once (CLASSES_PATH . "/managers/LoginHistoryManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class HomeLoad extends CmsLoad {

    public function load() {
        $this->initTodayVisitors();
    }

    private function initTodayVisitors() {
        $loginHistoryManager = LoginHistoryManager::getInstance($this->config, $this->args);
        $todayVisitors = $loginHistoryManager->getTodayVisitors();
        list($guestVisitors, $userVisitors, $companyVisitors, $serviceCompanyVisitors, $adminVisitors) = $this->filterVisitorsByType($todayVisitors);
        $this->addParam('today_visitors', $todayVisitors);
        $this->addParam('today_guest_visitors', $guestVisitors);
        $this->addParam('today_user_visitors', $userVisitors);
        $this->addParam('today_company_visitors', $companyVisitors);
        $this->addParam('today_service_company_visitors', $serviceCompanyVisitors);
        $this->addParam('today_admin_visitors', $adminVisitors);
    }

    private function filterVisitorsByType($visitors) {
        $guestVisitors = array();
        $userVisitors = array();
        $companyVisitors = array();
        $serviceCompanyVisitors = array();
        $adminVisitors = array();
        foreach ($visitors as $visitor) {
            switch ($visitor->getCustomerType()) {
                case 'guest':
                    $guestVisitors[] = $visitor;
                    break;
                case 'user':
                    $userVisitors[] = $visitor;
                    break;
                case 'company':
                    $companyVisitors[] = $visitor;
                    break;
                case 'service_company':
                    $serviceCompanyVisitors[] = $visitor;
                    break;
                case 'admin':
                    $adminVisitors[] = $visitor;
                    break;
            }
        }
        return array($guestVisitors, $userVisitors, $companyVisitors, $serviceCompanyVisitors, $adminVisitors);
    }

  

    public function getTemplate() {
        return TEMPLATES_DIR . "/cms/home.tpl";
    }

}

?>