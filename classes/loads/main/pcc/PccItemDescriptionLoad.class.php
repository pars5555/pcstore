<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/pcc_managers/PcConfiguratorManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class PccItemDescriptionLoad extends GuestLoad {

	public function load() {

		if ($_REQUEST["item_id"]) {
			$item_id = $_REQUEST["item_id"];
			if (strpos($item_id, ',') !== false) {
				$item_id = explode(',', $item_id);
			}
			$itemManager = ItemManager::getInstance($this->config, $this->args);
			$userLevel = $this->getUserLevel();
			$userId = $this->getUserId();
			if (is_array($item_id)) {
				$itemDto = array();
				foreach ($item_id as $key => $itemId) {
					$itemD = $itemManager->getItemsForOrder($itemId, $userId, $userLevel);
					if ($itemD != null) {
						$itemDto[] = $itemD;
					}
				}
				$itemPicturesCount = 0;
			} else {
				$itemDto = $itemManager->getItemsForOrder($item_id, $userId, $userLevel);
				if ($itemDto == null) {
					//TODO tell user about that IMPORTANT					
					return;
				}
				$itemPicturesCount = $itemDto->getPicturesCount();
			}

			$this->addParam("item_id", $item_id);
			$this->addParam("item", $itemDto);
			$this->addParam("item_pictures_count", $itemPicturesCount);
			$this->addParam("itemManager", $itemManager);
		}
	}

	public function getDefaultLoads($args) {
		$loads = array();
		return $loads;
	}

	public function isValidLoad($namespace, $load) {
		return true;
	}

	public function getTemplate() {
		return TEMPLATES_DIR . "/main/pc_configurator/pcc_item_description.tpl";
	}

	public function getRequestGroup() {
		return RequestGroups::$guestRequest;
	}

	protected function logRequest() {
		return false;
	}

}

?>