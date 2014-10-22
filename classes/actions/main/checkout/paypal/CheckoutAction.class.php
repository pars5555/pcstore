<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/PaypalManager.class.php");
require_once (CLASSES_PATH . "/managers/OrdersManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class CheckoutAction extends GuestAction {

    public function service() {
        $paypalManager = PaypalManager::getInstance();
        $order_id = $_REQUEST['order_id'];
        $ordersManager = OrdersManager::getInstance($this->config, $this->args);
        list($paymentAmount, $shippingUsd) = $ordersManager->getOrderTotalUsdToPay($order_id);
        $currencyCodeType = "USD";
        $paymentType = "Sale";
        $returnURL = "https://pcstore.am/dyn/main_checkout_paypal/do_order_confirm";
        $cancelURL = "https://pcstore.am/dyn/main_checkout_paypal/do_order_cancel";
        $items = array();
        $items[] = array('name' => 'Pcstore Order Number #' . $order_id, 'amt' => $paymentAmount, 'qty' => 1);
        $_SESSION["Payment_Amount"] = $paymentAmount + $shippingUsd;
        $resArray = $paypalManager->CallShortcutExpressCheckout($paymentAmount, $currencyCodeType, $paymentType, $returnURL, $cancelURL, $items, $shippingUsd);
        $ack = strtoupper($resArray["ACK"]);
        if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
            $ordersManager->updateTextField($order_id, '3rd_party_payment_token', $resArray["TOKEN"]);
            $paypalManager->RedirectToPayPal($resArray["TOKEN"]);
        } else {
            //Display a user friendly Error on the page using any of the following error information returned by PayPal
            $ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
            $ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
            $ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
            $ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
            echo "SetExpressCheckout API call failed. ";
            echo "Detailed Error Message: " . $ErrorLongMsg;
            echo "Short Error Message: " . $ErrorShortMsg;
            echo "Error Code: " . $ErrorCode;
            echo "Error Severity Code: " . $ErrorSeverityCode;
        }
    }

    public function getRequestGroup() {
        return RequestGroups::$userRequest;
    }

}

?>