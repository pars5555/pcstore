<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/util/pcc_categories_constants/CategoriesConstants.php");
require_once (CLASSES_PATH . "/managers/pcc_managers/PcConfiguratorManager.class.php");
require_once (CLASSES_PATH . "/managers/LanguageManager.class.php");

/**
 * PcConfiguratorManager class is responsible for creating,
 */
class PccMessagesManager extends AbstractManager {

    /**
     * @var app config
     */
    private $config;

    /**
     * @var passed arguemnts
     */
    private $args;

    /**
     * @var singleton instance of class
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
        //$this->itemManager = ItemManager::getInstance($this->config, $this->args);
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

            self::$instance = new PccMessagesManager($config, $args);
        }
        return self::$instance;
    }

    /**
     * Returns the array containing not compatible components names
     */
    public function getItemAllNotCompatibleReasonsMessages($itemDto, $component_index) {

        $reasonsMessages = "<ul>";
        $resons = $this->getItemAllNotCompatibleReasons($itemDto, false);

        if ($component_index == PcConfiguratorManager::$PCC_COMPONENTS['mb']) {
            if (in_array(PcConfiguratorManager::PCC_ATA_STORAGE_INTERFACE_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(330);
                $reasonsMessages .= "</li>";
            } else
            if (in_array(PcConfiguratorManager::PCC_ATA_STORAGE_COUNT_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(331);
                $reasonsMessages .= "</li>";
            }
            if (in_array(PcConfiguratorManager::PCC_SATA_STORAGE_INTERFACE_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(332);
                $reasonsMessages .= "</li>";
            } else
            if (in_array(PcConfiguratorManager::PCC_SATA_STORAGE_COUNT_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(333);
                $reasonsMessages .= "</li>";
            }
            if (in_array(PcConfiguratorManager::PCC_CASE_SIZE_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(334);
                $reasonsMessages .= "</li>";
            }
            if (in_array(PcConfiguratorManager::PCC_COOLER_SOCKET_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(335);
                $reasonsMessages .= "</li>";
            }
            if (in_array(PcConfiguratorManager::PCC_CPU_SOCKET_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(336);
                $reasonsMessages .= "</li>";
            }
            if (in_array(PcConfiguratorManager::PCC_GRAPHICS_INTERFACE_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(337);
                $reasonsMessages .= "</li>";
            }

            if (in_array(PcConfiguratorManager::PCC_RAM_TYPE_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(338);
                $reasonsMessages .= "</li>";
            } elseif (in_array(PcConfiguratorManager::PCC_RAM_COUNT_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(339);
                $reasonsMessages .= "</li>";
            }
        } else if ($component_index == PcConfiguratorManager::$PCC_COMPONENTS['case']) {
            if (in_array(PcConfiguratorManager::PCC_CASE_SIZE_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(340);
                $reasonsMessages .= "</li>";
            }
        } else if ($component_index == PcConfiguratorManager::$PCC_COMPONENTS['cpu']) {
            if (in_array(PcConfiguratorManager::PCC_MB_SOCKET_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(341);
                $reasonsMessages .= "</li>";
            }
            if (in_array(PcConfiguratorManager::PCC_COOLER_SOCKET_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(342);
                $reasonsMessages .= "</li>";
            }
        } else if ($component_index == PcConfiguratorManager::$PCC_COMPONENTS['ram']) {
            if (in_array(PcConfiguratorManager::PCC_RAM_TYPE_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(343);
                $reasonsMessages .= "</li>";
            }
            if (in_array(PcConfiguratorManager::PCC_RAM_COUNT_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(344);
                $reasonsMessages .= "</li>";
            }
        } else if ($component_index == PcConfiguratorManager::$PCC_COMPONENTS['hdd']) {
            if (in_array(PcConfiguratorManager::PCC_STORAGE_INTERFACE_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(345);
                $reasonsMessages .= "</li>";
            }
        } else if ($component_index == PcConfiguratorManager::$PCC_COMPONENTS['ssd']) {
            if (in_array(PcConfiguratorManager::PCC_STORAGE_INTERFACE_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(345/* todo ssd todo change 345 */);
                $reasonsMessages .= "</li>";
            }
        } else if ($component_index == PcConfiguratorManager::$PCC_COMPONENTS['cooler']) {
            if (in_array(PcConfiguratorManager::PCC_MB_SOCKET_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(346);
                $reasonsMessages .= "</li>";
            }
            if (in_array(PcConfiguratorManager::PCC_CPU_SOCKET_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(347);
                $reasonsMessages .= "</li>";
            }
        } else if ($component_index == PcConfiguratorManager::$PCC_COMPONENTS['opt']) {
            if (in_array(PcConfiguratorManager::PCC_STORAGE_INTERFACE_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(348);
                $reasonsMessages .= "</li>";
            }
        } else if ($component_index == PcConfiguratorManager::$PCC_COMPONENTS['graphics']) {
            if (in_array(PcConfiguratorManager::PCC_GRAPHICS_INTERFACE_COMPATIBLE_DB, $resons)) {
                $reasonsMessages .= "<li>";
                $reasonsMessages .= $this->getPhraseSpan(349);
                $reasonsMessages .= "</li>";
            }
        }
        $reasonsMessages .= "</ul>";
        return $reasonsMessages;
    }

    public function getItemAllNotCompatibleReasons($itemDto, $implode = false) {
        $ret = array();
        foreach (PcConfiguratorManager::$PCC_ALL_NOT_COMPATIBILITY_REASONS_DB_FN as $db => $fn) {
            $ffn = 'get' . ucfirst($fn);
            if ($itemDto->$ffn() === 0 || $itemDto->$ffn() === '0') {
                $ret[] = $db;
            }
        }
        return $implode === true ? implode(',', $ret) : $ret;
    }

    public function getMapper() {
        return null;
    }

}

?>