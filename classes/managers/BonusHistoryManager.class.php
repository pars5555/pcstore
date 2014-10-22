<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/BonusHistoryMapper.class.php");

/**
 * AdminManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class BonusHistoryManager extends AbstractManager {

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
		$this->mapper = BonusHistoryMapper::getInstance();
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

			self::$instance = new BonusHistoryManager($config, $args);
		}
		return self::$instance;
	}

	public function addRow($userId, $points, $description) {
		$dto = $this->mapper->createDto();
		$dto->setUserId($userId);
		$dto->setPoints($points);
		$dto->setDescription($description);
		$dto->setDatetime(date('Y-m-d H:i:s'));
		return $this->mapper->insertDto($dto);
	}

	public function getUserBonusesAfterGivenDatetime($userId, $datetime) {
		return $this->mapper->getUserBonusesAfterGivenDatetime($userId, $datetime);
	}

}

?>