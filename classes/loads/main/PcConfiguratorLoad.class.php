<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/pcc_managers/PcConfiguratorManager.class.php");
require_once (CLASSES_PATH . "/managers/CustomerCartManager.class.php");
require_once (CLASSES_PATH . "/managers/BundleItemsManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/LanguageManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class PcConfiguratorLoad extends GuestLoad {

	private $customerCartManager;
	private $bundleItemsManager;
	private $itemManager;
	private $customerEmail;

	private function initVars() {

		$customer = $this->getCustomer();
		if ($customer) {
			$this->customerEmail = strtolower($customer->getEmail());
			$this->customerCartManager = CustomerCartManager::getInstance($this->config, $this->args);
			$this->bundleItemsManager = BundleItemsManager::getInstance($this->config, $this->args);
			$this->itemManager = ItemManager::getInstance($this->config, $this->args);
		}
	}

	public function load() {

		$this->setDescriptionTagValue('Build you computer (PC) using this tool and order it. We\'ll configure your PC and proceed your order!');
		$this->setKeywordsTagValue('Build PC, Build Computer, Build Desktop Computer');
		$this->setTitleTagValue('PC Configurator, build your computer!');
		$pccm = PcConfiguratorManager::getInstance($this->config, $this->args);

		$this->addParam('pccm', $pccm);
		if (isset($this->args[0])) {
			$edited_pc_cart_item_id = $this->args[0];

			//if (!isset($this->customerEmail))
			$this->initVars();
			if (!isset($this->customerEmail)) {
				exit;
			}

			$pcToBeEdit = $this->getPcToBeEdited($edited_pc_cart_item_id);
			if ($pcToBeEdit != null) {
				$this->setPcConfiguratorRequestParamsCorrespondingToEditedPcComponents($pcToBeEdit);
				$this->addParam("configurator_mode_edit_cart_row_id", $edited_pc_cart_item_id);
			}
		}

		$right_side_panel_width = $this->getCmsVar('pc_configurator_right_side_panel_width');

		$width = intval($this->getCmsVar('whole_page_width')) - $right_side_panel_width;
		$this->addParam('left_side_panel_width', $width);
		$this->addParam('pcc_components_count', count(PcConfiguratorManager::$PCC_COMPONENTS));
                
                $lm = LanguageManager::getInstance($this->config, $this->args);
		$componentDisplayNames = array();
		foreach (PcConfiguratorManager::$PCC_COMPONENTS_DISPLAY_NAMES_IDS as $pid) {
			$componentDisplayNames[] = $pid;
		}
		$this->addParam("component_display_names", $componentDisplayNames);
                
	}

	private function setPcConfiguratorRequestParamsCorrespondingToEditedPcComponents($pcToBeEdit) {

		foreach ($pcToBeEdit as $key => $item) {
			if (strtotime($item->getItemAvailableTillDate()) + 86400 < time() || $item->getItemHidden() == 1) {
				continue;
			}

			$cat_ids = substr($item->getItemCategoriesIds(), 1, -1);
			$cat_ids = explode(',', $cat_ids);
			$ctype = PcConfiguratorManager::getPcComponentTypeByItemCategoriesIds($cat_ids);

			switch ($ctype) {
				case CategoriesConstants::CASE_CHASSIS :
					$_REQUEST['case'] = $item->getBundleItemId();
					break;
				case CategoriesConstants::MOTHER_BOARD :
					$_REQUEST['mb'] = $item->getBundleItemId();
					break;
				case CategoriesConstants::RAM_MEMORY :
					$_REQUEST['rams'] = implode(',', array_fill(0, $item->getBundleItemCount(), $item->getBundleItemId()));
					break;
				case CategoriesConstants::CPU_PROCESSOR :
					$_REQUEST['cpu'] = $item->getBundleItemId();
					break;
				case CategoriesConstants::HDD_HARD_DRIVE :
					$hdds = $this->secure($_REQUEST['hdds']);
					if (!empty($hdds)) {
						$_REQUEST['hdds'] .= ',';
					} else {
						$_REQUEST['hdds'] = "";
					}
					$_REQUEST['hdds'] .= implode(',', array_fill(0, $item->getBundleItemCount(), $item->getBundleItemId()));
					break;
				case CategoriesConstants::SSD_SOLID_STATE_DRIVE :
					$ssds = $this->secure($_REQUEST['ssds']);
					if (!empty($ssds)) {
						$_REQUEST['ssds'] .= ',';
					} else {
						$_REQUEST['ssds'] = "";
					}
					$_REQUEST['ssds'] .= implode(',', array_fill(0, $item->getBundleItemCount(), $item->getBundleItemId()));
					break;
				case CategoriesConstants::COOLER :
					$_REQUEST['cooler'] = $item->getBundleItemId();
					break;
				case CategoriesConstants::MONITOR :
					$_REQUEST['monitor'] = $item->getBundleItemId();
					break;
				case CategoriesConstants::OPTICAL_DRIVE :
					$opts = $this->secure($_REQUEST['opts']);
					if (!empty($opts)) {
						$_REQUEST['opts'] .= ',';
					} else {
						$_REQUEST['opts'] = "";
					}
					$_REQUEST['opts'] .= implode(',', array_fill(0, $item->getBundleItemCount(), $item->getBundleItemId()));
					break;
				case CategoriesConstants::POWER :
					$_REQUEST['power'] = $item->getBundleItemId();
					break;
				case CategoriesConstants::VIDEO_CARD :
					$_REQUEST['graphics'] = $item->getBundleItemId();
					break;
				case CategoriesConstants::KEYBOARD :
					$_REQUEST['keyboard'] = $item->getBundleItemId();
					break;
				case CategoriesConstants::MOUSE :
					$_REQUEST['mouse'] = $item->getBundleItemId();
					break;
				case CategoriesConstants::SPEAKER :
					$_REQUEST['speaker'] = $item->getBundleItemId();
					break;
			}
		}
	}

	private function getPcToBeEdited($edited_pc_cart_item_id) {
		$userLevel = $this->getUserLevel();
		$user_id = $this->getUserId();
		return $this->customerCartManager->getCustomerCart($this->customerEmail, $user_id, $userLevel, $edited_pc_cart_item_id);
	}

	public function getDefaultLoads($args) {
		$loads = array();
		/*
		  $loadName = "pcc_total_calculations";
		  $loads[$loadName]["load"] = "loads/main/pcc/" . $this->generateLoadClassName($loadName);
		  $loads[$loadName]["args"] = array("parentLoad" => &$this);
		  $loads[$loadName]["loads"] = array(); */

		$loadName = "pcc_auto_configuration_by_filters";
		$loads[$loadName]["load"] = "loads/main/pcc/" . $this->generateLoadClassName($loadName);
		$loads[$loadName]["args"] = array("parentLoad" => &$this);
		$loads[$loadName]["loads"] = array();

		$loadName = "pcc_select_case";
		$loads[$loadName]["load"] = "loads/main/pcc/" . $this->generateLoadClassName($loadName);
		$loads[$loadName]["args"] = array("parentLoad" => &$this);
		$loads[$loadName]["loads"] = array();

		return $loads;
	}

	public function isValidLoad($namespace, $load) {
		return true;
	}

	public function getTemplate() {
		return TEMPLATES_DIR . "/main/pc_configurator/pc_configurator.tpl";
	}

	public function getRequestGroup() {
		return RequestGroups::$guestRequest;
	}

}

?>