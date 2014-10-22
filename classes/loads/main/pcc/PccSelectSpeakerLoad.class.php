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
class PccSelectSpeakerLoad extends PccSelectComponentLoad {

	public function getRequiredCategoriesFormulasArray() {
		return array(CategoriesConstants::SPEAKER);
	}

	public function getSelectedSameItemsCount() {
		if ($_REQUEST['speaker']) {
			return 1;
		} else {
			return 0;
		}
	}

	public function getComponentTypeIndex() {
		return PcConfiguratorManager::$PCC_COMPONENTS['speaker'];
	}

	public function getNeededCategoriesIdsAndOrFormulaArray() {
		return null;
	}

	public function getSelectedComponentItemId() {
		$speaker = null;
		if ($_REQUEST['speaker']) {
			$speaker = $this->secure($_REQUEST['speaker']);
			$this->addParam('selected_component_id', $speaker);
		}
		return $speaker;
	}

	public function getTabHeaderInfoText() {
		return $this->getPhraseSpan(243);
	}

}

?>