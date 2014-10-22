<?php

require_once (CLASSES_PATH . "/actions/company/CompanyAction.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/SentSmsManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class ConfirmSmsNumberAction extends CompanyAction {

	public function service() {
		if (isset($_REQUEST['number'])) {
			$number = $this->secure($_REQUEST['number']);

			$sentSmsManager = SentSmsManager::getInstance($this->config, $this->args);
			$number = SentSmsManager::getValidArmenianNumber($number);
			if ($number == null) {
				$jsonArr = array('status' => "err", "errText" => "Invalid cell phone number!", 'number_valid' => "false");
				echo json_encode($jsonArr);
				return false;
			}

			$company = $this->getCustomer();
			$lastSmsValidationCode = substr(uniqid(rand(), true), 0, 6);
			$companyManager = CompanyManager::getInstance($this->config, $this->args);
			$companyManager->setLastSmsValidationCode($company->getId(), $lastSmsValidationCode);
			$sentSmsManager->sendSmsToArmenia($number, $lastSmsValidationCode);
			$jsonArr = array('status' => "ok", 'number_valid' => "true");
			echo json_encode($jsonArr);
			return true;
		} elseif (isset($_REQUEST['code'])) {
			$code = $this->secure($_REQUEST['code']);
			$company = $this->getCustomer();
			$c = $company->getLastSmsValidationCode();
			if ($code == $c) {
				$jsonArr = array('status' => "ok", 'code_valid' => "true");
				echo json_encode($jsonArr);
				return true;
			} else {
				$jsonArr = array('status' => "err", "errText" => "Invalid sms code!", 'code_valid' => "false");
				echo json_encode($jsonArr);
				return false;
			}
		}
	}

}

?>