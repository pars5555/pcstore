<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/OrderMapper.class.php");

/**
 * OrdersManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class OrdersManager extends AbstractManager {

    /**
     * @var app config
     */
    private $config;
    public $orderStatusesValues = array(0/* in process */, 1/* completed */, 2/* canceled */, 3 /* confirmed */);
    public $orderStatusesDisplayNamesIds = array(373, 374, 375, 590);

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
        $this->mapper = OrderMapper::getInstance();

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

            self::$instance = new OrdersManager($config, $args);
        }
        return self::$instance;
    }

    public function addOrder($userEmail, $orderDealerPriceUsd, $cho_shipping_tel, $cho_billing_tel, $cho_shipping_cell, $cho_billing_cell, $paymentType, $userTypeString, $dollarExchange, $cho_do_shipping, $cho_shipping_address, $cho_billing_address, $cho_shipping_region, $cho_shipping_recipient_name, $cho_billing_recipient_name, $cho_billing_region, $billing_is_same_as_shipping, $usablePoints, $shippingCost, $grandTotalAMD, $grandTotalUSD, $usedDealsIds, $totalPromoDiscountAmd, $includedVat, $metadataObject) {

        $dto = $this->mapper->createDto();
        $dto->setCustomerEmail($userEmail);
        $dto->setBillingPhone($cho_billing_tel);
        $dto->setBillingRecipientName($cho_billing_recipient_name);
        $dto->setShippingRecipientName($cho_shipping_recipient_name);
        $dto->setShippingPhone($cho_shipping_tel);
        $dto->setShippingCell($cho_shipping_cell);
        $dto->setBillingCell($cho_billing_cell);
        $dto->setPaymentType($paymentType);
        $dto->setCustomerType($userTypeString);
        $dto->setDollarExchangeUsdAmd($dollarExchange);
        $dto->setDoShipping($cho_do_shipping);
        $dto->setShippingAddress($cho_shipping_address);
        $dto->setBillingAddress($cho_billing_address);
        $dto->setShippingRegion($cho_shipping_region);
        $dto->setBillingRegion($cho_billing_region);
        $dto->setBillingIsSameAsShipping($billing_is_same_as_shipping);
        $dto->setShippingAmd($shippingCost);
        $dto->setUsedPoints($usablePoints);
        $dto->setOrderTotalAmd($grandTotalAMD);
        $dto->setOrderTotalUsd($grandTotalUSD);
        $dto->setUsedDealsIds($usedDealsIds);
        $dto->setTotalPromoDiscountAmd($totalPromoDiscountAmd);
        $dto->setMetadataJson(json_encode($metadataObject, JSON_PRETTY_PRINT));
        $dto->setOrderDealerPriceUsd($orderDealerPriceUsd);
        $dto->setOrderDateTime(date('Y-m-d H:i:s'));
        if ($includedVat == 1) {
            $dto->setIncludedVat(1);
        }
        return $this->mapper->insertDto($dto);
    }

    public function getOrderJoinedWithDetails($orderId) {
        return $this->mapper->getOrderJoinedWithDetails($orderId);
    }

    public function getOrderBy3rdPartyToken($token) {
        $dtos = $this->selectByField('3rd_party_payment_token', $token);
        if (!empty($dtos)) {
            return $dtos[0];
        }
        return null;
    }

    public function getOrderStatus($orderId) {

        $dto = $this->mapper->selectByPK($orderId);
        if ($dto) {
            return $dto->getStatus();
        } else
            return null;
    }

    public function getCustomerTotalConfirmedOrdersCount($customer_email) {
        $dtos = $this->selectByField(array('customer_email' => $customer_email, 'status' => 1));
        return count($dtos);
    }

    public function getCustomerOrderJoinedWithDetails($customer_email) {
        return $this->mapper->getCustomerOrderJoinedWithDetails($customer_email);
    }

    public function getAllOrdersJoinedWithDetails($show_only) {
        if ($show_only == -1) {
            return $this->mapper->getAllOrdersJoinedWithDetails();
        } else {
            return $this->mapper->getAllOrdersJoinedWithDetails($show_only);
        }
    }

    public function setOrderStatus($orderId, $status) {
        return $this->mapper->updateTextField($orderId, 'status', $status);
    }

    public function setOrderDeliverDateTime($orderId, $dateTime) {
        return $this->mapper->updateTextField($orderId, 'delivered_date_time', $dateTime);
    }

    public function calcOrderProfitAmd($orderId) {
        $dto = $this->mapper->selectByPK($orderId);
        if (isset($dto)) {
            $orderTotalUsd = floatval($dto->getOrderTotalUsd());
            $orderTotalAmd = intval($dto->getOrderTotalAmd());
            $orderDealerPriceUsd = floatval($dto->getOrderDealerPriceUsd());
            $dollarExchangeUsdAmd = floatval($dto->getDollarExchangeUsdAmd());
            $profit = $orderTotalUsd + $orderTotalAmd / $dollarExchangeUsdAmd - $orderDealerPriceUsd;
            $itemManager = ItemManager::getInstance($this->config, $this->args);
            return $itemManager->exchangeFromUsdToAMD($profit, $dollarExchangeUsdAmd, true);
        }
        return 0;
    }

    public function setOrderCancelReasonText($orderId, $text) {
        return $this->mapper->updateTextField($orderId, 'cancel_reason_text', $text);
    }

    public function getOrderStatusesDisplayNamesPhrases() {
        $orderStatusesDisplayNames = array();
        foreach ($this->orderStatusesDisplayNamesIds as $key => $phraseId) {
            $orderStatusesDisplayNames[] = $this->getPhrase($phraseId);
        }
        return $orderStatusesDisplayNames;
    }

    public function getOrderTotalUsdToPay($order, $grandTotal = false) {
        if (!is_object($order) && is_numeric($order)) {
            $orderDto = $this->selectByPK($order);
        } else {
            $orderDto = $order;
        }
        $orderTotalAmd = intval($orderDto->getOrderTotalAmd());
        $usedPoints = intval($orderDto->getUsedPoints());
        $orderTotalUsd = $orderDto->getOrderTotalUsd();
        $shippingAmd = $orderDto->getShippingAmd();
        $itemManager = ItemManager::getInstance($this->config, $this->args);
        $exchangeFromAMDToUSD = $itemManager->exchangeFromAMDToUSD($orderTotalAmd - $usedPoints);
        $totalUsdToPay = floatval($exchangeFromAMDToUSD) + floatval($orderTotalUsd);
        $shippingUsd = ceil($itemManager->exchangeFromAMDToUSD(intval($shippingAmd))) * 100 / 100;
        $paymentAmount = ceil($totalUsdToPay * 100) / 100;
        if ($grandTotal == true) {
            return floatval($paymentAmount) + floatval($shippingUsd);
        } else {
            return array(floatval($paymentAmount), floatval($shippingUsd));
        }
    }

}

?>