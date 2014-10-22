<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/search/ItemSearchManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class PagingLoad extends GuestLoad {

    public function load() {

        if ($this->args["current_page_number"]) {
            $current_page_number = $this->args["current_page_number"];
        }
        if ($this->args["total_items_count"]) {
            $total_items_count = $this->args["total_items_count"];
        }
        $item_search_limit_rows = $this->getCmsVar("item_search_limit_rows");
        $item_search_max_showing_pages_count = $this->getCmsVar('item_search_max_showing_pages_count');
        $this->initPaging($current_page_number, $total_items_count, $item_search_limit_rows, $item_search_max_showing_pages_count);
        $itemSearchManager = ItemSearchManager::getInstance($this->config, $this->args);
        $this->addParam('itemSearchManager', $itemSearchManager);
    }

    public function getDefaultLoads($args) {
        $loads = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/paging.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

    protected function logRequest() {
        return false;
    }

}

?>