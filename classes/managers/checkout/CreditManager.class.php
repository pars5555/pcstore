<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");

/**
 * PcConfiguratorManager class is responsible for creating,
 */
class CreditManager extends AbstractManager {

    /**
     * @var singleton instance of class
     */
    private static $instance = null;

    /**
     * Initializes DB mappers
     *

     * @return
     */
    function __construct($config, $args) {
        $this->config = $config;
        $this->args = $args;
        //$this->itemManager = ItemManager::getInstance($this->config, $this->args);
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

            self::$instance = new CreditManager($config, $args);
        }
        return self::$instance;
    }

    public static function calcCredit($totalAmount, $deposit, $interestRate, $creditMonths, $commission) {
        $totalAmount = $totalAmount / (1 - $commission / 100);
        //credit monthly payment calculation
        $creditAmount = $totalAmount - $deposit;
        $monthlyInterestRatio = $interestRate / 12 / 100;
        return $creditAmount * $monthlyInterestRatio / (1 - 1 / pow(1 + $monthlyInterestRatio, $creditMonths));
    }

    public function getAllSuppliersCombinePossibleMonths($creditSuppliersDtos) {

        $ret = array();
        foreach ($creditSuppliersDtos as $csdto) {
            $ret = array_merge($ret, explode(',', $csdto->getPossibleCreditMonths()));
        }
        $ret = array_unique($ret);
        asort($ret, SORT_NUMERIC);
        return $ret;
    }

    public function getMapper() {
        return null;
    }

}

?>