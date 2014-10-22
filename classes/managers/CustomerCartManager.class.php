<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/managers/BundleItemsManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/CustomerCartMapper.class.php");

/**
 * OrdersManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class CustomerCartManager extends AbstractManager {

    /**
     * @var app config
     */
    private $config;
    private $bundleItemsManager;

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
        $this->config = $config;
        $this->args = $args;
        $this->mapper = CustomerCartMapper::getInstance();
        $this->bundleItemsManager = BundleItemsManager::getInstance($this->config, $this->args);
        $this->itemManager = ItemManager::getInstance($this->config, $this->args);
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

            self::$instance = new CustomerCartManager($config, $args);
        }
        return self::$instance;
    }

    public function emptyCustomerCart($customerEmail) {
        $bundles_ids = $this->mapper->getCustomerCartBundlesIdsJoinedByComma($customerEmail);
        $bundles_ids = trim($bundles_ids);
        if (isset($bundles_ids) && !empty($bundles_ids)) {
            $this->bundleItemsManager->deleteBundles($bundles_ids);
        }
        return $this->mapper->emptyCustomerCart($customerEmail);
    }

    public function updateById($id, $customerEmail, $item_id, $bundle_id, $last_dealer_price, $discount, $count) {
        $dto = $this->mapper->selectByPK($id);
        $dto->setCustomerEmail($customerEmail);
        if ($item_id > 0) {
            $dto->setItemId($item_id);
        }
        $dto->setLastDealerPrice($last_dealer_price);
        if ($bundle_id > 0) {
            $dto->setBundleId($bundle_id);
        }
        $dto->setDiscount($discount);
        $dto->setCount($count);
        $this->mapper->updateByPK($dto);
        return $id;
    }

    public function addToCart($customerEmail, $item_id, $bundle_id, $last_dealer_price, $add_count) {
        if ($item_id) {
            $dto = $this->getItemInCustomerCart($customerEmail, $item_id);
            if ($dto) {
                $count = (int) $dto->getCount() + (int) $add_count;
                if ($count > intval($this->getCmsVar('max_item_cart_count'))) {
                    $count = intval($this->getCmsVar('max_item_cart_count'));
                }
                $this->setCartElementCount($dto->getId(), $count);
                return $dto->getId();
            }
        }
        if ($bundle_id) {
            $dto = $this->getBundleInCustomerCart($customerEmail, $bundle_id);
            if ($dto) {
                $count = (int) $dto->getCount() + (int) $add_count;
                if ($count > intval($this->getCmsVar('max_item_cart_count'))) {
                    $count = intval($this->getCmsVar('max_item_cart_count'));
                }
                $this->setCartElementCount($dto->getId(), $count);
                return $dto->getId();
            }
        }
        $dto = $this->mapper->createDto();
        $dto->setCustomerEmail($customerEmail);
        if ($item_id > 0) {
            $dto->setItemId($item_id);
            $itemDto = $this->itemManager->selectByPK($item_id);
            $dto->setCachedItemDisplayName($itemDto->getDisplayName());
        }
        $dto->setLastDealerPrice($last_dealer_price);
        if ($bundle_id > 0) {
            $dto->setBundleId($bundle_id);
        }
        $dto->setCount($add_count);
        return $this->mapper->insertDto($dto);
    }

    public function getItemInCustomerCart($customerEmail, $item_id) {
        return $this->mapper->getItemInCustomerCart($customerEmail, $item_id);
    }

    public function getBundleInCustomerCart($customerEmail, $bundle_id) {
        return $this->mapper->getBundleInCustomerCart($customerEmail, $bundle_id);
    }

    public function setCartElementCount($row_id, $count) {
        $dto = $this->selectByPK($row_id);
        $dto->setCount($count);
        return $this->mapper->updateByPK($dto);
    }

    public function setItemDiscount($id, $discount) {
        return $this->mapper->updateNumericField($id, 'discount', $discount);
    }

    public function deleteCartElement($cartItemId) {
        $cartItemDto = $this->mapper->selectByPK($cartItemId);
        if ($cartItemDto) {
            $bundleId = intval($cartItemDto->getBundleId());
            $isSystemBundle = $cartItemDto->getIsSystemBundle();
            if ($bundleId > 0 && $isSystemBundle == 0) {
                $this->bundleItemsManager->deleteBundle($bundleId);
            }
            return $this->deleteByPK($cartItemId);
        }
        return null;
    }

    public function getCustomerCart($customer_email, $user_id, $userLevel, $id = null) {
        $profitFormula = $this->itemManager->getItemProfitFormula($user_id, $userLevel);
        return $this->mapper->getCustomerCart($customer_email, $user_id, $userLevel, $profitFormula, $id);
    }

    public function getCustomerCartTotalCount($customer_email) {
        return $this->mapper->getCustomerCartTotalCount($customer_email);
    }

    public function deleteCompanyRelatedItemsFromCustomerCart($customerEmail, $companyId) {
        $this->mapper->deleteCustomerItemsByCompanyId($customerEmail, $companyId);
        $bundlesIds = $this->getCustomerBundlesIdsByCompanyId($customerEmail, $companyId);
        if (!empty($bundlesIds)) {
            $this->mapper->deleteByBundlesIds($customerEmail, $bundlesIds);
        }
    }

    public function getCustomerItemsByCompanyId($customerEmail, $companyId) {
        return $this->mapper->getCustomerItemsByCompanyId($customerEmail, $companyId);
    }

    public function getCustomerBundlesIdsByCompanyId($customerEmail, $companyId) {
        return $this->mapper->getCustomerBundlesIdsByCompanyId($customerEmail, $companyId);
    }

    /**
     * Groups the bundles item into sub array
     */
    public function groupBundleItemsInArray($cartItemsDtos) {
        $ret = array();
        $lbid = 0;
        foreach ($cartItemsDtos as $key => $cartItem) {
            $bid = $cartItem->getBundleId();
            if ($bid > 0) {
                if ($lbid != $bid) {
                    $ret[] = array($cartItem);
                } else {
                    $ret[count($ret) - 1][] = $cartItem;
                }
                $lbid = $bid;
            } else {
                $ret[] = $cartItem;
            }
        }
        return $ret;
    }

    public function calcCartTotalDealerPrice($cartItemsDtos, $includedVat = 0) {
        $ret = 0;
        foreach ($cartItemsDtos as $cartItem) {

            if ($includedVat == 1 && $cartItem->getItemVatPrice() > 0) {
                $itemDealerPrice = $cartItem->getItemVatPrice();
            } else {
                $itemDealerPrice = $cartItem->getItemDealerPrice();
            }
            $ret += floatval($itemDealerPrice * $cartItem->getCount());
        }
        return $ret;
    }

    public function checkAllNonBundleItemsHasVatPrice($groupedCartItems) {
        if (empty($groupedCartItems)) {
            return true;
        }
        foreach ($groupedCartItems as $key => $groupedCartItem) {
            if (!is_array($groupedCartItem)) {
                if ($groupedCartItem->getItemAvailable() == 0) {
                    continue;
                }
                if ($groupedCartItem->getItemVatPrice() <= 0) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Returns total of the cart items in following format
     * array($grandTotalAMD, $grandTotalUSD)
     * @param $groupedCartItems should have getCustomerCart function format and
     * then group the result item using groupBundleItemsInArray function.
     */
    public function calcCartTotal($groupedCartItems, $withPromoDiscount = false, $userLevel, $includedVat = 0) {
        $checkoutManager = CheckoutManager::getInstance($this->config, $this->args);
        assert(is_array($groupedCartItems));
        if (empty($groupedCartItems)) {
            return null;
        }
        $grandTotalAMD = 0;
        $grandTotalUSD = 0;

        foreach ($groupedCartItems as $key => $groupedCartItem) {
            if (is_array($groupedCartItem)) {
                //so it's bundle item
                assert(!empty($groupedCartItem));
                $bundleItemTotalDealsDiscountAMD = 0;
                if ($withPromoDiscount == true) {
                    $bundleItemTotalDealsDiscountAMD = $checkoutManager->getBundleItemTotalDealsDiscountAMD($groupedCartItem);
                    $discountParam = floatval(1 - floatval($groupedCartItem[0]->getDiscount() / 100));
                } else {
                    $discountParam = 1;
                }
                list($bundleTotAMD, $bundleTotUSD, $specialFeesTotalAMD) = $this->bundleItemsManager->calcBundlePriceForCustomerWithoutDiscount($groupedCartItem, $userLevel);

                $bundleCount = intval($groupedCartItem[0]->getCount());
                $grandTotalAMD += $bundleTotAMD * $bundleCount * $discountParam + $bundleCount * $specialFeesTotalAMD - $bundleItemTotalDealsDiscountAMD * $bundleCount;
                $grandTotalUSD += $bundleTotUSD * $bundleCount;
            } else {
                if ($groupedCartItem->getItemAvailable() == 0)
                    continue;

                if ($userLevel == UserGroups::$ADMIN || $userLevel == UserGroups::$COMPANY || $groupedCartItem->getIsDealerOfThisCompany() == 1) {
                    $itemTotalPrice = floatval($includedVat == 1 ? $groupedCartItem->getVatPrice() : $groupedCartItem->getDealerPrice()) * intval($groupedCartItem->getCount());
                } else {
                    $itemTotalPrice = floatval($includedVat == 1 ? $groupedCartItem->getCustomerVatItemPrice() : $groupedCartItem->getCustomerItemPrice()) * intval($groupedCartItem->getCount());
                }
                $dealDiscountAmd = 0;
                if ($withPromoDiscount == true) {
                    if ($groupedCartItem->getDealDiscountAmd() > 0) {
                        $dealDiscountAmd = $groupedCartItem->getDealDiscountAmd();
                    }
                    $discountParam = floatval(1 - floatval($groupedCartItem->getDiscount() / 100));
                } else {
                    $discountParam = 1;
                }

                if ($userLevel == UserGroups::$ADMIN || $userLevel == UserGroups::$COMPANY || $groupedCartItem->getIsDealerOfThisCompany() == 1) {
                    $grandTotalUSD += $itemTotalPrice;
                } else {
                    $grandTotalAMD += $this->itemManager->exchangeFromUsdToAMD($itemTotalPrice) * $discountParam - $dealDiscountAmd * intval($groupedCartItem->getCount());
                }
            }
        }
        return array(intval($grandTotalAMD), $grandTotalUSD);
    }

    /**
     * @param $groupedCartItems should have getCustomerCart function format and
     * then group the result item using groupBundleItemsInArray function.
     * Return following format
     * array("CartRowId"=>change, ...)
     * if change is array(0=>ItemDisplayName, 1=>false) meanse item is not available,
     * if change is array(0=>ItemDisplayName, 1=>oldDealerPrice, 2=>currentDealerPrice) then meanse item price is changed
     * if change is array("itemId"=>change, ...) meanse item is bundle and there are changes,
     */
    public function getCustomerCartItemsChanges($groupedCartItems) {
        $cartChanges = array();
        foreach ($groupedCartItems as $key => $groupedCartItem) {
            if (is_array($groupedCartItem)) {
                $bundleItemsChanges = $this->getBundleItemsChanges($groupedCartItem);
                if (!empty($bundleItemsChanges)) {
                    $cartChanges[$groupedCartItem[0]->getId()] = $bundleItemsChanges;
                }
            } else {
                $item = $groupedCartItem;
                $itemLastDealerPrice = floatval($item->getLastDealerPrice());
                $itemDealerPrice = floatval($item->getItemDealerPrice());
                if ($item->getItemAvailable() == 0) {
                    $cartChanges[$item->getId()] = array($item->getCachedItemDisplayName() . '', false);
                    continue;
                }
                if ($itemLastDealerPrice != $itemDealerPrice) {
                    $cartChanges[$item->getId()] = array($item->getItemDisplayName() . '', $itemLastDealerPrice, $itemDealerPrice);
                }
            }
        }
        return $cartChanges;
    }

    /**
     */
    public function setCustomerCartItemsPriceChangesToCurrentItemPrices($groupedCartItems) {
        foreach ($groupedCartItems as $key => $groupedCartItem) {
            if (is_array($groupedCartItem)) {
                $bundleItemsChanges = $this->setBundleItemsPriceChangesToCurrentItemsPrice($groupedCartItem);
            } else {
                $item = $groupedCartItem;
                $itemLastDealerPrice = floatval($item->getLastDealerPrice());
                $itemDealerPrice = floatval($item->getItemDealerPrice());
                if ($item->getItemAvailable() != 0) {
                    if ($itemLastDealerPrice != $itemDealerPrice) {
                        $id = $item->getId();
                        $dto = $this->mapper->selectByPK($id);
                        $dto->setLastDealerPrice($itemDealerPrice);
                        $this->mapper->updateByPK($dto);
                    }
                }
            }
        }
    }

    /**
     * @param $groupedCartItems should have getCustomerCart function format and
     * then group the result item using groupBundleItemsInArray function.
     * Returns true if all items are available of false is any item is uinavailable in customer cart
     * if cart is empty return true
     */
    public function areAllItemsAvailableInCustomerCart($groupedCartItems) {
        $cartChanges = $this->getCustomerCartItemsChanges($groupedCartItems);
        if (empty($cartChanges)) {
            return true;
        }
        foreach ($cartChanges as $cartRowId => $change) {
            if (isset($change[0]) && isset($change[1]) && isset($change[2])) {
                //this is the case that item price is changed
            } else if (isset($change[0]) && isset($change[1])) {
                //this is the case that item is not available
                return false;
            } else {
                //bundle has changes
                foreach ($change as $itemId => $itemChange) {
                    if (isset($itemChange[0]) && isset($itemChange[1]) && isset($itemChange[2])) {
                        //this is the case that item price is changed
                    } else if (isset($itemChange[0]) && isset($itemChange[1])) {
                        //this is the case that item is not available
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * Return following format
     * array("itemId"=>change, ...)  ,
     * if change is array(0=>ItemDisplayName, 1=>false) meanse item is not available,
     * if change is array(0=>ItemDisplayName, 1=>oldDealerPrice, 2=>currentDealerPrice) then meanse item price is changed
     */
    public function getBundleItemsChanges($bundleCartItems) {
        $ret = array();
        foreach ($bundleCartItems as $key => $item) {
            if ($item->getSpecialFeeId() > 0) {
                continue;
            }
            $itemLastDealerPrice = floatval($item->getBundleItemLastDealerPrice());
            $itemDealerPrice = floatval($item->getItemDealerPrice());
            if ($item->getItemAvailable() == 0) {
                $ret[$item->getBundleItemId()] = array($item->getBundleCachedItemDisplayName() . '', false);
                continue;
            }
            if ($itemLastDealerPrice != $itemDealerPrice) {
                $ret[$item->getBundleItemId()] = array($item->getItemDisplayName() . '', $itemLastDealerPrice, $itemDealerPrice);
            }
        }
        return $ret;
    }

    /**
     */
    public function setBundleItemsPriceChangesToCurrentItemsPrice($bundleCartItems) {
        foreach ($bundleCartItems as $key => $item) {
            if ($item->getSpecialFeeId() == 0 && $item->getItemAvailable() == 1) {
                $itemLastDealerPrice = floatval($item->getBundleItemLastDealerPrice());
                $itemDealerPrice = floatval($item->getItemDealerPrice());
                if ($itemLastDealerPrice != $itemDealerPrice) {
                    $bundleId = $item->getBundleId();
                    $itemId = $item->getBundleItemId();
                    $this->bundleItemsManager->changeBundleItemLastDealerPriceByItemId($bundleId, $itemId, $itemDealerPrice);
                }
            }
        }
    }

}

?>