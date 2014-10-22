<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/CompaniesPriceListManager.class.php");
require_once (CLASSES_PATH . "/managers/admin/DbStructureManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class TableViewLoad extends GuestLoad {

    public function load() {
        $dbStructureManager = DbStructureManager::getInstance($this->config, $this->args);
        $tablesNames = $dbStructureManager->getTablesNames();
        $tablesNamesArrayUf = self::convertToUserFriendlyNamesArray($tablesNames);
        array_unshift($tablesNames, 0);
        array_unshift($tablesNamesArrayUf, 'Select Table...');

        $table_name = $this->secure($_REQUEST['table_name']);
        if (!empty($_REQUEST['page'])) {
            $page = intval($this->secure($_REQUEST['page']));
        } else {
            $page = 1;
        }
        if (isset($_REQUEST['order_by_field_name'])) {
            $order_by_field_name = $this->secure($_REQUEST['order_by_field_name']);
        }
        if (isset($_REQUEST['order_by_asc_desc'])) {
            $order_by_asc_desc = strtolower($this->secure($_REQUEST['order_by_asc_desc']));
        }

        $searchText = "";
        if (isset($_REQUEST['search_text'])) {
            $searchText = $_REQUEST['search_text'];
        }

        if (isset($_REQUEST['search_column_name'])) {
            $searchColumnName = $_REQUEST['search_column_name'];
        }

        $table_view_per_page_options = explode(',', $this->getCmsVar('table_view_per_page_options'));
        if (!empty($_REQUEST['rows_per_page'])) {
            $rows_per_page = intval($this->secure($_REQUEST['rows_per_page']));
        } else {
            $rows_per_page = intval($table_view_per_page_options[1]);
        }
//$confirm_on_change_enable = $this->secure($_REQUEST['confirm_on_change_enable']);
        $this->addParam('table_view_per_page_options', $table_view_per_page_options);
        $this->addParam('all_table_names', $tablesNames);
        $this->addParam('all_table_names_uf', $tablesNamesArrayUf);
        $this->addParam('table_name', $table_name);
        $this->addParam('table_name_uf', self::generateUserFriendlyName($table_name));
        $this->addParam('rows_per_page', $rows_per_page);
        $this->addParam('page', $page);
        $this->addParam('search_text', $searchText);
        $this->addParam('search_column', $searchColumnName);
        $tableMapperClassName = $this->generateTableMapperClassName($table_name);
        if (!empty($tableMapperClassName)) {
            $tableMapper = call_user_func(array($tableMapperClassName, 'getInstance'));
            if ($tableMapper->getTableName() === $table_name) {
                //$tableManagerClassName = $this->generateTableManagerClassName($table_name);
                //$tableManager = & call_user_func(array($tableManagerClassName, 'getInstance'), $this->config, $this->args);
                $tableColumns = $dbStructureManager->getTableColumns($table_name);
                $tableColumnsNamesMapArray = $this->getColumnNamesArray($tableColumns);
                $tableColumnsNamesArray = array_keys($tableColumnsNamesMapArray);
                if (!isset($order_by_field_name) || !in_array($order_by_field_name, $tableColumnsNamesArray)) {
                    $order_by_field_name = '';
                }
                if (!isset($order_by_asc_desc) || ($order_by_asc_desc != 'asc' && $order_by_asc_desc != 'desc')) {
                    $order_by_asc_desc = 'asc';
                }
                $this->addParam('order_by_asc_desc', $order_by_asc_desc);
                $order_by_asc_desc_options = array('asc' => 'Ascending', 'desc' => 'Descending');
                $this->addParam('order_by_asc_desc_options', $order_by_asc_desc_options);


                $rowsCountWithoutLimit = $tableMapper->getAllByFiltersCount($searchText, $searchColumnName);
                $lastPage = intval($rowsCountWithoutLimit / $rows_per_page) + 1;
                if ($page > $lastPage) {
                    $page = $lastPage;
                }
                $selectAllByFilters = $tableMapper->selectAllByFilters(($page - 1) * $rows_per_page, $rows_per_page, $order_by_field_name, $order_by_asc_desc, $searchText, $searchColumnName);


                $tableColumnsNamesMapArrayUserFriendly = self::convertToUserFriendlyNamesArray($tableColumnsNamesMapArray);
                $this->addParam('tableColumnsNamesArray', $tableColumnsNamesMapArrayUserFriendly);
                array_unshift($tableColumnsNamesMapArrayUserFriendly, 'Default order');
                $this->addParam('tableColumnsNamesArrayForSorting', $tableColumnsNamesMapArrayUserFriendly);
                $this->addParam('table_exists', true);
                $this->addParam('order_by_field_name', $order_by_field_name);
                $this->addParam('rowDtos', AbstractDto::dtosToArray($selectAllByFilters));
                $this->addParam('tableColumns', $tableColumns);
                $this->addParam('tableColumnsNamesJoined', implode(',', $tableColumnsNamesArray));

                $this->addParam('totalRowsCount', $rowsCountWithoutLimit);
                $this->addParam('allPagesArray', range(1, $lastPage));
                $this->addParam('table_pk_name', $tableMapper->getPKFieldName());
            }
        }
    }

    public static function generateUserFriendlyName($text) {
        $arr = explode('_', $text);
        $arr1 = array_map(function($e) {
            return ucfirst($e);
        }, $arr);
        return implode(' ', $arr1);
    }

    public static function convertToUserFriendlyNamesArray($array) {
        return array_map(function($e) {
            return TableViewLoad::generateUserFriendlyName($e);
        }, $array);
    }

    private function getColumnNamesArray($tableColumns) {
        $ret = array();
        foreach ($tableColumns as $tc) {
            $ret[$tc['Field']] = $tc['Field'];
        }
        return $ret;
    }

    public function getDefaultLoads($args) {
        $loads = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/table_view.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

}

?>