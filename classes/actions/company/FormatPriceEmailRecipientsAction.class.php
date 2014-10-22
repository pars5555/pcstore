<?php

require_once (CLASSES_PATH . "/actions/company/CompanyAction.class.php");
require_once (CLASSES_PATH . "/managers/CompanyExtendedProfileManager.class.php");
require_once (CLASSES_PATH . "/managers/EmailSenderManager.class.php");
require_once (CLASSES_PATH . "/managers/UninterestingEmailsManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class FormatPriceEmailRecipientsAction extends CompanyAction {

    public function service() {

        $toEmails = strtolower(trim($_REQUEST['to_emails']));
        $valid_addresses = EmailSenderManager::getEmailsFromText($toEmails);
        $uninterestingEmailsManager = UninterestingEmailsManager::getInstance($this->config, $this->args);
        $valid_addresses = $uninterestingEmailsManager->removeUninterestingEmailsFromList($valid_addresses);

        //saving valis recipients
        $companyDto = $this->getCustomer();
        if (!$companyDto) {
            return false;
        }
        $companyExtendedProfileManager = CompanyExtendedProfileManager::getInstance($this->config, $this->args);
        $dto = $companyExtendedProfileManager->getByCompanyId($companyDto->getId());

        $dto->setDealerEmails(implode(';', $valid_addresses));
        $companyExtendedProfileManager->updateByPK($dto);


        $jsonArr = array('status' => "ok", "valid_email_addresses" => implode(';', $valid_addresses));
        echo json_encode($jsonArr);
        return true;
    }

}

?>