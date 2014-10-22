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
class PccSelectCpuLoad extends PccSelectComponentLoad {

	public function getRequiredCategoriesFormulasArray() {
		return array('(', CategoriesConstants::CPU_SOCKET_478, 'or', CategoriesConstants::CPU_SOCKET_775, 'or', CategoriesConstants::CPU_SOCKET_1150, 'or', CategoriesConstants::CPU_SOCKET_1155, 'or', CategoriesConstants::CPU_SOCKET_1156, 'or', CategoriesConstants::CPU_SOCKET_1366, 'or', CategoriesConstants::CPU_SOCKET_2011, ')');
	}

	public function getSelectedSameItemsCount() {
		if ($_REQUEST['cpu']) {
			return 1;
		} else {
			return 0;
		}
	}

	public function getComponentTypeIndex() {
		return PcConfiguratorManager::$PCC_COMPONENTS['cpu'];
	}

	public function getNeededCategoriesIdsAndOrFormulaArray() {
		$pccm = PcConfiguratorManager::getInstance($this->config, $this->args);
		if ($_REQUEST['mb']) {
			$mb = $this->secure($_REQUEST['mb']);
			$motherboard_socket = $pccm->getMbSocket($mb);
		}

		if ($_REQUEST['cooler']) {
			$cooler = $this->secure($_REQUEST['cooler']);
			$cooler_sockets = $pccm->getCoolerSockets($cooler);
		}
		$neededCategoriesIdsAndOrFormulaArray = array();

		//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< checking MB compatibility >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		if ($motherboard_socket) {
			//if Mb soxket is 478
			if ($motherboard_socket == CategoriesConstants::MB_SOCKET_478) {
				//CPU socket should be 478
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::CPU_SOCKET_478;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_MB_SOCKET_COMPATIBLE_DB;
			} else
			//if MB soxket is 775
			if ($motherboard_socket == CategoriesConstants::MB_SOCKET_775) {
				//CPU socket should be 775
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::CPU_SOCKET_775;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_MB_SOCKET_COMPATIBLE_DB;
			} else
			//if MB soxket is 1150
			if ($motherboard_socket == CategoriesConstants::MB_SOCKET_1150) {
				//CPU socket should be 1150
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::CPU_SOCKET_1150;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_MB_SOCKET_COMPATIBLE_DB;
			} else
			//if MB soxket is 1155
			if ($motherboard_socket == CategoriesConstants::MB_SOCKET_1155) {
				//CPU socket should be 1155
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::CPU_SOCKET_1155;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_MB_SOCKET_COMPATIBLE_DB;
			} else
			//if MB soxket is 1156
			if ($motherboard_socket == CategoriesConstants::MB_SOCKET_1156) {
				//CPU socket should be 1156
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::CPU_SOCKET_1156;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_MB_SOCKET_COMPATIBLE_DB;
			} else
			//if MB soxket is 1366
			if ($motherboard_socket == CategoriesConstants::MB_SOCKET_1366) {
				//CPU socket should be 1366
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::CPU_SOCKET_1366;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_MB_SOCKET_COMPATIBLE_DB;
			} else
			//if MB soxket is 2011
			if ($motherboard_socket == CategoriesConstants::MB_SOCKET_2011) {
				//CPU socket should be 2011
				$neededCategoriesIdsAndOrFormulaArray[] = '(';
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::CPU_SOCKET_2011;
				$neededCategoriesIdsAndOrFormulaArray[] = ')';
				$neededCategoriesIdsAndOrFormulaArray[] = ':';
				$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_MB_SOCKET_COMPATIBLE_DB;
			}
		}

		//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< checking COOLER compatibility >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		if ($cooler_sockets) {

			$neededCategoriesIdsAndOrFormulaArray[] = '(';
			if (in_array(CategoriesConstants::COOLER_SOCKET_478, $cooler_sockets)) {
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::CPU_SOCKET_478;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
			}
			if (in_array(CategoriesConstants::COOLER_SOCKET_775, $cooler_sockets)) {
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::CPU_SOCKET_775;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
			}
			if (in_array(CategoriesConstants::COOLER_SOCKET_1150, $cooler_sockets)) {
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::CPU_SOCKET_1150;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
			}
			if (in_array(CategoriesConstants::COOLER_SOCKET_1155, $cooler_sockets)) {
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::CPU_SOCKET_1155;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
			}
			if (in_array(CategoriesConstants::COOLER_SOCKET_1156, $cooler_sockets)) {
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::CPU_SOCKET_1156;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
			}
			if (in_array(CategoriesConstants::COOLER_SOCKET_1366, $cooler_sockets)) {
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::CPU_SOCKET_1366;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
			}
			if (in_array(CategoriesConstants::COOLER_SOCKET_2011, $cooler_sockets)) {
				$neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::CPU_SOCKET_2011;
				$neededCategoriesIdsAndOrFormulaArray[] = 'or';
			}
			array_pop($neededCategoriesIdsAndOrFormulaArray);
			$neededCategoriesIdsAndOrFormulaArray[] = ')';
			$neededCategoriesIdsAndOrFormulaArray[] = ':';
			$neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_COOLER_SOCKET_COMPATIBLE_DB;
		}
		return $neededCategoriesIdsAndOrFormulaArray;
	}

	public function getSelectedComponentItemId() {
		$cpu = null;
		if ($_REQUEST['cpu']) {
			$cpu = $this->secure($_REQUEST['cpu']);
			$this->addParam('selected_component_id', $cpu);
		}
		return $cpu;
	}

	public function getTabHeaderInfoText() {
		return $this->getPhraseSpan(232);
	}

}

?>