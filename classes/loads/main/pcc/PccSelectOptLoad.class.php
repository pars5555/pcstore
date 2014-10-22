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
class PccSelectOptLoad extends PccSelectComponentLoad {

	public function getSelectedItemCount($item) {
		if ($_REQUEST['opts']) {
			$opts = $this->secure($_REQUEST['opts']);
			$opts = ',' . $opts . ',';
			return substr_count($opts, $item->getId());
		} else {
			return 0;
		}
	}

	public function getComponentMaxPossibleCount($item) {
		if ($_REQUEST['mb']) {
			$mb = $this->secure($_REQUEST['mb']);
		}
		$pccm = PcConfiguratorManager::getInstance($this->config, $this->args);
		$interface = $pccm->getOptInterface($item);
		$selected_opt_count = (int) $this->getSelectedItemCount($item);
		if ($interface == CategoriesConstants::OPTICAL_DRIVE_SATA) {
			$mb_free_sata_storage_count = (int) $pccm->getMbSataStorageFreePortCount($mb);
			return $selected_opt_count + $mb_free_sata_storage_count;
		} else if ($interface == CategoriesConstants::OPTICAL_DRIVE_IDE) {
			$mb_free_ata_storage_count = (int) $pccm->getMbAtaStorageFreePortCount($mb);
			return $selected_opt_count + $mb_free_ata_storage_count;
		}
	}

	public function getRequiredCategoriesFormulasArray() {
		return array('(', CategoriesConstants::OPTICAL_DRIVE_SATA, 'or', CategoriesConstants::OPTICAL_DRIVE_IDE, ')');
	}

	public function getSelectedSameItemsCount() {
		if ($_REQUEST['opts']) {
			$opts = $this->secure($_REQUEST['opts']);
			$opts = explode(',', $opts);
			$opts = array_unique($opts);
			return count($opts);
		} else {
			return 0;
		}
	}

	public function getComponentTypeIndex() {
		return PcConfiguratorManager::$PCC_COMPONENTS['opt'];
	}

	public function getNeededCategoriesIdsAndOrFormulaArray() {
		$pccm = PcConfiguratorManager::getInstance($this->config, $this->args);
		if ($_REQUEST['mb']) {
			$mb = $this->secure($_REQUEST['mb']);
		}

		if ($_REQUEST['mb']) {
			$mb = $this->secure($_REQUEST['mb']);
			$motherboard_sata_ide_support = $pccm->getMbSataIdeSupport($mb);
		}

		$neededCategoriesIdsAndOrFormulaArray = array();
		if ($motherboard_sata_ide_support) {
			if (in_array(CategoriesConstants::MB_SATA_SUPPORTED, $motherboard_sata_ide_support) && in_array(CategoriesConstants::MB_IDE_SUPPORTED, $motherboard_sata_ide_support)) {
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::OPTICAL_DRIVE_SATA;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::OPTICAL_DRIVE_IDE;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_STORAGE_INTERFACE_COMPATIBLE_DB;
			} elseif (in_array(CategoriesConstants::MB_IDE_SUPPORTED, $motherboard_sata_ide_support)) {
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::OPTICAL_DRIVE_IDE;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_STORAGE_INTERFACE_COMPATIBLE_DB;
			} elseif (in_array(CategoriesConstants::MB_SATA_SUPPORTED, $motherboard_sata_ide_support)) {
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::OPTICAL_DRIVE_SATA;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_STORAGE_INTERFACE_COMPATIBLE_DB;
			}
		}
	}

	public function getSelectedComponentItemId() {
		$opts = null;
		if ($_REQUEST['opts']) {
			$opts = $this->secure($_REQUEST['opts']);
			$this->addParam('selected_components_ids_str', $opts);
			$opts = explode(',', $opts);
			$this->addParam('selected_components_ids_array', $opts);
		}
		$this->addParam('multiselect_component', true);
		$this->addParam('multi_count_selection_item', true);
		return $opts;
	}

	public function getTabHeaderInfoText() {
		return $this->getPhraseSpan(237);
	}

}

?>