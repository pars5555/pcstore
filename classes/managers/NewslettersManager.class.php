<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/NewsletterMapper.class.php");

/**
 * AdminManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class NewslettersManager extends AbstractManager {

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
        $this->mapper = NewsletterMapper::getInstance();
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

            self::$instance = new NewslettersManager($config, $args);
        }
        return self::$instance;
    }

    public function getAllNewslettersMap() {
        $dtos = $this->selectAll();
        $ret = array();
        foreach ($dtos as $dto) {
            $ret[$dto->getId()] = $dto->getTitle();
        }
        return $ret;
    }

    public function addNewsletter($title, $html, $include_all_active_users) {
        $dto = $this->mapper->createDto();
        $dto->setTitle($title);
        $dto->setHtml(addslashes($html));
        $dto->setIncludeAllActiveUsers($include_all_active_users);
        return $this->mapper->insertDto($dto, false);
    }

    public function saveNewsletter($id, $html, $include_all_active_users) {
        $this->mapper->updateTextField($id, 'html', addslashes($html), false);
        $this->mapper->updateTextField($id, 'include_all_active_users', $include_all_active_users);
    }

    public function getByTitle($title) {
        $dtos = $this->selectByField('title', $title);
        if (count($dtos) === 1) {
            return $dtos[0];
        }
        return null;
    }

}

?>