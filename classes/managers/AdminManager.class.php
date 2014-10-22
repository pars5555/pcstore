<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/AdminMapper.class.php");

/**
 * AdminManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class AdminManager extends AbstractManager {

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
        $this->mapper = AdminMapper::getInstance();
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

            self::$instance = new AdminManager($config, $args);
        }
        return self::$instance;
    }

    public function getAdminByEmailAndPassword($email, $password) {
        return $this->mapper->getAdmin($email, $password);
    }

    public function updateAdminHash($uId) {
        $hash = $this->generateHash($uId);
        $adminDto = $this->mapper->createDto();
        $adminDto->setId($uId);
        $adminDto->setHash($hash);
        $this->mapper->updateByPK($adminDto);
        return $hash;
    }

    public function generateHash($id) {
        return md5($id * time() * 19);
    }

    public function validate($id, $hash) {
        return $this->mapper->validate($id, $hash);
    }

    public function getSmsEnabledAdmins() {
        return $this->mapper->getSmsEnabledAdmins();
    }

    public function setLastPingToNow($id) {
        $this->mapper->updateTextField($id, 'last_ping', date('Y-m-d H:i:s'));
    }

    public function setPriceGroup($id, $value) {
        $this->mapper->updateTextField($id, 'price_group', $value);
    }

    public function enableSound($id, $value) {
        $this->mapper->updateNumericField($id, 'sound_on', $value);
    }

}

?>