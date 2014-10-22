<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");

/**
 * @author Vahagn Sookiasian
 */
class CompanyGroupActionsAction extends GuestAction {

    public function service() {
        $action = $_REQUEST['action'];
        $companyId = $this->getUserId();
        $userLevel = $this->getUserLevel();
        switch ($action) {
            case 'delete_attachment':
                $fileName = $this->secure($_REQUEST['file_name']);
                if ($userLevel == UserGroups::$COMPANY) {
                    $fileFullPath = HTDOCS_TMP_DIR_ATTACHMENTS . "/companies/" . $companyId . '/' . $fileName;
                } else {
                    $fileFullPath = HTDOCS_TMP_DIR_ATTACHMENTS . "/service_companies/" . $companyId . '/' . $fileName;
                }
                if (file_exists($fileFullPath)) {
                    unlink($fileFullPath);
                    $this->ok();
                } else {
                    $this->error(array('message' => 'File not found!!!'));
                }
        }
        $this->error(array('message' => 'Unknown action!'));
    }

    public function getRequestGroup() {
        return RequestGroups::$companyAndServiceCompanyRequest;
    }

}

?>