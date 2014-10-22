<?php

require_once (CLASSES_PATH . "/loads/admin/AdminLoad.class.php");
require_once (CLASSES_PATH . "/managers/CmsSettingsManager.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");

class SystemConfigLoad extends AdminLoad {

    public function load() {
        
    }

    public function getDefaultLoads($args) {
        $loads = array();
        $actionsViewLoadName = "AdminActionsViewLoad";
        $loads["admin_actions_view"]["load"] = "loads/admin/sysconfig/" . $actionsViewLoadName;
        $loads["admin_actions_view"]["args"] = array("parentLoad" => &$this);
        $loads["admin_actions_view"]["loads"] = array();

        $tableViewLoadName = "TableViewLoad";
        $loads["table_view"]["load"] = "loads/main/" . $tableViewLoadName;
        $loads["table_view"]["args"] = array("parentLoad" => &$this);
        $loads["table_view"]["loads"] = array();

        $galleryViewLoadName = "GalleryViewLoad";
        $loads["gallery_view"]["load"] = "loads/admin/sysconfig/" . $galleryViewLoadName;
        $loads["gallery_view"]["args"] = array("parentLoad" => &$this);
        $loads["gallery_view"]["loads"] = array();

        $searchStatisticsViewLoadName = "SearchStatisticsViewLoad";
        $loads["search_statistics_view"]["load"] = "loads/admin/sysconfig/" . $searchStatisticsViewLoadName;
        $loads["search_statistics_view"]["args"] = array("parentLoad" => &$this);
        $loads["search_statistics_view"]["loads"] = array();

        $adminUserManagementLoadName = "AdminUserManagementLoad";
        $loads["admin_user_management"]["load"] = "loads/admin/sysconfig/" . $adminUserManagementLoadName;
        $loads["admin_user_management"]["args"] = array("parentLoad" => &$this);
        $loads["admin_user_management"]["loads"] = array();

        $itemsManagementLoadName = "ItemsManagementLoad";
        $loads["items_management"]["load"] = "loads/admin/sysconfig/" . $itemsManagementLoadName;
        $loads["items_management"]["args"] = array("parentLoad" => &$this);
        $loads["items_management"]["loads"] = array();

        $companiesManagementLoadName = "CompaniesManagementLoad";
        $loads["companies_management"]["load"] = "loads/admin/sysconfig/" . $companiesManagementLoadName;
        $loads["companies_management"]["args"] = array("parentLoad" => &$this);
        $loads["companies_management"]["loads"] = array();

        $customerAlertsAfterLoginLoadName = "CustomerAlertsAfterLoginLoad";
        $loads["customer_alerts_after_login"]["load"] = "loads/admin/sysconfig/" . $customerAlertsAfterLoginLoadName;
        $loads["customer_alerts_after_login"]["args"] = array("parentLoad" => &$this);
        $loads["customer_alerts_after_login"]["loads"] = array();

        return $loads;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/admin/sysconfig/system_config.tpl";
    }

}

?>