<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/OrdersManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/CustomerCartManager.class.php");
require_once (CLASSES_PATH . '/lib/stripe-php-1.17.1/lib/Stripe.php');

/**
 * @author Vahagn Sookiasian
 */
class ConfirmStripePaymentAction extends GuestAction {

    public function service() {
        $order_id = $_REQUEST['order_id'];
        $token = $_REQUEST['stripeToken'];
        $userManager = UserManager::getInstance($this->config, $this->args);
        $ordersManager = OrdersManager::getInstance($this->config, $this->args);
        $orderTotalUsdToPay = $ordersManager->getOrderTotalUsdToPay($order_id, true);

        Stripe::setApiKey($this->getCmsVar('stripe_secret_key'));

        $email = $userManager->getRealEmailAddressByUserDto($this->getCustomer());
        try {
            $charge = Stripe_Charge::create(array(
                        "card" => $token,
                        "description" => $email . ' (order #' . $order_id . ')',
                        'amount' => $orderTotalUsdToPay * 100,
                        'currency' => 'usd'
            ));
        } catch (Exception $e) {
            $this->error(array('message' => $e->getMessage()));
        }
        $ordersManager->updateTextField($order_id, '3rd_party_payment_token', $token);
        $ordersManager->updateTextField($order_id, '3rd_party_payment_received', 1);
        $this->ok();
    }

    public function getRequestGroup() {
        return RequestGroups::$userCompanyRequest;
    }

}

/* object(Stripe_Charge)#106 (5) {
  ["_apiKey":protected]=>
  string(32) "sk_test_z0hIow3tcyWDS0feyPHQoWaw"
  ["_values":protected]=>
  array(22) {
  ["id"]=>
  string(27) "ch_14VzUGFFzt7PxaaLfVWPXcxv"
  ["object"]=>
  string(6) "charge"
  ["created"]=>
  int(1409119252)
  ["livemode"]=>
  bool(false)
  ["paid"]=>
  bool(true)
  ["amount"]=>
  int(1199)
  ["currency"]=>
  string(3) "usd"
  ["refunded"]=>
  bool(false)
  ["card"]=>
  object(Stripe_Card)#103 (5) {
  ["_apiKey":protected]=>
  string(32) "sk_test_z0hIow3tcyWDS0feyPHQoWaw"
  ["_values":protected]=>
  array(20) {
  ["id"]=>
  string(29) "card_14VzU9FFzt7PxaaLvHK3YaPq"
  ["object"]=>
  string(4) "card"
  ["last4"]=>
  string(4) "4242"
  ["brand"]=>
  string(4) "Visa"
  ["funding"]=>
  string(6) "credit"
  ["exp_month"]=>
  int(11)
  ["exp_year"]=>
  int(2016)
  ["fingerprint"]=>
  string(16) "EbOy2E4RWOIsM5ZX"
  ["country"]=>
  string(2) "US"
  ["name"]=>
  string(18) "pars5555@yahoo.com"
  ["address_line1"]=>
  NULL
  ["address_line2"]=>
  NULL
  ["address_city"]=>
  NULL
  ["address_state"]=>
  NULL
  ["address_zip"]=>
  NULL
  ["address_country"]=>
  NULL
  ["cvc_check"]=>
  string(4) "pass"
  ["address_line1_check"]=>
  NULL
  ["address_zip_check"]=>
  NULL
  ["customer"]=>
  string(18) "cus_4fK8wMWZ8GHOAS"
  }
  ["_unsavedValues":protected]=>
  object(Stripe_Util_Set)#102 (1) {
  ["_elts":"Stripe_Util_Set":private]=>
  array(0) {
  }
  }
  ["_transientValues":protected]=>
  object(Stripe_Util_Set)#101 (1) {
  ["_elts":"Stripe_Util_Set":private]=>
  array(0) {
  }
  }
  ["_retrieveOptions":protected]=>
  array(0) {
  }
  }
  ["captured"]=>
  bool(true)
  ["refunds"]=>
  object(Stripe_List)#100 (5) {
  ["_apiKey":protected]=>
  string(32) "sk_test_z0hIow3tcyWDS0feyPHQoWaw"
  ["_values":protected]=>
  array(5) {
  ["object"]=>
  string(4) "list"
  ["total_count"]=>
  int(0)
  ["has_more"]=>
  bool(false)
  ["url"]=>
  string(47) "/v1/charges/ch_14VzUGFFzt7PxaaLfVWPXcxv/refunds"
  ["data"]=>
  array(0) {
  }
  }
  ["_unsavedValues":protected]=>
  object(Stripe_Util_Set)#99 (1) {
  ["_elts":"Stripe_Util_Set":private]=>
  array(0) {
  }
  }
  ["_transientValues":protected]=>
  object(Stripe_Util_Set)#98 (1) {
  ["_elts":"Stripe_Util_Set":private]=>
  array(0) {
  }
  }
  ["_retrieveOptions":protected]=>
  array(0) {
  }
  }
  ["balance_transaction"]=>
  string(28) "txn_14VzUGFFzt7PxaaL1z3joSYC"
  ["failure_message"]=>
  NULL
  ["failure_code"]=>
  NULL
  ["amount_refunded"]=>
  int(0)
  ["customer"]=>
  string(18) "cus_4fK8wMWZ8GHOAS"
  ["invoice"]=>
  NULL
  ["description"]=>
  NULL
  ["dispute"]=>
  NULL
  ["metadata"]=>
  object(Stripe_AttachedObject)#97 (5) {
  ["_apiKey":protected]=>
  string(32) "sk_test_z0hIow3tcyWDS0feyPHQoWaw"
  ["_values":protected]=>
  array(0) {
  }
  ["_unsavedValues":protected]=>
  object(Stripe_Util_Set)#96 (1) {
  ["_elts":"Stripe_Util_Set":private]=>
  array(0) {
  }
  }
  ["_transientValues":protected]=>
  object(Stripe_Util_Set)#95 (1) {
  ["_elts":"Stripe_Util_Set":private]=>
  array(0) {
  }
  }
  ["_retrieveOptions":protected]=>
  array(0) {
  }
  }
  ["statement_description"]=>
  NULL
  ["receipt_email"]=>
  NULL
  }
  ["_unsavedValues":protected]=>
  object(Stripe_Util_Set)#105 (1) {
  ["_elts":"Stripe_Util_Set":private]=>
  array(0) {
  }
  }
  ["_transientValues":protected]=>
  object(Stripe_Util_Set)#104 (1) {
  ["_elts":"Stripe_Util_Set":private]=>
  array(0) {
  }
  }
  ["_retrieveOptions":protected]=>
  array(0) {
  }
  }








  object(Stripe_Charge)#106 (5) {
  ["_apiKey":protected]=>
  string(32) "sk_test_z0hIow3tcyWDS0feyPHQoWaw"
  ["_values":protected]=>
  array(22) {
  ["id"]=>
  string(27) "ch_14VzWTFFzt7PxaaLmwBzBftU"
  ["object"]=>
  string(6) "charge"
  ["created"]=>
  int(1409119389)
  ["livemode"]=>
  bool(false)
  ["paid"]=>
  bool(true)
  ["amount"]=>
  int(11990)
  ["currency"]=>
  string(3) "usd"
  ["refunded"]=>
  bool(false)
  ["card"]=>
  object(Stripe_Card)#103 (5) {
  ["_apiKey":protected]=>
  string(32) "sk_test_z0hIow3tcyWDS0feyPHQoWaw"
  ["_values":protected]=>
  array(20) {
  ["id"]=>
  string(29) "card_14VzWMFFzt7PxaaLHX32I0R2"
  ["object"]=>
  string(4) "card"
  ["last4"]=>
  string(4) "4242"
  ["brand"]=>
  string(4) "Visa"
  ["funding"]=>
  string(6) "credit"
  ["exp_month"]=>
  int(11)
  ["exp_year"]=>
  int(2016)
  ["fingerprint"]=>
  string(16) "EbOy2E4RWOIsM5ZX"
  ["country"]=>
  string(2) "US"
  ["name"]=>
  string(18) "pars5555@yahoo.com"
  ["address_line1"]=>
  NULL
  ["address_line2"]=>
  NULL
  ["address_city"]=>
  NULL
  ["address_state"]=>
  NULL
  ["address_zip"]=>
  NULL
  ["address_country"]=>
  NULL
  ["cvc_check"]=>
  string(4) "pass"
  ["address_line1_check"]=>
  NULL
  ["address_zip_check"]=>
  NULL
  ["customer"]=>
  string(18) "cus_4fKAMN9lm85zIg"
  }
  ["_unsavedValues":protected]=>
  object(Stripe_Util_Set)#102 (1) {
  ["_elts":"Stripe_Util_Set":private]=>
  array(0) {
  }
  }
  ["_transientValues":protected]=>
  object(Stripe_Util_Set)#101 (1) {
  ["_elts":"Stripe_Util_Set":private]=>
  array(0) {
  }
  }
  ["_retrieveOptions":protected]=>
  array(0) {
  }
  }
  ["captured"]=>
  bool(true)
  ["refunds"]=>
  object(Stripe_List)#100 (5) {
  ["_apiKey":protected]=>
  string(32) "sk_test_z0hIow3tcyWDS0feyPHQoWaw"
  ["_values":protected]=>
  array(5) {
  ["object"]=>
  string(4) "list"
  ["total_count"]=>
  int(0)
  ["has_more"]=>
  bool(false)
  ["url"]=>
  string(47) "/v1/charges/ch_14VzWTFFzt7PxaaLmwBzBftU/refunds"
  ["data"]=>
  array(0) {
  }
  }
  ["_unsavedValues":protected]=>
  object(Stripe_Util_Set)#99 (1) {
  ["_elts":"Stripe_Util_Set":private]=>
  array(0) {
  }
  }
  ["_transientValues":protected]=>
  object(Stripe_Util_Set)#98 (1) {
  ["_elts":"Stripe_Util_Set":private]=>
  array(0) {
  }
  }
  ["_retrieveOptions":protected]=>
  array(0) {
  }
  }
  ["balance_transaction"]=>
  string(28) "txn_14VzWTFFzt7PxaaLdtSyxGgu"
  ["failure_message"]=>
  NULL
  ["failure_code"]=>
  NULL
  ["amount_refunded"]=>
  int(0)
  ["customer"]=>
  string(18) "cus_4fKAMN9lm85zIg"
  ["invoice"]=>
  NULL
  ["description"]=>
  NULL
  ["dispute"]=>
  NULL
  ["metadata"]=>
  object(Stripe_AttachedObject)#97 (5) {
  ["_apiKey":protected]=>
  string(32) "sk_test_z0hIow3tcyWDS0feyPHQoWaw"
  ["_values":protected]=>
  array(0) {
  }
  ["_unsavedValues":protected]=>
  object(Stripe_Util_Set)#96 (1) {
  ["_elts":"Stripe_Util_Set":private]=>
  array(0) {
  }
  }
  ["_transientValues":protected]=>
  object(Stripe_Util_Set)#95 (1) {
  ["_elts":"Stripe_Util_Set":private]=>
  array(0) {
  }
  }
  ["_retrieveOptions":protected]=>
  array(0) {
  }
  }
  ["statement_description"]=>
  NULL
  ["receipt_email"]=>
  NULL
  }
  ["_unsavedValues":protected]=>
  object(Stripe_Util_Set)#105 (1) {
  ["_elts":"Stripe_Util_Set":private]=>
  array(0) {
  }
  }
  ["_transientValues":protected]=>
  object(Stripe_Util_Set)#104 (1) {
  ["_elts":"Stripe_Util_Set":private]=>
  array(0) {
  }
  }
  ["_retrieveOptions":protected]=>
  array(0) {
  }
  }
 */
?>


