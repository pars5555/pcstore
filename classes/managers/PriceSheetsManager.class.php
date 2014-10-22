<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/PriceSheetsMapper.class.php");

/**
 * AdminManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class PriceSheetsManager extends AbstractManager {

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
        $this->mapper = PriceSheetsMapper::getInstance();
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

            self::$instance = new PriceSheetsManager($config, $args);
        }
        return self::$instance;
    }

    public function addRow($companyId,$priceIndex,  $sheetTitle, $visible) {
        $dto = $this->mapper->createDto();
        $dto->setCompanyId($companyId);
        $dto->setPriceIndex($priceIndex);
        $dto->setSheetTitle($sheetTitle);
        $dto->setVisible($visible);
        return $this->mapper->insertDto($dto);
    }

}

?>