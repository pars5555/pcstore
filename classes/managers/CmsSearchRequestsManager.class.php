<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/CmsSearchRequestsMapper.class.php");

/**
 * AdminManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class CmsSearchRequestsManager extends AbstractManager {

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
		$this->mapper = CmsSearchRequestsMapper::getInstance();
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

			self::$instance = new CmsSearchRequestsManager($config, $args);
		}
		return self::$instance;
	}

	public function emptyTable() {
		return $this->mapper->emptyTable();
	}

	public function addRow($searchText, $datetime , $win_uid) {
		$dto = $this->mapper->createDto();
		$dto->setSearchText($searchText);
		$dto->setDatetime($datetime);
		$dto->setWinUid($win_uid);
		return $this->mapper->insertDto($dto);
	}

	public function getSearchStatisticsByDays($daysNumber) {
		return $this->mapper->getSearchStatisticsByDays($daysNumber);
	}
	
	public function removeOldRowsByDays($days) {
		$this->mapper->removeOldRowsByDays($days);
	}

}

?>