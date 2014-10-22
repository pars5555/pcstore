<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/RequestHistoryMapper.class.php");

/**
 * AdminManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class RequestHistoryManager extends AbstractManager {

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
		$this->mapper = RequestHistoryMapper::getInstance();
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

			self::$instance = new RequestHistoryManager($config, $args);
		}
		return self::$instance;
	}

	public function removeOldRowsByDays($days) {
		$this->mapper->removeOldRowsByDays($days);
	}

	public function addRow($userType, $userEmail, $requestUrl, $request) {
		$dto = $this->mapper->createDto();
		$dto->setUserType($userType);
		$dto->setUserEmail($userEmail);
		$dto->setRequestUrl($requestUrl);
		$dto->setRequest(json_encode($request));
		$dto->setDatetime(date('Y-m-d H:i:s'));
		return $this->mapper->insertDto($dto);
	}

	public function getCustomerRecentRequestsCount($email, $daysNumber) {
		return $this->mapper->getCustomerRecentRequestsCount($email, $daysNumber);
	}

	public function getCustomerGivenRequestRecentCountByHours($email, $hours, $requestName) {
		return intval($this->mapper->getCustomerGivenRequestRecentCountByHours($email, $hours, $requestName));
	}

	public function getAllSearchRequests($daysNumberFromToday) {
		return $this->mapper->getAllSearchRequests($daysNumberFromToday);
	}

}

?>