<?php

require_once (CLASSES_PATH . "/loads/company/CompanyLoad.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/CompaniesPriceListManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyExtendedProfileManager.class.php");
require_once (CLASSES_PATH . "/managers/EmailSenderManager.class.php");
require_once (CLASSES_PATH . "/managers/EmailServersManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class UploadPriceLoad extends CompanyLoad {

    public function load() {

        $userLevel = $this->sessionManager->getUser()->getLevel();
        $companiesPriceListManager = CompaniesPriceListManager::getInstance($this->config, $this->args);
        $companyManager = CompanyManager::getInstance($this->config, $this->args);
        if ($userLevel === UserGroups::$COMPANY) {
            $companyId = $this->getUserId();
            $selectedCompanyId = $companyId;
            $companyExtendedProfileManager = CompanyExtendedProfileManager::getInstance($this->config, $this->args);
            $dto = $companyExtendedProfileManager->getByCompanyId($companyId);
            if (!isset($dto)) {
                $companyExtendedProfileManager->createDefaultExCompanyProfile($companyId);
            }
            $dto = $companyExtendedProfileManager->getByCompanyId($companyId);
            list($companyEmailServerLogins, $companyEmailServersEmailsCount) = $this->getCompanyEmailServerLogins($dto);
            $this->addParam("companyEmailServerLogins", $companyEmailServerLogins);
            $this->addParam("companyEmailServersEmailsCount", $companyEmailServersEmailsCount);
            $this->addParam("companyExProfileDto", $dto);
            $dealerEmails = trim($dto->getDealerEmails());
            $this->addParam("total_price_email_recipients_count", empty($dealerEmails) ? 0 : count(explode(';', $dealerEmails)));
            array_map('unlink', glob(HTDOCS_TMP_DIR_ATTACHMENTS . "/companies/" . $companyId . "/*"));
        } else if ($userLevel === UserGroups::$ADMIN) {
            $allCompanies = $companyManager->getAllCompanies(true, true);
            $companiesIds = $companyManager->getCompaniesIdsArray($allCompanies);
            $companiesNames = $companyManager->getCompaniesNamesArray($allCompanies);
            if (isset($_REQUEST['selected_company'])) {
                $selectedCompanyId = $this->secure($_REQUEST['selected_company']);
            } else {
                $selectedCompanyId = $allCompanies[0]->getId();
            }
            $this->addParam("companiesIds", $companiesIds);
            $this->addParam("companiesNames", $companiesNames);
        }
        $companyPrices = $companiesPriceListManager->getCompanyHistoryPricesOrderByDate($selectedCompanyId, 0, 50);
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
        return TEMPLATES_DIR . "/company/uploadprice/upload_price.tpl";
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