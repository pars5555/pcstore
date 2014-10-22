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
class PccSelectCaseLoad extends PccSelectComponentLoad {

	function getTabHeaderInfoText() {
		return $this->getPhraseSpan(225);
	}

	function getComponentTypeIndex() {
		return PcConfiguratorManager::$PCC_COMPONENTS['case'];
	}

	function getSelectedComponentItemId() {
		$case = null;
		if ($_REQUEST['case']) {
			$case = $this->secure($_REQUEST['case']);
			$this->addParam('selected_component_id', $case);
		}
		return $case;
	}

	function getNeededCategoriesIdsAndOrFormulaArray() {
		$pccm = PcConfiguratorManager::getInstance($this->config, $this->args);
		if ($_REQUEST['mb']) {
			$mb = $this->secure($_REQUEST['mb']);
			$motherboard_form_factor = $pccm->getMbFormFactor($mb);
		}
		$neededCategoriesIdsAndOrFormulaArray = array();
		if ($motherboard_form_factor) {
			//Motherboard is ATX
			if ($motherboard_form_factor == CategoriesConstants::MB_FORM_FACTOR_ATX) {
				//CASE size should be ATX
				$neededCategoriesIdsAndOrFormulaArray = array(CategoriesConstants::CASE_SIZE_ATX, ":", PcConfiguratorManager::PCC_CASE_SIZE_COMPATIBLE_DB);
				//Motherboard is mini ATX
			} else if ($motherboard_form_factor == CategoriesConstants::MB_FORM_FACTOR_MINI_ATX) {
				//CASE size should be ATX or mini ATX
				$neededCategoriesIdsAndOrFormulaArray = array(CategoriesConstants::CASE_SIZE_ATX, 'or', CategoriesConstants::CASE_SIZE_MINI_ATX, ":", PcConfiguratorManager::PCC_CASE_SIZE_COMPATIBLE_DB);
				//Motherboard is micro ATX
			} else if ($motherboard_form_factor == CategoriesConstants::MB_FORM_FACTOR_MICRO_ATX) {
				//CASE size should be ATX or mini ATX or Micro Atx
				$neededCategoriesIdsAndOrFormulaArray = array(CategoriesConstants::CASE_SIZE_ATX, 'or', CategoriesConstants::CASE_SIZE_MINI_ATX, 'or', CategoriesConstants::CASE_SIZE_MICRO_ATX, ":", PcConfiguratorManager::PCC_CASE_SIZE_COMPATIBLE_DB);
			}
		}
		return $neededCategoriesIdsAndOrFormulaArray;
	}

	function getRequiredCategoriesFormulasArray() {
		return array('(', CategoriesConstants::CASE_SIZE_ATX, 'or', CategoriesConstants::CASE_SIZE_MINI_ATX, 'or', CategoriesConstants::CASE_SIZE_MICRO_ATX, ')');
	}

	public function getSelectedSameItemsCount() {
		if ($_REQUEST['case']) {
			return 1;
		} else {
			return 0;
		}
	}

}

?>