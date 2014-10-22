<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class HideCompanyItemsAction extends AdminAction {

	public function service() {
		$companyId = $_REQUEST['company_id'];
		$itemManager = ItemManager::getInstance($this->config, $this->args);
		$itemManager->hideCompanyItems($companyId);
		$jsonArr = array('status' => "ok");
		echo json_encode($jsonArr);
		return false;
	}

}

?>