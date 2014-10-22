<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/UninterestingEmailsMapper.class.php");

/**
 * UserSubUsersManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class UninterestingEmailsManager extends AbstractManager {

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
        $this->mapper = UninterestingEmailsMapper::getInstance();
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

            self::$instance = new UninterestingEmailsManager($config, $args);
        }
        return self::$instance;
    }
    
    public function removeUninterestingEmailsFromList($emails)
    {
        $selectAll = $this->selectAll();
        $uninterestingEmailsArray = array();
        foreach ($selectAll  as $dto) {
            $uninterestingEmailsArray [] = trim($dto->getEmail());
        }
        return array_diff($emails, $uninterestingEmailsArray);
        
    }

}

?>