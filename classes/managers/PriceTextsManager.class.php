<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/PriceTextsMapper.class.php");

/**
 * AdminManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class PriceTextsManager extends AbstractManager {

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
        $this->mapper = PriceTextsMapper::getInstance();
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

            self::$instance = new PriceTextsManager($config, $args);
        }
        return self::$instance;
    }

    public function isCompanyPriceValuesReady($companyId) {
        $selectByField = $this->mapper->selectByField('company_id', $companyId);
        if (!empty($selectByField)) {
            $dto = $selectByField[0];
            if ($dto->getPriceValuesReady() == 1) {
                return true;
            }
        }
        return false;
    }

    public function setCompanyPriceValuesReady($companyId, $ready) {
        $selectByField = $this->mapper->selectByField('company_id', $companyId);
        if (!empty($selectByField)) {
            $dto = $selectByField[0];
            $dto->setPriceValuesReady($ready);
            $this->mapper->updateByPK($dto);
        }
    }

    public function setCompanyPriceText($companyId, $priceText) {
        $selectByField = $this->mapper->selectByField('company_id', $companyId);
        $rowExists = false;
        if (empty($selectByField)) {
            $dto = $this->mapper->createDto();
        } else {
            $dto = $selectByField[0];
            $rowExists = true;
        }
        $dto->setCompanyId($companyId);
        $dto->setPriceText($priceText);
        if ($rowExists) {
            return $this->mapper->updateByPK($dto);
        } else {
            return $this->mapper->insertDto($dto);
        }
    }

    public function appendCompanyPriceText($companyId, $priceText) {
        $selectByField = $this->mapper->selectByField('company_id', $companyId);
        if (empty($selectByField)) {
            return false;
        }
        $dto = $selectByField[0];
        $dto->setPriceText($dto->getPriceText() . $priceText);
        return $this->mapper->updateByPK($dto);
    }

}

?>