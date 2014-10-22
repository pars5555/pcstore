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
class PccSelectCoolerLoad extends PccSelectComponentLoad {

	function getTabHeaderInfoText() {
		return $this->getPhraseSpan(234);
	}

	function getComponentTypeIndex() {
		return PcConfiguratorManager::$PCC_COMPONENTS['cooler'];
	}

	function getSelectedComponentItemId() {
		if ($_REQUEST['cooler']) {
			$cooler = $this->secure($_REQUEST['cooler']);
			$this->addParam('selected_component_id', $cooler);
		}
		return $cooler;
	}

	function getNeededCategoriesIdsAndOrFormulaArray() {
		$pccm = PcConfiguratorManager::getInstance($this->config, $this->args);
		if ($_REQUEST['mb']) {
			$mb = $this->secure($_REQUEST['mb']);
			$motherboard_socket = $pccm->getMbSocket($mb);
		}

		if ($_REQUEST['cpu']) {
			$cpu = $this->secure($_REQUEST['cpu']);
			$cpu_socket = $pccm->getCpuSocket($cpu);
		}

		$neededCategoriesIdsAndOrFormulaArray = array();
		//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< checking Motherboard Socket compatibility >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		if ($motherboard_socket) {
			//if MB SOCKET is 478
			if ($motherboard_socket == CategoriesConstants::MB_SOCKET_478) {
				//Then Cooler Socket should be 478
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::COOLER_SOCKET_478;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_MB_SOCKET_COMPATIBLE_DB;
			} else
			//if MB SOCKET is 775
			if ($motherboard_socket == CategoriesConstants::MB_SOCKET_775) {
				//Then Cooler Socket should be 775
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::COOLER_SOCKET_775;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_MB_SOCKET_COMPATIBLE_DB;
			} else
			//if MB SOCKET is 1150
			if ($motherboard_socket == CategoriesConstants::MB_SOCKET_1150) {
				//Then Cooler Socket should be 1150
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::COOLER_SOCKET_1150;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_MB_SOCKET_COMPATIBLE_DB;
			} else
			//if MB SOCKET is 1155
			if ($motherboard_socket == CategoriesConstants::MB_SOCKET_1155) {
				//Then Cooler Socket should be 1155
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::COOLER_SOCKET_1155;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_MB_SOCKET_COMPATIBLE_DB;
			} else
			//if MB SOCKET is 1156
			if ($motherboard_socket == CategoriesConstants::MB_SOCKET_1156) {
				//Then Cooler Socket should be 1156
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::COOLER_SOCKET_1156;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_MB_SOCKET_COMPATIBLE_DB;
			} else
			//if MB SOCKET is 1366
			if ($motherboard_socket == CategoriesConstants::MB_SOCKET_1366) {
				//Then Cooler Socket should be 1366
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::COOLER_SOCKET_1366;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_MB_SOCKET_COMPATIBLE_DB;
			} else
			//if MB SOCKET is 2011
			if ($motherboard_socket == CategoriesConstants::MB_SOCKET_2011) {
				//Then Cooler Socket should be 2011
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::COOLER_SOCKET_2011;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_MB_SOCKET_COMPATIBLE_DB;
			}
		}

		//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< checking CPU Socket compatibility >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		if ($cpu_socket) {
			//if CPU SOCKET is 478
			if ($cpu_socket == CategoriesConstants::CPU_SOCKET_478) {
				//Then Cooler Socket should be 478
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::COOLER_SOCKET_478;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_CPU_SOCKET_COMPATIBLE_DB;
			} else
			//if CPU SOCKET is 775
			if ($cpu_socket == CategoriesConstants::CPU_SOCKET_775) {
				//Then Cooler Socket should be 775
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::COOLER_SOCKET_775;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_CPU_SOCKET_COMPATIBLE_DB;
			} else
			//if CPU SOCKET is 1150
			if ($cpu_socket == CategoriesConstants::CPU_SOCKET_1150) {
				//Then Cooler Socket should be 1150
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::COOLER_SOCKET_1150;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_CPU_SOCKET_COMPATIBLE_DB;
			} else
			//if CPU SOCKET is 1155
			if ($cpu_socket == CategoriesConstants::CPU_SOCKET_1155) {
				//Then Cooler Socket should be 1155
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::COOLER_SOCKET_1155;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_CPU_SOCKET_COMPATIBLE_DB;
			} else
			//if CPU SOCKET is 1156
			if ($cpu_socket == CategoriesConstants::CPU_SOCKET_1156) {
				//Then Cooler Socket should be 1156
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::COOLER_SOCKET_1156;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_CPU_SOCKET_COMPATIBLE_DB;
			} else
			//if CPU SOCKET is 1366
			if ($cpu_socket == CategoriesConstants::CPU_SOCKET_1366) {
				//Then Cooler Socket should be 1366
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::COOLER_SOCKET_1366;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_CPU_SOCKET_COMPATIBLE_DB;
			} else
			//if CPU SOCKET is 2011
			if ($cpu_socket == CategoriesConstants::CPU_SOCKET_2011) {
				//Then Cooler Socket should be 2011
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::COOLER_SOCKET_2011;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_CPU_SOCKET_COMPATIBLE_DB;
			}
		}
		return $neededCategoriesIdsAndOrFormulaArray;
	}

	public function getRequiredCategoriesFormulasArray() {
		return array('(', CategoriesConstants::COOLER_SOCKET_478, 'or', CategoriesConstants::COOLER_SOCKET_775, 'or', CategoriesConstants::COOLER_SOCKET_1150, 'or', CategoriesConstants::COOLER_SOCKET_1155, 'or', CategoriesConstants::COOLER_SOCKET_1156, 'or', CategoriesConstants::COOLER_SOCKET_1366, 'or', CategoriesConstants::COOLER_SOCKET_2011, ')');
	}

	public function getSelectedSameItemsCount() {
		if ($_REQUEST['cooler']) {
			return 1;
		} else {
			return 0;
		}
	}

}

?>