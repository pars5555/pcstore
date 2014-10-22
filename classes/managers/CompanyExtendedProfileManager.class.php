<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyBranchesManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/CompanyExtendedProfileMapper.class.php");

/**
 * AdminManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class CompanyExtendedProfileManager extends AbstractManager {

    /**
     * @var app config
     */
    private $config;

    /**
     * @var passed arguemnts
     */
    private $args;

    /**
     * @var singleton instance of class
     */
    private static $instance = null;

    /**
     * Initializes DB mappers
     *
     * @param object $config
     * @param object $args
     * @return
     */
    function __construct($config, $args) {
        $this->mapper = CompanyExtendedProfileMapper::getInstance();
        $this->config = $config;
        $this->args = $args;
    }

    /**
     * Returns an singleton instance of this class
     *
     * @param object $config
     * @param object $args
     * @return
     */
    public static function getInstance($config, $args) {

        if (self::$instance == null) {

            self::$instance = new CompanyExtendedProfileManager($config, $args);
        }
        return self::$instance;
    }

    public function getByCompanyId($id) {
        $dtos = $this->selectByField('company_id', $id);
        if (count($dtos) === 1) {
            return $dtos[0];
        }
        return null;
    }

    public function getCompanyEmailServerSettingByIndex($companyId, $index) {
        $dto = $this->getByCompanyId($companyId);
        $mailLoginsArrayJson = trim($dto->getMailLoginsArrayJson());
        if (!empty($mailLoginsArrayJson)) {
            $mailLoginsArray = json_decode($mailLoginsArrayJson);
        }
        $mailPasswordsArrayJson = trim($dto->getMailPasswordsArrayJson());
        $mailPasswordsArray = json_decode($mailPasswordsArrayJson);

        $mailServersArrayJson = trim($dto->getMailServersArrayJson());
        $mailServersArray = json_decode($mailServersArrayJson);
        return array($mailLoginsArray[$index], $mailPasswordsArray [$index], $mailServersArray[$index]);
    }

    public function changeCompanyEmailServerEmailsCountByIndex($companyId, $index, $emailCount) {
        $dto = $this->getByCompanyId($companyId);
        $mailServersEmailsCountArrayJson = trim($dto->getMailServersEmailsCountArrayJson());
        $mailServersEmailsCountArray = json_decode($mailServersEmailsCountArrayJson);
        $mailServersEmailsCountArray[$index] = $emailCount;
        $dto->setMailServersEmailsCountArrayJson(json_encode($mailServersEmailsCountArray));
        $this->mapper->updateByPK($dto);
    }

    public function deleteCompanyEmailServerByIndex($companyId, $index) {
        $dto = $this->getByCompanyId($companyId);
        $mailLoginsArrayJson = trim($dto->getMailLoginsArrayJson());
        if (!empty($mailLoginsArrayJson)) {
            $mailLoginsArray = json_decode($mailLoginsArrayJson);
            array_splice($mailLoginsArray, $index, 1);
        }
        $mailPasswordsArrayJson = trim($dto->getMailPasswordsArrayJson());
        $mailPasswordsArray = json_decode($mailPasswordsArrayJson);
        array_splice($mailPasswordsArray, $index, 1);

        $mailServersArrayJson = trim($dto->getMailServersArrayJson());
        $mailServersArray = json_decode($mailServersArrayJson);
        array_splice($mailServersArray, $index, 1);

        $mailServersEmailsCountArrayJson = trim($dto->getMailServersEmailsCountArrayJson());
        $mailServersEmailsCountArray = json_decode($mailServersEmailsCountArrayJson);
        array_splice($mailServersEmailsCountArray, $index, 1);

        $dto->setMailLoginsArrayJson(json_encode($mailLoginsArray));
        $dto->setMailPasswordsArrayJson(json_encode($mailPasswordsArray));
        $dto->setMailServersArrayJson(json_encode($mailServersArray));
        $dto->setMailServersEmailsCountArrayJson(json_encode($mailServersEmailsCountArray));
        $this->mapper->updateByPK($dto);
    }

    public function createDefaultExCompanyProfile($companyId) {
        $dto = $this->mapper->createDto();
        $dto->setCompanyId($companyId);
        $companyManager = CompanyManager::getInstance($this->config, $this->args);
        $companyBranchesManager = CompanyBranchesManager::getInstance($this->config, $this->args);
        $companyDto = $companyManager->selectByPK($companyId);
        $companyBranches = $companyBranchesManager->getCompaniesBranches($companyId);
        list($phones, $addresses) = $this->getCompanyPhonesAndAddressesFromBranches($companyBranches);
        $emailBodyTemplate = $this->getPhrase(467);
        $emailBody = str_replace(array('{company_access_code}', '{company_id}', '{company_addresses}', '{company_phones}'), array($companyDto->getAccessKey(), $companyId, implode('<br>', $addresses), implode('<br>', $phones)), $emailBodyTemplate);
        $dto->setPriceEmailBody($emailBody);
        $dto->setPriceEmailSubject($companyDto->getName() . ' Price');
        $this->mapper->insertDto($dto);
    }

    public function addUnsubscribeEmailForCompany($companyId, $md_email) {
        $dto = $this->selectByField('company_id', $companyId);
        if (empty($dto))
        {
            return false;
        }
        $dto = $dto[0];
        $dealerEmails = $dto->getDealerEmails();
        $dealerEmailsArray = explode(';', $dealerEmails);
        if (in_array($md_email, $dealerEmailsArray)) {
            $unsubscribedEmails = $dto->getUnsubscribedEmails();
            $unsubscribedEmailsArray = array();
            if (!empty($unsubscribedEmails)) {
                $unsubscribedEmailsArray = explode(';', $unsubscribedEmails);
            }
            $unsubscribedEmailsArray [] = $md_email;
            $unsubscribedEmailsArray = array_unique($unsubscribedEmailsArray);
            $unsubscribedEmails = implode(';', $unsubscribedEmailsArray);
            $this->updateTextField($dto->getId(), 'unsubscribed_emails', $unsubscribedEmails);
            return true;
        }
        return false;
    }

    private function getCompanyPhonesAndAddressesFromBranches($companyBranches) {
        $phones = array();
        $addresses = array();
        foreach ($companyBranches as $companyBranch) {
            $phones = array_merge($phones, explode(',', trim($companyBranch->getPhones())));
            $addresses[] = $companyBranch->getStreet() . ', ' . $companyBranch->getRegion() . ', ' . $companyBranch->getZip();
        }
        return array(array_unique($phones), array_unique($addresses));
    }

}

?>