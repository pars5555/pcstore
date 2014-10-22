<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/CompanyItemCheckListMapper.class.php");

/**
 * CustomerAlertListManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class CompanyItemCheckListManager extends AbstractManager {

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
		$this->mapper = CompanyItemCheckListMapper::getInstance();
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

			self::$instance = new CompanyItemCheckListManager($config, $args);
		}
		return self::$instance;
	}

	public function setResponseSentToCustomerFieldValue($id, $value) {
		return $this->mapper->updateTextField($id, 'response_sent_to_customer_uid', $value);
	}

	public function setSentToCompanyFieldValue($id, $value) {
		return $this->mapper->updateTextField($id, 'sent_to_company_uid', $value);
	}

	public function getCompanyQuestionsFromCustomers($companyId, $winUid) {
		return $this->mapper->getCompanyQuestionsFromCustomers($companyId, $winUid);
	}

	public function getCustomerSentQuestionsResponses($customerEmail, $winUid) {
		return $this->mapper->getCustomerSentQuestionsResponses($customerEmail, $winUid);
	}

	public function getSentCompanyItemCheckDtos($companyId, $itemId) {
		return $this->mapper->getSentCompanyItemCheckDtos($companyId, $itemId);
	}

	public function setCompanyItemAvailability($companyId, $itemId, $itemAvailability) {
		$this->mapper->setCompanyItemAvailability($companyId, $itemId, $itemAvailability);
	}

	public function setCompanyRespondedAlertsAlreadySentToCustomer($customerEmail) {
		return $this->mapper->setCompanyRespondedAlertsAlreadySentToCustomer($customerEmail);
	}

	public function isItemCheckingStartedByAnotherUser($itemId) {
		$dtos = $this->mapper->isItemCheckingStartedByAnotherUser($itemId);
		return !empty($dtos);
	}

	public function isDuplicateItemChecking($itemId, $fromEmail) {
		$dtos = $this->mapper->isDuplicateItemChecking($itemId, $fromEmail);
		return !empty($dtos);
	}

	public function removeOldRowsBySeconds($timoutSeconds) {
		return $this->mapper->removeOldRowsBySeconds($timoutSeconds);
	}

	public function addCompanyItemCheckList($companyId, $itemId, $fromEmail, $fromName, $fromCustomerType, $keepAnonymous) {
		$isDuplicateItemChecking = $this->isDuplicateItemChecking($itemId, $fromEmail);
		if ($isDuplicateItemChecking) {
			return false;
		}
		$isItemCheckingStartedByAnotherUser = $this->isItemCheckingStartedByAnotherUser($itemId);
		$dto = $this->mapper->createDto();
		$dto->setCompanyId($companyId);
		$dto->setItemId($itemId);
		$dto->setFromEmail($fromEmail);
		$dto->setFromName($fromName);
		$dto->setFromCustomerType($fromCustomerType);
		$dto->setkeepAnonymous($keepAnonymous);
		$dto->setSentToCompany($isItemCheckingStartedByAnotherUser ? 1 : 0);
		$dto->setTimestamp(date('Y-m-d H:i:s'));
		return $this->mapper->insertDto($dto);
	}

}

?>