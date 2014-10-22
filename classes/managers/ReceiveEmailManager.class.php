<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/managers/EmailAccountsManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/AdminManager.class.php");
require_once (CLASSES_PATH . "/managers/OnlineUsersManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/CustomerAlertsManager.class.php");

/**
 * AdminManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class ReceiveEmailManager {

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
    function __construct() {
        
    }

    /**
     * Returns an singleton instance of this class
     *
     * @param object $config
     * @param object $args
     * @return
     */
    public static function getInstance() {

        if (self::$instance == null) {

            self::$instance = new ReceiveEmailManager();
        }
        return self::$instance;
    }

    public function checkPriceEmailsAndAddAlertsToOnlineAdmins() {
        $emailServersManager = new EmailAccountsManager(null, null);
        $infoEmailDto = $emailServersManager->selectByPK('info');
        $login = $infoEmailDto->getLogin();
        $pass = $infoEmailDto->getPass();
        $imapInbox = $infoEmailDto->getImapInbox();
        $connection = @imap_open($imapInbox, $login, $pass);
        if (!$connection) {
            return imap_last_error();
        }
        $emailsIds = imap_search($connection, 'UNSEEN');
        if (empty($emailsIds) || !is_array($emailsIds)) {
            return "No unread emails";
        }
        rsort($emailsIds);
        $attachments = array();
        foreach ($emailsIds as $emailId) {
            //$overview = imap_fetch_overview($connection, $emailId, 0);
            $structure = imap_fetchstructure($connection, $emailId);
            $header = imap_headerinfo($connection, $emailId);
            $fromEmails = array();
            if (!empty($header->from)) {
                foreach ($header->from as $from) {
                    $fromEmails[] = $from->mailbox . '@' . $from->host;
                }
            }
            list($companyId, $companyName, $companyType) = $this->determineCompanyIdFromPriceEmail($fromEmails);
            if ($companyId == false) {
                continue;
            }
            if (isset($structure->parts) && count($structure->parts)) {
                for ($i = 0; $i < count($structure->parts); $i++) {
                    $attachments[$emailId][$i] = array(
                        'is_attachment' => false,
                        'filename' => '',
                        'name' => '',
                        'attachment' => ''
                    );

                    if ($structure->parts[$i]->ifdparameters) {
                        foreach ($structure->parts[$i]->dparameters as $object) {
                            if (strtolower($object->attribute) == 'filename') {
                                $attachments[$emailId][$i]['is_attachment'] = true;
                                $attachments[$emailId][$i]['filename'] = $object->value;
                            }
                        }
                    }

                    if ($structure->parts[$i]->ifparameters) {
                        foreach ($structure->parts[$i]->parameters as $object) {
                            if (strtolower($object->attribute) == 'name') {
                                $attachments[$emailId][$i]['is_attachment'] = true;
                                $attachments[$emailId][$i]['name'] = $object->value;
                            }
                        }
                    }

                    if ($attachments[$emailId][$i]['is_attachment']) {
                        $attachments[$emailId][$i]['attachment'] = imap_fetchbody($connection, $emailId, $i + 1, FT_UID | FT_PEEK);
                        if ($structure->parts[$i]->encoding == 3) {
                            $attachments[$emailId][$i]['attachment'] = base64_decode($attachments[$emailId][$i]['attachment']);
                        } elseif ($structure->parts[$i]->encoding == 4) {
                            $attachments[$emailId][$i]['attachment'] = quoted_printable_decode($attachments[$emailId][$i]['attachment']);
                        }
                    }
                }
            }


            /* iterate through each attachment and save it */
            foreach ($attachments[$emailId] as $attachment) {
                if ($attachment['is_attachment'] == 1) {
                    $filename = $attachment['name'];
                    if (empty($filename)) {
                        $filename = $attachment['filename'];
                    }
                    if (strpos($filename, '.xls') !== false || strpos($filename, '.pdf') !== false) {
                        $onlineAdminsEmails = $this->getOnlineAdminsEmails();
                        if (!empty($onlineAdminsEmails)) {
                            $customerAlertsManager = CustomerAlertsManager::getInstance(null, null);
                            foreach ($onlineAdminsEmails as $adminEmail) {
                                $customerAlertsManager->addNewEmailFromCompanyAlert($adminEmail, $companyName);
                            }
                        }
                        break;
                    }
                    /* $filename = $attachment['name'];
                      if (empty($filename))
                      $filename = $attachment['filename'];
                      if (empty($filename))
                      $filename = time() . ".dat";

                      $fp = fopen($emailId . "-" . $filename, "w+");
                      fwrite($fp, $attachment['attachment']);
                      fclose($fp); */
                }
            }
        }
        return true;
    }

    private function determineCompanyIdFromPriceEmail($fromEmails) {
        if (is_array($fromEmails)) {
            $fromEmails = implode(',', $fromEmails);
        }

        $companyManager = CompanyManager::getInstance(null, null);
        $allCompaniesDtos = $companyManager->selectAll();
        $selectedCompaniesIds = array();
        foreach ($allCompaniesDtos as $companyDto) {
            $companyEmail = $companyDto->getEmail();
            $priceEmailsKeywords = trim($companyDto->getPriceEmailsKeywords());
            $priceEmailsKeywordsArray = array();
            if (!empty($priceEmailsKeywords)) {
                $priceEmailsKeywordsArray = explode(',', $priceEmailsKeywords);
            }
            if (strpos($fromEmails, $companyEmail) !== false) {
                return array($companyDto->getId(), $companyDto->getName(), 'company');
            }
            foreach ($priceEmailsKeywordsArray as $priceEmailsKeyword) {
                if (strpos($fromEmails, $priceEmailsKeyword) !== false) {
                    $selectedCompaniesIds[$companyDto->getId()] = $companyDto->getName();
                }
            }
        }

        $serviceCompanyManager = ServiceCompanyManager::getInstance(null, null);
        $allServiceCompaniesDtos = $serviceCompanyManager->selectAll();
        $selectedServiceCompaniesIds = array();
        foreach ($allServiceCompaniesDtos as $serviceCompanyDto) {
            $serviceCompanyEmail = $serviceCompanyDto->getEmail();
            $priceEmailsKeywords = $serviceCompanyDto->getPriceEmailsKeywords();
            if (!empty($priceEmailsKeywords)) {
                $priceEmailsKeywordsArray = explode(',', $priceEmailsKeywords);
            }
            if (strpos($fromEmails, $serviceCompanyEmail) !== false) {
                return array($serviceCompanyDto->getId(), $serviceCompanyDto->getName(), 'service_company');
            }
            foreach ($priceEmailsKeywordsArray as $priceEmailsKeyword) {
                if (strpos($fromEmails, $priceEmailsKeyword) !== false) {
                    $selectedServiceCompaniesIds[$serviceCompanyDto->getId()] = $serviceCompanyDto->getName();
                }
            }
        }

        if (count($selectedCompaniesIds) === 1) {
            $keys = array_keys($selectedCompaniesIds);
            return array($keys[0], $selectedCompaniesIds[$keys[0]], 'company');
        }
        if (count($selectedServiceCompaniesIds) === 1) {
            $keys = array_keys($selectedServiceCompaniesIds);
            return array($keys[0], $selectedServiceCompaniesIds[$keys[0]], 'service_company');
        }

        //@TODO retrun the correct company id and type
        return array(false, false, false);
    }

    private function getOnlineAdminsEmails() {
        $adminManager = AdminManager::getInstance(null, null);
        $adminsDtos = $adminManager->selectAll();
        $onlineUsersManager = OnlineUsersManager::getInstance(null, null);
        $ret = array();
        foreach ($adminsDtos as $adminDto) {
            $adminEmail = $adminDto->getEmail();
            $onlineAdminDto = $onlineUsersManager->selectByField('email', $adminEmail);
            if (!empty($onlineAdminDto)) {
                $ret [] = $adminEmail;
            }
        }
        return $ret;
    }

}

?>