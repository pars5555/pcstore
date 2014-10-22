<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/EmailTemplatesMapper.class.php");

/**
 * CategoryHierarchyManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class EmailTemplatesManager extends AbstractManager {

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
        $this->mapper = EmailTemplatesMapper::getInstance();
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

            self::$instance = new EmailTemplatesManager();
        }
        return self::$instance;
    }

    public function getTemplate($id, $lc = 'en') {

        if (($lc == 'en' || $lc == 'am' || $lc == 'ru')) {
            $fname = "getContent" . ucfirst($_COOKIE['ul']);
        } else {
            $fname = 'getContentEn';
        }
        $emailTemplateDto = $this->mapper->selectByPK($this->secure($id));
        if (!isset($emailTemplateDto)) {
            return null;
        }
        $ret = $emailTemplateDto->$fname();
        if (!empty($ret)) {
            return $ret;
        } else {
            return $emailTemplateDto->getContentEn();
        }
    }

}

?>