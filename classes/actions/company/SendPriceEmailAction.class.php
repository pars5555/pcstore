<?php

require_once (CLASSES_PATH . "/actions/company/CompanyAction.class.php");
require_once (CLASSES_PATH . "/managers/CompanyExtendedProfileManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyExtendedProfileManager.class.php");
require_once (CLASSES_PATH . "/managers/EmailSenderManager.class.php");
require_once (CLASSES_PATH . "/managers/MandrillEmailSenderManager.class.php");
require_once (CLASSES_PATH . '/managers/CompanyPriceEmailHistoryManager.class.php');

/**
 * @author Vahagn Sookiasian
 */
class SendPriceEmailAction extends CompanyAction {

    private $extendedProfileManager;

    public function service() {
        $saveOnly = $_REQUEST['save_only'];
        $subject = $_REQUEST['subject'];
        $body = $_REQUEST['body'];
        $fromEmail = $_REQUEST['from_email'];

        $to = strtolower(trim($_REQUEST['to']));
        $isServiceCompany = ($this->getUserLevel() == UserGroups::$SERVICE_COMPANY);
        if ($isServiceCompany) {
            $this->extendedProfileManager = ServiceCompanyExtendedProfileManager::getInstance($this->config, $this->args);
        } else {
            $this->extendedProfileManager = CompanyExtendedProfileManager::getInstance($this->config, $this->args);
        }
        $companyId = $this->getUserId();
        $dto = $this->extendedProfileManager->getByCompanyId($companyId);
        if (!isset($dto)) {
            $this->error(array('message' => 'System Error!!!'));
        }
        $dto->setPriceEmailSubject($subject);
        $dto->setPriceEmailBody(addslashes($body));
        $dto->setFromEmail($fromEmail);
        $valid_addresses = EmailSenderManager::getEmailsFromText($to);
        $dto->setDealerEmails(implode(';', $valid_addresses));
        $this->extendedProfileManager->updateByPK($dto, false);
        if ($saveOnly != 1) {
            $company_price_email_interval_hours = intval($this->getCmsVar('company_price_email_interval_hours'));
            $allowSend = $this->canCompanySendPriceEmail($companyId, $isServiceCompany ? "service_company" : "company", $company_price_email_interval_hours, $valid_addresses);
            if ($allowSend) {
                $res = $this->sendEmailToDealersEmails($dto);
                if ($res !== true) {
                    $this->error(array('message' => $res));
                }
            } else {
                $this->error(array('message' => '`645` ' . $company_price_email_interval_hours . ' `646`'));
            }
        }
        $this->ok();
    }

    private function sendEmailToDealersEmails($companyExProfiledto, $byMandrill = false) {

        $dealerEmailsText = $companyExProfiledto->getDealerEmails();
        $dealerEmailsArray = explode(';', $dealerEmailsText);
        $subject = $companyExProfiledto->getPriceEmailSubject();
        $body = stripslashes($companyExProfiledto->getPriceEmailBody());
        $fromEmail = $companyExProfiledto->getFromEmail();
        $companyName = $this->getCustomer()->getName();

        $isServiceCompany = ($this->getUserLevel() == UserGroups::$SERVICE_COMPANY);
        $priceFiles = array();
        if ($isServiceCompany) {
            $companiesPriceListManager = ServiceCompaniesPriceListManager::getInstance($this->config, $this->args);
            $companyId = $companyExProfiledto->getServiceCompanyId();
            $companyLastPrices = $companiesPriceListManager->getCompanyLastPrices($companyId);
            if (!empty($companyLastPrices)) {
                foreach ($companyLastPrices as $key => $clp) {
                    $priceFiles[] = DATA_DIR . "/service_companies_prices/" . $companyId . '/' . $clp->getFileName() . '.' . $clp->getFileExt();
                }
            }
            $tmpSubdirectoryName = 'service_companies';
        } else {
            $companiesPriceListManager = CompaniesPriceListManager::getInstance($this->config, $this->args);
            $companyId = $companyExProfiledto->getCompanyId();
            $companyLastPrices = $companiesPriceListManager->getCompanyLastPrices($companyId);
            if (!empty($companyLastPrices)) {
                foreach ($companyLastPrices as $key => $clp) {
                    $priceFiles[] = DATA_DIR . "/companies_prices/" . $companyId . '/' . $clp->getFileName() . '.' . $clp->getFileExt();
                }
            }
            $tmpSubdirectoryName = 'companies';
        }
        $allEmailFileAttachments = array();
        foreach ($priceFiles as $key => $priceFile) {
            $pfnParts = explode('.', $priceFile);
            $priceFileExtention = end($pfnParts);
            if (file_exists($priceFile)) {
                $postfix = "";
                if ($key > 0) {
                    $postfix = '_' . ($key + 1);
                }
                $allEmailFileAttachments [($companyName . ' Pricelist' . $postfix . '.' . $priceFileExtention)] = $priceFile;
            }
        }
        $additionalAttachedFiles = array();
        $dir = HTDOCS_TMP_DIR_ATTACHMENTS . '/' . $tmpSubdirectoryName . '/' . $companyId . '/';
        if (is_dir($dir)) {
            $additionalAttachedFiles = scandir($dir);
        }
        foreach ($additionalAttachedFiles as $file) {
            $fileFullPath = $dir . $file;
            if (is_file($fileFullPath)) {
                $path_parts = pathinfo($fileFullPath);
                $basename = $path_parts['basename'];
                $fileDisplayName = substr($basename, strpos($basename, '_') + 1);
                $allEmailFileAttachments[$fileDisplayName] = $fileFullPath;
            }
        }
        if ($byMandrill == true) {
            $mandrillEmailSenderManager = new MandrillEmailSenderManager($this->getCmsVar("mandrill_api_key"));
            $body .= '<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>';
            $body .= '<p style="font-size:10px"><a href="*|UNSUB:http://pcstore.am/unsub/' . ($isServiceCompany ? 'sc' : 's') . '/' . $companyId . '|*">Click here to unsubscribe.</a></p>';
            $res = $mandrillEmailSenderManager->sendHtmlEmail($dealerEmailsArray, $subject, $body, $fromEmail, $companyName, $allEmailFileAttachments, ($isServiceCompany ? 'service_company' : 'company') . '_' . $companyId);
            $sentSuccess = is_array($res);
        } else {
            $emailSenderManager = new EmailSenderManager('gmail');
            $res = $emailSenderManager->sendBulkEmailWithAttachmentsUsingPcstoreEmails($dealerEmailsArray, $subject, $body, array(), $allEmailFileAttachments, $fromEmail, $companyName);
            $sentSuccess = ($res === true);
        }
        if ($sentSuccess !== true) {
            return $res;
        } else {
            $companyPriceEmailHistoryManager = CompanyPriceEmailHistoryManager::getInstance($this->config, $this->args);
            $companyPriceEmailHistoryManager->addRow($companyId, $isServiceCompany ? "service_company" : "company", $fromEmail, $dealerEmailsArray, $body, $subject, array_keys($allEmailFileAttachments));
        }
        return true;
    }

    private function canCompanySendPriceEmail($companyId, $companyType, $company_price_email_interval_hours, $toEmails) {
        if (count($toEmails) <= 2) {
            return true;
        }
        $companyPriceEmailHistoryManager = CompanyPriceEmailHistoryManager::getInstance($this->config, $this->args);
        $companySentEmailsByHoursDtos = $companyPriceEmailHistoryManager->getCompanySentEmailsByHours($companyId, $companyType, $company_price_email_interval_hours);
        if (empty($companySentEmailsByHoursDtos)) {
            return true;
        }
        $allToEmailsArray = array();
        foreach ($companySentEmailsByHoursDtos as $companySentEmailsByHoursDto) {
            $dtoToEmails = $companySentEmailsByHoursDto->getToEmails();
            $toEmailsArray = explode(',', $dtoToEmails);
            $allToEmailsArray = array_merge($allToEmailsArray, $toEmailsArray);
        }
        array_unique($allToEmailsArray);
        $intersectedEmailsCount = count(array_intersect($allToEmailsArray, $toEmails));
        // if 60% of the emails are already send in last X hours then don't allow to send again
        if ($intersectedEmailsCount >= intval(count($toEmails) * 0.6)) {
            return false;
        }
        return true;
    }

}

?>