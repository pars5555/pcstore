<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/SentEmailsMapper.class.php");

/**
 * CategoryHierarchyManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class SentEmailsManager extends AbstractManager {

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
        $this->mapper = SentEmailsMapper::getInstance();
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

            self::$instance = new SentEmailsManager($config, $args);
        }
        return self::$instance;
    }

    public function addRow($from, $to, $subject, $body) {
        $dto = $this->mapper->createDto();
        $dto->setFrom($from);
        $dto->setTo($to);
        $dto->setSubject($this->secure($subject));
        $dto->setBody($this->secure($body));
        $dto->setSentDate(date('Y-m-d H:i:s'));
        return $this->mapper->insertDto($dto);
    }

    public function updateRowLogById($id, $log) {
        return $this->mapper->updateTextField($id, "log", $log);
    }

}

?>