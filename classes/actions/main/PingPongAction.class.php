<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/OnlineUsersManager.class.php");
require_once (CLASSES_PATH . "/managers/CustomerAlertListManager.class.php");
require_once (CLASSES_PATH . "/managers/CustomerAlertsManager.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class PingPongAction extends GuestAction {

	public function service() {


		$onlineUserManager = new OnlineUsersManager($this->config, $this->args);
		$customer = $this->getCustomer();

		if ($this->getUserLevel() !== UserGroups::$GUEST) {

			//add to online users table
			$customerEmail = $customer->getEmail();
			$newAdded = $onlineUserManager->addOnlineUser($this->getUserLevel(), $customer) > 0;

			if ($newAdded) {
				$customerMessagesAfterLoginManager = CustomerMessagesAfterLoginManager::getInstance($this->config, $this->args);
				$customerAfterLoginMessagesDtos = $customerMessagesAfterLoginManager->getCustomerMessages($customer->getEmail());
				$customerMessagesAfterLoginManager->incrementCustomerMessagesShowedCount($customerAfterLoginMessagesDtos);
				$customerAfterLoginMessagesDtosToArray = AbstractDto::dtosToArray($customerAfterLoginMessagesDtos);
			}

			//check user alerts
			$winUid = $_REQUEST['win_uid'];
			$customerAlertListManager = CustomerAlertListManager::getInstance($this->config, $this->args);
			$registeredCustomerAlertsDtosToArray = $customerAlertListManager->getRegisteredCustomerAlerts($customer, $winUid);
			
			$customerAlertsManager = CustomerAlertsManager::getInstance($this->config, $this->args);
			$customerMessagesDtos = $customerAlertsManager->getCustomerAlertsByCustomerLogin($customerEmail, $winUid);			
			$customerMessagesToPhpArray = AbstractDto::dtosToArray($customerMessagesDtos);
		} else {
			//add guest in online users table
			$guest_online_table_id = $_COOKIE['guest_online_table_id'];

			if ($guest_online_table_id) {
				$onlineUser = $onlineUserManager->selectByPK($guest_online_table_id);
				if (isset($onlineUser)) {
					$onlineUserManager->updateOnlineUserAttributes($onlineUser);
				} else {
					$newId = $onlineUserManager->addOnlineUser($this->getUserLevel(), null);
					if (isset($newId)) {
						$this->setcookie('guest_online_table_id', $newId);
					}
				}
			} else {
				$newId = $onlineUserManager->addOnlineUser($this->getUserLevel(), null);
				if (isset($newId)) {
					$this->setcookie('guest_online_table_id', $newId);
				}
			}
		}

		$jsonArr = array('status' => "ok");
		if (!empty($registeredCustomerAlertsDtosToArray)) {
			$jsonArr['alerts'] = $registeredCustomerAlertsDtosToArray;
		}
		if (!empty($customerAfterLoginMessagesDtosToArray)) {
			$jsonArr['customer_after_login_messages'] = $customerAfterLoginMessagesDtos;
		}
		if (!empty($customerMessagesToPhpArray)) {
			$jsonArr['messages'] = $customerMessagesToPhpArray;
		}
		echo json_encode($jsonArr);
		return true;
	}

	public function getRequestGroup() {
		return RequestGroups::$guestRequest;
	}

	protected function logRequest() {
		return false;
	}

}

?>