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
class PccSelectRamLoad extends PccSelectComponentLoad {

	public function getSelectedItemCount($item) {
		if ($_REQUEST['rams']) {
			$rams = $this->secure($_REQUEST['rams']);
			$rams = ',' . $rams . ',';
			return substr_count($rams, $item->getId());
		} else {
			return 0;
		}
	}

	public function getComponentMaxPossibleCount($item) {
		$pccm = PcConfiguratorManager::getInstance($this->config, $this->args);
		$ramKitCount = $pccm->getRamKitCountRepresentedInInteger($item);
		return $this->getSelectedMotherBoardRamSlotCount() / $ramKitCount;
	}

	public function getSelectedMotherBoardRamSlotCount() {
		if ($_REQUEST['mb']) {
			$pccm = PcConfiguratorManager::getInstance($this->config, $this->args);
			$mb = $this->secure($_REQUEST['mb']);
			return $pccm->getMbRamSlotCountRepresentedInInteger($mb);
		} else {
			//return the maximum system supported motherboard ram slot count which currently is 8
			return PcConfiguratorManager::$maximum_system_mb_ram_count_supported;
		}
	}

	public function getRequiredCategoriesFormulasArray() {
		return array('(', '(', CategoriesConstants::RAM_KIT_COUNT_1, 'or', CategoriesConstants::RAM_KIT_COUNT_2, 'or', CategoriesConstants::RAM_KIT_COUNT_3, 'or', CategoriesConstants::RAM_KIT_COUNT_4, 'or', CategoriesConstants::RAM_KIT_COUNT_6, 'or', CategoriesConstants::RAM_KIT_COUNT_8, ')', 'and', '(', CategoriesConstants::RAM_TYPE_DDR, 'or', CategoriesConstants::RAM_TYPE_DDR2, 'or', CategoriesConstants::RAM_TYPE_DDR3, ')', ')');
	}

	public function getSelectedSameItemsCount() {
		if ($_REQUEST['rams']) {
			$rams = $this->secure($_REQUEST['rams']);
			$rams = explode(',', $rams);
			$rams = array_unique($rams);
			return count($rams);
		} else {
			return 0;
		}
	}

	public function getComponentTypeIndex() {
		return PcConfiguratorManager::$PCC_COMPONENTS['ram'];
	}

	public function getNeededCategoriesIdsAndOrFormulaArray() {
		$pccm = PcConfiguratorManager::getInstance($this->config, $this->args);
		if ($_REQUEST['mb']) {
			$mb = $this->secure($_REQUEST['mb']);
			$motherboard_ram_type = $pccm->getMbRamType($mb);
			$motherboard_ram_slot_count = $pccm->getMbRamSlotCount($mb);
			//$motherboard_ram_slot_count_integer = $pccm->getMbRamSlotCountRepresentedInInteger($selected_mb);
		}

		$neededCategoriesIdsAndOrFormulaArray = array();
		if ($motherboard_ram_type) {
			if ($motherboard_ram_type == CategoriesConstants::MB_RAM_TYPE_DDR) {
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_TYPE_DDR;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_RAM_TYPE_COMPATIBLE_DB;
			} elseif ($motherboard_ram_type == CategoriesConstants::MB_RAM_TYPE_DDR2) {
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_TYPE_DDR2;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_RAM_TYPE_COMPATIBLE_DB;
			} elseif ($motherboard_ram_type == CategoriesConstants::MB_RAM_TYPE_DDR3) {
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_TYPE_DDR3;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_RAM_TYPE_COMPATIBLE_DB;
			}
		}

		if ($motherboard_ram_slot_count) {
			//Motherboard RAM SLOT COUNT is 1
			if ($motherboard_ram_slot_count == CategoriesConstants::MB_RAM_SLOT_COUNT_1) {
				//then RAM kit count should be 1
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_1;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_RAM_COUNT_COMPATIBLE_DB;
			} else
			//Motherboard RAM SLOT COUNT is 2
			if ($motherboard_ram_slot_count == CategoriesConstants::MB_RAM_SLOT_COUNT_2) {
				//then RAM kit count can be 1 or 2
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_1;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_2;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_RAM_COUNT_COMPATIBLE_DB;
			} else
			//Motherboard RAM SLOT COUNT is 3
			if ($motherboard_ram_slot_count == CategoriesConstants::MB_RAM_SLOT_COUNT_3) {
				//then RAM kit count can be 1 , 2 or 3
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_1;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_2;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_3;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_RAM_COUNT_COMPATIBLE_DB;
			} else
			//Motherboard RAM SLOT COUNT is 4
			if ($motherboard_ram_slot_count == CategoriesConstants::MB_RAM_SLOT_COUNT_4) {
				//then RAM kit count can be 1,2,3 or 4
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_1;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_2;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_3;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_4;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_RAM_COUNT_COMPATIBLE_DB;
			} else
			//Motherboard RAM SLOT COUNT is 6
			if ($motherboard_ram_slot_count == CategoriesConstants::MB_RAM_SLOT_COUNT_6) {
				//then RAM kit count can  be 1,2,3,4 or 6
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_1;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_2;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_3;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_4;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_6;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_RAM_COUNT_COMPATIBLE_DB;
			} else
			//Motherboard RAM SLOT COUNT is 8
			if ($motherboard_ram_slot_count == CategoriesConstants::MB_RAM_SLOT_COUNT_8) {
				//then RAM kit count can  be 1,2,3,4,6 or 8
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_1;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_2;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_3;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_4;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_6;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::RAM_KIT_COUNT_8;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_RAM_COUNT_COMPATIBLE_DB;
			}
		}
		return $neededCategoriesIdsAndOrFormulaArray;
	}

	public function getSelectedComponentItemId() {
		$rams = null;
		if ($_REQUEST['rams']) {
			$rams = $this->secure($_REQUEST['rams']);
			$this->addParam('selected_components_ids_str', $rams);
			$rams = explode(',', $rams);
			$this->addParam('selected_components_ids_array', $rams);
			// var_dump($selected_rams);
		}
		$this->addParam('multi_count_selection_item', true);
		return $rams;
	}

	public function getTabHeaderInfoText() {
		return $this->getPhraseSpan(235);
	}

}

?>