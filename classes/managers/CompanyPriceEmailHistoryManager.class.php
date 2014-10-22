<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/CompanyPriceEmailHistoryMapper.class.php");

/**
 * AdminManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class CompanyPriceEmailHistoryManager extends AbstractManager {

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
        $this->mapper = CompanyPriceEmailHistoryMapper::getInstance();
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

            self::$instance = new CompanyPriceEmailHistoryManager($config, $args);
        }
        return self::$instance;
    }

    public function addRow($companyId, $companyType, $fromEmail, $toEmails, $body, $subject, $attachments) {
        if (is_array($toEmails)) {
            $toEmails = implode(',', $toEmails);
        }
        if (is_array($attachments)) {
            $attachments = implode(',', $attachments);
        }
        $dto = $this->createDto();
        $dto->setCompanyId($companyId);
        $dto->setCompanyType($companyType);
        $dto->setFromEmail($fromEmail);
        $dto->setToEmails($toEmails);
        $dto->setBody($body);
        $dto->setSubject($subject);
        $dto->setAttachments($attachments);
        $dto->setDatetime(date('Y-m-d H:i:s'));
        return $this->insertDto($dto);
    }

    public function getCompanySentEmailsByHours($companyId, $companyType, $hours) {
        return $this->mapper->getCompanySentEmailsByHours($companyId, $companyType, $hours);
    }

}

?>