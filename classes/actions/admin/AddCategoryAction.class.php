<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/CategoryManager.class.php");
require_once (CLASSES_PATH . "/managers/AdminManager.class.php");
require_once (CLASSES_PATH . "/managers/CategoryHierarchyManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class AddCategoryAction extends AdminAction {

	public function service() {

		$categoryManager = new CategoryManager($this->config, $this->args);
		$categoryHierarchyManager = new CategoryHierarchyManager($this->config, $this->args);
		$categoryTitle = $this->secure($_REQUEST["category_title"]);
		$parentCategoryId = $this->secure($_REQUEST["parent_category_id"]);
		$adminManager = new AdminManager($this->config, $this->args);
		$adminId = $this->sessionManager->getUser()->getId();
		$adminDto = $adminManager->selectByPK($adminId);
		if ($adminDto) {
			$sortIndex = count($categoryHierarchyManager->getCategoryChildrenIdsArray($parentCategoryId)) + 1;
			$categoryId = $categoryManager->addCategory($categoryTitle, '0', '0', '1');
			$categoryHierarchyManager->addSubCategoryToCategory($parentCategoryId, $categoryId, $sortIndex);
			$jsonArr = array('status' => "ok", "message" => "ok");
			echo json_encode($jsonArr);
			return true;
		} else {
			$jsonArr = array('status' => "err", "errText" => "System Error: You are not Admin!");
			echo json_encode($jsonArr);
			return false;
		}
	}

}

?>