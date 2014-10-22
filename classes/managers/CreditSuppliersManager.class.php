<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/CreditSuppliersMapper.class.php");

/**
 * OrdersManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class CreditSuppliersManager extends AbstractManager {

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
		$this->mapper = CreditSuppliersMapper::getInstance();
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

			self::$instance = new CreditSuppliersManager($config, $args);
		}
		return self::$instance;
	}

	public function getAllCreditSuppliers() {
		return $this->mapper->getAllCreditSuppliers();
	}

	public function getSuppliersIdsArray($creditSuppliersDtos) {
		$ret = array();
		foreach ($creditSuppliersDtos as $csdto) {
			$ret[] = $csdto->getId();
		}
		return $ret;
	}

	public function getSuppliersDisplayNameIdsArray($creditSuppliersDtos) {
		$ret = array();
		foreach ($creditSuppliersDtos as $csdto) {
			$ret[] = $csdto->getDisplayNameId();
		}
		return $ret;
	}

	public function getCreditSuppliersInMapArrayById($creditSuppliersDtos) {
		$ret = array();
		foreach ($creditSuppliersDtos as $csdto) {
			$ret[$csdto->getId()] = $csdto;
		}
		return $ret;
	}

}

?>