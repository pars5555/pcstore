<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/CategoryManager.class.php");
require_once (CLASSES_PATH . "/managers/AdminManager.class.php");
require_once (CLASSES_PATH . "/managers/CategoryHierarchyManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class RemoveCategoryAction extends AdminAction {

    public function service() {

        $categoryManager = CategoryManager::getInstance($this->config, $this->args);
        $categoryHierarchyManager = CategoryHierarchyManager::getInstance($this->config, $this->args);
        $categoryId = $this->secure($_REQUEST["category_id"]);

        $adminManager = AdminManager::getInstance($this->config, $this->args);
        $adminId = $this->sessionManager->getUser()->getId();
        $adminDto = $adminManager->selectByPK($adminId);

        if ($adminDto) {
            if ($categoryHierarchyManager->hasCategoryChildren($categoryId)) {
                $jsonArr = array('status' => "err", "errText" => "You can only remove 'Leaf' categories!");
                echo json_encode($jsonArr);
                return false;
            }
            $categoryManager->deleteByPK($categoryId);
            $categoryHierarchyManager->removeCategoryHierarchyByChildCategoryID($categoryId);
            //todo remove category name from items table `categories_names` field.  
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