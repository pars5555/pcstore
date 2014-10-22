<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/PaypalTransactionsMapper.class.php");

/**
 * PaypalTransactionsManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class PaypalTransactionsManager extends AbstractManager {

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
        $this->mapper = PaypalTransactionsMapper::getInstance();
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

            self::$instance = new PaypalTransactionsManager($config, $args);
        }
        return self::$instance;
    }

    public function setOrderPaymentError($orderId, $message) {
        $dtos = $this->selectByField('order_id', $orderId);
        if (empty($dtos)) {
            $dto = $this->createDto();
            $dto->setOrderId($orderId);
            $dto->setPaymentReceived(0);
            $dto->setMessage($message);
            $this->insertDto($dto);
        } else {
            $dto = $dtos[0];
            $dto->setOrderId($orderId);
            $dto->setPaymentReceived(0);
            $dto->setMessage($message);
            $this->updateByPk($dto);
        }
    }

    public function setOrderCompleted($orderId) {
        $dtos = $this->selectByField('order_id', $orderId);
        if (empty($dtos)) {
            $dto = $this->createDto();
            $dto->setOrderId($orderId);
            $dto->setPaymentReceived(1);
            $dto->setMessage('');
            $this->insertDto($dto);
        } else {
            $dto = $dtos[0];
            $dto->setOrderId($orderId);
            $dto->setPaymentReceived(1);
            $dto->setMessage('');
            $this->updateByPk($dto);
        }
    }

}

?>