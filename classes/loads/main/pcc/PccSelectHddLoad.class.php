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
class PccSelectHddLoad extends PccSelectComponentLoad {

	public function getComponentTypeIndex() {
		return PcConfiguratorManager::$PCC_COMPONENTS['hdd'];
	}

	public function getNeededCategoriesIdsAndOrFormulaArray() {
		$pccm = PcConfiguratorManager::getInstance($this->config, $this->args);
		if ($_REQUEST['mb']) {
			$mb = $this->secure($_REQUEST['mb']);
			$motherboard_sata_ide_support = $pccm->getMbSataIdeSupport($mb);
		}
		$neededCategoriesIdsAndOrFormulaArray = array();

		if ($motherboard_sata_ide_support) {
			if (in_array(CategoriesConstants::MB_SATA_SUPPORTED, $motherboard_sata_ide_support) && in_array(CategoriesConstants::MB_IDE_SUPPORTED, $motherboard_sata_ide_support)) {
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::HDD_SATA;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::HDD_ATA;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_STORAGE_INTERFACE_COMPATIBLE_DB;
			} elseif (in_array(CategoriesConstants::MB_IDE_SUPPORTED, $motherboard_sata_ide_support)) {
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::HDD_ATA;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_STORAGE_INTERFACE_COMPATIBLE_DB;
			} elseif (in_array(CategoriesConstants::MB_SATA_SUPPORTED, $motherboard_sata_ide_support)) {
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::HDD_SATA;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_STORAGE_INTERFACE_COMPATIBLE_DB;
			}
		}
		return $neededCategoriesIdsAndOrFormulaArray;
	}

	public function getSelectedComponentItemId() {
		$hdds = null;
		if ($_REQUEST['hdds']) {
			$hdds = $this->secure($_REQUEST['hdds']);
			$this->addParam('selected_components_ids_str', $hdds);
			$hdds = explode(',', $hdds);
			$this->addParam('selected_components_ids_array', $hdds);
		}
		$this->addParam('multiselect_component', true);
		$this->addParam('multi_count_selection_item', true);
		return $hdds;
	}

	public function getTabHeaderInfoText() {
		return $this->getPhraseSpan(236);
	}

	public function getSelectedItemCount($item) {
		if ($_REQUEST['hdds']) {
			$hdds = $this->secure($_REQUEST['hdds']);
			$hdds = ',' . $hdds . ',';
			return substr_count($hdds, $item->getId());
		} else {
			return 0;
		}
	}

	public function getComponentMaxPossibleCount($item) {
		if ($_REQUEST['mb']) {
			$mb = $this->secure($_REQUEST['mb']);
		}
		$pccm = PcConfiguratorManager::getInstance($this->config, $this->args);
		$interface = $pccm->getHddInterface($item);
		$selected_hdd_count = (int) $this->getSelectedItemCount($item);
		if ($interface == CategoriesConstants::HDD_SATA) {
			$mb_free_sata_storage_count = (int) $pccm->getMbSataStorageFreePortCount($mb);
			return $selected_hdd_count + $mb_free_sata_storage_count;
		} else if ($interface == CategoriesConstants::HDD_ATA) {
			$mb_free_ata_storage_count = (int) $pccm->getMbAtaStorageFreePortCount($mb);
			return $selected_hdd_count + $mb_free_ata_storage_count;
		}
	}

	public function getRequiredCategoriesFormulasArray() {
		return array('(', CategoriesConstants::HDD_SATA, 'or', CategoriesConstants::HDD_ATA, ')');
	}

	public function getSelectedSameItemsCount() {
		if ($_REQUEST['hdds']) {
			$hdds = $this->secure($_REQUEST['hdds']);
			$hdds = explode(',', $hdds);
			$hdds = array_unique($hdds);
			return count($hdds);
		} else {
			return 0;
		}
	}

}

?>