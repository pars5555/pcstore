<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/managers/CustomerCartManager.class.php");
require_once (CLASSES_PATH . "/managers/checkout/CheckoutManager.class.php");
require_once (CLASSES_PATH . "/managers/BundleItemsManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/SpecialFeesManager.class.php");
require_once (CLASSES_PATH . "/managers/DealsManager.class.php");
require_once (CLASSES_PATH . "/managers/DiscountPromoCodesManager.class.php");

/**
 * PcConfiguratorManager class is responsible for creating,
 */
class CheckoutManager extends AbstractManager {

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

            self::$instance = new CheckoutManager($config, $args);
        }
        return self::$instance;
    }

    public function initShippingParamsFromRequest() {
        $cho_include_vat = $_REQUEST['cho_include_vat'];
        $cho_do_shipping = $_REQUEST['cho_do_shipping'];
        $cho_shipping_recipient_name = $_REQUEST['cho_shipping_recipient_name'];
        $cho_shipping_address = $_REQUEST['cho_shipping_address'];
        $cho_shipping_region = $_REQUEST['cho_shipping_region'];
        $cho_shipping_tel = $_REQUEST['cho_shipping_tel'];
        $cho_shipping_cell = $_REQUEST['cho_shipping_cell'];
        $cho_payment_type = $_REQUEST['cho_payment_type'];
        $cho_credit_supplier_id = $_REQUEST['cho_credit_supplier_id'];
        $cho_selected_deposit_amount = $_REQUEST['cho_selected_deposit_amount'];
        $cho_selected_credit_months = $_REQUEST['cho_selected_credit_months'];
        $metadataObject = null;
        if ($cho_payment_type === 'bank') {
            $cho_company_name = $this->secure($_REQUEST['cho_company_name']);
            $cho_company_hvhh = $this->secure($_REQUEST['cho_company_hvhh']);
            $cho_company_address = $this->secure($_REQUEST['cho_company_address']);
            $cho_company_bank = $this->secure($_REQUEST['cho_company_bank']);
            $cho_company_bank_account_number = $this->secure($_REQUEST['cho_company_bank_account_number']);
            $cho_company_delivering_address = $this->secure($_REQUEST['cho_company_delivering_address']);
            $metadataObject = new stdClass();
            $metadataObject->company_name = $cho_company_name;
            $metadataObject->company_hvhh = $cho_company_hvhh;
            $metadataObject->company_address = $cho_company_address;
            $metadataObject->company_bank = $cho_company_bank;
            $metadataObject->bank_account_number = $cho_company_bank_account_number;
            $metadataObject->company_delivering_address = $cho_company_delivering_address;
        }

        $billing_is_different_checkbox = $_REQUEST['billing_is_different_checkbox'];
        if ($billing_is_different_checkbox != '1') {
            $cho_billing_recipient_name = $cho_shipping_recipient_name;
            $cho_billing_address = $cho_shipping_address;
            $cho_billing_region = $cho_shipping_region;
            $cho_billing_tel = $cho_shipping_tel;
            $cho_billing_cell = $cho_shipping_cell;
        } else {
            $cho_billing_recipient_name = $_REQUEST['cho_billing_recipient_name'];
            $cho_billing_address = $_REQUEST['cho_billing_address'];
            $cho_billing_region = $_REQUEST['cho_billing_region'];
            $cho_billing_cell = $_REQUEST['cho_billing_cell'];
            $cho_billing_tel = $_REQUEST['cho_billing_tel'];
        }
        $cho_apply_user_points = $_REQUEST['cho_apply_user_points'];

        return array($cho_include_vat, $cho_do_shipping, $cho_shipping_recipient_name,
            $cho_shipping_address, $cho_shipping_region, $cho_shipping_tel,
            $cho_shipping_cell, $billing_is_different_checkbox, $cho_billing_recipient_name,
            $cho_billing_address, $cho_billing_region, $cho_billing_tel, $cho_billing_cell,
            $cho_payment_type, $cho_apply_user_points, $cho_credit_supplier_id,
            $cho_selected_deposit_amount, $cho_selected_credit_months, $metadataObject);
    }

    /**
     * @param $groupedCartItems should have getCustomerCart function format and
     * then group the result item using groupBundleItemsInArray function.
     * Return following format
     * array("CartRowId"=>change, ...)
     * if change is array(0=>ItemDisplayName, 1=>false) meanse item is not available,
     * if change is array(0=>ItemDisplayName, 1=>oldDealerPrice, 2=>currentDealerPrice) then meanse item price is changed
     * if change is array("ItemId"=>change, ...) meanse item is bundle and there are changes,
     */
    public function getCustomerCartChangesMessages($cartChanges) {
        if (empty($cartChanges)) {
            return array();
        }
        $messages = array();
        foreach ($cartChanges as $cartRowId => $change) {
            if (isset($change[0]) && isset($change[1]) && isset($change[2])) {
                $messages[] = $this->getItemPriceChangedMessage($change[0], $change[1], $change[2]);
                //this is the case that item price is changed
            } else if (isset($change[0]) && isset($change[1])) {
                //this is the case that item is not available
                $messages[] = $this->getItemNoLongerAvailableMessage($change[0]);
            } else {
                //TODO show all details messages to user about bundle's items changes
                foreach ($change as $itemId => $itemChange) {
                    if (isset($itemChange[0]) && isset($itemChange[1]) && isset($itemChange[2])) {
                        $messages[] = $this->getItemPriceChangedMessage($itemChange[0], $itemChange[1], $itemChange[2]);
                        //this is the case that item price is changed
                    } else if (isset($itemChange[0]) && isset($itemChange[1])) {
                        //this is the case that item is not available
                        $messages[] = $this->getItemNoLongerAvailableMessage($itemChange[0]);
                    }
                }
            }
        }
        return $messages;
    }

    public function applyDealsDiscountsOnCartItems($groupedCartItems, $promo_codes_arrray, $vatIncluded = 0) {
        $dealsManager = DealsManager::getInstance($this->config, $this->args);
        $realDealsPromos = array();
        foreach ($promo_codes_arrray as $promoCode) {

            $deal = $dealsManager->getDealsByPromoCode($promoCode);

            if (isset($deal)) {
                $dealExist = $this->applyDealItemDiscountOnCustomerCartItems($groupedCartItems, $deal, $vatIncluded);
                if ($dealExist) {
                    if (!in_array($deal->getPromoCode(), $realDealsPromos)) {
                        $realDealsPromos[] = $deal->getPromoCode();
                    }
                }
            }
        }
        return $realDealsPromos;
    }

    public function applyAllItemsPromoOnCartItems($groupedCartItems, $promo_codes_arrray, $vatIncluded = 0) {
        $discountPromoCodesManager = DiscountPromoCodesManager::getInstance($this->config, $this->args);
        foreach ($promo_codes_arrray as $promoCode) {
            $dto = $discountPromoCodesManager->getByPromoCode($promoCode);
            if (isset($dto) && $dto->getUsed() == 0) {
                $this->applyDiscountPromoOnCustomerCartItems($groupedCartItems, $dto, $vatIncluded);
                return $promoCode;
            }
        }
        return null;
    }

    public function setCartItemsDiscount($groupedCartItems, $searchDiscount, $pccDiscount) {
        $customerCartManager = CustomerCartManager::getInstance($this->config, $this->args);
        foreach ($groupedCartItems as $ci) {
            if (is_array($ci)) {
                $customerCartManager->setItemDiscount($ci[0]->getId(), $pccDiscount);
            } else {
                $customerCartManager->setItemDiscount($ci->getId(), $searchDiscount);
            }
        }
    }

    public function getBundleItemTotalDealsDiscountAMD($bundleItem) {
        $ret = 0;
        foreach ($bundleItem as $item) {
            if ($item->getDealDiscountAmd() > 0) {
                $ret +=$item->getDealDiscountAmd();
            }
        }
        return $ret;
    }

    public function getCartTotalDealsDiscountAMD($groupedCartItems) {
        $ret = 0;
        foreach ($groupedCartItems as $cartUnit) {
            if (is_array($cartUnit)) {
                $bundleItemTotalDealsDiscountAMD = $this->getBundleItemTotalDealsDiscountAMD($cartUnit);
                if ($bundleItemTotalDealsDiscountAMD > 0) {
                    $bundleCount = intval($cartUnit[0]->getCount());
                    $ret +=$bundleItemTotalDealsDiscountAMD * $bundleCount;
                }
            } else {
                if ($cartUnit->getDealDiscountAmd() > 0) {
                    $ret+=$cartUnit->getDealDiscountAmd() * $cartUnit->getCount();
                }
            }
        }
        return $ret;
    }

    public function applyDealItemDiscountOnCustomerCartItems($groupedCartItems, $dealDto, $vatIncluded = 0) {
        $itemManager = ItemManager::getInstance($this->config, $this->args);
        $dealItemId = $dealDto->getItemId();
        $dealPrice = $dealDto->getPriceAmd();
        $dealExist = false;
        foreach ($groupedCartItems as $cartUnit) {
            if (is_array($cartUnit)) {
                foreach ($cartUnit as $bundleItem) {
                    $itemId = $bundleItem->getBundleItemId();
                    if ($itemId == $dealItemId && $bundleItem->isDealerOfThisCompany() == 0) {
                        $customerItemPriceAmd = $itemManager->exchangeFromUsdToAMD($bundleItem->getCustomerItemPrice());
                        $discoutAMD = $customerItemPriceAmd - $dealPrice;
                        $bundleItem->setDealDiscountAmd($discoutAMD);
                        $dealExist = true;
                    }
                }
            } else {
                $itemId = $cartUnit->getItemId();
                if ($itemId == $dealItemId && $cartUnit->isDealerOfThisCompany() == 0) {
                    $discoutAMD = 0;
                    if ($vatIncluded == 1) {
                        $customerItemPriceAmd = $itemManager->exchangeFromUsdToAMD($cartUnit->getCustomerVatItemPrice());
                        $discoutAMD = $customerItemPriceAmd - $dealPrice;
                    } else {
                        $customerItemPriceAmd = $itemManager->exchangeFromUsdToAMD($cartUnit->getCustomerItemPrice());
                        $discoutAMD = $customerItemPriceAmd - $dealPrice;
                    }
                    $cartUnit->setDealDiscountAmd($discoutAMD);
                    $dealExist = true;
                }
            }
        }
        return $dealExist;
    }

    public function applyDiscountPromoOnCustomerCartItems($groupedCartItems, $discountDto, $vatIncluded = 0) {
        $itemManager = ItemManager::getInstance($this->config, $this->args);
        $discountParam = 1 - intval($discountDto->getDiscountPercent()) / 100;
        foreach ($groupedCartItems as $cartUnit) {
            if (is_array($cartUnit)) {
                foreach ($cartUnit as $bundleItem) {
                    if ($bundleItem->isDealerOfThisCompany() == 0) {
                        $itemId = $bundleItem->getBundleItemId();
                        $itemDto = $itemManager->selectByPK($itemId);
                        $itemListPrice = $itemDto->getListPriceAmd();
                        $itemPriceAfterDiscount = $itemListPrice * $discountParam;
                        $customerItemPriceAmd = $itemManager->exchangeFromUsdToAMD($bundleItem->getCustomerItemPrice());
                        $discoutAMD = $customerItemPriceAmd - $itemPriceAfterDiscount;
                        $bundleItem->setDealDiscountAmd($discoutAMD);
                    }
                }
            } else {
                $itemId = $cartUnit->getItemId();
                if ($cartUnit->isDealerOfThisCompany() == 0) {
                    $discoutAMD = 0;
                    if ($vatIncluded != 1) {
                        $itemDto = $itemManager->selectByPK($itemId);
                        $itemListPrice = $itemDto->getListPriceAmd();
                        $itemPriceAfterDiscount = $itemListPrice * $discountParam;
                        $customerItemPriceAmd = $itemManager->exchangeFromUsdToAMD($cartUnit->getCustomerItemPrice());
                        $discoutAMD = $customerItemPriceAmd - $itemPriceAfterDiscount;
                    }
                    $cartUnit->setDealDiscountAmd($discoutAMD);
                }
            }
        }
    }

    public function getItemPriceChangedMessage($itemDisplayName, $itemOldPrice, $itemCurrentPrice) {
        if ($itemOldPrice < $itemCurrentPrice) {
            return $this->getPhraseSpan(369) . ' - ' . $itemDisplayName;
        } else {
            return $this->getPhraseSpan(371) . ' - ' . $itemDisplayName;
        }
    }

    public function getItemNoLongerAvailableMessage($itemDisplayName) {
        if (strlen($itemDisplayName) > 100) {
            $itemDisplayName = substr($itemDisplayName, 0, 100) . '...';
        }
        return $itemDisplayName . ' - ' . $this->getPhraseSpan(294);
    }

    public function getBundleChangeMessage($itemDisplayName) {
        if (strlen($itemDisplayName) > 100) {
            $itemDisplayName = substr($itemDisplayName, 0, 100) . '...';
        }
        return $itemDisplayName . ' - ' . $this->getPhraseSpan(294);
    }

    /**
     * Returns 'amd' if $cartItemsDtos contains only AMD items for customer,
     * 				 'usd' if $cartItemsDtos contains only USD items for customer,
     *   	 		 'both' if $cartItemsDtos contains both AMD and USD items for customer.
     *   	 		 null if $cartItemsDtos is empty.
     */
    public function getPriceVariety($cartItemsDtos, $userLevel) {
        assert(is_array($cartItemsDtos));
        if (empty($cartItemsDtos)) {
            return null;
        }
        $isUsdItem = (($cartItemsDtos[0]->getIsDealerOfThisCompany() == 1) || $userLevel == UserGroups::$ADMIN || $userLevel == UserGroups::$COMPANY) && !($cartItemsDtos[0]->getSpecialFeePrice() > 0);
        $ret = $isUsdItem ? 'usd' : 'amd';
        foreach ($cartItemsDtos as $cartItem) {
            $isUsdItem = (($cartItem->getIsDealerOfThisCompany() == 1) || $userLevel == UserGroups::$ADMIN || $userLevel == UserGroups::$COMPANY) && !($cartItem->getSpecialFeePrice() > 0);
            if ($isUsdItem && $ret === 'amd')
                return 'both';
            if (!$isUsdItem && $ret === 'usd')
                return 'both';
        }
        return $ret;
    }

    public function isDiscountAvailableForAtleastOneItem($cartItemsDtos) {
        assert(is_array($cartItemsDtos));
        if (empty($cartItemsDtos)) {
            return false;
        }
        foreach ($cartItemsDtos as $key => $cartItem) {
            if ($cartItem->getDiscount() > 0)
                return true;
        }
        return false;
    }

    public function getCustomerCartGrandTotals($customer, $sesUser, $userLevel) {
        $customerEmail = strtolower($customer->getEmail());
        $customerCartManager = CustomerCartManager::getInstance($this->config, $this->args);
        $userLevel = $sesUser->getLevel();
        $user_id = $sesUser->getId();
        $cartItemsDtos = $customerCartManager->getCustomerCart($customerEmail, $user_id, $userLevel);
        $groupedCartItems = $customerCartManager->groupBundleItemsInArray($cartItemsDtos);
        list($grandTotalAMD, $grandTotalUSD) = $customerCartManager->calcCartTotal($groupedCartItems, true, $userLevel);
        return array($grandTotalAMD, $grandTotalUSD);
    }

    public function getMapper() {
        return null;
    }

}

?>