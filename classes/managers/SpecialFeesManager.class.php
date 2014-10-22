<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/SpecialFeesMapper.class.php");

/**
 * UserManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class SpecialFeesManager extends AbstractManager {

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
        $this->mapper = SpecialFeesMapper::getInstance();
        $this->config = $config;
        $this->args = $args;
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
            self::$instance = new SpecialFeesManager($config, $args);
        }
        return self::$instance;
    }

    /**
     * $ids can be array or ids string joind with comma
     */
    public function getSpecialFeesByIds($special_fees_ids) {
        if (is_array($special_fees_ids)) {
            //TODO check if ids are exists in Table
            $special_fees_ids = implode(',', $special_fees_ids);
        } else {
            //TODO check if ids are exists in Table
        }
        $specialFeesDtos = $this->mapper->getSpecialFeesByIds($special_fees_ids);
        if (isset($specialFeesDtos) and count($specialFeesDtos) > 0) {
            return $specialFeesDtos;
        } else {
            return null;
        }
    }

    public function getSpecialFeesTotalPrice($specialFeesDtos) {
        $ret = 0;
        foreach ($specialFeesDtos as $key => $specialFeeDto) {
            $ret += (int) $specialFeeDto->getPrice();
        }
        return (int) $ret;
    }

    public function getPcBuildFee() {
        $dto = $this->selectByPK(1);
        assert(isset($dto));
        return $dto;
    }

    public function getShippingCost($regionKey) {
        $regionKey = strtolower($regionKey);
        switch ($regionKey) {
            case
            'aragatsotn' :
                break;
            case 'ararat' :
                break;
            case 'armavir' :
                break;
            case 'gegharkunik' :
                break;
            case 'kotayk' :
                break;
            case 'lori' :
                break;
            case 'shirak' :
                break;
            case 'syunik' :
                break;
            case 'tavush' :
                break;
            case 'vayots dzor' :
                break;
            case 'yerevan' :
                $dto = $this->selectByPK(2);
                break;
        }
        return $dto;
    }

}

?>