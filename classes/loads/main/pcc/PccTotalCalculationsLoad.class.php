<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/SpecialFeesManager.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/pcc_managers/PcConfiguratorManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class PccTotalCalculationsLoad extends GuestLoad {

	public $pcGrandTotalAmd;
	public $pcGrandTotalUsd;

	public function load() {
		$user = $this->getUser();
		$pccm = PcConfiguratorManager::getInstance($this->config, $this->args);
		$selectedComponents = $pccm->getSelectedComponentsDtosOrderedInArray($user);
		$itemManager = ItemManager::getInstance($this->config, $this->args);
		$this->addParam("selected_components", $selectedComponents);
		$priceGroup = null;
		if ($this->getUserLevel() === UserGroups::$ADMIN) {
			$customer = $this->getCustomer();
			$priceGroup = $customer->getPriceGroup();
		}
		list($totalUsd, $totalAmd) = $pccm->getSelectedComponentSubTotalsAndTotals($selectedComponents, $user->getLevel(), $priceGroup);
		$required_components_ids = $pccm->getRequestComponentRequiredComponents($this->sessionManager->getUser());
		if (count($required_components_ids) === 0) {
			$this->addParam("ready_to_order", "true");
		} else {
			$this->addParam("ready_to_order", "false");
		}
		if (intval($_REQUEST['configurator_mode_edit_cart_row_id']) > 0) {
			$this->addParam("configurator_mode_edit_cart_row_id", intval($_REQUEST['configurator_mode_edit_cart_row_id']));
		}

		$this->addParam("pccm", $pccm);
		$this->addParam("total_usd", $totalUsd);
		$this->addParam("total_amd", $totalAmd);

		$vipCustomer = 0;
		if ($this->getUserLevel() === UserGroups::$USER) {
			$customer = $this->getCustomer();
			$userManager = UserManager::getInstance($this->config, $this->args);
			$vipCustomer = $userManager->isVipAndVipEnabled($customer);
		}
		if ($this->getUserLevel() === UserGroups::$ADMIN) {
			$customer = $this->getCustomer();
			$vipCustomer = $customer->getPriceGroup() === 'vip';
		}
		if ($vipCustomer) {
			$pc_configurator_discount = floatval($this->getCmsVar('vip_pc_configurator_discount'));
		} else {
			$pc_configurator_discount = floatval($this->getCmsVar('pc_configurator_discount'));
		}

		$this->addParam("pc_configurator_discount", $pc_configurator_discount);

		$selectedComponentProfitWithDiscount = $pccm->calcSelectedComponentProfitWithDiscount($selectedComponents, $user->getLevel(), $pc_configurator_discount, $priceGroup);

                $pcBuildFeeAMD = $pccm->calcPcBuildFee($selectedComponentProfitWithDiscount);
		$this->addParam("pc_build_fee_amd", $pcBuildFeeAMD);

		$this->pcGrandTotalAmd = $totalAmd * (1 - $pc_configurator_discount / 100) + $pcBuildFeeAMD;
		$this->pcGrandTotalUsd = $totalUsd;
		$this->addParam("grand_total_amd", $this->pcGrandTotalAmd);

		$this->addParam("itemManager", $itemManager);
	}

	public function getDefaultLoads($args) {

		$loads = array();
		if (isset($this->pcGrandTotalAmd)) {
			$loadName = "pcc_credit_calculation";
			$loads[$loadName]["load"] = "loads/main/pcc/" . $this->generateLoadClassName($loadName);
			$loads[$loadName]["args"] = array("parentLoad" => &$this);
			$loads[$loadName]["args"]["pcGrandTotalAmd"] = $this->pcGrandTotalAmd;
			$loads[$loadName]["args"]["pcGrandTotalUsd"] = $this->pcGrandTotalUsd;
			$loads[$loadName]["loads"] = array();
		}
		return $loads;
	}

	public function isValidLoad($namespace, $load) {
		return true;
	}

	public function getTemplate() {
		return TEMPLATES_DIR . "/main/pc_configurator/pcc_total_calculations.tpl";
	}

	public function getRequestGroup() {
		return RequestGroups::$guestRequest;
	}

	protected function logRequest() {
		return false;
	}

}

?>