<?php

require_once (CLASSES_PATH . "/loads/servicecompany/ServiceCompanyLoad.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompaniesPriceListManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyExtendedProfileManager.class.php");
require_once (CLASSES_PATH . "/managers/EmailSenderManager.class.php");
require_once (CLASSES_PATH . "/managers/EmailServersManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ServiceUploadPriceLoad extends ServiceCompanyLoad {

    public function load() {
        $userLevel = $this->sessionManager->getUser()->getLevel();
        $serviceCompaniesPriceListManager = ServiceCompaniesPriceListManager::getInstance($this->config, $this->args);
        $serviceCompanyManager = ServiceCompanyManager::getInstance($this->config, $this->args);
        if ($userLevel === UserGroups::$SERVICE_COMPANY) {
            $serviceCompanyId = $this->getUserId();
            $selectedCompanyId = $serviceCompanyId;
            $serviceCompanyExtendedProfileManager = ServiceCompanyExtendedProfileManager::getInstance($this->config, $this->args);
            $dto = $serviceCompanyExtendedProfileManager->getByCompanyId($serviceCompanyId);
            if (!isset($dto)) {
                $serviceCompanyExtendedProfileManager->createDefaultExCompanyProfile($serviceCompanyId);
            }
            $dto = $serviceCompanyExtendedProfileManager->getByCompanyId($serviceCompanyId);
            list($companyEmailServerLogins, $companyEmailServersEmailsCount) = $this->getCompanyEmailServerLogins($dto);
            $this->addParam("companyEmailServerLogins", $companyEmailServerLogins);
            $this->addParam("companyEmailServersEmailsCount", $companyEmailServersEmailsCount);
            $this->addParam("companyExProfileDto", $dto);
            $dealerEmails = trim($dto->getDealerEmails());
            $this->addParam("total_price_email_recipients_count", empty($dealerEmails) ? 0 : count(explode(';', $dealerEmails)));
            array_map('unlink', glob(HTDOCS_TMP_DIR_ATTACHMENTS . "/service_companies/" . $serviceCompanyId . "/*"));
        } else if ($userLevel === UserGroups::$ADMIN) {
            $allCompanies = $serviceCompanyManager->selectAll();
            $companiesIds = $serviceCompanyManager->getCompaniesIdsArray($allCompanies);
            $companiesNames = $serviceCompanyManager->getCompaniesNamesArray($allCompanies);
            if (isset($_REQUEST['selected_company'])) {
                $selectedCompanyId = $this->secure($_REQUEST['selected_company']);
            } else {
                $selectedCompanyId = $allCompanies[0]->getId();
            }
            $this->addParam("companiesIds", $companiesIds);
            $this->addParam("companiesNames", $companiesNames);
        }

        $companyPrices = $serviceCompaniesPriceListManager->getCompanyHistoryPricesOrderByDate($selectedCompanyId, 0, 50);
        $this->addParam("company_prices", $companyPrices);
        $this->addParam("selectedCompanyId", $selectedCompanyId);
        if (isset($_REQUEST['show_send_email_to_dealers']) && $_REQUEST['show_send_email_to_dealers'] == 1) {
            $this->addParam("show_send_email_to_dealers", 1);
        }
        $emailServersManager = EmailServersManager::getInstance($this->config, $this->args);
        $allEmailServers = $emailServersManager->selectAll();
        $this->addParam("allEmailServers", $allEmailServers);
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/servicecompany/uploadprice/upload_price.tpl";
    }

    public function getCompanyEmailServerLogins($dto) {
        $mailLoginsArrayJson = trim($dto->getMailLoginsArrayJson());
        if (!empty($mailLoginsArrayJson)) {
            $mailLoginsArray = json_decode($mailLoginsArrayJson);
        }

        $mailMailServersEmailsCountArrayJson = trim($dto->getMailServersEmailsCountArrayJson());
        if (!empty($mailLoginsArrayJson)) {
            $mailMailServersEmailsCountArray = json_decode($mailMailServersEmailsCountArrayJson);
        }
        if (!empty($mailLoginsArray) && !empty($mailMailServersEmailsCountArray)) {
            return array($mailLoginsArray, $mailMailServersEmailsCountArray);
        }
        return array(array(), array());
    }

}

?>