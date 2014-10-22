<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/CustomerLocalEmailsManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class RestoreCustomerLocalEmailsAction extends GuestAction {

	public function service() {

		$email_ids = $_REQUEST['email_ids'];
		$email_ids_array = explode(',', $email_ids);
		$customerLocalEmailsManager = CustomerLocalEmailsManager::getInstance($this->config, $this->args);
		$customerLocalEmailsManager->restoreEmails($this->getCustomerLogin(), $email_ids_array);
		$jsonArr = array('status' => "ok");
		echo json_encode($jsonArr);
		return true;
	}

	public function getRequestGroup() {
		return RequestGroups::$userCompanyRequest;
	}

}

?>