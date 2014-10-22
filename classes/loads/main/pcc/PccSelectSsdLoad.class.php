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
class PccSelectSsdLoad extends PccSelectComponentLoad {

    public function getComponentTypeIndex() {
        return PcConfiguratorManager::$PCC_COMPONENTS['ssd'];
    }

    public function getNeededCategoriesIdsAndOrFormulaArray() {
        $pccm = PcConfiguratorManager::getInstance($this->config, $this->args);
        if (isset($_REQUEST['mb'])) {
            $mb = $this->secure($_REQUEST['mb']);
            $motherboard_sata_ide_support = $pccm->getMbSataIdeSupport($mb);
        }
        $neededCategoriesIdsAndOrFormulaArray = array();

        if ($motherboard_sata_ide_support) {
            if (in_array(CategoriesConstants::MB_SATA_SUPPORTED, $motherboard_sata_ide_support)) {
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::SSD_SOLID_STATE_DRIVE;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_STORAGE_INTERFACE_COMPATIBLE_DB;
            }
        }
        return $neededCategoriesIdsAndOrFormulaArray;
    }

    public function getSelectedComponentItemId() {
        $ssds = null;
        if (isset($_REQUEST['ssds'])) {
            $ssds = $this->secure($_REQUEST['ssds']);
            $this->addParam('selected_components_ids_str', $ssds);
            $ssds = explode(',', $ssds);
            $this->addParam('selected_components_ids_array', $ssds);
        }
        $this->addParam('multiselect_component', true);
        $this->addParam('multi_count_selection_item', true);
        return $ssds;
    }

    public function getTabHeaderInfoText() {
        return $this->getPhraseSpan(236);
    }

    public function getSelectedItemCount($item) {
        if (isset($_REQUEST['ssds'])) {
            $ssds = $this->secure($_REQUEST['ssds']);
            $ssds = ',' . $ssds . ',';
            return substr_count($ssds, $item->getId());
        } else {
            return 0;
        }
    }

    public function getComponentMaxPossibleCount($item) {
        if (isset($_REQUEST['mb'])) {
            $mb = $this->secure($_REQUEST['mb']);
        }
        $pccm = PcConfiguratorManager::getInstance($this->config, $this->args);
        $selected_ssd_count = (int) $this->getSelectedItemCount($item);
        $mb_free_sata_storage_count = (int) $pccm->getMbSataStorageFreePortCount($mb);
        return $selected_ssd_count + $mb_free_sata_storage_count;
    }

    public function getRequiredCategoriesFormulasArray() {
        return array(CategoriesConstants::SSD_SOLID_STATE_DRIVE);
    }

    public function getSelectedSameItemsCount() {
        if (isset($_REQUEST['ssds'])) {
            $ssds = $this->secure($_REQUEST['ssds']);
            $ssds = explode(',', $ssds);
            $ssds = array_unique($ssds);
            return count($ssds);
        } else {
            return 0;
        }
    }

}

?>