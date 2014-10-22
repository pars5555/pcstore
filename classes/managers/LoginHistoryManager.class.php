<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/LoginHistoryMapper.class.php");

/**
 * AdminManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class LoginHistoryManager extends AbstractManager {

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
        $this->mapper = LoginHistoryMapper::getInstance();
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

            self::$instance = new LoginHistoryManager($config, $args);
        }
        return self::$instance;
    }

    public function addRow($email, $customerType, $ip, $host, $country, $browserName, $browserVersion, $browserPlatform) {
        $dto = $this->mapper->createDto();
        $dto->setEmail($email);
        $dto->setCustomerType($customerType);
        $dto->setDatetime(date('Y-m-d H:i:s'));
        $dto->setIp($ip);
        $dto->setHost($host);
        $dto->setCountry($country);
        $dto->setBrowserName($browserName);
        $dto->setBrowserVersion($browserVersion);
        $dto->setBrowserPlatform($browserPlatform);
        return $this->mapper->insertDto($dto);
    }

    public function getTodayVisitors() {
        $today = date('Y-m-d');
        return $this->getVisitorsByDateRange($today, $today);
    }

    public function getVisitorsByDateRange($dateStart, $dateEnd) {
        $dateStart = $dateStart . ' 00:00:00';
        $dateEnd = $dateEnd . ' 23:59:59';
        return $this->mapper->getVisitorsByDate($dateStart, $dateEnd);
    }

}

?>