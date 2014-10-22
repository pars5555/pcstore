<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/BundleItemsMapper.class.php");

/**
 * OrdersManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class BundleItemsManager extends AbstractManager {

    /**
     * @var app config
     */
    private $config;
    private $itemManager;

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
        $this->mapper = BundleItemsMapper::getInstance();
        $this->config = $config;
        $this->args = $args;
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

            self::$instance = new BundleItemsManager($config, $args);
        }
        return self::$instance;
    }

    public function deleteBundle($bundleId) {
        return $this->mapper->deleteBundle($bundleId);
    }

    public function deleteBundles($bundlesIds) {
        assert(isset($bundlesIds));
        if (is_array($bundlesIds)) {
            $bundlesIds = implode(',', $bundlesIds);
        }
        return $this->mapper->deleteBundles($bundlesIds);
    }

    public function createBundle($bundle_display_name_id, $bundle_items_ids, $special_fees_ids = "", $bundleId = null) {
        if (empty($bundle_items_ids)) {
            $bundle_items_ids = array();
        }
        if (!is_array($bundle_items_ids)) {
            $bundle_items_ids = explode(',', $bundle_items_ids);
        }
        if (empty($bundle_items_ids)) {
            return false;
        }

        if (empty($special_fees_ids)) {
            $special_fees_ids = array();
        }

        if (!is_array($special_fees_ids)) {
            $special_fees_ids = explode(',', $special_fees_ids);
        }

        $itemIdWithCount = array_count_values($bundle_items_ids);
        $dtosArray = array();
        if ($bundleId != null) {
            $currentBundleId = intval($bundleId);
        } else {
            $currentBundleId = intval($this->getLastBundleId()) + 1;
        }
        $itemsIdsArray = array_keys($itemIdWithCount);
        $itemsOnlyForDealerPrice = $this->itemManager->getItemsByIds($itemsIdsArray);
        $itemsOnlyForDealerPrice = $this->itemManager->putItemsDtosInArrayWithItemIdInArrayKey($itemsOnlyForDealerPrice);
        foreach ($itemIdWithCount as $itemId => $itemCount) {
            $dto = $this->mapper->createDto();
            $dto->setBundleId($currentBundleId);
            $dto->setBundleDisplayNameId($bundle_display_name_id);
            $dto->setItemId($itemId);
            $dto->setItemLastDealerPrice($itemsOnlyForDealerPrice[$itemId]->getDealerPrice());
            $dto->setCachedItemDisplayName($itemsOnlyForDealerPrice[$itemId]->getDisplayName());

            $dto->setItemCount($itemCount);
            $dtosArray[] = $dto;
        }
        foreach ($special_fees_ids as $key => $specialFeeId) {
            $dto = $this->mapper->createDto();
            $dto->setBundleId($currentBundleId);
            $dto->setBundleDisplayNameId($bundle_display_name_id);
            $dto->setSpecialFeeId($specialFeeId);
            $dto->setItemCount(1);
            $dtosArray[] = $dto;
        }
        $this->mapper->insertDtos($dtosArray);
        return $currentBundleId;
    }

    public function getLastBundleId() {
        return $this->mapper->getLastBundleId();
    }

    /**
     * $special_fees format is following
     * array("specialFeeID"=>"dymanicCalculatedPrice")
     * dymanicCalculatedPrice should be -1 if given special fee is fixed price, otherwise it should be the price for the special fee in AMD.
     */
    public function addSpecialFeesToBundle($bundleId, $bundle_display_name_id, $special_fees) {
        if (empty($special_fees)) {
            $special_fees = array();
        }
        $dtosArray = array();
        foreach ($special_fees as $id => $specialFeeDynPrice) {
            $dto = $this->mapper->createDto();
            $dto->setBundleId($bundleId);
            $dto->setBundleDisplayNameId($bundle_display_name_id);
            $dto->setSpecialFeeId($id);
            $dto->setSpecialFeeDynamicPrice($specialFeeDynPrice);
            $dto->setItemCount(1);
            $dtosArray[] = $dto;
        }
        $this->mapper->insertDtos($dtosArray);
        return $currentBundleId;
    }

    public function calcBundlePriceForCustomerWithDiscount($bundleItems, $userLevel) {
        $discountParam = floatval(1 - floatval($bundleItems[0]->getDiscount() / 100));
        list($bundleTotAMD, $bundleTotUSD, $specialFeesTotalAMD) = $this->calcBundlePriceForCustomerWithoutDiscount($bundleItems, $userLevel);
        $bundleTotalAMD += $bundleTotAMD * $discountParam + $specialFeesTotalAMD;
        $bundleTotalUSD += $bundleTotUSD;
        return array($bundleTotalAMD, $bundleTotalUSD);
    }

    /**
     * 
     * 
     */
    public function calcBundleProfitWithDiscount($bundleItems, $discountPercent) {
        assert(is_array($bundleItems));
        if (empty($bundleItems)) {
            return 0;
        }
        $profit = 0;
        $discountParam = 1 - $discountPercent / 100;
        foreach ($bundleItems as $key => $bundleItem) {
            if ($bundleItem->getSpecialFeeId() > 0 || $bundleItem->getItemAvailable() == 0) {
                continue;
                //@TODO show error
            }
            if ($bundleItem->getIsDealerOfThisCompany() != 1) {
                $itemCustomerTotal = floatval($bundleItem->getCustomerItemPrice()) * intval($bundleItem->getBundleItemCount()) * $discountParam;
                $itemDealerTotal = floatval($bundleItem->getItemDealerPrice()) * intval($bundleItem->getBundleItemCount());
                $profit += $itemCustomerTotal - $itemDealerTotal;
            }
        }
        return $profit;
    }

    /**
     * Returns Bundle items total AMD and USD and total Special Fees AMD without discount in following format
     * array($totalAMD, $totalUSD, $specialFeesTotalAMD)
     */
    public function calcBundlePriceForCustomerWithoutDiscount($bundleItems, $userLevel) {
        assert(is_array($bundleItems));
        if (empty($bundleItems)) {
            return null;
        }

        $totalAMD = 0;
        $specialFeesTotalAMD = 0;
        $totalUSD = 0;

        foreach ($bundleItems as $key => $bundleItem) {

            if ($bundleItem->getSpecialFeeId() > 0) {
                if ($bundleItem->getSpecialFeeDynamicPrice() >= 0) {
                    $specialFeeAMD = floatval($bundleItem->getSpecialFeeDynamicPrice());
                } else {
                    $specialFeeAMD = floatval($bundleItem->getSpecialFeePrice());
                }
                $specialFeesTotalAMD += $specialFeeAMD;
            } else {
                if ($bundleItem->getItemAvailable() == 0) {
                    continue;
                    //@TODO show error
                }
                if ($userLevel === UserGroups::$ADMIN || $userLevel === UserGroups::$COMPANY || $bundleItem->getIsDealerOfThisCompany() == 1) {
                    $itemPrice = $bundleItem->getItemDealerPrice();
                } else {
                    $itemPrice = $bundleItem->getCustomerItemPrice();
                }
                $itemTotal = floatval($itemPrice) * intval($bundleItem->getBundleItemCount());
                if ($userLevel === UserGroups::$ADMIN || $userLevel === UserGroups::$COMPANY || $bundleItem->getIsDealerOfThisCompany() == 1) {
                    $totalUSD += $itemTotal;
                } else {
                    $totalAMD += $this->itemManager->exchangeFromUsdToAMD($itemTotal);
                }
            }
        }
        return array($totalAMD, $totalUSD, $specialFeesTotalAMD);
    }

    public function changeBundleItemLastDealerPriceByItemId($bundleId, $itemId, $newPrice) {
        return $this->mapper->changeBundleItemLastDealerPriceByItemId($bundleId, $itemId, $newPrice);
    }

}

?>