<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/CmsSettingsMapper.class.php");

/**
 * OrdersManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class CmsSettingsManager extends AbstractManager {

    private $allVars;

    /**
     * @var singleton instance of class
     */
    private static $instance = null;

    /**
     * Initializes DB mappers
     *

     * @return
     */
    function __construct() {
        $this->mapper = CmsSettingsMapper::getInstance();
        $this->cacheAllVars();
    }

    /**
     * Returns an singleton instance of this class
     *

     * @return
     */
    public static function getInstance() {

        if (self::$instance == null) {
            self::$instance = new CmsSettingsManager();
        }
        return self::$instance;
    }

    public function getAllVarsArray() {
        return $this->allVars;
    }

    public function getValue($key) {
        return $this->allVars[$key];
    }

    public function saveVariableValue($key, $value) {
        $dto = $this->mapper->selectByPK($key);
        if ($dto) {
            $dto->setValue($value);
            return $this->mapper->updateByPk($dto);
        }
        return false;
    }

    private function cacheAllVars() {
        $allVarsDtos = $this->mapper->selectAll();
        foreach ($allVarsDtos as $key => $varDto) {
            $this->allVars[$varDto->getVar()] = $varDto->getValue();
        }
    }

}

?>