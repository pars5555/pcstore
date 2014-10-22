<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");

require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/CustomerCartManager.class.php");
require_once (CLASSES_PATH . "/managers/BundleItemsManager.class.php");
require_once (CLASSES_PATH . "/managers/SpecialFeesManager.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/pcc_managers/PcConfiguratorManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class AddConfiguratorBuiltPcToCartAction extends GuestAction {

    public function service() {
        $customer = $this->getCustomer();
        $customerEmail = strtolower($customer->getEmail());
        $customerCartManager = CustomerCartManager::getInstance($this->config, $this->args);
        $bundleItemsManager = BundleItemsManager::getInstance($this->config, $this->args);
        $itemManager = ItemManager::getInstance($this->config, $this->args);
        $userLevel = $this->sessionManager->getUser()->getLevel();
        $user_id = $this->getUserId();
        $add_count = 1;
        if (isset($_REQUEST['add_count'])) {
            $add_count = $this->secure($_REQUEST['add_count']);
        }
        if (isset($_REQUEST['bundle_items_ids'])) {
            $bundle_items_ids = $this->secure($_REQUEST['bundle_items_ids']);
        } else {
            $jsonArr = array('status' => "err", "errText" => "System error: Try to add empty bundle to cart!");
            echo json_encode($jsonArr);
            return false;
        }
        $userManager = UserManager::getInstance($this->config, $this->args);
        $vipCustomer = $userManager->isVipAndVipEnabled($customer);
        if ($vipCustomer) {
            $discount = floatval($this->getCmsVar('vip_pc_configurator_discount'));
        } else {
            $discount = floatval($this->getCmsVar('pc_configurator_discount'));
        }
        $bundle_display_name_id = 287;
        //means "computer"
        $bundle_items_ids_array = explode(',', $bundle_items_ids);
        $itemsDto = $itemManager->getItemsForOrder($bundle_items_ids_array, $user_id, $userLevel);
        if (count($itemsDto) !== count($bundle_items_ids_array)) {
            $jsonArr = array('status' => "err", "errText" => "Some items are not available!");
            echo json_encode($jsonArr);
            return false;
        }
        $last_dealer_price = 0;
        foreach ($itemsDto as $key => $itemDto) {
            $last_dealer_price += $itemDto->getDealerPrice();
        }
        if (isset($_REQUEST['replace_cart_row_id']) && $_REQUEST['replace_cart_row_id'] > 0) {
            $replace_cart_row_id = $_REQUEST['replace_cart_row_id'];
            $dto = $customerCartManager->selectByPK($replace_cart_row_id);
            if ($dto) {
                $replaceing_bundle_id = $dto->getBundleId();
                $addedItemId = $customerCartManager->updateById($replace_cart_row_id, $customerEmail, 0, $replaceing_bundle_id, $last_dealer_price, $discount, $dto->getCount());
                $bundleItemsManager->deleteBundle($replaceing_bundle_id);
            } else {
                $jsonArr = array('status' => "err", "errText" => "System Error: Bundle is not available in your cart!");
                echo json_encode($jsonArr);
                return false;
            }
        }

        $bundle_id = $bundleItemsManager->createBundle($bundle_display_name_id, $bundle_items_ids, "", $replaceing_bundle_id);

        if (!isset($addedItemId)) {
            $addedItemId = $customerCartManager->addToCart($customerEmail, 0, $bundle_id, $last_dealer_price, $add_count);
        }

        ///////////start add build fee if it should be add///////////////////

        $bundleItems = $customerCartManager->getCustomerCart($customerEmail, $user_id, $userLevel, $addedItemId);
        //list($bpAMD, $bpUSD, $specialFeesTotalAMD) = $bundleItemsManager->calcBundlePriceForCustomerWithoutDiscount($bundleItems, $userLevel);
        $specialFeesManager = SpecialFeesManager::getInstance($this->config, $this->args);
        /* 	var_dump($bpAMD , intval($this->getCmsVar('pc_buid_fee_free_amd_over')));
          var_dump($bpAMD < intval($this->getCmsVar('pc_buid_fee_free_amd_over')));
          exit; */
        $pccm = PcConfiguratorManager::getInstance($this->config, $this->args);

        $bundleProfitWithoutDiscountUSD = 0;
        if ($userLevel != UserGroups::$ADMIN && $userLevel != UserGroups::$COMPANY) {
            $bundleProfitWithoutDiscountUSD = $bundleItemsManager->calcBundleProfitWithDiscount($bundleItems, $discount);
        }
        $pcBuildFee = $pccm->calcPcBuildFee($bundleProfitWithoutDiscountUSD);
        $pcBuildFeeId = $specialFeesManager->getPcBuildFee()->getId();
        $special_fees = array($pcBuildFeeId => $pcBuildFee);
        if ($pcBuildFee > 0) {
            $bundleItemsManager->addSpecialFeesToBundle($bundle_id, $bundle_display_name_id, $special_fees);
        }

        ///////////end add  build fee if it should be add///////////////////

        $totalCount = $customerCartManager->getCustomerCartTotalCount($customerEmail);
        $jsonArr = array('status' => "ok", "cart_items_count" => $totalCount);
        echo json_encode($jsonArr);
        return true;
    }

    public function getRequestGroup() {
        return RequestGroups::$userCompanyRequest;
    }

}
