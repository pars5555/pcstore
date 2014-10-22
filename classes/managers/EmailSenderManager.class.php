<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/managers/SentEmailsManager.class.php");
require_once (CLASSES_PATH . "/managers/EmailAccountsManager.class.php");
require_once (CLASSES_PATH . "/managers/LanguageManager.class.php");
require_once (CLASSES_PATH . "/managers/EmailTemplatesManager.class.php");
require_once (CLASSES_PATH . "/managers/EmailServersManager.class.php");
require_once (CLASSES_PATH . "/managers/CompaniesPriceListManager.class.php");
require_once (CLASSES_PATH . "/managers/ServiceCompaniesPriceListManager.class.php");
require_once (CLASSES_PATH . "/util/phpmailer_v5.1/class.phpmailer.php");
require_once (CLASSES_PATH . "/util/FAZSmarty.class.php");
require_once (CLASSES_PATH . "/util/MailSender.class.php");

/**
 * CategoryHierarchyManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class EmailSenderManager extends AbstractManager {

    public $sentEmailsManager;
    public $emailAccountsManager;
    private $mail;
    private $dayMaxSendingLimit;

    /**
     * Initializes DB mappers
     *
     * @param object $config
     * @param object $args
     * @return
     */
    function __construct($server) {
        $this->sentEmailsManager = SentEmailsManager::getInstance(null, null);
        $this->emailAccountsManager = EmailAccountsManager::getInstance(null, null);
        $this->initPHPMailer($server);
    }

    public function getMapper() {
        return null;
    }

    private function initPHPMailer($server) {
        $this->mail = new PHPMailer(true);
        $this->mail->IsSMTP();
        try {
            // enables SMTP debug information (for testing)
            $this->mail->SMTPAuth = true;
            // enable SMTP authentication
            // sets the prefix to the servier

            $emailServersManager = EmailServersManager::getInstance(null, null);
            $emailServerDto = $emailServersManager->getByName($server);
            if (isset($emailServerDto)) {
                $this->mail->SMTPSecure = $emailServerDto->getSmtpAuth();
                $this->mail->Host = $emailServerDto->getSmtpHost();
                $this->mail->Port = intval($emailServerDto->getSmtpPort());
                $this->dayMaxSendingLimit = intval($emailServerDto->getDayMaxSendingLimit());
            }
            $this->mail->IsHTML(true);
            $this->mail->CharSet = 'UTF-8';
        } catch (phpmailerException $e) {
            return $e->errorMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return true;
    }

    public function sendBulkEmailWithAttachmentsUsingPcstoreEmails($recipients, $subject, $templateIdOrHtml, $params = array(), $attachments, $fromEmail = '', $fromName = '') {
        if (empty($recipients)) {
            return 'Empty Recipients!';
        }
        $company_price_email_one_time_recipients_limit = intval($this->getCmsVar('company_price_email_one_time_recipients_limit'));
        if ($company_price_email_one_time_recipients_limit < 1 || $company_price_email_one_time_recipients_limit > 100) {
            $company_price_email_one_time_recipients_limit = 50;
        }
        $bulk_email_sending_servers = $this->getCmsVar('bulk_email_sending_servers');
        $emailServers = explode(',', $bulk_email_sending_servers);

        if (count($emailServers) * $this->dayMaxSendingLimit < count($recipients)) {
            return 'Email servers count is not enought to send all the emails to recipients!';
        }
        $recipientsCountForEachMailServer = ceil(count($recipients) / count($emailServers));
        $recipientsGroupsForEachEmailServer = array_chunk($recipients, $recipientsCountForEachMailServer);
        foreach ($recipientsGroupsForEachEmailServer as $serverKey => $recipientsGroup) {
            if (count($recipientsGroup) > $company_price_email_one_time_recipients_limit) {
                $recipientsGroupChunked = array_chunk($recipientsGroup, $company_price_email_one_time_recipients_limit);
                foreach ($recipientsGroupChunked as $r) {
                    $this->sendEmailWithAttachments($emailServers[$serverKey], $r, $subject, $templateIdOrHtml, $params, $attachments, $fromEmail, $fromName);
                }
            } else {
                $this->sendEmailWithAttachments($emailServers[$serverKey], $recipientsGroup, $subject, $templateIdOrHtml, $params, $attachments, $fromEmail, $fromName);
            }
        }

        return true;
    }

    public function sendEmailWithAttachments($fromId, $recipients, $subject, $templateIdOrHtml, $params = array(), $attachments, $fromEmail = '', $fromName = '', $singleTo = false) {
        if (!is_array($recipients)) {
            $recipients = array($recipients);
        }
        $emailAccountDto = $this->emailAccountsManager->selectByPK($fromId);
        $this->mail->SingleTo = $singleTo;
        if (!isset($emailAccountDto)) {
            return false;
        }
        $this->mail->Username = $emailAccountDto->getLogin();
        $this->mail->Password = $emailAccountDto->getPass();
        $this->mail->ClearAllRecipients();
        $this->mail->ClearAttachments();

        foreach ($recipients as $emailAddress) {
            try {
                $this->mail->AddAddress(trim($emailAddress));
            } catch (Exception $ex) {
            }
        }

        if (!empty($fromEmail)) {
            $this->mail->SetFrom($fromEmail, $fromName);
            $this->mail->AddReplyTo($fromEmail);
        } else {
            $this->mail->SetFrom($emailAccountDto->getLogin(), $emailAccountDto->getFromName());
        }
        $this->mail->Subject = $subject;

        $body = $this->compileIfTemplate($templateIdOrHtml, $params);
        $this->mail->MsgHTML($body);
        if (!empty($attachments)) {
            if (!is_array($attachments)) {
                $attachments = array($attachments);
            }
            foreach ($attachments as $title => $file) {
                if (file_exists($file)) {
                    $this->mail->AddAttachment($file, $title);
                }
            }
        }
        try {
            $this->mail->Send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function sendEmail($fromId, $recipients, $subject, $templateIdOrHtml, $params = array(), $fromEmail = '', $fromName = '', $log = true, $singleTo = false) {
        if (!is_array($recipients)) {
            $recipients = array($recipients);
        }

        $emailAccountDto = $this->emailAccountsManager->selectByPK($fromId);
        $this->mail->SingleTo = $singleTo;

        if ($emailAccountDto) {

            $this->mail->Username = $emailAccountDto->getLogin();
            $this->mail->Password = $emailAccountDto->getPass();

            $this->mail->ClearAllRecipients();
            $this->mail->ClearAttachments();

            foreach ($recipients as $emailAddress) {
                try {
                    $this->mail->AddAddress(trim($emailAddress));
                } catch (Exception $ex) {
                    
                }
            }

            if (!empty($fromEmail)) {
                $this->mail->SetFrom($fromEmail, $fromName);
                $this->mail->AddReplyTo($fromEmail);
            } else {
                $this->mail->SetFrom($emailAccountDto->getLogin(), $emailAccountDto->getFromName());
            }

            $this->mail->Subject = $subject;
            $this->mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';

            $body = $this->compileIfTemplate($templateIdOrHtml, $params);
            $this->mail->MsgHTML($body);
            if ($log == true) {
                $rowId = $this->sentEmailsManager->addRow($emailAccountDto->getLogin(), implode(',', $recipients), $subject, $body);
            }
            try {
                $this->mail->Send();
                if ($log == true) {
                    $this->sentEmailsManager->updateRowLogById($rowId, 'Email succussfully sent!');
                }
            } catch (Exception $e) {
                $sendByPhpMail = self::sendByPhpMail(empty($fromEmail) ? $emailAccountDto->getLogin() : $fromEmail, $recipients, $subject, $body);
                if ($log == true) {
                    $this->sentEmailsManager->updateRowLogById($rowId, $e->getMessage() . '. Trying to send by php mail(), returns: ' . $sendByPhpMail ? 'ok' : 'error');
                }
            }
        }
    }

    public static function sendByPhpMail($from, $recipients, $subject, $body) {
        $mailSender = new MailSender();
        return $mailSender->sendHtml($from, $recipients, $subject, $body);
    }

    public static function getEmailsFromText($text) {
        $text = strtolower(trim($text));
        $pattern = "/(?:[a-z0-9!#$%&'*+=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/";
        preg_match_all($pattern, $text, $matches);
        $valid_addresses = array();
        foreach ($matches[0] as $emailAddress) {
            if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                $valid_addresses[] = $emailAddress;
            }
        }
        $valid_addresses = array_unique($valid_addresses);
        return $valid_addresses;
    }

    private function compileIfTemplate($templateId, $params) {
        $ul = $_COOKIE['ul'];
        if (!($ul === 'en' || $ul === 'ru' || $ul === 'am')) {
            $ul = 'en';
        }
        $params["ul"] = $ul;
        $template = null;
        if (strlen($templateId) <= 50) {
            $template = EmailTemplatesManager::getInstance()->getTemplate($templateId, $ul);
        }
        if (!isset($template)) {
            return $templateId;
        }
        $smarty = new FAZSmarty();
        $lm = LanguageManager::getInstance(null, null);
        $params["all_phrases"] = $lm->getAllPhrases();
        $smarty->assign("ns", $params);
        require_once ($smarty->_get_plugin_filepath('function', 'eval'));
        return smarty_function_eval(array('var' => $template), $smarty);
    }

}

?>