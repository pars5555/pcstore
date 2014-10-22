<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/CategoryHierarchyManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class ChangeCategoryOrderAction extends AdminAction {

	public function service() {

		$categoryHierarchyManager = new CategoryHierarchyManager($this->config, $this->args);
		$move_up = $this->secure($_REQUEST["move_up"]);
		$categoryId = $this->secure($_REQUEST["category_id"]);

		if ($move_up == '1') {
			$categoryHierarchyManager->MoveCategoryOrderUp($categoryId);
		} else {
			$categoryHierarchyManager->MoveCategoryOrderDown($categoryId);
		}
		$jsonArr = array('status' => "ok", "message" => "ok");
		echo json_encode($jsonArr);
		return true;
	}

}

?>