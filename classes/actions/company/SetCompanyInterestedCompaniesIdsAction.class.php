<?php

require_once (CLASSES_PATH . "/actions/company/CompanyAction.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class SetCompanyInterestedCompaniesIdsAction extends CompanyAction {

	public function service() {
		$companies_ids = $this->secure($_REQUEST['companies_ids']);
		$companies_ids = explode(',', $companies_ids);
		$int_companies_ids = array();
		foreach ($companies_ids as $cid) {
			$c = intval($cid);
			if ($c > 0) {
				$int_companies_ids[] = $c;
			}
		}
		$companies_ids = implode(',', $int_companies_ids);
		$companyManager = CompanyManager::getInstance($this->config, $this->args);
		$companyManager->setInterestedCompaniesIdsForSms($this->getUserId(), $companies_ids);
		$company = $companyManager->selectByPK($this->getUserId());
		$sms_receive_phone_number = $company->getPriceUploadSmsPhoneNumber();
		if (empty($sms_receive_phone_number)) {
			$jsonArr = array('status' => "warning", "message" => $this->getPhrase(404));
			echo json_encode($jsonArr);
			return false;
		}
		$jsonArr = array('status' => "ok");
		echo json_encode($jsonArr);
		return true;
	}

}

?>