<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
abstract class PccSelectComponentLoad extends GuestLoad {

    public function load() {
        $pccm = PcConfiguratorManager::getInstance($this->config, $this->args);
        $pccm->manageComponentLoadRequestBeforeLoad();
        $allSelectedComponentsIdsArray = $pccm->getSelectedItemsIdsFromRequest();
        $this->addParam("allSelectedComponentsIdsArray", $allSelectedComponentsIdsArray);
        $itemManager = ItemManager::getInstance($this->config, $this->args);
        $userLevel = $this->getUserLevel();
        $requiredCategoriesFormulasArray = $this->getRequiredCategoriesFormulasArray();
        $neededCategoriesIdsAndOrFormulaArray = $this->getNeededCategoriesIdsAndOrFormulaArray();

        $selected_component_item_id = $this->getSelectedComponentItemId();

        $search_text = null;
        if (isset($_REQUEST['search_component'])) {
            $search_text = $this->secure($_REQUEST['search_component']);
        }
        list($offset, $limit) = $this->getItemsOffsetAndLimit($search_text);
        $itemsDtos = $itemManager->getPccItemsByCategoryFormula($this->getUserId(), $userLevel, $requiredCategoriesFormulasArray, $neededCategoriesIdsAndOrFormulaArray, $offset, $limit, $selected_component_item_id, $search_text);
        $this->addParam("itemsDtos", $itemsDtos);
        $this->addParam("itemManager", $itemManager);
        $this->addParam('componentLoad', $this);
        $ci = $this->getComponentTypeIndex();
        $this->addParam('componentName', $pccm->getComponentKeywordByIndex($ci));
        $this->addParam('componentIndex', $ci);
        $this->addParam('pcmm', PccMessagesManager::getInstance($this->config, $this->args));
        $this->addParam('tab_header_info_text', $this->getTabHeaderInfoText());
    }

    public function getItemsOffsetAndLimit($search_text) {

        $vipCustomer = 0;
        if ($this->getUserLevel() === UserGroups::$USER) {
            $customer = $this->getCustomer();
            $userManager = UserManager::getInstance($this->config, $this->args);
            $vipCustomer = $userManager->isVipAndVipEnabled($customer);
        }
        if ($vipCustomer) {
            $pc_configurator_discount = floatval($this->getCmsVar('vip_pc_configurator_discount'));
        } else {
            $pc_configurator_discount = floatval($this->getCmsVar('pc_configurator_discount'));
        }
        $this->addParam("pc_configurator_discount", $pc_configurator_discount);

        $itemManager = ItemManager::getInstance($this->config, $this->args);
        $load_more = false;
        $offset = 0;
        if (isset($_REQUEST['load_more'])) {
            $load_more = true;
            if (isset($_REQUEST['loaded_items_count'])) {
                $offset = intval($this->secure($_REQUEST['loaded_items_count']));
            }
        }
        $this->addParam('load_more', $load_more);

        if (isset($_REQUEST['take_in_count_search_text']) && $_REQUEST['take_in_count_search_text'] == 1) {
            $this->addParam('only_update_component_container', 1);
        }

        $limit = 0;
        if (!$load_more) {
            $limit = $this->getSelectedSameItemsCount();
        }
        $limit += intval($this->getCmsVar('pcc_load_more_count'));
        $itemsTotalCount = intval($itemManager->getPccItemsByCategoryFormulaCount($this->getRequiredCategoriesFormulasArray(), $search_text));
        if ($offset + $limit < $itemsTotalCount) {
            $this->addParam("load_more_reach_to_end", 'false');
        } else {
            $this->addParam("load_more_reach_to_end", 'true');
        }
        return array($offset, $limit);
    }

    public abstract function getRequiredCategoriesFormulasArray();

    public abstract function getNeededCategoriesIdsAndOrFormulaArray();

    public abstract function getSelectedSameItemsCount();

    public abstract function getTabHeaderInfoText();

    public abstract function getComponentTypeIndex();

    public abstract function getSelectedComponentItemId();

    protected function logRequest() {
        return false;
    }

    public function getDefaultLoads($args) {
        $loads = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/pc_configurator/pcc_select_component.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

}

?>