<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/pcc_managers/PcComponentValidatorManager.class.php");
require_once (CLASSES_PATH . "/util/pcc_categories_constants/CategoriesConstants.php");
require_once (CLASSES_PATH . "/managers/SpecialFeesManager.class.php");
require_once (CLASSES_PATH . "/managers/BundleItemsManager.class.php");

/**
 * PcConfiguratorManager class is responsible for creating,
 */
class PcConfiguratorManager extends AbstractManager {

    public static $PCC_COMPONENTS = array('case' => 1, 'cpu' => 2, 'mb' => 3, 'cooler' => 4, 'ram' => 5, 'hdd' => 6, 'ssd' => 7, 'opt' => 8, 'monitor' => 9, 'graphics' => 10, 'power' => 11, 'keyboard' => 12, 'mouse' => 13, 'speaker' => 14);
    public static $PCC_COMPONENTS_DISPLAY_NAMES_IDS = array(198, 200, 202, 204, 206, 208, 208, 210, 212, 214, 216, 218, 220, 222);
    public static $maximum_system_mb_ram_count_supported = 8;
    public static $maximum_system_mb_sata_count_supported = 14;
    public static $maximum_system_mb_graphics_count_supported = 1;
    public static $maximum_system_mb_ata_count_supported = 2;

    const PCC_CASE_SIZE_COMPATIBLE_DB = "case_size_is_compatible";
    const PCC_CASE_SIZE_COMPATIBLE_FN = "caseSizeIsCompatible";
    const PCC_RAM_TYPE_COMPATIBLE_DB = "ram_type_is_compatible";
    const PCC_RAM_TYPE_COMPATIBLE_FN = "ramTypeIsCompatible";
    const PCC_RAM_COUNT_COMPATIBLE_DB = "ram_count_is_compatible";
    const PCC_RAM_COUNT_COMPATIBLE_FN = "ramCountIsCompatible";
    const PCC_CPU_SOCKET_COMPATIBLE_DB = "cpu_socket_is_compatible";
    const PCC_CPU_SOCKET_COMPATIBLE_FN = "cpuSocketIsCompatible";
    const PCC_MB_SOCKET_COMPATIBLE_DB = "mb_socket_is_compatible";
    const PCC_MB_SOCKET_COMPATIBLE_FN = "mbSocketIsCompatible";
    const PCC_GRAPHICS_INTERFACE_COMPATIBLE_DB = "graphics_interface_is_compatible";
    const PCC_GRAPHICS_INTERFACE_COMPATIBLE_FN = "graphicsInterfaceIsCompatible";
    const PCC_STORAGE_INTERFACE_COMPATIBLE_DB = "storage_interface_is_compatible";
    const PCC_STORAGE_INTERFACE_COMPATIBLE_FN = "storageInterfaceIsCompatible";
    const PCC_SATA_STORAGE_INTERFACE_COMPATIBLE_DB = "sata_storage_interface_is_compatible";
    const PCC_SATA_STORAGE_INTERFACE_COMPATIBLE_FN = "sataStorageInterfaceIsCompatible";
    const PCC_ATA_STORAGE_INTERFACE_COMPATIBLE_DB = "ata_storage_interface_is_compatible";
    const PCC_ATA_STORAGE_INTERFACE_COMPATIBLE_FN = "ataStorageInterfaceIsCompatible";
    const PCC_SATA_STORAGE_COUNT_COMPATIBLE_DB = "sata_storage_count_is_compatible";
    const PCC_SATA_STORAGE_COUNT_COMPATIBLE_FN = "sataStorageCountIsCompatible";
    const PCC_ATA_STORAGE_COUNT_COMPATIBLE_DB = "ata_storage_count_is_compatible";
    const PCC_ATA_STORAGE_COUNT_COMPATIBLE_FN = "ataStorageCountIsCompatible";
    const PCC_COOLER_SOCKET_COMPATIBLE_DB = "cooler_socket_is_compatible";
    const PCC_COOLER_SOCKET_COMPATIBLE_FN = "coolerSocketIsCompatible";

    public static $PCC_ALL_NOT_COMPATIBILITY_REASONS_DB_FN = array(self::PCC_CASE_SIZE_COMPATIBLE_DB => self::PCC_CASE_SIZE_COMPATIBLE_FN, self::PCC_RAM_TYPE_COMPATIBLE_DB => self::PCC_RAM_TYPE_COMPATIBLE_FN, self::PCC_RAM_COUNT_COMPATIBLE_DB => self::PCC_RAM_COUNT_COMPATIBLE_FN, self::PCC_CPU_SOCKET_COMPATIBLE_DB => self::PCC_CPU_SOCKET_COMPATIBLE_FN, self::PCC_MB_SOCKET_COMPATIBLE_DB => self::PCC_MB_SOCKET_COMPATIBLE_FN, self::PCC_GRAPHICS_INTERFACE_COMPATIBLE_DB => self::PCC_GRAPHICS_INTERFACE_COMPATIBLE_FN, self::PCC_STORAGE_INTERFACE_COMPATIBLE_DB => self::PCC_STORAGE_INTERFACE_COMPATIBLE_FN, self::PCC_SATA_STORAGE_INTERFACE_COMPATIBLE_DB => self::PCC_SATA_STORAGE_INTERFACE_COMPATIBLE_FN, self::PCC_ATA_STORAGE_INTERFACE_COMPATIBLE_DB => self::PCC_ATA_STORAGE_INTERFACE_COMPATIBLE_FN, self::PCC_SATA_STORAGE_COUNT_COMPATIBLE_DB => self::PCC_SATA_STORAGE_COUNT_COMPATIBLE_FN, self::PCC_ATA_STORAGE_COUNT_COMPATIBLE_DB => self::PCC_ATA_STORAGE_COUNT_COMPATIBLE_FN, self::PCC_COOLER_SOCKET_COMPATIBLE_DB => self::PCC_COOLER_SOCKET_COMPATIBLE_FN);

    /**
     * @var app config
     */
    private $config;

    /**
     * @var passed arguemnts
     */
    private $args;

    /**
     * @var singleton instnce of class
     */
    private static $instance = null;

    /**
     * Initializes DB mappers
     *
     * @param object $config
     * @param object $args
     * @return
     */
    function __construct($config, $args) {
        $this->config = $config;
        $this->args = $args;
        $this->itemManager = ItemManager::getInstance($this->config, $this->args);
        $this->pcComponentValidatorManager = PcComponentValidatorManager::getInstance($this->config, $this->args);
    }

    /**
     * Returns an singleton instance of this class
     *
     * @param object $config
     * @param object $args
     * @return
     */
    public static function getInstance($config, $args) {

        if (self::$instance == null) {

            self::$instance = new PcConfiguratorManager($config, $args);
        }
        return self::$instance;
    }

    /**
     * Returns given case size if given item is case and has size set. otherwise returns null.
     */
    public function getCaseSize($case) {
        if ($case instanceof ItemDto) {
            $itemDto = $case;
        } else {
            $itemDto = $this->itemManager->selectByPK($case);
        }
        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();
            assert(strpos($catIdsStr, ',' . CategoriesConstants::CASE_CHASSIS . ',') !== false);
            if (strpos($catIdsStr, ',' . CategoriesConstants::CASE_SIZE_ATX . ',') !== false) {
                return CategoriesConstants::CASE_SIZE_ATX;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::CASE_SIZE_MINI_ATX . ',') !== false) {
                return CategoriesConstants::CASE_SIZE_MINI_ATX;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::CASE_SIZE_MICRO_ATX . ',') !== false) {
                return CategoriesConstants::CASE_SIZE_MICRO_ATX;
            }
        }
        return null;
    }

    /**
     * Returns given mb socket if given item is mb and has socket set. otherwise returns null.
     */
    public function getMbSocket($mb) {
        if ($mb instanceof ItemDto) {
            $itemDto = $mb;
        } else {
            $itemDto = $this->itemManager->selectByPK($mb);
        }
        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();
            assert(strpos($catIdsStr, ',' . CategoriesConstants::MOTHER_BOARD . ',') !== false);
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_SOCKET_478 . ',') !== false) {
                return CategoriesConstants::MB_SOCKET_478;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_SOCKET_775 . ',') !== false) {
                return CategoriesConstants::MB_SOCKET_775;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_SOCKET_1150 . ',') !== false) {
                return CategoriesConstants::MB_SOCKET_1150;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_SOCKET_1155 . ',') !== false) {
                return CategoriesConstants::MB_SOCKET_1155;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_SOCKET_1156 . ',') !== false) {
                return CategoriesConstants::MB_SOCKET_1156;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_SOCKET_1366 . ',') !== false) {
                return CategoriesConstants::MB_SOCKET_1366;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_SOCKET_2011 . ',') !== false) {
                return CategoriesConstants::MB_SOCKET_2011;
            }
        }
        return null;
    }

    /**
     * Returns given mb form factor if given item is mb and has form factor set. otherwise returns null.
     */
    public function getMbFormFactor($mb) {
        if ($mb instanceof ItemDto) {
            $itemDto = $mb;
        } else {
            $itemDto = $this->itemManager->selectByPK($mb);
        }
        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();
            assert(strpos($catIdsStr, ',' . CategoriesConstants::MOTHER_BOARD . ',') !== false);
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_FORM_FACTOR_ATX . ',') !== false) {
                return CategoriesConstants::MB_FORM_FACTOR_ATX;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_FORM_FACTOR_MINI_ATX . ',') !== false) {
                return CategoriesConstants::MB_FORM_FACTOR_MINI_ATX;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_FORM_FACTOR_MICRO_ATX . ',') !== false) {
                return CategoriesConstants::MB_FORM_FACTOR_MICRO_ATX;
            }
        }
        return null;
    }

    /**
     * Returns given mb ram slot count if given item is mb and has ram slot count set. otherwise returns null.
     */
    public function getMbRamSlotCount($mb) {
        if ($mb instanceof ItemDto) {
            $itemDto = $mb;
        } else {
            $itemDto = $this->itemManager->selectByPK($mb);
        }
        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();
            assert(strpos($catIdsStr, ',' . CategoriesConstants::MOTHER_BOARD . ',') !== false);
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_RAM_SLOT_COUNT_1 . ',') !== false) {
                return CategoriesConstants::MB_RAM_SLOT_COUNT_1;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_RAM_SLOT_COUNT_2 . ',') !== false) {
                return CategoriesConstants::MB_RAM_SLOT_COUNT_2;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_RAM_SLOT_COUNT_3 . ',') !== false) {
                return CategoriesConstants::MB_RAM_SLOT_COUNT_3;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_RAM_SLOT_COUNT_4 . ',') !== false) {
                return CategoriesConstants::MB_RAM_SLOT_COUNT_4;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_RAM_SLOT_COUNT_6 . ',') !== false) {
                return CategoriesConstants::MB_RAM_SLOT_COUNT_6;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_RAM_SLOT_COUNT_8 . ',') !== false) {
                return CategoriesConstants::MB_RAM_SLOT_COUNT_8;
            }
            assert(false);
        }
        return null;
    }

    public function getMbGraphicsSlotCount($mb) {
        if ($mb instanceof ItemDto) {
            $itemDto = $mb;
        } else {
            $itemDto = $this->itemManager->selectByPK($mb);
        }
        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();
            assert(strpos($catIdsStr, ',' . CategoriesConstants::MOTHER_BOARD . ',') !== false);
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_GRAPHICS_SLOT_COUNT_2 . ',') !== false) {
                return CategoriesConstants::MB_GRAPHICS_SLOT_COUNT_2;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_GRAPHICS_SLOT_COUNT_3 . ',') !== false) {
                return CategoriesConstants::MB_GRAPHICS_SLOT_COUNT_3;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_GRAPHICS_SLOT_COUNT_4 . ',') !== false) {
                return CategoriesConstants::MB_GRAPHICS_SLOT_COUNT_4;
            }
        }
        return CategoriesConstants::MB_GRAPHICS_SLOT_COUNT_1;
    }

    /**
     * Returns given mb SATA ports count if given item is mb and has SATA ports count set. otherwise returns null.
     */
    public function getMbSataSlotCount($mb) {
        if ($mb instanceof ItemDto) {
            $itemDto = $mb;
        } else {
            $itemDto = $this->itemManager->selectByPK($mb);
        }
        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();
            assert(strpos($catIdsStr, ',' . CategoriesConstants::MOTHER_BOARD . ',') !== false);
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_SATA_PORT_COUNT_1 . ',') !== false) {
                return CategoriesConstants::MB_SATA_PORT_COUNT_1;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_SATA_PORT_COUNT_2 . ',') !== false) {
                return CategoriesConstants::MB_SATA_PORT_COUNT_2;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_SATA_PORT_COUNT_3 . ',') !== false) {
                return CategoriesConstants::MB_SATA_PORT_COUNT_3;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_SATA_PORT_COUNT_4 . ',') !== false) {
                return CategoriesConstants::MB_SATA_PORT_COUNT_4;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_SATA_PORT_COUNT_5 . ',') !== false) {
                return CategoriesConstants::MB_SATA_PORT_COUNT_5;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_SATA_PORT_COUNT_6 . ',') !== false) {
                return CategoriesConstants::MB_SATA_PORT_COUNT_6;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_SATA_PORT_COUNT_7 . ',') !== false) {
                return CategoriesConstants::MB_SATA_PORT_COUNT_7;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_SATA_PORT_COUNT_8 . ',') !== false) {
                return CategoriesConstants::MB_SATA_PORT_COUNT_8;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_SATA_PORT_COUNT_10 . ',') !== false) {
                return CategoriesConstants::MB_SATA_PORT_COUNT_10;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_SATA_PORT_COUNT_12 . ',') !== false) {
                return CategoriesConstants::MB_SATA_PORT_COUNT_12;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_SATA_PORT_COUNT_14 . ',') !== false) {
                return CategoriesConstants::MB_SATA_PORT_COUNT_14;
            }
        }
        return null;
    }

    /**
     * Returns given mb ATA ports count if given item is mb and has ATA ports count set. otherwise returns null.
     */
    public function getMbAtaSlotCount($mb) {
        if ($mb instanceof ItemDto) {
            $itemDto = $mb;
        } else {
            $itemDto = $this->itemManager->selectByPK($mb);
        }
        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();
            assert(strpos($catIdsStr, ',' . CategoriesConstants::MOTHER_BOARD . ',') !== false);
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_ATA_PORT_COUNT_1 . ',') !== false) {
                return CategoriesConstants::MB_ATA_PORT_COUNT_1;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_ATA_PORT_COUNT_2 . ',') !== false) {
                return CategoriesConstants::MB_ATA_PORT_COUNT_2;
            }
        }
        return null;
    }

    /**
     * Returns given mb video interface if given item is mb and has video interface set. otherwise returns null.
     */
    public function getMbGraphicsInterface($mb) {
        if ($mb instanceof ItemDto) {
            $itemDto = $mb;
        } else {
            $itemDto = $this->itemManager->selectByPK($mb);
        }
        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();
            assert(strpos($catIdsStr, ',' . CategoriesConstants::MOTHER_BOARD . ',') !== false);
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_GRAPHICS_AGP . ',') !== false) {
                return CategoriesConstants::MB_GRAPHICS_AGP;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_GRAPHICS_PCI_EXPRESS . ',') !== false) {
                return CategoriesConstants::MB_GRAPHICS_PCI_EXPRESS;
            }
        }
        return null;
    }

    /**
     * Returns true if given mb has onboard video, otherwise returns false.
     */
    public function hasMbOnBoardGraphics($mb) {
        if ($mb instanceof ItemDto) {
            $itemDto = $mb;
        } else {
            $itemDto = $this->itemManager->selectByPK($mb);
        }
        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();
            assert(strpos($catIdsStr, ',' . CategoriesConstants::MOTHER_BOARD . ',') !== false);
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_GRAPHICS_ONBOARD . ',') !== false) {
                return true;
            } else {
                return false;
            }
        }
        return null;
    }

    /**
     * Returns true if given case has power supply, otherwise returns false.
     */
    public function hasCasePowerSupply($case) {
        if ($case instanceof ItemDto) {
            $itemDto = $case;
        } else {
            $itemDto = $this->itemManager->selectByPK($case);
        }
        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();
            assert(strpos($catIdsStr, ',' . CategoriesConstants::CASE_CHASSIS . ',') !== false);
            return strpos($catIdsStr, ',' . CategoriesConstants::CASE_POWER_SUPPLY . ',') !== false && strpos($catIdsStr, ',' . CategoriesConstants::CASE_NO_POWER_SUPPLY . ',') === false;
        }
        return null;
    }

    /**
     * Returns given mb ram type if given item is mb and has ram type set. otherwise returns null.
     */
    public function getMbRamType($mb) {
        if ($mb instanceof ItemDto) {
            $itemDto = $mb;
        } else {
            $itemDto = $this->itemManager->selectByPK($mb);
        }

        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();
            assert(strpos($catIdsStr, ',' . CategoriesConstants::MOTHER_BOARD . ',') !== false);
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_RAM_TYPE_DDR . ',') !== false) {
                return CategoriesConstants::MB_RAM_TYPE_DDR;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_RAM_TYPE_DDR2 . ',') !== false) {
                return CategoriesConstants::MB_RAM_TYPE_DDR2;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_RAM_TYPE_DDR3 . ',') !== false) {
                return CategoriesConstants::MB_RAM_TYPE_DDR3;
            }
        }
        return null;
    }

    /**
     * Returns given mb sata/ide support if given item is mb and has sata/ide slots. otherwise returns null.
     */
    public function getMbSataIdeSupport($mb) {
        if ($mb instanceof ItemDto) {
            $itemDto = $mb;
        } else {
            $itemDto = $this->itemManager->selectByPK($mb);
        }
        $ret = array();
        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();
            assert(strpos($catIdsStr, ',' . CategoriesConstants::MOTHER_BOARD . ',') !== false);
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_IDE_SUPPORTED . ',') !== false) {
                $ret[] = CategoriesConstants::MB_IDE_SUPPORTED;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::MB_SATA_SUPPORTED . ',') !== false) {
                $ret[] = CategoriesConstants::MB_SATA_SUPPORTED;
            }
            return $ret;
        }
        return null;
    }

    public function getTotalRamsCount($rams_ids) {
        $ret = 0;

        foreach ($rams_ids as $key => $ram_id) {
            $ret += (int) $this->getRamKitCountRepresentedInInteger($ram_id);
        }
        RETURN $ret;
    }

    /**
     * Returns given RAM kit count if given item is ram and has kit count. otherwise returns null.
     */
    public function getRamKitCount($ram) {
        if ($ram instanceof ItemDto) {
            $itemDto = $ram;
        } else {
            $itemDto = $this->itemManager->selectByPK($ram);
        }
        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();
            assert(strpos($catIdsStr, ',' . CategoriesConstants::RAM_MEMORY . ',') !== false);
            if (strpos($catIdsStr, ',' . CategoriesConstants::RAM_KIT_COUNT_1 . ',') !== false) {
                return CategoriesConstants::RAM_KIT_COUNT_1;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::RAM_KIT_COUNT_2 . ',') !== false) {
                return CategoriesConstants::RAM_KIT_COUNT_2;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::RAM_KIT_COUNT_3 . ',') !== false) {
                return CategoriesConstants::RAM_KIT_COUNT_3;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::RAM_KIT_COUNT_4 . ',') !== false) {
                return CategoriesConstants::RAM_KIT_COUNT_4;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::RAM_KIT_COUNT_6 . ',') !== false) {
                return CategoriesConstants::RAM_KIT_COUNT_6;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::RAM_KIT_COUNT_8 . ',') !== false) {
                return CategoriesConstants::RAM_KIT_COUNT_8;
            }
        }
        return null;
    }

    /**
     * Returns given ram type if given item is ram and has type. otherwise returns null.
     */
    public function getRamType($ram) {
        if ($ram instanceof ItemDto) {
            $itemDto = $ram;
        } else {
            $itemDto = $this->itemManager->selectByPK($ram);
        }
        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();
            assert(strpos($catIdsStr, ',' . CategoriesConstants::RAM_MEMORY . ',') !== false);
            if (strpos($catIdsStr, ',' . CategoriesConstants::RAM_TYPE_DDR . ',') !== false) {
                return CategoriesConstants::RAM_TYPE_DDR;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::RAM_TYPE_DDR2 . ',') !== false) {
                return CategoriesConstants::RAM_TYPE_DDR2;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::RAM_TYPE_DDR3 . ',') !== false) {
                return CategoriesConstants::RAM_TYPE_DDR3;
            }
        }
        return null;
    }

    /**
     * Returns given cpu socket if given item is cpu and has socket set. otherwise returns null.
     */
    public function getCpuSocket($cpu) {
        if ($cpu instanceof ItemDto) {
            $itemDto = $cpu;
        } else {
            $itemDto = $this->itemManager->selectByPK($cpu);
        }
        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();
            assert(strpos($catIdsStr, ',' . CategoriesConstants::CPU_PROCESSOR . ',') !== false);
            if (strpos($catIdsStr, ',' . CategoriesConstants::CPU_SOCKET_478 . ',') !== false) {
                return CategoriesConstants::CPU_SOCKET_478;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::CPU_SOCKET_775 . ',') !== false) {
                return CategoriesConstants::CPU_SOCKET_775;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::CPU_SOCKET_1150 . ',') !== false) {
                return CategoriesConstants::CPU_SOCKET_1150;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::CPU_SOCKET_1155 . ',') !== false) {
                return CategoriesConstants::CPU_SOCKET_1155;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::CPU_SOCKET_1156 . ',') !== false) {
                return CategoriesConstants::CPU_SOCKET_1156;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::CPU_SOCKET_1366 . ',') !== false) {
                return CategoriesConstants::CPU_SOCKET_1366;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::CPU_SOCKET_2011 . ',') !== false) {
                return CategoriesConstants::CPU_SOCKET_2011;
            }
        }
        return null;
    }

    /**
     * Returns given cooler socket if given item is cooler and has socket set. otherwise returns null.
     */
    public function getCoolerSockets($cooler) {
        if ($cooler instanceof ItemDto) {
            $itemDto = $cooler;
        } else {
            $itemDto = $this->itemManager->selectByPK($cooler);
        }
        $ret = array();
        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();

            assert(strpos($catIdsStr, ',' . CategoriesConstants::COOLER . ',') !== false);
            if (strpos($catIdsStr, ',' . CategoriesConstants::COOLER_SOCKET_478 . ',') !== false) {
                $ret[] = CategoriesConstants::COOLER_SOCKET_478;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::COOLER_SOCKET_775 . ',') !== false) {
                $ret[] = CategoriesConstants::COOLER_SOCKET_775;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::COOLER_SOCKET_1150 . ',') !== false) {
                $ret[] = CategoriesConstants::COOLER_SOCKET_1150;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::COOLER_SOCKET_1155 . ',') !== false) {
                $ret[] = CategoriesConstants::COOLER_SOCKET_1155;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::COOLER_SOCKET_1156 . ',') !== false) {
                $ret[] = CategoriesConstants::COOLER_SOCKET_1156;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::COOLER_SOCKET_1366 . ',') !== false) {
                $ret[] = CategoriesConstants::COOLER_SOCKET_1366;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::COOLER_SOCKET_2011 . ',') !== false) {
                $ret[] = CategoriesConstants::COOLER_SOCKET_2011;
            }
            return $ret;
        }
        return null;
    }

    /**
     * Returns given video card interface if given item is video card and has interface set. otherwise returns null.
     */
    public function getGraphicsInterface($graphics) {
        if ($graphics instanceof ItemDto) {
            $itemDto = $graphics;
        } else {
            $itemDto = $this->itemManager->selectByPK($graphics);
        }
        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();
            assert(strpos($catIdsStr, ',' . CategoriesConstants::VIDEO_CARD . ',') !== false);
            if (strpos($catIdsStr, ',' . CategoriesConstants::VIDEO_INTERFACE_AGP . ',') !== false) {
                return CategoriesConstants::VIDEO_INTERFACE_AGP;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::VIDEO_INTERFACE_PCI_EXPRESS . ',') !== false) {
                return CategoriesConstants::VIDEO_INTERFACE_PCI_EXPRESS;
            }
        }
        return null;
    }

    public function isCaseCompatibleWithMb($case, $mb) {
        $caseSize = $this->getCaseSize($case);
        switch ($caseSize) {
            case CategoriesConstants::CASE_SIZE_ATX :
                $caseSizeRepresentedByInteger = 9;
                break;
            case CategoriesConstants::CASE_SIZE_MINI_ATX :
                $caseSizeRepresentedByInteger = 6;
                break;
            case CategoriesConstants::CASE_SIZE_MICRO_ATX :
                $caseSizeRepresentedByInteger = 3;
                break;
            default :
                assert(false);
            //case size should be one of values above
        }

        $mbFormFactor = $this->getMbFormFactor($mb);
        switch ($mbFormFactor) {
            case CategoriesConstants::MB_FORM_FACTOR_ATX :
                $mbFormFactorRepresentedByInteger = 9;
                break;
            case CategoriesConstants::MB_FORM_FACTOR_MINI_ATX :
                $mbFormFactorRepresentedByInteger = 6;
                break;
            case CategoriesConstants::MB_FORM_FACTOR_MICRO_ATX :
                $mbFormFactorRepresentedByInteger = 3;
                break;
            default :
                assert(false);
            //motherboard form factor should be one of values above
        }
        return $mbFormFactorRepresentedByInteger <= $caseSizeRepresentedByInteger;
    }

    public function getMbCorrespondingCaseSizes($mb) {
        $mbFormFactor = $this->getMbFormFactor($mb);
        switch ($mbFormFactor) {
            case CategoriesConstants::MB_FORM_FACTOR_MICRO_ATX :
                return array(CategoriesConstants::CASE_SIZE_MICRO_ATX, 'OR', CategoriesConstants::CASE_SIZE_MINI_ATX, 'OR', CategoriesConstants::CASE_SIZE_ATX);
            case CategoriesConstants::MB_FORM_FACTOR_MINI_ATX :
                return array(CategoriesConstants::CASE_SIZE_MINI_ATX, 'OR', CategoriesConstants::CASE_SIZE_ATX);
            case CategoriesConstants::MB_FORM_FACTOR_ATX :
                return array(CategoriesConstants::CASE_SIZE_ATX);
        }
        assert(false);
    }

    public function getMbCorrespondingRamType($mb) {
        $mbRamType = $this->getMbRamType($mb);
        switch ($mbRamType) {
            case CategoriesConstants::MB_RAM_TYPE_DDR :
                return CategoriesConstants::RAM_TYPE_DDR;
            case CategoriesConstants::MB_RAM_TYPE_DDR2 :
                return CategoriesConstants::RAM_TYPE_DDR2;
            case CategoriesConstants::MB_RAM_TYPE_DDR3 :
                return CategoriesConstants::RAM_TYPE_DDR3;
        }
        assert(false);
    }

    public function getMbCorrespondingCpuSocket($mb) {
        $mbSocket = $this->getMbSocket($mb);
        switch ($mbSocket) {
            case CategoriesConstants::MB_SOCKET_478 :
                return CategoriesConstants::CPU_SOCKET_478;
            case CategoriesConstants::MB_SOCKET_775 :
                return CategoriesConstants::CPU_SOCKET_775;
            case CategoriesConstants::MB_SOCKET_1150 :
                return CategoriesConstants::CPU_SOCKET_1150;
            case CategoriesConstants::MB_SOCKET_1155 :
                return CategoriesConstants::CPU_SOCKET_1155;
            case CategoriesConstants::MB_SOCKET_1156 :
                return CategoriesConstants::CPU_SOCKET_1156;
            case CategoriesConstants::MB_SOCKET_1366 :
                return CategoriesConstants::CPU_SOCKET_1366;
            case CategoriesConstants::MB_SOCKET_2011 :
                return CategoriesConstants::CPU_SOCKET_2011;
        }
        assert(false);
    }

    public function getMbCorrespondingCoolerSocket($mb) {
        $mbSocket = $this->getMbSocket($mb);
        switch ($mbSocket) {
            case CategoriesConstants::MB_SOCKET_478 :
                return CategoriesConstants::COOLER_SOCKET_478;
            case CategoriesConstants::MB_SOCKET_775 :
                return CategoriesConstants::COOLER_SOCKET_775;
            case CategoriesConstants::MB_SOCKET_1150 :
                return CategoriesConstants::COOLER_SOCKET_1150;
            case CategoriesConstants::MB_SOCKET_1155 :
                return CategoriesConstants::COOLER_SOCKET_1155;
            case CategoriesConstants::MB_SOCKET_1156 :
                return CategoriesConstants::COOLER_SOCKET_1156;
            case CategoriesConstants::MB_SOCKET_1366 :
                return CategoriesConstants::COOLER_SOCKET_1366;
            case CategoriesConstants::MB_SOCKET_2011 :
                return CategoriesConstants::COOLER_SOCKET_2011;
        }
        assert(false);
    }

    public function isCoolerCompatibleWithMb($cooler, $mb) {
        $coolerSockets = $this->getCoolerSockets($cooler);
        $mbSocket = $this->getMbSocket($mb);
        switch ($mbSocket) {
            case CategoriesConstants::MB_SOCKET_478 :
                return in_array(CategoriesConstants::COOLER_SOCKET_478, $coolerSockets);
            case CategoriesConstants::MB_SOCKET_775 :
                return in_array(CategoriesConstants::COOLER_SOCKET_775, $coolerSockets);
            case CategoriesConstants::MB_SOCKET_1150 :
                return in_array(CategoriesConstants::COOLER_SOCKET_1150, $coolerSockets);
            case CategoriesConstants::MB_SOCKET_1155 :
                return in_array(CategoriesConstants::COOLER_SOCKET_1155, $coolerSockets);
            case CategoriesConstants::MB_SOCKET_1156 :
                return in_array(CategoriesConstants::COOLER_SOCKET_1156, $coolerSockets);
            case CategoriesConstants::MB_SOCKET_1366 :
                return in_array(CategoriesConstants::COOLER_SOCKET_1366, $coolerSockets);
            case CategoriesConstants::MB_SOCKET_2011 :
                return in_array(CategoriesConstants::COOLER_SOCKET_2011, $coolerSockets);
        }
        assert(false);
        //motherboard socket should be one of above values.
    }

    public function isCpuCompatibleWithMb($cpu, $mb) {
        $cpuSocket = $this->getCpuSocket($cpu);
        $mbSocket = $this->getMbSocket($mb);
        switch ($mbSocket) {
            case CategoriesConstants::MB_SOCKET_478 :
                return $cpuSocket == CategoriesConstants::CPU_SOCKET_478;
            case CategoriesConstants::MB_SOCKET_775 :
                return $cpuSocket == CategoriesConstants::CPU_SOCKET_775;
            case CategoriesConstants::MB_SOCKET_1150 :
                return $cpuSocket == CategoriesConstants::CPU_SOCKET_1150;
            case CategoriesConstants::MB_SOCKET_1155 :
                return $cpuSocket == CategoriesConstants::CPU_SOCKET_1155;
            case CategoriesConstants::MB_SOCKET_1156 :
                return $cpuSocket == CategoriesConstants::CPU_SOCKET_1156;
            case CategoriesConstants::MB_SOCKET_1366 :
                return $cpuSocket == CategoriesConstants::CPU_SOCKET_1366;
            case CategoriesConstants::MB_SOCKET_2011 :
                return $cpuSocket == CategoriesConstants::CPU_SOCKET_2011;
        }
        assert(false);
        //motherboard socket should be one of above values.
    }

    public function isCpuCompatibleWithCooler($cpu, $cooler) {
        $coolerSockets = $this->getCoolerSockets($cooler);
        $cpuSocket = $this->getCpuSocket($cpu);
        switch ($cpuSocket) {
            case CategoriesConstants::CPU_SOCKET_478 :
                return in_array(CategoriesConstants::COOLER_SOCKET_478, $coolerSockets);
            case CategoriesConstants::CPU_SOCKET_775 :
                return in_array(CategoriesConstants::COOLER_SOCKET_775, $coolerSockets);
            case CategoriesConstants::CPU_SOCKET_1150 :
                return in_array(CategoriesConstants::COOLER_SOCKET_1150, $coolerSockets);
            case CategoriesConstants::CPU_SOCKET_1155 :
                return in_array(CategoriesConstants::COOLER_SOCKET_1155, $coolerSockets);
            case CategoriesConstants::CPU_SOCKET_1156 :
                return in_array(CategoriesConstants::COOLER_SOCKET_1156, $coolerSockets);
            case CategoriesConstants::CPU_SOCKET_1366 :
                return in_array(CategoriesConstants::COOLER_SOCKET_1366, $coolerSockets);
            case CategoriesConstants::CPU_SOCKET_2011 :
                return in_array(CategoriesConstants::COOLER_SOCKET_2011, $coolerSockets);
        }
        assert(false);
        //CPU socket should be one of above values.
    }

    public function isCoolerCompatibleWithCpu($cooler, $cpu) {
        return $this->isCpuCompatibleWithCooler($cpu, $cooler);
    }

    public function isGraphicsCardCompatibleWithMb($graphics, $mb) {
        $graphicsInterface = $this->getGraphicsInterface($graphics);
        $mbGraphicsInterface = $this->getMbGraphicsInterface($mb);
        switch ($mbGraphicsInterface) {
            case CategoriesConstants::MB_GRAPHICS_AGP :
                return $graphicsInterface == CategoriesConstants::VIDEO_INTERFACE_AGP;
            case CategoriesConstants::MB_GRAPHICS_PCI_EXPRESS :
                return $graphicsInterface == CategoriesConstants::VIDEO_INTERFACE_PCI_EXPRESS;
        }
        assert(false);
        //motherboard socket should be one of above values.
    }

    /**
     * $hdds can be array and not array. If it's array then length of the array should be more that 1
     */
    public function isHddsInterfaceCompatibleWithMb($hdds, $mb) {
        $hddInterface = $this->getHddsInterfaces($hdds);
        $mbSataIdeSupport = $this->getMbSataIdeSupport($mb);
        $hddInterfacesRepresentedInIntegerArray = array();
        if (in_array(CategoriesConstants::HDD_SATA, $hddInterface)) {
            $hddInterfacesRepresentedInIntegerArray[] = 1;
        }
        if (in_array(CategoriesConstants::HDD_ATA, $hddInterface)) {
            $hddInterfacesRepresentedInIntegerArray[] = 2;
        }

        $mbInterfacesRepresentedInIntegerArray = array();
        if (in_array(CategoriesConstants::MB_SATA_SUPPORTED, $mbSataIdeSupport)) {
            $mbInterfacesRepresentedInIntegerArray[] = 1;
        }
        if (in_array(CategoriesConstants::MB_IDE_SUPPORTED, $mbSataIdeSupport)) {
            $mbInterfacesRepresentedInIntegerArray[] = 2;
        }
        foreach ($hddInterfacesRepresentedInIntegerArray as $key => $hddSupportedInteger) {
            if (!in_array($hddSupportedInteger, $mbInterfacesRepresentedInIntegerArray)) {
                return false;
            }
        }
        return true;
    }

    /**
     * $opts can be array and not array. If it's array then length of the array should be more that 1
     */
    public function isOptsInterfaceCompatibleWithMb($opts, $mb) {
        $optInterface = $this->getOptsInterfaces($opts);
        $mbSataIdeSupport = $this->getMbSataIdeSupport($mb);
        $optInterfacesRepresentedInIntegerArray = array();
        if (in_array(CategoriesConstants::OPTICAL_DRIVE_SATA, $optInterface)) {
            $optInterfacesRepresentedInIntegerArray[] = 1;
        }
        if (in_array(CategoriesConstants::OPTICAL_DRIVE_IDE, $optInterface)) {
            $optInterfacesRepresentedInIntegerArray[] = 2;
        }

        $mbInterfacesRepresentedInIntegerArray = array();
        if (in_array(CategoriesConstants::MB_SATA_SUPPORTED, $mbSataIdeSupport)) {
            $mbInterfacesRepresentedInIntegerArray[] = 1;
        }
        if (in_array(CategoriesConstants::MB_IDE_SUPPORTED, $mbSataIdeSupport)) {
            $mbInterfacesRepresentedInIntegerArray[] = 2;
        }
        foreach ($optInterfacesRepresentedInIntegerArray as $key => $optSupportedInteger) {
            if (!in_array($optSupportedInteger, $mbInterfacesRepresentedInIntegerArray)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns given hdd(s) interfaces if given item(s) are hdd(s) and has interface set. otherwise returns null.
     */
    public function getHddsInterfaces($hdds) {
        if (isset($hdds)) {
            if (is_array($hdds)) {
                $interfaces = array();
                foreach ($hdds as $key => $hdd) {
                    $intf = $this->getHddInterface($hdd);
                    if (!in_array($intf, $interfaces)) {
                        $interfaces[] = $intf;
                    }
                }
                return $interfaces;
            } else {
                return array($this->getHddInterface($hdds));
            }
        } else {
            return array();
        }
    }

    /**
     * Returns given opt(s) interfaces if given item(s) are opt(s) and has interface set. otherwise returns null.
     */
    public function getOptsInterfaces($itemId) {
        if (isset($itemId)) {
            if (is_array($itemId)) {
                $interfaces = array();
                foreach ($itemId as $key => $item_id) {
                    $intf = $this->getOptInterface($item_id);
                    if (!in_array($intf, $interfaces)) {
                        $interfaces[] = $intf;
                    }
                }
                return $interfaces;
            } else {
                return array($this->getOptInterface($itemId));
            }
        } else {
            return array();
        }
    }

    /**
     * Returns given hdd card interface if given item is hdd card and has interface set. otherwise returns null.
     */
    public function getHddInterface($hdd) {
        if ($hdd instanceof ItemDto) {
            $itemDto = $hdd;
        } else {
            $itemDto = $this->itemManager->selectByPK($hdd);
        }

        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();
            assert(strpos($catIdsStr, ',' . CategoriesConstants::HDD_HARD_DRIVE . ',') !== false);
            if (strpos($catIdsStr, ',' . CategoriesConstants::HDD_ATA . ',') !== false) {
                return CategoriesConstants::HDD_ATA;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::HDD_SATA . ',') !== false) {
                return CategoriesConstants::HDD_SATA;
            }
        }
        return null;
    }

    /**
     * Returns given ssd card interface if given item is ssd card and has interface set. otherwise returns null.
     */
    public function getSsdInterface($ssd) {
        if ($ssd instanceof ItemDto) {
            $itemDto = $ssd;
        } else {
            $itemDto = $this->itemManager->selectByPK($ssd);
        }

        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();
            assert(strpos($catIdsStr, ',' . CategoriesConstants::SSD_SOLID_STATE_DRIVE . ',') !== false);
            return CategoriesConstants::SSD_SOLID_STATE_DRIVE;
        }
        return null;
    }

    /**
     * Returns given opt card interface if given item is opt card and has interface set. otherwise returns null.
     */
    public function getOptInterface($opt) {
        if ($opt instanceof ItemDto) {
            $itemDto = $opt;
        } else {
            $itemDto = $this->itemManager->selectByPK($opt);
        }
        if ($itemDto) {
            $catIdsStr = $itemDto->getCategoriesIds();
            assert(strpos($catIdsStr, ',' . CategoriesConstants::OPTICAL_DRIVE . ',') !== false);
            if (strpos($catIdsStr, ',' . CategoriesConstants::OPTICAL_DRIVE_IDE . ',') !== false) {
                return CategoriesConstants::OPTICAL_DRIVE_IDE;
            }
            if (strpos($catIdsStr, ',' . CategoriesConstants::OPTICAL_DRIVE_SATA . ',') !== false) {
                return CategoriesConstants::OPTICAL_DRIVE_SATA;
            }
        }
        return null;
    }

    /**
     * Returns currently selected SATA and ATA storage counts in array,
     * First element of array is SATA storage count, second is ATA.
     */
    public function getSelectedStorageComponentCount() {
        $sata_storage_count = 0;
        $ata_storage_count = 0;
        if (isset($_REQUEST['hdds'])) {
            $hdds = $_REQUEST['hdds'];
            $hdds = explode(',', $hdds);
            foreach ($hdds as $key => $hdd) {
                $interface = $this->getHddInterface($hdd);
                if ($interface === CategoriesConstants::HDD_SATA) {
                    $sata_storage_count++;
                } else if ($interface === CategoriesConstants::HDD_ATA) {
                    $ata_storage_count++;
                }
            }
        }
        if (isset($_REQUEST['opts'])) {
            $opts = $_REQUEST['opts'];
            $opts = explode(',', $opts);
            foreach ($opts as $key => $opt) {
                $interface = $this->getOptInterface($opt);
                if ($interface === CategoriesConstants::OPTICAL_DRIVE_SATA) {
                    $sata_storage_count++;
                } else if ($interface === CategoriesConstants::OPTICAL_DRIVE_IDE) {
                    $ata_storage_count++;
                }
            }
        }
        if (isset($_REQUEST['ssds'])) {
            $ssds = $_REQUEST['ssds'];
            $ssdsArray = array();
            if (!empty($ssds)) {
                $ssdsArray = explode(',', $ssds);
            }
            $sata_storage_count += count($ssdsArray);
        }

        return array($sata_storage_count, $ata_storage_count);
    }

    public function getSelectedGraphicsComponentCount() {
        $pci_express_graphics_count = 0;
        $agp_graphics_count = 0;
        if (isset($_REQUEST['graphics'])) {
            $graphics = $_REQUEST['graphics'];
            $graphics = explode(',', $graphics);
            foreach ($graphics as $key => $graphics) {
                $interface = $this->getGraphicsInterface($graphics);
                if ($interface === CategoriesConstants::VIDEO_INTERFACE_PCI_EXPRESS) {
                    $pci_express_graphics_count++;
                } else if ($interface === CategoriesConstants::VIDEO_INTERFACE_AGP) {
                    $agp_graphics_count++;
                }
            }
        }
        return array($pci_express_graphics_count, $agp_graphics_count);
    }

    public function getMbSataStorageFreePortCount($mb) {
        if (isset($mb)) {
            $c = $this->getSelectedStorageComponentCount();
            $busy_sata_storage_count = $c[0];
            $ssc = $this->getMbSataPortCountRepresentedInInteger($mb);
            return (int) $ssc - (int) $busy_sata_storage_count;
        } else {
            $c = $this->getSelectedStorageComponentCount();
            $busy_sata_storage_count = $c[0];
            $ssc = self::$maximum_system_mb_sata_count_supported;
            return (int) $ssc - (int) $busy_sata_storage_count;
        }
    }

    public function getMbPciExpressFreePortCount($mb) {
        if (isset($mb)) {
            $c = $this->getSelectedGraphicsComponentCount();
            $busy_pci_express_graphics_count = $c[0];
            $ssc = $this->getMbGraphicsSlotCountRepresentedInInteger($mb);
            return $ssc - $busy_pci_express_graphics_count;
        } else {
            $c = $this->getSelectedGraphicsComponentCount();
            $busy_pci_express_graphics_count = $c[0];
            $ssc = self::$maximum_system_mb_graphics_count_supported;
            return (int) $ssc - (int) $busy_pci_express_graphics_count;
        }
    }

    public function getMbAgpFreePortCount($mb) {
        if (isset($mb)) {
            $c = $this->getSelectedGraphicsComponentCount();
            $busy_agp_graphics_count = $c[1];
            $ssc = $this->getMbGraphicsSlotCountRepresentedInInteger($mb);
            return $ssc - $busy_agp_graphics_count;
        } else {
            $c = $this->getSelectedGraphicsComponentCount();
            $busy_agp_graphics_count = $c[1];
            $ssc = self::$maximum_system_mb_graphics_count_supported;
            return (int) $ssc - (int) $busy_agp_graphics_count;
        }
    }

    public function getMbAtaStorageFreePortCount($mb) {
        if (isset($mb)) {
            $c = $this->getSelectedStorageComponentCount();
            $busy_ata_storage_count = $c[1];
            $apc = $this->getMbAtaPortCountRepresentedInInteger($mb);
            return (int) $apc - (int) $busy_ata_storage_count;
        } else {
            $c = $this->getSelectedStorageComponentCount();
            $busy_ata_storage_count = $c[1];
            $apc = self::$maximum_system_mb_ata_count_supported;
            return (int) $apc - (int) $busy_ata_storage_count;
        }
    }

    public function isRamTypeCompatibleWithMb($ram, $mb) {
        $ramType = $this->getRamType($ram);
        $mbRamType = $this->getMbRamType($mb);
        switch ($ramType) {
            case CategoriesConstants::RAM_TYPE_DDR :
                return CategoriesConstants::MB_RAM_TYPE_DDR === $mbRamType;
            case CategoriesConstants::RAM_TYPE_DDR2 :
                return CategoriesConstants::MB_RAM_TYPE_DDR2 === $mbRamType;
            case CategoriesConstants::RAM_TYPE_DDR3 :
                return CategoriesConstants::MB_RAM_TYPE_DDR3 === $mbRamType;
        }
        assert(false);
        //ram type should be one of above values.
        return null;
    }

    public function getRamKitCountRepresentedInInteger($ram) {
        $ramKitCount = $this->getRamKitCount($ram);
        switch ($ramKitCount) {
            case CategoriesConstants::RAM_KIT_COUNT_1 :
                return 1;
            case CategoriesConstants::RAM_KIT_COUNT_2 :
                return 2;
            case CategoriesConstants::RAM_KIT_COUNT_3 :
                return 3;
            case CategoriesConstants::RAM_KIT_COUNT_4 :
                return 4;
            case CategoriesConstants::RAM_KIT_COUNT_6 :
                return 6;
            case CategoriesConstants::RAM_KIT_COUNT_8 :
                return 8;
            default :
                assert(false);
        }
        assert(false);
        return null;
    }

    public function getMbRamSlotCountRepresentedInInteger($mb) {
        $mbRamSlotCount = $this->getMbRamSlotCount($mb);
        switch ($mbRamSlotCount) {
            case CategoriesConstants::MB_RAM_SLOT_COUNT_1 :
                return 1;
            case CategoriesConstants::MB_RAM_SLOT_COUNT_2 :
                return 2;
            case CategoriesConstants::MB_RAM_SLOT_COUNT_3 :
                return 3;
            case CategoriesConstants::MB_RAM_SLOT_COUNT_4 :
                return 4;
            case CategoriesConstants::MB_RAM_SLOT_COUNT_6 :
                return 6;
            case CategoriesConstants::MB_RAM_SLOT_COUNT_8 :
                return 8;
            default :
                assert(false);
        }
        assert(false);
    }

    public function getMbGraphicsSlotCountRepresentedInInteger($mb) {
        $mbGraphicsSlotCount = $this->getMbGraphicsSlotCount($mb);
        switch ($mbGraphicsSlotCount) {
            case CategoriesConstants::MB_GRAPHICS_SLOT_COUNT_1:
                return 1;
            case CategoriesConstants::MB_GRAPHICS_SLOT_COUNT_2 :
                return 2;
            case CategoriesConstants::MB_GRAPHICS_SLOT_COUNT_3 :
                return 3;
            case CategoriesConstants::MB_GRAPHICS_SLOT_COUNT_4 :
                return 4;
            default:
                return 1;
        }
        return 1;
    }

    public function getMbSataPortCountRepresentedInInteger($mb) {
        $mbSataPortCount = $this->getMbSataSlotCount($mb);
        switch ($mbSataPortCount) {
            case CategoriesConstants::MB_SATA_PORT_COUNT_1 :
                return 1;
            case CategoriesConstants::MB_SATA_PORT_COUNT_2 :
                return 2;
            case CategoriesConstants::MB_SATA_PORT_COUNT_3 :
                return 3;
            case CategoriesConstants::MB_SATA_PORT_COUNT_4 :
                return 4;
            case CategoriesConstants::MB_SATA_PORT_COUNT_5 :
                return 5;
            case CategoriesConstants::MB_SATA_PORT_COUNT_6 :
                return 6;
            case CategoriesConstants::MB_SATA_PORT_COUNT_7 :
                return 7;
            case CategoriesConstants::MB_SATA_PORT_COUNT_8 :
                return 8;
            case CategoriesConstants::MB_SATA_PORT_COUNT_10 :
                return 10;
            case CategoriesConstants::MB_SATA_PORT_COUNT_12 :
                return 12;
            case CategoriesConstants::MB_SATA_PORT_COUNT_14 :
                return 14;
            default :
                var_dump($mbSataPortCount);
                assert(false);
        }
        assert(false);
    }

    public function getMbAtaPortCountRepresentedInInteger($mb) {
        $mbAtaPortCount = $this->getMbAtaSlotCount($mb);
        if ($mbAtaPortCount != null) {
            switch ($mbAtaPortCount) {
                case CategoriesConstants::MB_ATA_PORT_COUNT_1 :
                    return 1;
                case CategoriesConstants::MB_ATA_PORT_COUNT_2 :
                    return 2;
                default :
                    assert(false);
            }
        }
        return 0;
    }

    public function isRamCountCompatibleWithMb($rams, $mb) {
        $rams = explode(',', $rams);
        $ramKitCountRepresentedByInteger = $this->getRamKitCountRepresentedInInteger($rams[0]);
        $mbRamSlotCountRepresentedByInteger = $this->getMbRamSlotCountRepresentedInInteger($mb);
        return count($rams) * $ramKitCountRepresentedByInteger <= $mbRamSlotCountRepresentedByInteger;
    }

    public function isGraphicsCountCompatibleWithMb($graphics, $mb) {
        $graphics = explode(',', $graphics);
        $mbRamSlotCountRepresentedByInteger = $this->getMbRamSlotCountRepresentedInInteger($mb);
        return count($graphics) <= $mbRamSlotCountRepresentedByInteger;
    }

    public function getComponentKeywordByIndex($ci) {
        $ci = (int) $ci;
        return array_search($ci, self::$PCC_COMPONENTS);
    }

    public function getComponentKeywordsByIndexes($cis) {
        $ret = array();
        foreach ($cis as $key => $ci) {
            $ret[] = $this->getComponentKeywordByIndex($ci);
        }
        return $ret;
    }

    /*
      public function removeUnavailableItemsFromRequest() {
      $select_items = $this->getSelectedItemsIdsImplodedFromRequest();
      $itemsDto = $this->itemManager->getItemsForOrder($items_ids, null, 0);
      $select_items = explode($select_items , ',');
      foreach ($select_items as $index => $itemId) {
      if (!isset($itemsDto[$index]))
      $this->unsetSelectedItemIdFromRequest($itemId);
      }
      }
     */

    public static $SELECTED_ITEMS_REQUEST_PARAM_NAMES = array("case", "mb", "rams",
        "cpu", "hdds", "ssds", "cooler", "monitor", "graphics", "opts", "power", "keyboard", "mouse", "speaker");

    /*
      public function unsetSelectedItemIdFromRequest($id) {
      foreach (self::$SELECTED_ITEMS_REQUEST_PARAM_NAMES as $key => $req_name) {
      while ($pos = strpos($id, ',' . $_REQUEST[$req_name] . ',') !== false) {
      if ($pos !== false) {
      $_REQUEST[$req_name] = ',' . $_REQUEST[$req_name] . ',';
      $reqPart1 = substr($_REQUEST[$req_name], 0, $pos);
      $reqPart2 = substr($_REQUEST[$req_name], $pos + strlen($id) + 2);
      $_REQUEST[$req_name] = $reqPart1 . ',0,' . $reqPart2;
      $_REQUEST[$req_name] = ltrim($_REQUEST[$req_name], ',');
      $_REQUEST[$req_name] = rtrim($_REQUEST[$req_name], ',');
      }
      }
      }
      }
     */

    /**
     * This function should call on any component load function first line.
     */
    public function manageComponentLoadRequestBeforeLoad() {
        //$this->removeUnavailableItemsFromRequest();

        if (!isset($_REQUEST['last_selected_component_id']) || $_REQUEST['last_selected_component_id'] == 0) {
            return;
        }
        if ($_REQUEST['cpu']) {
            $cpu = $this->secure($_REQUEST['cpu']);
            $lastSelectedComponentTypeIndex = $this->secure($_REQUEST['last_selected_component_type_index']);
            $lastSelectedComponentId = $this->secure($_REQUEST['last_selected_component_id']);
            if ($lastSelectedComponentTypeIndex == PcConfiguratorManager::$PCC_COMPONENTS['mb']) {
                if (!$this->isCpuCompatibleWithMb($cpu, $lastSelectedComponentId)) {
                    unset($_REQUEST['cpu']);
                }
            }
            if ($lastSelectedComponentTypeIndex == PcConfiguratorManager::$PCC_COMPONENTS['cooler']) {
                if (!$this->isCpuCompatibleWithCooler($cpu, $lastSelectedComponentId)) {
                    unset($_REQUEST['cpu']);
                }
            }
        }
        if ($_REQUEST['cooler']) {
            $cooler = $this->secure($_REQUEST['cooler']);
            $lastSelectedComponentTypeIndex = $this->secure($_REQUEST['last_selected_component_type_index']);
            $lastSelectedComponentId = $this->secure($_REQUEST['last_selected_component_id']);
            if ($lastSelectedComponentTypeIndex == PcConfiguratorManager::$PCC_COMPONENTS['mb']) {
                if (!$this->isCoolerCompatibleWithMb($cooler, $lastSelectedComponentId)) {
                    unset($_REQUEST['cooler']);
                }
            }
            if ($lastSelectedComponentTypeIndex == PcConfiguratorManager::$PCC_COMPONENTS['cpu']) {
                if (!$this->isCoolerCompatibleWithCpu($cooler, $lastSelectedComponentId)) {
                    unset($_REQUEST['cooler']);
                }
            }
        }
        if ($_REQUEST['case']) {
            $case = $this->secure($_REQUEST['case']);
            $lastSelectedComponentTypeIndex = $this->secure($_REQUEST['last_selected_component_type_index']);
            $lastSelectedComponentId = $this->secure($_REQUEST['last_selected_component_id']);
            if ($lastSelectedComponentTypeIndex == PcConfiguratorManager::$PCC_COMPONENTS['mb']) {
                if (!$this->isCaseCompatibleWithMb($case, $lastSelectedComponentId)) {
                    unset($_REQUEST['case']);
                }
            }
        }
        if ($_REQUEST['graphics']) {
            $graphics = $this->secure($_REQUEST['graphics']);
            $lastSelectedComponentTypeIndex = $this->secure($_REQUEST['last_selected_component_type_index']);
            $lastSelectedComponentId = $this->secure($_REQUEST['last_selected_component_id']);
            if ($lastSelectedComponentTypeIndex == PcConfiguratorManager::$PCC_COMPONENTS['mb']) {
                if (!$this->isGraphicsCardCompatibleWithMb($graphics, $lastSelectedComponentId)) {
                    unset($_REQUEST['graphics']);
                }
            }
        }
        if ($_REQUEST['opts']) {
            $opts = $this->secure($_REQUEST['opts']);
            $lastSelectedComponentTypeIndex = $this->secure($_REQUEST['last_selected_component_type_index']);
            $lastSelectedComponentId = $this->secure($_REQUEST['last_selected_component_id']);
            if ($lastSelectedComponentTypeIndex == PcConfiguratorManager::$PCC_COMPONENTS['mb']) {
                if (!$this->isOptsInterfaceCompatibleWithMb($opts, $lastSelectedComponentId)) {
                    unset($_REQUEST['opts']);
                } else {
                    $selected_storages_count = $this->getSelectedStorageComponentCount();
                    $selected_sata_storage_count = $selected_storages_count[0];
                    $selected_ata_storage_count = $selected_storages_count[1];
                    if ($selected_sata_storage_count > $this->getMbSataPortCountRepresentedInInteger($lastSelectedComponentId)) {
                        unset($_REQUEST['opts']);
                    }
                    if ($selected_ata_storage_count > $this->getMbAtaPortCountRepresentedInInteger($lastSelectedComponentId)) {
                        unset($_REQUEST['opts']);
                    }
                }
            }
        }

        if ($_REQUEST['hdds']) {
            $hdds = $this->secure($_REQUEST['hdds']);
            $lastSelectedComponentTypeIndex = $this->secure($_REQUEST['last_selected_component_type_index']);
            $lastSelectedComponentId = $this->secure($_REQUEST['last_selected_component_id']);
            if ($lastSelectedComponentTypeIndex == PcConfiguratorManager::$PCC_COMPONENTS['mb']) {
                if (!$this->isHddsInterfaceCompatibleWithMb($hdds, $lastSelectedComponentId)) {
                    unset($_REQUEST['hdds']);
                } else {
                    $selected_storages_count = $this->getSelectedStorageComponentCount();
                    $selected_sata_storage_count = $selected_storages_count[0];
                    $selected_ata_storage_count = $selected_storages_count[1];
                    if ($selected_sata_storage_count > $this->getMbSataPortCountRepresentedInInteger($lastSelectedComponentId)) {
                        unset($_REQUEST['hdds']);
                    }
                    if ($selected_ata_storage_count > $this->getMbAtaPortCountRepresentedInInteger($lastSelectedComponentId)) {
                        unset($_REQUEST['hdds']);
                    }
                }
            }
        }

        if ($_REQUEST['ssds']) {
            $lastSelectedComponentTypeIndex = $this->secure($_REQUEST['last_selected_component_type_index']);
            $lastSelectedComponentId = $this->secure($_REQUEST['last_selected_component_id']);
            if ($lastSelectedComponentTypeIndex == PcConfiguratorManager::$PCC_COMPONENTS['mb']) {
                $mbSataIdeSupport = $this->getMbSataIdeSupport($mb);
                if (!in_array(CategoriesConstants::MB_SATA_SUPPORTED, $mbSataIdeSupport)) {
                    unset($_REQUEST['ssds']);
                } else {
                    $selected_storages_count = $this->getSelectedStorageComponentCount();
                    $selected_sata_storage_count = $selected_storages_count[0];
                    if ($selected_sata_storage_count > $this->getMbSataPortCountRepresentedInInteger($lastSelectedComponentId)) {
                        unset($_REQUEST['ssds']);
                    }
                }
            }
        }

        if ($_REQUEST['mb']) {
            $mb = $this->secure($_REQUEST['mb']);
            $lastSelectedComponentTypeIndex = $this->secure($_REQUEST['last_selected_component_type_index']);
            $lastSelectedComponentId = $this->secure($_REQUEST['last_selected_component_id']);
            if ($lastSelectedComponentTypeIndex == PcConfiguratorManager::$PCC_COMPONENTS['case']) {
                if (!$this->isCaseCompatibleWithMb($lastSelectedComponentId, $mb)) {
                    unset($_REQUEST['mb']);
                }
            } else if ($lastSelectedComponentTypeIndex == PcConfiguratorManager::$PCC_COMPONENTS['cpu']) {
                if (!$this->isCpuCompatibleWithMb($lastSelectedComponentId, $mb)) {
                    unset($_REQUEST['mb']);
                }
            } else if ($lastSelectedComponentTypeIndex == PcConfiguratorManager::$PCC_COMPONENTS['cooler']) {
                if (!$this->isCoolerCompatibleWithMb($lastSelectedComponentId, $mb)) {
                    unset($_REQUEST['mb']);
                }
            } else if ($lastSelectedComponentTypeIndex == PcConfiguratorManager::$PCC_COMPONENTS['hdd']) {
                $lastSelectedComponentId = explode(',', $lastSelectedComponentId);
                if (!$this->isHddsInterfaceCompatibleWithMb($lastSelectedComponentId, $mb)) {
                    unset($_REQUEST['mb']);
                }
            } else if ($lastSelectedComponentTypeIndex == PcConfiguratorManager::$PCC_COMPONENTS['graphics']) {
                if (!$this->isGraphicsCardCompatibleWithMb($lastSelectedComponentId, $mb)) {
                    unset($_REQUEST['mb']);
                }
            } else if ($lastSelectedComponentTypeIndex == PcConfiguratorManager::$PCC_COMPONENTS['ram']) {
                if (!$this->isRamCountCompatibleWithMb($lastSelectedComponentId, $mb)) {
                    unset($_REQUEST['mb']);
                }
                $lastSelectedComponentId = explode(',', $lastSelectedComponentId);
                if (!$this->isRamTypeCompatibleWithMb($lastSelectedComponentId[0], $mb)) {
                    unset($_REQUEST['mb']);
                }
            } else if ($lastSelectedComponentTypeIndex == PcConfiguratorManager::$PCC_COMPONENTS['opt']) {
                $lastSelectedComponentId = explode(',', $lastSelectedComponentId);
                if (!$this->isOptsInterfaceCompatibleWithMb($lastSelectedComponentId, $mb)) {
                    unset($_REQUEST['mb']);
                }
            }
        }

        if ($_REQUEST['rams']) {
            $rams = $this->secure($_REQUEST['rams']);
            $lastSelectedComponentTypeIndex = $this->secure($_REQUEST['last_selected_component_type_index']);
            $lastSelectedComponentId = $this->secure($_REQUEST['last_selected_component_id']);
            if ($lastSelectedComponentTypeIndex == PcConfiguratorManager::$PCC_COMPONENTS['mb']) {
                if (!$this->isRamTypeCompatibleWithMb($rams, $lastSelectedComponentId)) {
                    unset($_REQUEST['rams']);
                } else if (!$this->isRamCountCompatibleWithMb($rams, $lastSelectedComponentId)) {
                    $ramMaxAllowedCount = $this->getMbRamSlotCountRepresentedInInteger($lastSelectedComponentId);
                    $rams = $_REQUEST['rams'];
                    $rams = explode(',', $rams);
                    $oneKitCount = $this->getRamKitCountRepresentedInInteger($rams[0]);
                    assert($oneKitCount > 0);
                    //kit count should be greather that 0
                    $allowedSelectedRamKitCount = floor((int) $ramMaxAllowedCount / (int) $oneKitCount);
                    if ($allowedSelectedRamKitCount == 0) {
                        unset($_REQUEST['rams']);
                    } else {
                        $rams = array_slice($rams, 0, (int) $allowedSelectedRamKitCount);
                        $_REQUEST['rams'] = implode(',', $rams);
                    }
                }
            }
        }

        if ($_REQUEST['graphics']) {
            $graphics = $this->secure($_REQUEST['graphics']);
            $lastSelectedComponentTypeIndex = $this->secure($_REQUEST['last_selected_component_type_index']);
            $lastSelectedComponentId = $this->secure($_REQUEST['last_selected_component_id']);
            if ($lastSelectedComponentTypeIndex == PcConfiguratorManager::$PCC_COMPONENTS['mb']) {
                if (!$this->isGraphicsCardCompatibleWithMb($graphics, $lastSelectedComponentId)) {
                    unset($_REQUEST['graphics']);
                } else if (!$this->isGraphicsCountCompatibleWithMb($graphics, $lastSelectedComponentId)) {
                    $graphicsMaxAllowedCount = $this->getMbGraphicsSlotCountRepresentedInInteger($lastSelectedComponentId);
                    $graphics = $_REQUEST['graphics'];
                    $graphics = explode(',', $graphics);
                    $graphics = array_slice($graphics, 0, $graphicsMaxAllowedCount);
                    $_REQUEST['graphics'] = implode(',', $graphics);
                }
            }
        }
    }

    public function getRequestComponentSelectedComponents() {
        $case = $this->secure($_REQUEST["case"]);
        $mb = $this->secure($_REQUEST["mb"]);
        $rams = $this->secure($_REQUEST["rams"]);
        $cpu = $this->secure($_REQUEST["cpu"]);
        $hdds = $this->secure($_REQUEST["hdds"]);
        $ssds = $this->secure($_REQUEST["ssds"]);
        $cooler = $this->secure($_REQUEST["cooler"]);
        $monitor = $this->secure($_REQUEST["monitor"]);
        $graphics = $this->secure($_REQUEST["graphics"]);
        $opts = $this->secure($_REQUEST["opts"]);
        $power = $this->secure($_REQUEST["power"]);
        $keyboard = $this->secure($_REQUEST["keyboard"]);
        $mouse = $this->secure($_REQUEST["mouse"]);
        $speaker = $this->secure($_REQUEST["speaker"]);
        $selected_components_ids = array();
        if ($case > 0) {
            $selected_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['case'];
        }

        if ($cpu > 0) {
            $selected_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['cpu'];
        }

        if ($mb > 0) {
            $selected_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['mb'];
        }

        if ($cooler > 0) {
            $selected_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['cooler'];
        }

        if ($rams > 0) {
            $selected_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['ram'];
        }

        if ($hdds > 0) {
            $selected_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['hdd'];
        }

        if ($ssds > 0) {
            $selected_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['ssd'];
        }

        if ($opts > 0) {
            $selected_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['opt'];
        }

        if ($monitor > 0) {
            $selected_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['monitor'];
        }

        if ($graphics > 0) {
            $selected_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['graphics'];
        }

        if ($power > 0) {
            $selected_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['power'];
        }

        if ($keyboard > 0) {
            $selected_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['keyboard'];
        }

        if ($mouse > 0) {
            $selected_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['mouse'];
        }

        if ($speaker > 0) {
            $selected_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['speaker'];
        }

        return $selected_components_ids;
    }

    public function getRequestComponentRequiredComponents($user) {
        list($selectedCaseDto, $selectedCpuDto, $selectedMbDto, $selectedCoolerDto, $selectedRamsDto, $selectedHddsDto, $selectedSsdsDto, $selectedOptsDto, $selectedMonitorDto, $selectedGraphicsDto, $selectedPowerDto, $selectedKeyDto, $selectedMouseDto, $selectedSpeakerDto) = $this->getSelectedComponentsDtosOrderedInArray($user);

        $required_components_ids = array();

        if (!isset($selectedCaseDto)) {
            $required_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['case'];
        }

        if (!isset($selectedCpuDto)) {
            $required_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['cpu'];
        }

        if (!isset($selectedMbDto)) {
            $required_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['mb'];
        }

        if (!isset($selectedCoolerDto)) {
            $required_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['cooler'];
        }

        if (!isset($selectedRamsDto) || empty($selectedRamsDto)) {
            $required_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['ram'];
        }

        if (empty($selectedHddsDto) && empty($selectedSsdsDto)) {
            $required_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['hdd'];
        }

        if (!isset($selectedGraphicsDto)) {
            if (isset($selectedMbDto)) {
                $ov = $this->hasMbOnBoardGraphics($selectedMbDto->getId());
                if ($ov !== true) {
                    $required_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['graphics'];
                }
            }
        }

        if (!isset($selectedPowerDto)) {
            if (isset($selectedCaseDto)) {
                $ps = $this->hasCasePowerSupply($selectedCaseDto->getId());
                if ($ps === false) {
                    $required_components_ids[] = PcConfiguratorManager::$PCC_COMPONENTS['power'];
                }
            }
        }
        return $required_components_ids;
    }

    /**
     * Returns TRUE if all array items are the same item, FALSE oterwise
     * $itemsDtos can be also object just single object, in this case function returns TRUE for any object
     */
    public function groupSameItemsInSubArrays($itemsDtos) {
        assert(isset($itemsDtos));
        if (!is_array($itemsDtos)) {
            return $itemsDtos;
        }
        $ret = array();
        foreach ($itemsDtos as $key => $item) {
            $ret[intval($item->getId())][] = $item;
        }
        return $ret;
    }

    function implode_r($glue, array $arr) {
        $ret = '';
        foreach ($arr as $piece) {
            if (is_array($piece)) {
                $ret .= $glue . $this->implode_r($glue, $piece);
            } else {
                if (isset($piece)) {
                    $ret .= $glue . $piece;
                } else {
                    $ret .= $glue . '0';
                }
            }
        }
        $ret = ltrim($ret, ",");
        return $ret;
    }

    public function getSelectedItemsIdsImplodedFromRequest() {
        return $this->implode_r(',', $this->getSelectedItemsIdsFromRequest());
    }

    /**
     * Returns selected components from $_REQUEST in following format
     * array($selected_case, $selected_cpu, $selected_mb, $selected_cooler, $selected_rams, $selected_hdds, $selected_opts,
      $selected_monitor, $selected_graphics, $selected_power, $selected_keyboard, $selected_mouse, $selected_speaker);
     * mutliselect items ids are in subarrays
     */
    public function getSelectedItemsIdsFromRequest() {
        $csse = $this->secure($_REQUEST["case"]);


        $mb = $this->secure($_REQUEST["mb"]);

        $rams = $this->secure($_REQUEST["rams"]);
        if (isset($rams) && strlen($rams) > 0) {
            if (strpos($rams, ',') !== false) {
                $rams = explode(',', $rams);
            } else {
                $rams = array($rams);
            }
        } else {
            unset($rams);
        }
        $cpu = $this->secure($_REQUEST["cpu"]);
        $hdds = $this->secure($_REQUEST["hdds"]);
        if (isset($hdds) && strlen($hdds) > 0) {
            if (strpos($hdds, ',') !== false) {
                $hdds = explode(',', $hdds);
            } else {
                $hdds = array($hdds);
            }
        } else {
            unset($hdds);
        }
        $ssds = $this->secure($_REQUEST["ssds"]);
        if (isset($ssds) && strlen($ssds) > 0) {
            if (strpos($ssds, ',') !== false) {
                $ssds = explode(',', $ssds);
            } else {
                $ssds = array($ssds);
            }
        } else {
            unset($ssds);
        }
        $cooler = $this->secure($_REQUEST["cooler"]);
        $monitor = $this->secure($_REQUEST["monitor"]);

        $graphics = $this->secure($_REQUEST["graphics"]);
        if (isset($graphics) && strlen($graphics) > 0) {
            if (strpos($graphics, ',') !== false) {
                $graphics = explode(',', $graphics);
            } else {
                $graphics = array($graphics);
            }
        } else {
            unset($graphics);
        }


        $opts = $this->secure($_REQUEST["opts"]);
        if (isset($opts) && strlen($opts) > 0) {
            if (strpos($opts, ',') !== false) {
                $opts = explode(',', $opts);
            } else {
                $opts = array($opts);
            }
        } else {
            unset($opts);
        }

        $power = $this->secure($_REQUEST["power"]);
        $keyboard = $this->secure($_REQUEST["keyboard"]);
        $mouse = $this->secure($_REQUEST["mouse"]);
        $speaker = $this->secure($_REQUEST["speaker"]);
        $this->validateComponents($csse, $cpu, $mb, $cooler, $rams, $hdds, $ssds, $opts, $monitor, $graphics, $power, $keyboard, $mouse, $speaker);
        return array($csse, $cpu, $mb, $cooler, $rams, $hdds, $ssds, $opts, $monitor, $graphics, $power, $keyboard, $mouse, $speaker);
    }

    public function validateComponents(&$case, &$cpu, &$mb, &$cooler, &$rams, &$hdds, &$ssds, &$opts, &$monitor, &$graphics, &$power, &$keyboard, &$mouse, &$speaker) {
        if (!$this->pcComponentValidatorManager->isCase($case)) {
            $case = null;
        }
        if (!$this->pcComponentValidatorManager->isCpu($cpu)) {
            $cpu = null;
        }
        if (!$this->pcComponentValidatorManager->isMotherboard($mb)) {
            $mb = null;
        }
        if (!$this->pcComponentValidatorManager->isCooler($cooler)) {
            $cooler = null;
        }

        if (isset($rams) && is_array($rams)) {
            $validRams = array();
            foreach ($rams as $ram) {
                if ($this->pcComponentValidatorManager->isRam($ram)) {
                    $validRams[] = $ram;
                }
            }
            $rams = $validRams;
        }
        if (isset($hdds) && is_array($hdds)) {
            $validHdds = array();
            foreach ($hdds as $hdd) {
                if ($this->pcComponentValidatorManager->isHdd($hdd)) {
                    $validHdds[] = $hdd;
                }
            }
            $hdds = $validHdds;
        }

        if (isset($ssds) && is_array($ssds)) {
            $validSsds = array();
            foreach ($ssds as $ssd) {
                if ($this->pcComponentValidatorManager->isSsd($ssd)) {
                    $validSsds[] = $ssd;
                }
            }
            $ssds = $validSsds;
        }

        if (isset($opts) && is_array($opts)) {
            $validOpts = array();
            foreach ($opts as $opt) {
                if ($this->pcComponentValidatorManager->isOpticalDrive($opt)) {
                    $validOpts[] = $opt;
                }
            }
            $opts = $validOpts;
        }
        if (!$this->pcComponentValidatorManager->isMonitor($monitor)) {
            $monitor = null;
        }

        if (isset($graphics) && is_array($graphics)) {
            $validGraphics = array();
            foreach ($graphics as $vga) {
                if ($this->pcComponentValidatorManager->isGraphicsCard($vga)) {
                    $validGraphics[] = $vga;
                }
            }
            $graphics = $validGraphics;
        }

        if (!$this->pcComponentValidatorManager->isPowerSupply($power)) {
            $power = null;
        }
        if (!$this->pcComponentValidatorManager->isKeyboard($keyboard)) {
            $keyboard = null;
        }
        if (!$this->pcComponentValidatorManager->isMouse($mouse)) {
            $mouse = null;
        }
        if (!$this->pcComponentValidatorManager->isSpeaker($speaker)) {
            $speaker = null;
        }
    }

    /**
     * Groups the same component types in sub array
     */
    public function getSelectedComponentsDtosOrderedInArray($user) {
        list($case, $cpu, $mb, $cooler, $rams, $hdds, $ssds, $opts,
                $monitor, $graphics, $power, $keyboard, $mouse, $speaker) = $this->getSelectedItemsIdsFromRequest();

        $userLevel = $user->getLevel();
        $user_id = $user->getId();
        if (intval($case) > 0) {
            $selectedCaseDto = $this->itemManager->getItemsForOrder($case, $user_id, $userLevel);
        }
        if (intval($mb) > 0) {
            $selectedMbDto = $this->itemManager->getItemsForOrder($mb, $user_id, $userLevel);
        }

        if (isset($rams)) {

            $selectedRamsDto = array();
            foreach ($rams as $key => $ram) {
                if (intval($ram) > 0) {
                    $selectedRamsDto[] = $this->itemManager->getItemsForOrder($ram, $user_id, $userLevel);
                }
            }
            if (count($selectedRamsDto) === 1) {
                $selectedRamsDto = $selectedRamsDto[0];
            }
        }

        $selectedCpuDto = $this->itemManager->getItemsForOrder($cpu, $user_id, $userLevel);

        if (isset($hdds)) {

            $selectedHddsDto = array();
            foreach ($hdds as $key => $hdd) {
                if (intval($hdd) > 0) {
                    $selectedHddsDto[] = $this->itemManager->getItemsForOrder($hdd, $user_id, $userLevel);
                }
            }
            if (count($selectedHddsDto) === 1) {
                $selectedHddsDto = $selectedHddsDto[0];
            }
        }

        if (isset($ssds)) {

            $selectedSsdsDto = array();
            foreach ($ssds as $key => $ssd) {
                if (intval($ssd) > 0) {
                    $selectedSsdsDto[] = $this->itemManager->getItemsForOrder($ssd, $user_id, $userLevel);
                }
            }
            if (count($selectedSsdsDto) === 1) {
                $selectedSsdsDto = $selectedSsdsDto[0];
            }
        }

        if (intval($cooler) > 0) {
            $selectedCoolerDto = $this->itemManager->getItemsForOrder($cooler, $user_id, $userLevel);
        }
        if (intval($monitor) > 0) {
            $selectedMonitorDto = $this->itemManager->getItemsForOrder($monitor, $user_id, $userLevel);
        }
        if (isset($graphics)) {
            $selectedGraphicsDto = array();

            foreach ($graphics as $vga) {
                if (intval($vga) > 0) {
                    $selectedGraphicsDto[] = $this->itemManager->getItemsForOrder($vga, $user_id, $userLevel);
                }
            }
            if (count($selectedGraphicsDto) === 1) {
                $selectedGraphicsDto = $selectedGraphicsDto[0];
            }
        }

        if (isset($opts)) {

            $selectedOptsDto = array();
            foreach ($opts as $key => $opt) {
                if (intval($opt) > 0) {
                    $selectedOptsDto[] = $this->itemManager->getItemsForOrder($opt, $user_id, $userLevel);
                }
            }
            if (count($selectedOptsDto) === 1) {
                $selectedOptsDto = $selectedOptsDto[0];
            }
        }
        if (intval($power) > 0) {
            $selectedPowerDto = $this->itemManager->getItemsForOrder($power, $user_id, $userLevel);
        }
        if (intval($keyboard) > 0) {
            $selectedKeyDto = $this->itemManager->getItemsForOrder($keyboard, $user_id, $userLevel);
        }
        if (intval($mouse) > 0) {
            $selectedMouseDto = $this->itemManager->getItemsForOrder($mouse, $user_id, $userLevel);
        }
        if (intval($speaker) > 0) {
            $selectedSpeakerDto = $this->itemManager->getItemsForOrder($speaker, $user_id, $userLevel);
        }

        return array($selectedCaseDto, $selectedCpuDto, $selectedMbDto, $selectedCoolerDto, $selectedRamsDto, $selectedHddsDto, $selectedSsdsDto, $selectedOptsDto, $selectedMonitorDto, $selectedGraphicsDto, $selectedPowerDto, $selectedKeyDto, $selectedMouseDto, $selectedSpeakerDto);
    }

    public function calcSelectedComponentProfitWithDiscount($selectedComponentsArray, $userLevel, $discountPercent, $adminPriceGroup = null) {
        $profit = 0;
        $discountParam = 1 - $discountPercent / 100;
        foreach ($selectedComponentsArray as $key => $item) {
            if ($item) {
                if (is_array($item)) {
                    foreach ($item as $key => $it) {
                        assert($it != null);
                        if (($userLevel === UserGroups::$ADMIN && $adminPriceGroup != 'admin') ||
                                ($userLevel !== UserGroups::$COMPANY && $userLevel !== UserGroups::$ADMIN && $it->getIsDealerOfThisCompany() != 1)) {
                            $price = (float) $it->getCustomerItemPrice() * $discountParam;
                            $profit += $price - $it->getDealerPrice();
                        }
                    }
                } else {
                    if (($userLevel === UserGroups::$ADMIN && $adminPriceGroup != 'admin') ||
                            ($userLevel !== UserGroups::$COMPANY && $userLevel !== UserGroups::$ADMIN && $item->getIsDealerOfThisCompany() != 1)) {
                        $price = (float) $item->getCustomerItemPrice() * $discountParam;
                        $profit += $price - $item->getDealerPrice();
                    }
                }
            }
        }
        return $profit;
    }

    /**
     * Returns the given components total in AMD and USD
     * return format is following
     * returned result has the same length and order as input array.
     */
    public function getSelectedComponentSubTotalsAndTotals($selectedComponentsArray, $userLevel, $adminPriceGroup = null) {
        $totalUsd = 0;
        $totalAmd = 0;
        foreach ($selectedComponentsArray as $key => $item) {
            if ($item) {
                if (is_array($item)) {
                    foreach ($item as $key => $it) {
                        assert($it != null);

                        if ((!isset($adminPriceGroup) || $adminPriceGroup == 'admin') &&
                                ($userLevel === UserGroups::$COMPANY || $userLevel === UserGroups::$ADMIN || $it->getIsDealerOfThisCompany() == 1)) {
                            $price = (float) $it->getDealerPrice();
                            $totalUsd += $price;
                        } else {
                            $price = (float) $it->getCustomerItemPrice();
                            $p = $this->itemManager->exchangeFromUsdToAMD($price);
                            $totalAmd += $p;
                        }
                    }
                } else {
                    if ((!isset($adminPriceGroup) || $adminPriceGroup == 'admin') &&
                            ($userLevel === UserGroups::$COMPANY || $userLevel === UserGroups::$ADMIN || $item->getIsDealerOfThisCompany() == 1)) {
                        $price = (float) $item->getDealerPrice();
                        $totalUsd += $price;
                    } else {
                        $price = (float) $item->getCustomerItemPrice();
                        $p = $this->itemManager->exchangeFromUsdToAMD($price);
                        $totalAmd += $p;
                    }
                }
            }
        }
        return array($totalUsd, $totalAmd);
    }

    public static function getPcComponentTypeByItemCategoriesIds($categoriesIdsArray) {
        if (in_array(CategoriesConstants::CASE_CHASSIS, $categoriesIdsArray))
            return CategoriesConstants::CASE_CHASSIS;
        else if (in_array(CategoriesConstants::MOTHER_BOARD, $categoriesIdsArray))
            return CategoriesConstants::MOTHER_BOARD;
        else if (in_array(CategoriesConstants::RAM_MEMORY, $categoriesIdsArray))
            return CategoriesConstants::RAM_MEMORY;
        else if (in_array(CategoriesConstants::CPU_PROCESSOR, $categoriesIdsArray))
            return CategoriesConstants::CPU_PROCESSOR;
        else if (in_array(CategoriesConstants::HDD_HARD_DRIVE, $categoriesIdsArray))
            return CategoriesConstants::HDD_HARD_DRIVE;
        else if (in_array(CategoriesConstants::SSD_SOLID_STATE_DRIVE, $categoriesIdsArray))
            return CategoriesConstants::SSD_SOLID_STATE_DRIVE;
        else if (in_array(CategoriesConstants::COOLER, $categoriesIdsArray))
            return CategoriesConstants::COOLER;
        else if (in_array(CategoriesConstants::MONITOR, $categoriesIdsArray))
            return CategoriesConstants::MONITOR;
        else if (in_array(CategoriesConstants::OPTICAL_DRIVE, $categoriesIdsArray))
            return CategoriesConstants::OPTICAL_DRIVE;
        else if (in_array(CategoriesConstants::POWER, $categoriesIdsArray))
            return CategoriesConstants::POWER;
        else if (in_array(CategoriesConstants::KEYBOARD, $categoriesIdsArray))
            return CategoriesConstants::KEYBOARD;
        else if (in_array(CategoriesConstants::MOUSE, $categoriesIdsArray))
            return CategoriesConstants::MOUSE;
        else if (in_array(CategoriesConstants::SPEAKER, $categoriesIdsArray))
            return CategoriesConstants::SPEAKER;
        else if (in_array(CategoriesConstants::VIDEO_CARD, $categoriesIdsArray))
            return CategoriesConstants::VIDEO_CARD;
    }

    public function calcPcBuildFee($bundleProfitWithDiscountUSD) {

        $specialFeesManager = SpecialFeesManager::getInstance($this->config, $this->args);
        $startingPcBuildFeeAMD = intval($specialFeesManager->getPcBuildFee()->getPrice());
        $bundleProfitWithDiscountAMD = $this->itemManager->exchangeFromUsdToAMD($bundleProfitWithDiscountUSD);
        $pcBuildFeeAMD = $startingPcBuildFeeAMD - $bundleProfitWithDiscountAMD;
        if ($pcBuildFeeAMD < 0) {
            $pcBuildFeeAMD = 0;
        }
        return $pcBuildFeeAMD;
    }

    public function getMapper() {
        return null;
    }

}

?>