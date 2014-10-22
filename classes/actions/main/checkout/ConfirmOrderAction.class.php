<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");

require_once (CLASSES_PATH . "/managers/OrdersManager.class.php");
require_once (CLASSES_PATH . "/managers/OrderDetailsManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/CustomerCartManager.class.php");
require_once (CLASSES_PATH . "/managers/BundleItemsManager.class.php");
require_once (CLASSES_PATH . "/managers/SpecialFeesManager.class.php");
require_once (CLASSES_PATH . "/managers/CreditOrdersManager.class.php");
require_once (CLASSES_PATH . "/managers/checkout/CreditManager.class.php");
require_once (CLASSES_PATH . "/managers/CreditSuppliersManager.class.php");
require_once (CLASSES_PATH . "/managers/EmailSenderManager.class.php");
require_once (CLASSES_PATH . "/managers/checkout/CheckoutManager.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/EmailAccountsManager.class.php");
require_once (CLASSES_PATH . "/managers/checkout/CheckoutManager.class.php");
require_once (CLASSES_PATH . "/managers/DiscountPromoCodesManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class ConfirmOrderAction extends GuestAction {

    private $itemManager;
    private $customerCartManager;
    private $ordersManager;
    private $creditOrdersManager;
    private $bundleItemsManager;
    private $specialFeesManager;
    private $orderDetailsManager;
    private $checkoutManager;

    public function service() {

        $this->initManagers();
        list($cho_include_vat, $cho_do_shipping, $cho_shipping_recipient_name,
                $cho_shipping_address, $cho_shipping_region, $cho_shipping_tel,
                $cho_shipping_cell, $billing_is_different_checkbox,
                $cho_billing_recipient_name, $cho_billing_address,
                $cho_billing_region, $cho_billing_tel, $cho_billing_cell,
                $cho_payment_type, $cho_apply_user_points, $cho_credit_supplier_id,
                $cho_selected_deposit_amount, $cho_selected_credit_months, $metadataObject) = $this->checkoutManager->initShippingParamsFromRequest();

        $userTypeString = $this->getUserLevelString();
        $customer = $this->getCustomer();
        $userEmail = $customer->getEmail();
        $dollarExchange = $this->getCmsVar("us_dollar_exchange");
        $paymentType = $cho_payment_type;
        $userLevel = $this->getUserLevel();
        $customerCurrentCart = $this->customerCartManager->getCustomerCart($userEmail, $this->getUserId(), $userLevel);
        $calcCartTotalDealerPrice = $this->customerCartManager->calcCartTotalDealerPrice($customerCurrentCart, $cho_include_vat);
        $groupedCartItems = $this->customerCartManager->groupBundleItemsInArray($customerCurrentCart);
        $allItemsAreAvailable = $this->customerCartManager->areAllItemsAvailableInCustomerCart($groupedCartItems);

        if (!$allItemsAreAvailable) {
            $jsonArr = array('status' => "err", "message" => 'Some item(s) are missing!');
            echo json_encode($jsonArr);
            return false;
        }

        //calculating deals discounts
        //all cart items, bundle items grouped in sub array
        $cartTotalDealsDiscountAMD = 0;
        $existingDealsPromoCodes = "";
        if (!empty($_REQUEST['cho_promo_codes'])) {
            $checkoutManager = CheckoutManager::getInstance($this->config, $this->args);
            $cho_promo_codes = $this->secure($_REQUEST['cho_promo_codes']);
            $cho_promo_codes_arrray = explode(',', $cho_promo_codes);
            $validPromoDiscount = $checkoutManager->applyAllItemsPromoOnCartItems($groupedCartItems, $cho_promo_codes_arrray, $cho_include_vat);
            $existingDealsPromoCodesArray = $checkoutManager->applyDealsDiscountsOnCartItems($groupedCartItems, $cho_promo_codes_arrray, $cho_include_vat);
            $existingDealsPromoCodesArray[] = $validPromoDiscount;
            $existingDealsPromoCodes = implode(',', $existingDealsPromoCodesArray);
            $cartTotalDealsDiscountAMD = $checkoutManager->getCartTotalDealsDiscountAMD($groupedCartItems);
        }

        list($grandTotalAMD, $grandTotalUSD) = $this->customerCartManager->calcCartTotal($groupedCartItems, true, $userLevel, $cho_include_vat);

        if ($grandTotalAMD < intval($this->getCmsVar("minimum_order_amount_amd"))) {
            $jsonArr = array('status' => "err", "message" => $this->getPhrase(420) . $this->getCmsVar("minimum_order_amount_amd"));
            echo json_encode($jsonArr);
            return false;
        }

        $shippingCost = 0;
        if ($cho_do_shipping != 1) {
            $cho_billing_cell = $this->secure($_REQUEST['send_to_cell_phone']);
        } else {
            $specialFeesManager = SpecialFeesManager::getInstance($this->config, $this->args);
            if ($grandTotalAMD < intval($this->getCmsVar('shipping_in_yerevan_free_amd_over'))) {
                $region = $this->secure($_REQUEST['cho_shipping_region']);
                $shippingCostDto = $specialFeesManager->getShippingCost($region);
                $shippingCost = intval(isset($shippingCostDto) ? intval($shippingCostDto->getPrice()) : -1);
            } else {
                $shippingCost = 0;
            }
        }
        if ($shippingCost == -1) {
            $jsonArr = array('status' => "err", "message" => 'Shipping is not available!');
            echo json_encode($jsonArr);
            return false;
        }

        $usablePoints = 0;
        if ($userLevel === UserGroups::$USER) {
            $grandTotalAMDWithShipping = $grandTotalAMD + $shippingCost;
            $userPoints = $customer->getPoints();
            if ($userPoints > 0 && $grandTotalAMDWithShipping > 0 && $cho_apply_user_points == 1) {
                if ($userPoints > $grandTotalAMDWithShipping) {
                    $usablePoints = $grandTotalAMDWithShipping;
                } else {
                    $usablePoints = $userPoints;
                }
            }
        }

        //start order confirming
        $orderId = $this->ordersManager->addOrder($userEmail, $calcCartTotalDealerPrice, $cho_shipping_tel, $cho_billing_tel, $cho_shipping_cell, $cho_billing_cell, $paymentType, $userTypeString, $dollarExchange, $cho_do_shipping
                , $cho_shipping_address, $cho_billing_address, $cho_shipping_region, $cho_shipping_recipient_name, $cho_billing_recipient_name, $cho_billing_region, ($billing_is_different_checkbox != '1'), $usablePoints, $shippingCost, $grandTotalAMD, $grandTotalUSD, $existingDealsPromoCodes, $cartTotalDealsDiscountAMD, $cho_include_vat, $metadataObject);
        $this->orderDetailsManager->addOrderDetails($orderId, $userEmail, $this->getUser(), $cho_include_vat);

        //reduce user point if any used
        if ($usablePoints > 0) {
            $userManager = UserManager::getInstance($this->config, $this->args);
            $description = "User points used to pay for order numer $orderId.";
            $userManager->subtractUserPoints($this->getUserId(), $usablePoints, $description);
        }

        if ($paymentType == 'credit') {
            $this->creditOrdersManager = CreditOrdersManager::getInstance($this->config, $this->args);
            $creditSuppliersManager = CreditSuppliersManager::getInstance($this->config, $this->args);
            $creditSupplierDto = $creditSuppliersManager->selectByPK($cho_credit_supplier_id);
            $annualInterestPercent = floatval($creditSupplierDto->getAnnualInterestPercent());
            $annualInterestPercent += floatval($creditSupplierDto->getAnnualCommision());
            $commission = $creditSupplierDto->getCommission();
            $creditMonthlyPayment = CreditManager::calcCredit($grandTotalAMD, $cho_selected_deposit_amount, $annualInterestPercent, $cho_selected_credit_months, $commission);
            $this->creditOrdersManager->addCreditOrder($orderId, $cho_selected_deposit_amount, $cho_credit_supplier_id, $cho_selected_credit_months, $annualInterestPercent, $creditMonthlyPayment);
        }
        $this->customerCartManager->emptyCustomerCart($userEmail);
        $this->emailOrderDetails($orderId);

        if (isset($validPromoDiscount)) {
            $discountPromoCodesManager = DiscountPromoCodesManager::getInstance($this->config, $this->args);
            $discountDto = $discountPromoCodesManager->getByPromoCode($validPromoDiscount);
            if ($discountDto) {
                $discountDto->setUsed(1);
                $discountPromoCodesManager->updateByPK($discountDto);
            }
        }

        $jsonArr = array('status' => "ok", "order_id" => $orderId);
        echo json_encode($jsonArr);
        return true;
    }

    private function initManagers() {
        $this->itemManager = ItemManager::getInstance($this->config, $this->args);
        $this->customerCartManager = CustomerCartManager::getInstance($this->config, $this->args);
        $this->ordersManager = OrdersManager::getInstance($this->config, $this->args);
        $this->bundleItemsManager = BundleItemsManager::getInstance($this->config, $this->args);
        $this->specialFeesManager = SpecialFeesManager::getInstance($this->config, $this->args);
        $this->orderDetailsManager = OrderDetailsManager::getInstance($this->config, $this->args);
        $this->checkoutManager = CheckoutManager::getInstance($this->config, $this->args);
    }

    private function emailOrderDetails($orderId) {
        $emailSenderManager = new EmailSenderManager('gmail');

        $customer = $this->getCustomer();
        $userManager = UserManager::getInstance($this->config, $this->args);
        $customerEmail = $userManager->getRealEmailAddressByUserDto($customer);
        $emailAccountsManager = EmailAccountsManager::getInstance($this->config, $this->args);
        $infoEmailAddress = $emailAccountsManager->getEmailAddressById('info');
        $recipients = array($customerEmail, $infoEmailAddress);
        $lname = $customer->getLastName();
        $userName = $customer->getName();
        if (isset($lname)) {
            $userName .= ' ' . $lname;
        }

        $orderJoinedDetailsDtos = $this->ordersManager->getOrderJoinedWithDetails($orderId);

        $goupedOrderJoinedDetailsDtos = $this->groupBundlesInOrderJoinedDetailsDtos($orderJoinedDetailsDtos);

        $paymentType = $orderJoinedDetailsDtos[0]->getPaymentType();
        $payment_option_values = explode(',', $this->getCmsVar('payment_option_values'));
        $payment_options_display_names_ids = explode(',', $this->getCmsVar('payment_options_display_names_ids'));
        $index = array_search($paymentType, $payment_option_values);
        $paymentTypeDisplayNameId = $payment_options_display_names_ids[$index];
        $subject = $this->getPhrase(299);
        if ($paymentType == 'credit') {
            $template = "customer_credit_order";
        } else {
            $template = "customer_cash_order";
        }
        $params = array("user_name" => $userName, "dtos" => $goupedOrderJoinedDetailsDtos, "itemManager" => $this->itemManager, "support_phone" => $this->getCmsVar('pcstore_support_phone_number'), 'paymentTypeDisplayNameId' => $paymentTypeDisplayNameId);
        if ($paymentType == 'credit') {
            $creditSuppliersManager = CreditSuppliersManager::getInstance($this->config, $this->args);
            $csid = $orderJoinedDetailsDtos[0]->getCreditOrdersCreditSupplierId();
            $creditSupplierDto = $creditSuppliersManager->selectByPK($csid);
            $commission = $creditSupplierDto->getCommission();
            $grandTotalAMD = intval($orderJoinedDetailsDtos[0]->getOrderTotalAmd());
            $grandTotalAmdWithCommission = intval($grandTotalAMD / (1 - $commission / 100));
            $params['grandTotalAmdWithCommission'] = $grandTotalAmdWithCommission;
        }
        $emailSenderManager->sendEmail('orders', $recipients, $subject, $template, $params, '', '', true, false);
    }

    public function groupBundlesInOrderJoinedDetailsDtos($orderJoinedDetailsDtos) {
        $ret = array();
        $lbid = 0;
        foreach ($orderJoinedDetailsDtos as $key => $orderItem) {
            $bid = $orderItem->getOrderDetailsBundleId();
            if ($bid > 0) {
                if ($lbid != $bid) {
                    $ret[] = array($orderItem);
                } else {
                    $ret[count($ret) - 1][] = $orderItem;
                }
                $lbid = $bid;
            } else {
                $ret[] = $orderItem;
            }
        }
        return $ret;
    }

    public function getRequestGroup() {
        return RequestGroups::$userCompanyRequest;
    }

}

?>