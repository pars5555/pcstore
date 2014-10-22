<?php

require_once (CLASSES_PATH . '/lib/mandrill-api-php/src/Mandrill.php');

/**
 * UserManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class MandrillEmailSenderManager {

    /**
     * @var singleton instance of class
     */
    private static $instance = null;
    private $mandrill;

    /**
     * Initializes DB mappers
     *
     * @param object $config
     * @param object $args
     * @return
     */
    function __construct($mandrillApiKey) {
        try {
            $this->mandrill = new Mandrill($mandrillApiKey);
        } catch (Exception $exc) {
            
        }
    }

    /**
     * Returns an singleton instance of this class
     */
    public static function getInstance($mandrillApiKey) {
        if (self::$instance == null) {
            self::$instance = new EmailSenderManager($mandrillApiKey);
        }
        return self::$instance;
    }

    public function sendHtmlEmail($to, $subject, $bodyHtml, $fromEmail, $fromName, $attachedFiles = null, $subaccountId = null) {
        if (!is_array($to)) {
            $to = array($to);
        }
        $params = $this->getEmailParams($to, $subject, $bodyHtml, $fromEmail, $fromName, $attachedFiles, $subaccountId);
        if (!isset($this->mandrill->messages)) {
            return 'Mandrill initialization error! Please check API key.';
        }
        try {
            $res = $this->mandrill->messages->send($params, count($to) > 10, 'Main Pool', null);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        if (is_array($res)) {
            return $res;
        }
        if (is_object($res) && isset($res->status) && $res->status == 'error') {
            return $res->message;
        }
        return 'Unknown Error';
    }

    private function getEmailParams($to, $subject, $bodyHtml, $fromEmail, $fromName, $attachedFiles = null, $subaccountId = null) {
        $paramsTo = array();
        foreach ($to as $emailAddress) {
            $paramsTo[] = array(
                'email' => $emailAddress,
                'name' => null,
                'type' => 'to'
            );
        }
        $params = array(
            'html' => $bodyHtml,
            'text' => strip_tags($bodyHtml),
            'subject' => $subject,
            'from_email' => $fromEmail,
            'from_name' => $fromName,
            'headers' => array('Reply-To' => $fromEmail,
                'Content-type' => 'text/html; charset=utf-8'),
            'preserve_recipients' => false,
            'to' => $paramsTo
        );
        if (!empty($subaccountId)) {
            $params ['subaccount'] = $subaccountId;
        }
        if (!empty($attachedFiles)) {
            $params ['attachments'] = array();
            foreach ($attachedFiles as $fileDisplayName => $file) {
                $params ['attachments'] [] = array(
                    'type' => $this->getMimeType($file),
                    'name' => $fileDisplayName,
                    'content' => base64_encode(file_get_contents($file))
                );
            }
        }
        return $params;
    }

    private function getMimeType($filename) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filename);
        finfo_close($finfo);
        return $mime;
    }

}

?>