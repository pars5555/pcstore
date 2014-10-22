<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/CategoryManager.class.php");
require_once (CLASSES_PATH . "/managers/AdminManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class ChangeCategoryAttributesAction extends AdminAction {

    public function service() {

        $categoryManager = new CategoryManager($this->config, $this->args);
        $categoryId = $this->secure($_REQUEST["category_id"]);
        $display_name = $this->secure($_REQUEST["display_name"]);
        $last_clickable = $this->secure($_REQUEST["last_clickable"]);

        $adminManager = new AdminManager($this->config, $this->args);
        $adminId = $this->sessionManager->getUser()->getId();
        $adminDto = $adminManager->selectByPK($adminId);
        if ($adminDto) {
            $categoryDto = $categoryManager->getCategoryById($categoryId);
            if (!$categoryDto) {
                $jsonArr = array('status' => "err", "errText" => "System Error: Category doesn't exist!");
                echo json_encode($jsonArr);
                return false;
            }
            $categoryManager->updateCategoryAttributes($categoryId, $display_name, $last_clickable);

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