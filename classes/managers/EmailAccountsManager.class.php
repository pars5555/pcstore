<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/EmailAccountsMapper.class.php");

/**
 * CategoryHierarchyManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class EmailAccountsManager extends AbstractManager {

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
        $this->mapper = EmailAccountsMapper::getInstance();
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

            self::$instance = new EmailAccountsManager($config, $args);
        }
        return self::$instance;
    }

    public function getEmailAccountsIds() {
        $ret = array();
        $allDtos = $this->mapper->selectAll();
        foreach ($allDtos as $dto) {
            $ret[] = $dto->getId();
        }
        return $ret;
    }

    public function getEmailAddressById($id) {
        $dto = $this->mapper->selectByPK($id);
        if (isset($dto)) {
            return $dto->getLogin();
        }
        return '';
    }

}

?>