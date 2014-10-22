<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/DealsManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class DealsLoad extends GuestLoad {

	public function load() {

		$dealsManager = DealsManager::getInstance($this->config, $this->args);
		$itemManager = ItemManager::getInstance($this->config, $this->args);
		$this->addParam("itemManager", $itemManager);

		//today deals
		$todayDealDto = $dealsManager->getTodayDeal();
		if ($todayDealDto) {
			$itemId = $todayDealDto->getItemId();
			$todayItem = $itemManager->getItemsForOrder($itemId, $this->getUserId(), $this->getUserLevel(), true);                        
			if (isset($todayItem) && ($todayItem->getIsDealerOfThisCompany() == 0 || $this->getUserLevel() === UserGroups::$ADMIN)) {
				$totalDurationSeconds = intval($todayDealDto->getDurationMinutes()) * 60;
				$currentTimeInSeconds = time();
				$dealStartTimeInSeconds = strtotime($todayDealDto->getDate() . ' ' . $todayDealDto->getStartTime());
				$dealEndTimeInSeconds = strtotime($todayDealDto->getDate() . ' ' . $todayDealDto->getStartTime()) + $totalDurationSeconds;
				$dealIsEnable = $currentTimeInSeconds > $dealStartTimeInSeconds && $currentTimeInSeconds < $dealEndTimeInSeconds - 10;				
                                if ($dealIsEnable) {                                    
					$this->addParam("todayItem", $todayItem);
					$this->addParam("today_deal_seconds_to_end", $dealEndTimeInSeconds - $currentTimeInSeconds);
					$this->addParam('today_deal_promo_code', $todayDealDto->getPromoCode());
					$this->addParam('today_deal_fixed_price', $todayDealDto->getPriceAmd());
				}
			}
		}

		//lighting deals
		/* $lightingDeals = $dealsManager->getLightingDeals();
		  if (!empty($lightingDeals)) {

		  $this->addParam("lightingDeals", $lightingDeals);
		  } */
	}

	public function getDefaultLoads($args) {
		$loads = array();
		return $loads;
	}

	public function isValidLoad($namespace, $load) {
		return true;
	}

	public function getTemplate() {
		return TEMPLATES_DIR . "/main/deals.tpl";
	}

	public function getRequestGroup() {
		return RequestGroups::$guestRequest;
	}

	protected function logRequest() {
		return false;
	}

}

?>