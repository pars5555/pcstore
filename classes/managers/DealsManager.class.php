<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/DealsMapper.class.php");

/**
 * AdminManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class DealsManager extends AbstractManager {

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
		$this->mapper = DealsMapper::getInstance();
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

			self::$instance = new DealsManager($config, $args);
		}
		return self::$instance;
	}

	public function getTodayDeal() {
		return $this->mapper->getDateDailyDeal(date('Y-m-d'));
	}

	public function getLightingDeals() {
		return $this->mapper->getDateTimeLightingDeals(date('Y-m-d H:i:s'));
	}

	public function getDealsByPromoCode($promoCode) {
		return $this->mapper->getDealsByPromoCode($promoCode);
	}

}

?>