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
class PccSelectMouseLoad extends PccSelectComponentLoad {

	public function getRequiredCategoriesFormulasArray() {
		return array(CategoriesConstants::MOUSE);
	}

	public function getSelectedSameItemsCount() {
		if ($_REQUEST['mouse']) {
			return 1;
		} else {
			return 0;
		}
	}

	public function getComponentTypeIndex() {
		return PcConfiguratorManager::$PCC_COMPONENTS['mouse'];
	}

	public function getNeededCategoriesIdsAndOrFormulaArray() {
		return array();
	}

	public function getSelectedComponentItemId() {
		if ($_REQUEST['mouse']) {
			$mouse = $this->secure($_REQUEST['mouse']);
			$this->addParam('selected_component_id', $mouse);
		}
		return $mouse;
	}

	public function getTabHeaderInfoText() {
		return $this->getPhraseSpan(241);
	}

}

?>