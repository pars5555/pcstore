<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/CompaniesPriceListManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyDealersManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/util/pcc_categories_constants/CategoriesConstants.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/pcc_managers/PcConfiguratorManager.class.php");
require_once (CLASSES_PATH . "/managers/pcc_managers/PccMessagesManager.class.php");
require_once (CLASSES_PATH . "/loads/main/pcc/PccSelectComponentLoad.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class PccSelectMbLoad extends PccSelectComponentLoad {

    public function getRequiredCategoriesFormulasArray() {
        return array('(', '(', CategoriesConstants::MB_FORM_FACTOR_ATX, 'or', CategoriesConstants::MB_FORM_FACTOR_MINI_ATX, 'or', CategoriesConstants::MB_FORM_FACTOR_MICRO_ATX, ')', 'and', '(', CategoriesConstants::MB_SOCKET_478, 'or', CategoriesConstants::MB_SOCKET_775, 'or', CategoriesConstants::MB_SOCKET_1150, 'or', CategoriesConstants::MB_SOCKET_1155, 'or', CategoriesConstants::MB_SOCKET_1156, 'or', CategoriesConstants::MB_SOCKET_1366, 'or', CategoriesConstants::MB_SOCKET_2011, ')', 'and', '(', CategoriesConstants::MB_RAM_SLOT_COUNT_1, 'or', CategoriesConstants::MB_RAM_SLOT_COUNT_2, 'or', CategoriesConstants::MB_RAM_SLOT_COUNT_3, 'or', CategoriesConstants::MB_RAM_SLOT_COUNT_4, 'or', CategoriesConstants::MB_RAM_SLOT_COUNT_6, 'or', CategoriesConstants::MB_RAM_SLOT_COUNT_8, ')', 'and', '(', CategoriesConstants::MB_RAM_TYPE_DDR, 'or', CategoriesConstants::MB_RAM_TYPE_DDR2, 'or', CategoriesConstants::MB_RAM_TYPE_DDR3, ')', 'and', '(', CategoriesConstants::MB_GRAPHICS_ONBOARD, 'or', CategoriesConstants::MB_GRAPHICS_PCI_EXPRESS, 'or', CategoriesConstants::MB_GRAPHICS_AGP, ')', 'and', '(', CategoriesConstants::MB_SATA_SUPPORTED, 'or', CategoriesConstants::MB_IDE_SUPPORTED, ')', ')');
    }

    public function getSelectedSameItemsCount() {
        if ($_REQUEST['mb']) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getComponentTypeIndex() {
        return PcConfiguratorManager::$PCC_COMPONENTS['mb'];
    }

    public function getNeededCategoriesIdsAndOrFormulaArray() {
        $pccm = PcConfiguratorManager::getInstance($this->config, $this->args);
        if ($_REQUEST['case']) {
            $case = $this->secure($_REQUEST['case']);
            $case_size = $pccm->getCaseSize($case);
        }

        if ($_REQUEST['cpu']) {
            $cpu = $this->secure($_REQUEST['cpu']);
            $cpu_socket = $pccm->getCpuSocket($cpu);
        }

        if ($_REQUEST['rams']) {
            $rams = $this->secure($_REQUEST['rams']);
            $rams = explode(',', $rams);
            $ram_type = $pccm->getRamType($rams[0]);
            $total_rams_count = $pccm->getTotalRamsCount($rams);
        }

        if ($_REQUEST['cooler']) {
            $cooler = $this->secure($_REQUEST['cooler']);
            $cooler_sockets = $pccm->getCoolerSockets($cooler);
        }

        if ($_REQUEST['graphics']) {
            $graphics = $this->secure($_REQUEST['graphics']);
            $graphics_interface = $pccm->getGraphicsInterface($graphics);
        }

        if ($_REQUEST['hdds']) {
            $hdds = $this->secure($_REQUEST['hdds']);
            $hdds = explode(',', $hdds);
            $hdd_interface = $pccm->getHddsInterfaces($hdds);
        }

        if ($_REQUEST['opts']) {
            $opts = $this->secure($_REQUEST['opts']);
            $opts = explode(',', $opts);
            $opt_interface = $pccm->getOptsInterfaces($opts);
        }

        $ssc = $pccm->getSelectedStorageComponentCount();
        $totalSelectedSataStorageCount = $ssc[0];
        $totalSelectedAtaStorageCount = $ssc[1];

        $neededCategoriesIdsAndOrFormulaArray = array();

        //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< checking CASE compatibility >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        if ($case_size) {
            //if CASE is ATX
            if ($case_size == CategoriesConstants::CASE_SIZE_ATX) {
                //MB can be AXT, MiniATX, MicroATX
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_FORM_FACTOR_ATX;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_FORM_FACTOR_MINI_ATX;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_FORM_FACTOR_MICRO_ATX;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_CASE_SIZE_COMPATIBLE_DB;
            } else

            //if CASE is miniATX
            if ($case_size == CategoriesConstants::CASE_SIZE_MINI_ATX) {
                //MB can be MiniATX, MicroATX
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_FORM_FACTOR_MINI_ATX;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_FORM_FACTOR_MICRO_ATX;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_CASE_SIZE_COMPATIBLE_DB;
            } else
            //if CASE is microATX
            if ($case_size == CategoriesConstants::CASE_SIZE_MICRO_ATX) {
                //MB can be only MicroATX
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_FORM_FACTOR_MICRO_ATX;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_CASE_SIZE_COMPATIBLE_DB;
            }
        }

        //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< checking CPU compatibility >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        if ($cpu_socket) {
            //if CPU soxket is 478
            if ($cpu_socket == CategoriesConstants::CPU_SOCKET_478) {
                //MB socket should be 478
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SOCKET_478;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_CPU_SOCKET_COMPATIBLE_DB;
            } else
            //if CPU soxket is 775
            if ($cpu_socket == CategoriesConstants::CPU_SOCKET_775) {
                //MB socket should be 775
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SOCKET_775;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_CPU_SOCKET_COMPATIBLE_DB;
            } else
            //if CPU soxket is 1150
            if ($cpu_socket == CategoriesConstants::CPU_SOCKET_1150) {
                //MB socket should be 1150
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SOCKET_1150;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_CPU_SOCKET_COMPATIBLE_DB;
            } else
            //if CPU soxket is 1155
            if ($cpu_socket == CategoriesConstants::CPU_SOCKET_1155) {
                //MB socket should be 1155
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SOCKET_1155;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_CPU_SOCKET_COMPATIBLE_DB;
            } else
            //if CPU soxket is 1156
            if ($cpu_socket == CategoriesConstants::CPU_SOCKET_1156) {
                //MB socket should be 1156
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SOCKET_1156;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_CPU_SOCKET_COMPATIBLE_DB;
            } else
            //if CPU soxket is 1366
            if ($cpu_socket == CategoriesConstants::CPU_SOCKET_1366) {
                //MB socket should be 1366
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SOCKET_1366;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_CPU_SOCKET_COMPATIBLE_DB;
            } else
            //if CPU soxket is 2011
            if ($cpu_socket == CategoriesConstants::CPU_SOCKET_2011) {
                //MB socket should be 2011
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SOCKET_2011;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_CPU_SOCKET_COMPATIBLE_DB;
            }
        }


        //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< checking RAM TYPE compatibility >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        if ($ram_type) {
            //if RAM TYPE is DDR
            if ($ram_type == CategoriesConstants::RAM_TYPE_DDR) {
                //MB RAM TYPE should be DDR
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_TYPE_DDR;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_RAM_TYPE_COMPATIBLE_DB;
            } else
            //if RAM TYPE is DDR2
            if ($ram_type == CategoriesConstants::RAM_TYPE_DDR2) {
                //MB RAM TYPE should be DDR2
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_TYPE_DDR2;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_RAM_TYPE_COMPATIBLE_DB;
            } else
            //if RAM TYPE is DDR3
            if ($ram_type == CategoriesConstants::RAM_TYPE_DDR3) {
                //MB RAM TYPE should be DDR3
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_TYPE_DDR3;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_RAM_TYPE_COMPATIBLE_DB;
            }
        }

        //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< checking RAM COUNT compatibility >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

        if ($total_rams_count) {
            //if RAM COUNT is 1
            if ($total_rams_count == 1) {
                //MB RAM SLOT COUNT can be 1 or more
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_1;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_2;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_3;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_4;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_6;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_8;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_RAM_COUNT_COMPATIBLE_DB;
            } else
            //if RAM COUNT is 2
            if ($total_rams_count == 2) {
                //MB RAM SLOT COUNT can be 2 or more
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_2;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_3;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_4;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_6;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_8;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_RAM_COUNT_COMPATIBLE_DB;
            } else
            //if RAM COUNT is 3
            if ($total_rams_count == 3) {
                //MB RAM SLOT COUNT can be 3 or more
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_3;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_4;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_6;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_8;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_RAM_COUNT_COMPATIBLE_DB;
            } else
            //if RAM COUNT is 4
            if ($total_rams_count == 4) {
                //MB RAM SLOT COUNT can be 4 or more
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_4;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_6;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_8;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_RAM_COUNT_COMPATIBLE_DB;
            } else
            //if RAM COUNT is 6
            if ($total_rams_count == 5 || $total_rams_count == 6) {
                //MB RAM SLOT COUNT can be 6 or more
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_6;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_8;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_RAM_COUNT_COMPATIBLE_DB;
            } else
            //if RAM COUNT is 8
            if ($total_rams_count == 7 || $total_rams_count == 8) {
                //MB RAM SLOT COUNT can be 8
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_RAM_SLOT_COUNT_8;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_RAM_COUNT_COMPATIBLE_DB;
            } else {
                assert(false);
                // system max mb ram slot count support is 8
            }
        }

        //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< checking Sata port count compatibility compatibility >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        if ($totalSelectedAtaStorageCount > 0) {
            //if Stat Storage COUNT is 1
            if ($totalSelectedAtaStorageCount == 1) {
                //MB Sata port count should be 1 or more
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_ATA_PORT_COUNT_1;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_ATA_PORT_COUNT_2;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_ATA_STORAGE_COUNT_COMPATIBLE_DB;
            } else if ($totalSelectedAtaStorageCount == 2) {
                //MB Ata port count should be 2 or more
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_ATA_PORT_COUNT_2;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_ATA_STORAGE_COUNT_COMPATIBLE_DB;
            }
        }

        //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< checking Sata port count compatibility compatibility >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        if ($totalSelectedSataStorageCount > 0) {
            //if Stat Storage COUNT is 1
            if ($totalSelectedSataStorageCount == 1) {
                //MB Sata port count should be 1 or more
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_1;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_2;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_3;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_4;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_5;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_6;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_7;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_8;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_10;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_12;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_14;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_SATA_STORAGE_COUNT_COMPATIBLE_DB;
            } else if ($totalSelectedSataStorageCount == 2) {
                //MB Sata port count should be 2 or more
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_2;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_3;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_4;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_5;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_6;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_7;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_8;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_10;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_12;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_14;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_SATA_STORAGE_COUNT_COMPATIBLE_DB;
            } elseif ($totalSelectedSataStorageCount == 3) {
                //MB Sata port count should be 3 or more
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_3;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_4;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_5;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_6;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_7;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_8;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_10;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_12;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_14;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_SATA_STORAGE_COUNT_COMPATIBLE_DB;
            } elseif ($totalSelectedSataStorageCount == 4) {
                //MB Sata port count should be 4 or more
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_4;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_5;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_6;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_7;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_8;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_10;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_12;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_14;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_SATA_STORAGE_COUNT_COMPATIBLE_DB;
            } elseif ($totalSelectedSataStorageCount == 5) {
                //MB Sata port count should be 5 or more
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_5;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_6;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_7;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_8;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_10;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_12;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_14;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_SATA_STORAGE_COUNT_COMPATIBLE_DB;
            } elseif ($totalSelectedSataStorageCount == 6) {
                //MB Sata port count should be 6 or more
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_6;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_7;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_8;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_10;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_12;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_14;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_SATA_STORAGE_COUNT_COMPATIBLE_DB;
            } elseif ($totalSelectedSataStorageCount == 7) {
                //MB Sata port count should be 8 or more
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_7;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_8;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_10;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_12;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_14;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_SATA_STORAGE_COUNT_COMPATIBLE_DB;
            } elseif ($totalSelectedSataStorageCount == 8) {
                //MB Sata port count should be 18 or more
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_8;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_10;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_12;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_14;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_SATA_STORAGE_COUNT_COMPATIBLE_DB;
            } elseif ($totalSelectedSataStorageCount == 9 || $totalSelectedSataStorageCount == 10) {
                //MB Sata port count should be 10 or more
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_10;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_12;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_14;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_SATA_STORAGE_COUNT_COMPATIBLE_DB;
            } elseif ($totalSelectedSataStorageCount == 11 || $totalSelectedSataStorageCount == 12) {
                //MB Sata port count should be 12 or more or more
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_12;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_14;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_SATA_STORAGE_COUNT_COMPATIBLE_DB;
            } elseif ($totalSelectedSataStorageCount == 13 || $totalSelectedSataStorageCount == 14) {
                //MB Sata port count should be 14 or more
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_PORT_COUNT_14;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_SATA_STORAGE_COUNT_COMPATIBLE_DB;
            } else {
                assert(false);
                // system max mb sata port count support is 14
            }
        }

        //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< checking COOLER compatibility >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        if ($cooler_sockets) {

            $neededCategoriesIdsAndOrFormulaArray[] = '(';
            //if COOLER is 478
            if (in_array(CategoriesConstants::COOLER_SOCKET_478, $cooler_sockets)) {
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SOCKET_478;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
            }
            if (in_array(CategoriesConstants::COOLER_SOCKET_775, $cooler_sockets)) {
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SOCKET_775;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
            }
            if (in_array(CategoriesConstants::COOLER_SOCKET_1150, $cooler_sockets)) {
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SOCKET_1150;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
            }
            if (in_array(CategoriesConstants::COOLER_SOCKET_1155, $cooler_sockets)) {
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SOCKET_1155;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
            }
            if (in_array(CategoriesConstants::COOLER_SOCKET_1156, $cooler_sockets)) {
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SOCKET_1156;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
            }
            if (in_array(CategoriesConstants::COOLER_SOCKET_1366, $cooler_sockets)) {
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SOCKET_1366;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
            }
            if (in_array(CategoriesConstants::COOLER_SOCKET_2011, $cooler_sockets)) {
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SOCKET_2011;
                $neededCategoriesIdsAndOrFormulaArray[] = 'or';
            }
            array_pop($neededCategoriesIdsAndOrFormulaArray);
            $neededCategoriesIdsAndOrFormulaArray[] = ')';
            $neededCategoriesIdsAndOrFormulaArray[] = ':';
            $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_COOLER_SOCKET_COMPATIBLE_DB;
        }

        //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< checking VIDEO compatibility >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        if ($graphics_interface) {

            //if VIDEO INTERFACE is AGP
            if ($graphics_interface == CategoriesConstants::VIDEO_INTERFACE_AGP) {
                //MB Video Interface should be AGP
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_GRAPHICS_AGP;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_GRAPHICS_INTERFACE_COMPATIBLE_DB;
            } else
            //if VIDEO INTERFACE is PCI-PEXPRESS
            if ($graphics_interface == CategoriesConstants::VIDEO_INTERFACE_PCI_EXPRESS) {
                //MB Video Interface should be PCI-EXPRESS
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_GRAPHICS_PCI_EXPRESS;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_GRAPHICS_INTERFACE_COMPATIBLE_DB;
            }
        }

        //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< checking HDD compatibility >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        if ($hdd_interface) {
            if (in_array(CategoriesConstants::HDD_ATA, $hdd_interface)) {
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_IDE_SUPPORTED;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_ATA_STORAGE_INTERFACE_COMPATIBLE_DB;
            }
            if (in_array(CategoriesConstants::HDD_SATA, $hdd_interface)) {
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_SUPPORTED;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_SATA_STORAGE_INTERFACE_COMPATIBLE_DB;
            }
        }

        //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< checking SSD compatibility >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

        if (isset($_REQUEST['ssds']) && !empty($_REQUEST['ssds'])) {
            $neededCategoriesIdsAndOrFormulaArray[] = '(';
            $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_SUPPORTED;
            $neededCategoriesIdsAndOrFormulaArray[] = ')';
            $neededCategoriesIdsAndOrFormulaArray[] = ':';
            $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_SATA_STORAGE_INTERFACE_COMPATIBLE_DB;
        }

        //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< checking HDD compatibility >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        if ($opt_interface) {
            if (in_array(CategoriesConstants::OPTICAL_DRIVE_IDE, $opt_interface)) {
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_IDE_SUPPORTED;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_ATA_STORAGE_INTERFACE_COMPATIBLE_DB;
            }
            if (in_array(CategoriesConstants::OPTICAL_DRIVE_SATA, $opt_interface)) {
                $neededCategoriesIdsAndOrFormulaArray[] = '(';
                $neededCategoriesIdsAndOrFormulaArray[] = CategoriesConstants::MB_SATA_SUPPORTED;
                $neededCategoriesIdsAndOrFormulaArray[] = ')';
                $neededCategoriesIdsAndOrFormulaArray[] = ':';
                $neededCategoriesIdsAndOrFormulaArray[] = PcConfiguratorManager::PCC_SATA_STORAGE_INTERFACE_COMPATIBLE_DB;
            }
        }
        return $neededCategoriesIdsAndOrFormulaArray;
    }

    public function getSelectedComponentItemId() {
        $mb = null;
        if ($_REQUEST['mb']) {
            $mb = $this->secure($_REQUEST['mb']);
            $this->addParam('selected_component_id', $mb);
        }

        return $mb;
    }

    public function getTabHeaderInfoText() {
        return $this->getPhraseSpan(233);
    }

}

?>