<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/CompaniesPriceListManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyDealersManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/util/pcc_categories_constants/CategoriesConstants.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/pcc_managers/PccMessagesManager.class.php");
require_once (CLASSES_PATH . "/managers/pcc_managers/PcConfiguratorManager.class.php");
require_once (CLASSES_PATH . "/loads/main/pcc/PccSelectComponentLoad.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class PccSelectKeyboardLoad extends PccSelectComponentLoad {

	public function getRequiredCategoriesFormulasArray() {
		return array(CategoriesConstants::KEYBOARD);
	}

	public function getSelectedSameItemsCount() {
		if ($_REQUEST['keyboard']) {
			return 1;
		} else {
			return 0;
		}
	}

	public function getComponentTypeIndex() {
		return PcConfiguratorManager::$PCC_COMPONENTS['keyboard'];
	}

	public function getNeededCategoriesIdsAndOrFormulaArray() {
		return array();
	}

	public function getSelectedComponentItemId() {
		$keyboard = null;
		if ($_REQUEST['keyboard']) {
			$keyboard = $this->secure($_REQUEST['keyboard']);
			$this->addParam('selected_component_id', $keyboard);
		}
		return $keyboard;
	}

	public function getTabHeaderInfoText() {
		return $this->getPhraseSpan(242);
	}

}

?>