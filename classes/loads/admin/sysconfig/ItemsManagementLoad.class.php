<?php

require_once (CLASSES_PATH . "/loads/admin/AdminLoad.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");

class ItemsManagementLoad extends AdminLoad {

    public function load() {
        $loadPage = 0;
        if (isset($_REQUEST['load_page'])) {
            $loadPage = $_REQUEST['load_page'];
        }
        if ($loadPage == 1) {
            $this->addParam("page_loaded", 1);

            $companyManager = CompanyManager::getInstance($this->config, $this->args);
            $allCompanyDtos = $companyManager->getAllCompanies(false, false);
            $companyNames = array();
            $companyIds = array();
            foreach ($allCompanyDtos as $companyDto) {
                $companyNames[] = $companyDto->getName();
                $companyIds[] = $companyDto->getId();
            }
            $this->addParam("companyNames", $companyNames);
            $this->addParam("companyIds", $companyIds);
            $selectedCompanyId = $companyIds[0];
            if (isset($_REQUEST['selected_company_id'])) {
                $selectedCompanyId = intval($_REQUEST['selected_company_id']);
            }


            $includeHiddens = false;
            if (isset($_REQUEST['include_hiddens'])) {
                $includeHiddens = intval($_REQUEST['include_hiddens']) == 1;
            }
            $this->addParam('include_hiddens', $includeHiddens);

            $emptyModel = 0;
            if (isset($_REQUEST['empty_model'])) {
                $emptyModel = intval($_REQUEST['empty_model']);
            }
            $this->addParam('empty_model', $emptyModel);

            $emptyShortSpec = 0;
            if (isset($_REQUEST['empty_short_spec'])) {
                $emptyShortSpec = intval($_REQUEST['empty_short_spec']);
            }
            $this->addParam('empty_short_spec', $emptyShortSpec);

            $emptyFullSpec = 0;
            if (isset($_REQUEST['empty_full_spec'])) {
                $emptyFullSpec = intval($_REQUEST['empty_full_spec']);
            }
            $this->addParam('empty_full_spec', $emptyFullSpec);

            $picturesCount = 'any';
            if (isset($_REQUEST['pictures_count'])) {
                $picturesCount = strtolower($_REQUEST['pictures_count']);
            }
            $this->addParam('pictures_count', $picturesCount);
            $itemManager = ItemManager::getInstance($this->config, $this->args);
            $items = $itemManager->getItemsByAdminConditions($selectedCompanyId, $includeHiddens, $emptyModel, $emptyShortSpec, $emptyFullSpec, $picturesCount);
            $this->addParam("items", $items);
        } else {
            $this->addParam("page_loaded", 0);
        }
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/admin/sysconfig/items_management.tpl";
    }

}

?>