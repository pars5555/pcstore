<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/util/pcc_categories_constants/CategoriesConstants.php");

/**
 * PcConfiguratorManager class is responsible for creating,
 */
class PcComponentValidatorManager extends AbstractManager {

    /**
     * @var app config
     */
    private $itemManager;

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

            self::$instance = new PcComponentValidatorManager($config, $args);
        }
        return self::$instance;
    }

    public function isComponentAvailable($itemDto) {
        return $this->itemManager->isItemVisibleAndInAvailableDate($itemDto);
    }

    public function isCase($itemId) {
        if ($itemId > 0) {
            $itemDto = $this->itemManager->selectByPK($itemId);
            if ($this->isComponentAvailable($itemDto)) {
                $catIdsStr = $itemDto->getCategoriesIds();
                return strpos($catIdsStr, ',' . CategoriesConstants::CASE_CHASSIS . ',') !== false;
            }
        }
        return false;
    }

    public function isMotherboard($itemId) {
        if ($itemId > 0) {
            $itemDto = $this->itemManager->selectByPK($itemId);
            if ($this->isComponentAvailable($itemDto)) {
                $catIdsStr = $itemDto->getCategoriesIds();
                return strpos($catIdsStr, ',' . CategoriesConstants::MOTHER_BOARD . ',') !== false;
            }
        }
        return false;
    }

    public function isRam($itemId) {
        if ($itemId > 0) {
            $itemDto = $this->itemManager->selectByPK($itemId);
            if ($this->isComponentAvailable($itemDto)) {
                $catIdsStr = $itemDto->getCategoriesIds();
                return strpos($catIdsStr, ',' . CategoriesConstants::RAM_MEMORY . ',') !== false;
            }
        }
        return false;
    }

    public function isCpu($itemId) {
        if ($itemId > 0) {
            $itemDto = $this->itemManager->selectByPK($itemId);
            if ($this->isComponentAvailable($itemDto)) {
                $catIdsStr = $itemDto->getCategoriesIds();
                return strpos($catIdsStr, ',' . CategoriesConstants::CPU_PROCESSOR . ',') !== false;
            }
        }
        return false;
    }

    public function isHdd($itemId) {
        if ($itemId > 0) {
            $itemDto = $this->itemManager->selectByPK($itemId);
            if ($this->isComponentAvailable($itemDto)) {
                $catIdsStr = $itemDto->getCategoriesIds();
                return strpos($catIdsStr, ',' . CategoriesConstants::HDD_HARD_DRIVE . ',') !== false;
            }
        }
        return false;
    }

    public function isSsd($itemId) {
        if ($itemId > 0) {
            $itemDto = $this->itemManager->selectByPK($itemId);
            if ($this->isComponentAvailable($itemDto)) {
                $catIdsStr = $itemDto->getCategoriesIds();
                return strpos($catIdsStr, ',' . CategoriesConstants::SSD_SOLID_STATE_DRIVE . ',') !== false;
            }
        }
        return false;
    }

    public function isCooler($itemId) {
        if ($itemId > 0) {
            $itemDto = $this->itemManager->selectByPK($itemId);
            if ($this->isComponentAvailable($itemDto)) {
                $catIdsStr = $itemDto->getCategoriesIds();
                return strpos($catIdsStr, ',' . CategoriesConstants::COOLER . ',') !== false;
            }
        }
        return false;
    }

    public function isMonitor($itemId) {
        if ($itemId > 0) {
            $itemDto = $this->itemManager->selectByPK($itemId);
            if ($this->isComponentAvailable($itemDto)) {
                $catIdsStr = $itemDto->getCategoriesIds();
                return strpos($catIdsStr, ',' . CategoriesConstants::MONITOR . ',') !== false;
            }
        }
        return false;
    }

    public function isOpticalDrive($itemId) {
        if ($itemId > 0) {
            $itemDto = $this->itemManager->selectByPK($itemId);
            if ($this->isComponentAvailable($itemDto)) {
                $catIdsStr = $itemDto->getCategoriesIds();
                return strpos($catIdsStr, ',' . CategoriesConstants::OPTICAL_DRIVE . ',') !== false;
            }
        }
        return false;
    }

    public function isKeyboard($itemId) {
        if ($itemId > 0) {
            $itemDto = $this->itemManager->selectByPK($itemId);
            if ($this->isComponentAvailable($itemDto)) {
                $catIdsStr = $itemDto->getCategoriesIds();
                return strpos($catIdsStr, ',' . CategoriesConstants::KEYBOARD . ',') !== false;
            }
        }
        return false;
    }

    public function isMouse($itemId) {
        if ($itemId > 0) {
            $itemDto = $this->itemManager->selectByPK($itemId);
            if ($this->isComponentAvailable($itemDto)) {
                $catIdsStr = $itemDto->getCategoriesIds();
                return strpos($catIdsStr, ',' . CategoriesConstants::MOUSE . ',') !== false;
            }
        }
        return false;
    }

    public function isSpeaker($itemId) {
        if ($itemId > 0) {
            $itemDto = $this->itemManager->selectByPK($itemId);
            if ($this->isComponentAvailable($itemDto)) {
                $catIdsStr = $itemDto->getCategoriesIds();
                return strpos($catIdsStr, ',' . CategoriesConstants::SPEAKER . ',') !== false;
            }
        }
        return false;
    }

    public function isGraphicsCard($itemId) {
        if ($itemId > 0) {
            $itemDto = $this->itemManager->selectByPK($itemId);
            if ($this->isComponentAvailable($itemDto)) {
                $catIdsStr = $itemDto->getCategoriesIds();
                return strpos($catIdsStr, ',' . CategoriesConstants::VIDEO_CARD . ',') !== false;
            }
        }
        return false;
    }

    public function isPowerSupply($itemId) {
        if ($itemId > 0) {
            $itemDto = $this->itemManager->selectByPK($itemId);
            if ($this->isComponentAvailable($itemDto)) {
                $catIdsStr = $itemDto->getCategoriesIds();
                return strpos($catIdsStr, ',' . CategoriesConstants::POWER . ',') !== false;
            }
        }
        return false;
    }

    public function getMapper() {
        return null;
    }

}

?>