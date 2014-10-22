<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/CompaniesPriceListManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyDealersManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/util/pcc_categories_constants/CategoriesConstants.php");
require_once (CLASSES_PATH . "/managers/pcc_managers/PccMessagesManager.class.php");
require_once (CLASSES_PATH . "/managers/pcc_managers/PcConfiguratorManager.class.php");
require_once (CLASSES_PATH . "/loads/main/pcc/PccSelectComponentLoad.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class PccSelectPowerLoad extends PccSelectComponentLoad {

	public function getRequiredCategoriesFormulasArray() {
		return array(CategoriesConstants::POWER);
	}

	public function getSelectedSameItemsCount() {
		if ($_REQUEST['power']) {
			return 1;
		} else {
			return 0;
		}
	}

	public function getComponentTypeIndex() {
		return PcConfiguratorManager::$PCC_COMPONENTS['power'];
	}

	public function getNeededCategoriesIdsAndOrFormulaArray() {
		return null;
	}

	public function getSelectedComponentItemId() {
		if ($_REQUEST['power']) {
			$power = $this->secure($_REQUEST['power']);
			$this->addParam('selected_component_id', $power);
		}
		return $power;
	}

	public function getTabHeaderInfoText() {
		return $this->getPhraseSpan(240);
	}

}

?>