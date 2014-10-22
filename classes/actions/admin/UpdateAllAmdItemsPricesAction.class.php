<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class UpdateAllAmdItemsPricesAction extends AdminAction {

	public function service() {

		$itemManager = ItemManager::getInstance($this->config, $this->args);
		$itemManager->UpdateAllAmdItemsPrices();

		$jsonArr = array('status' => "ok");
		echo json_encode($jsonArr);
		return true;
	}

}

?>