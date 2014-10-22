<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class ResetCompanyItemsIndexesAction extends AdminAction {

	public function service() {
		$companyId = $_REQUEST['company_id'];
		$itemManager = ItemManager::getInstance($this->config, $this->args);
		$itemManager->resetCompanyItemsIndexes($companyId);
		$jsonArr = array('status' => "ok");
		echo json_encode($jsonArr);
		return false;
	}

}

?>