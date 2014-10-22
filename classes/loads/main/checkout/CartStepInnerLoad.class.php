<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/CustomerCartManager.class.php");
require_once (CLASSES_PATH . "/managers/checkout/CheckoutManager.class.php");
require_once (CLASSES_PATH . "/managers/BundleItemsManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class CartStepInnerLoad extends GuestLoad {

	private $bundleItemsManager;
	private $itemManager;

	public function load() {
		$customer = $this->getCustomer();
		if (!isset($customer)) {
			return false;
		}
		$userManager = UserManager::getInstance($this->config, $this->args);
		$vipCustomer = $userManager->isVipAndVipEnabled($customer);
		if ($vipCustomer) {
			$pccDiscount = floatval($this->getCmsVar('vip_pc_configurator_discount'));
		} else {
			$pccDiscount = floatval($this->getCmsVar('pc_configurator_discount'));
		}


		$this->addParam('req_params', $_REQUEST);
		$customerEmail = strtolower($customer->getEmail());
		$customerCartManager = CustomerCartManager::getInstance($this->config, $this->args);
		$checkoutManager = CheckoutManager::getInstance($this->config, $this->args);

		$this->bundleItemsManager = BundleItemsManager::getInstance($this->config, $this->args);
		$this->itemManager = ItemManager::getInstance($this->config, $this->args);
		if (isset($_REQUEST['id']) && isset($_REQUEST['count'])) {
			//this is the case when user change the items count from his cart
			//TODO Check if user has item in his cart
			$cartItemId = $this->secure($_REQUEST['id']);
			$cartItemCount = intval($this->secure($_REQUEST['count']));
			if ($cartItemCount > 0) {
				$customerCartManager->setCartElementCount($cartItemId, $cartItemCount);
			} else {
				$customerCartManager->deleteCartElement($cartItemId);
			}
		}
		$userLevel = $this->getUserLevel();
		$user_id = $this->getUserId();

		$_cartItemsDtos = $customerCartManager->getCustomerCart($customerEmail, $user_id, $userLevel);
		$_groupedCartItems = $customerCartManager->groupBundleItemsInArray($_cartItemsDtos);

		$checkoutManager->setCartItemsDiscount($_groupedCartItems, $searchDiscount, $pccDiscount);


		$cartItemsDtos = $customerCartManager->getCustomerCart($customerEmail, $user_id, $userLevel);
		$pv = $checkoutManager->getPriceVariety($cartItemsDtos, $userLevel);

		$discountAvailable = $checkoutManager->isDiscountAvailableForAtleastOneItem($cartItemsDtos);

		$groupedCartItems = $customerCartManager->groupBundleItemsInArray($cartItemsDtos);



		$cartChanges = $customerCartManager->getCustomerCartItemsChanges($groupedCartItems);
		$customerCartManager->setCustomerCartItemsPriceChangesToCurrentItemPrices($groupedCartItems);


		$customerCartChangesMessages = $checkoutManager->getCustomerCartChangesMessages($cartChanges);


		//all cart items, bundle items grouped in sub array
                $cho_include_vat = $this->secure($_REQUEST['cho_include_vat']);
		if (!empty($_REQUEST['cho_promo_codes'])) {
			$cho_promo_codes = $this->secure($_REQUEST['cho_promo_codes']);                        
			$cho_promo_codes_arrray = explode(',', $cho_promo_codes);
                        $validPromoDiscount = $checkoutManager->applyAllItemsPromoOnCartItems($groupedCartItems, $cho_promo_codes_arrray, $cho_include_vat);
			$existingDealsPromoCodesArray = $checkoutManager->applyDealsDiscountsOnCartItems($groupedCartItems, $cho_promo_codes_arrray,$cho_include_vat);
                        $existingDealsPromoCodesArray [] = $validPromoDiscount ;
			$_REQUEST['cho_promo_codes'] = implode(',', $existingDealsPromoCodesArray);
		}
		
		list($grandTotalAMD, $grandTotalUSD) = $customerCartManager->calcCartTotal($groupedCartItems, true, $userLevel, $cho_include_vat);
		$all_non_bundle_items_has_vat = $customerCartManager->checkAllNonBundleItemsHasVatPrice($groupedCartItems);
		if (!$all_non_bundle_items_has_vat && $cho_include_vat == 1) {
			$customerCartChangesMessages[] = $this->getPhraseSpan(566);
		}
		$this->addParam("all_non_bundle_items_has_vat", $all_non_bundle_items_has_vat);

		$this->addParam("minimum_order_amount_exceed", $grandTotalAMD >= intval($this->getCmsVar("minimum_order_amount_amd")));
		$this->addParam("minimum_order_amount_amd", $this->getCmsVar("minimum_order_amount_amd"));
		if (!empty($customerCartChangesMessages)) {
			$this->addParam('customerMessages', $customerCartChangesMessages);
		}
		$allItemsAreAvailable = $customerCartManager->areAllItemsAvailableInCustomerCart($groupedCartItems);

		//discount available for at leat one item in the cart
		$this->addParam('discountAvailable', $discountAvailable);

		//priceVariety the price variety in customer cart. Can be 'amd', 'usd' or 'both';

		$this->addParam('priceVariety', $pv);
		$this->addParam('checkoutManager', $checkoutManager);
		//all cart items, bundle items grouped in sub array 
		$this->addParam('cartItems', $groupedCartItems);
		$this->addParam('itemManager', $this->itemManager);


		$this->addParam('allItemsAreAvailable', $allItemsAreAvailable);
		$this->addParam('emptyCart', empty($cartItemsDtos));


		$this->addParam('bundleItemsManager', $this->bundleItemsManager);

		//cart grand totla included discounts, this is the final value that customer should pay for his cart
		$this->addParam('grandTotalAMD', $grandTotalAMD);
		$this->addParam('grandTotalUSD', $grandTotalUSD);
                
                
                
                $this->addParam('maxItemCartCount', intval($this->getCmsVar('max_item_cart_count')));
	}

	public function getDefaultLoads($args) {
		$loads = array();
		return $loads;
	}

	public function isValidLoad($namespace, $load) {
		return true;
	}

	public function getTemplate() {
		return TEMPLATES_DIR . "/main/checkout/cart_step_inner.tpl";
	}

	public function getRequestGroup() {
		return RequestGroups::$userCompanyRequest;
	}

}

?>