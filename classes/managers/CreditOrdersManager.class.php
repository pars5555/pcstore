<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/CreditOrdersMapper.class.php");

/**
 * OrdersManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class CreditOrdersManager extends AbstractManager {

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
        $this->mapper = CreditOrdersMapper::getInstance();
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

            self::$instance = new CreditOrdersManager($config, $args);
        }
        return self::$instance;
    }

    public function addCreditOrder($orderId, $deposit, $credit_supplier_id, $credit_months, $annualInterestPercent, $creditMonthlyPayment) {
        $this->mapper->insertValues(array('order_id', 'deposit', 'credit_supplier_id', 'credit_months', 'annual_interest_percent', 'monthly_payment'), array($orderId, $deposit, $credit_supplier_id, $credit_months, $annualInterestPercent, $creditMonthlyPayment));
    }

}

?>