<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/CustomerCartManager.class.php");
require_once (CLASSES_PATH . "/managers/checkout/CheckoutManager.class.php");
require_once (CLASSES_PATH . "/managers/BundleItemsManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/SpecialFeesManager.class.php");
require_once (CLASSES_PATH . "/managers/checkout/CreditManager.class.php");
require_once (CLASSES_PATH . "/managers/CreditSuppliersManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class FinalStepInnerLoad extends GuestLoad {

    public function load() {
        $customer = $this->getCustomer();
        $customerEmail = strtolower($customer->getEmail());
        $customerCartManager = CustomerCartManager::getInstance($this->config, $this->args);
        $checkoutManager = CheckoutManager::getInstance($this->config, $this->args);
        $bundleItemsManager = BundleItemsManager::getInstance($this->config, $this->args);
        $itemManager = ItemManager::getInstance($this->config, $this->args);


        $userLevel = $this->sessionManager->getUser()->getLevel();
        $user_id = $this->sessionManager->getUser()->getId();


        $cartItemsDtos = $customerCartManager->getCustomerCart($customerEmail, $user_id, $userLevel);
        $pv = $checkoutManager->getPriceVariety($cartItemsDtos, $userLevel);
        $discountAvailable = $checkoutManager->isDiscountAvailableForAtleastOneItem($cartItemsDtos);

        $groupedCartItems = $customerCartManager->groupBundleItemsInArray($cartItemsDtos);
        $cartChanges = $customerCartManager->getCustomerCartItemsChanges($groupedCartItems);

        $customerCartChangesMessages = $checkoutManager->getCustomerCartChangesMessages($cartChanges);
        if (!empty($customerCartChangesMessages)) {
            $this->addParam('customerMessages', $customerCartChangesMessages);
        }


        //all cart items, bundle items grouped in sub array
        $cartTotalDealsDiscountAMD = 0;
        if ($_REQUEST['cho_payment_type'] != 'cash') {
            $_REQUEST['cho_promo_codes'] = '';
        }
        
        $cho_include_vat = $this->secure($_REQUEST['cho_include_vat']);
        if (!empty($_REQUEST['cho_promo_codes'])) {

            $cho_promo_codes = $this->secure($_REQUEST['cho_promo_codes']);

            $cho_promo_codes_arrray = explode(',', $cho_promo_codes);
            $validPromoDiscount = $checkoutManager->applyAllItemsPromoOnCartItems($groupedCartItems, $cho_promo_codes_arrray, $cho_include_vat);
            $existingDealsPromoCodesArray = $checkoutManager->applyDealsDiscountsOnCartItems($groupedCartItems, $cho_promo_codes_arrray, $cho_include_vat);
            $existingDealsPromoCodesArray[] = $validPromoDiscount;
            $_REQUEST['cho_promo_codes'] = implode(',', $existingDealsPromoCodesArray);
            $cartTotalDealsDiscountAMD = $checkoutManager->getCartTotalDealsDiscountAMD($groupedCartItems);
            $this->addParam("cartTotalDealsDiscountAMD", $cartTotalDealsDiscountAMD);
        }


        list($grandTotalAMD, $grandTotalUSD) = $customerCartManager->calcCartTotal($groupedCartItems, true, $userLevel, $cho_include_vat);

        $allItemsAreAvailable = $customerCartManager->areAllItemsAvailableInCustomerCart($groupedCartItems);

        //discount available for at leat one item in the cart
        $this->addParam('discountAvailable', $discountAvailable);

        //priceVariety the price variety in customer cart. Can be 'amd', 'usd' or 'both';
        $this->addParam('priceVariety', $pv);



        $this->addParam('cartItems', $groupedCartItems);
        $this->addParam('itemManager', $itemManager);
        $this->addParam('checkoutManager', $checkoutManager);

        $this->addParam('allItemsAreAvailable', $allItemsAreAvailable);
        $this->addParam('emptyCart', empty($cartItemsDtos));

        $this->addParam('bundleItemsManager', $bundleItemsManager);

        //cart grand totla included discounts, this is the final value that customer should pay for his cart
        $this->addParam('grandTotalAMD', $grandTotalAMD);
        $this->addParam('grandTotalUSD', $grandTotalUSD);

        if (strtolower($_REQUEST['cho_shipping_region']) != 'yerevan' || ($_REQUEST['billing_is_different_checkbox'] && strtolower($_REQUEST['cho_billing_region']) != 'yerevan')) {
            $this->addParam('shipping_billing_region_ok', false);
        } else {
            $this->addParam('shipping_billing_region_ok', true);
        }

        $shippingCost = 0;
        if ($_REQUEST['cho_do_shipping'] == 1) {
            $specialFeesManager = SpecialFeesManager::getInstance($this->config, $this->args);
            if ($grandTotalAMD < intval($this->getCmsVar('shipping_in_yerevan_free_amd_over'))) {
                $region = $this->secure($_REQUEST['cho_shipping_region']);
                $shippingCostDto = $specialFeesManager->getShippingCost($region);
                $shippingCost = intval(isset($shippingCostDto) ? intval($shippingCostDto->getPrice()) : -1);
            } else {
                $shippingCost = 0;
            }
            $this->addParam('shipping_cost', $shippingCost);
        }
        $grandTotalAMDWithShipping = intval($grandTotalAMD + ($shippingCost > 0 ? $shippingCost : 0));

        if ($userLevel === UserGroups::$USER) {
            $userPoints = $customer->getPoints();
            $usablePoints = 0;
            if ($userPoints > 0 && $grandTotalAMDWithShipping > 0) {
                $this->addParam('user_points_applicable', 'true');
                if ($userPoints > $grandTotalAMDWithShipping) {
                    $usablePoints = $grandTotalAMDWithShipping;
                } else {
                    $usablePoints = $userPoints;
                }
                $this->addParam('usablePoints', $usablePoints);
            }
        }
        if ($_REQUEST['cho_payment_type'] == 'credit') {
            $_REQUEST['cho_apply_user_points'] = 0;
        }

        if ($_REQUEST['cho_apply_user_points'] == 1) {
            $grandTotalAMDWithShipping -= $usablePoints;
        }

        $this->addParam('grandTotalAMDWithShipping', $grandTotalAMDWithShipping);

        $this->addParam('final_step', 'true');

        if ($_REQUEST['cho_payment_type'] == 'credit') {
            $cho_credit_supplier_id = $this->secure($_REQUEST['cho_credit_supplier_id']);

            $cho_selected_deposit_amount = $this->secure($_REQUEST['cho_selected_deposit_amount']);
            $cho_selected_credit_months = $this->secure($_REQUEST['cho_selected_credit_months']);

            $creditSuppliersManager = CreditSuppliersManager::getInstance($this->config, $this->args);
            $allCreditSuppliersDtos = $creditSuppliersManager->getAllCreditSuppliers();
            $creditSuppliersIds = $creditSuppliersManager->getSuppliersIdsArray($allCreditSuppliersDtos);
            $creditSuppliersInMapArrayById = $creditSuppliersManager->getCreditSuppliersInMapArrayById($allCreditSuppliersDtos);
            $creditSupplierDto = $creditSuppliersInMapArrayById[$cho_credit_supplier_id];

            //var_dump($creditSupplierDto);
            $creditSupplierDisplayName = $this->getPhrase($creditSupplierDto->getDisplayNameId());
            $this->addParam('creditSupplierDisplayName', $creditSupplierDisplayName);

            $creditManager = CreditManager::getInstance($this->config, $this->args);
            $commission = $creditSupplierDto->getCommission();
            $annualInterestPercent = floatval($creditSupplierDto->getAnnualInterestPercent());
            $annualInterestPercent += floatval($creditSupplierDto->getAnnualCommision());
            $monthlyPaymentAmount = $creditManager->calcCredit($grandTotalAMD, $cho_selected_deposit_amount, $annualInterestPercent, $cho_selected_credit_months, $commission);
            $this->addParam("monthlyPaymentAmount", round($monthlyPaymentAmount));
        }

        $this->addParam('req_params', $_REQUEST);

        $paymentType = $_REQUEST['cho_payment_type'];
        $payment_option_values = explode(',', $this->getCmsVar('payment_option_values'));
        $payment_options_display_names_ids = explode(',', $this->getCmsVar('payment_options_display_names_ids'));
        $index = array_search($paymentType, $payment_option_values);
        $paymentTypeDisplayNameId = $payment_options_display_names_ids[$index];
        $this->addParam('paymentTypeDisplayNameId', $paymentTypeDisplayNameId);
        
         $this->addParam('maxItemCartCount', intval($this->getCmsVar('max_item_cart_count')));
         
         $userManager = UserManager::getInstance($this->config, $this->args);
         $vipCustomer = $userManager->isVipAndVipEnabled($customer);
         $this->addParam('vip_enabled', $vipCustomer?1:0);
         
    }

    public function getDefaultLoads($args) {
        $loads = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/checkout/final_step_inner.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

}

?>