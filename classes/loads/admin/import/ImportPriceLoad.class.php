<?php

require_once (CLASSES_PATH . "/loads/admin/AdminLoad.class.php");
require_once (CLASSES_PATH . "/managers/ImportPriceManager.class.php");
require_once (CLASSES_PATH . "/managers/CompaniesPriceListManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ImportPriceLoad extends AdminLoad {

    public function load() {
        ini_set('max_execution_time', '120');
        ini_set('memory_limit', "1G");
        $importPriceManager = ImportPriceManager::getInstance($this->config, $this->args);
        $company_id = $_REQUEST['company_id'];


        $companiesPriceListManager = CompaniesPriceListManager::getInstance($this->config, $this->args);
        $companyLastPrices = $companiesPriceListManager->getCompanyLastPrices($company_id);
        $selectPriceIndex = 0;
        if (isset($_REQUEST['price_index'])) {
            $selectPriceIndex = intval($_REQUEST['price_index']);
        }
        $companyPriceNames = array();
        foreach ($companyLastPrices as $priceIndex => $priceDto) {
            $companyPriceNames[$priceIndex] = 'Price' . ($priceIndex + 1);
        }
        $this->addParam('price_names', $companyPriceNames);
        $this->addParam('selected_price_index', $selectPriceIndex);


        $companyPriceSheetsNames = $importPriceManager->getCompanyPriceSheetsNamesFromCache($company_id, $selectPriceIndex);
        $this->addParam('price_sheets_names', $companyPriceSheetsNames);
        $selectSheetIndex = 0;
        if (isset($_REQUEST['sheet_index'])) {
            $selectSheetIndex = intval($_REQUEST['sheet_index']);
        }
        $this->addParam('selected_sheet_index', $selectSheetIndex);


        $selected_rows_index = array();
        if (isset($_REQUEST['selected_rows_index']) && strlen($_REQUEST['selected_rows_index']) > 0) {
            $selected_rows_index = explode(',', $_REQUEST['selected_rows_index']);
        }
        $this->addParam('selected_rows_index', $selected_rows_index);


        $values = $importPriceManager->loadCompanyPriceFromCache($company_id, $selectPriceIndex, $selectSheetIndex);
        if (!$values) {
            $this->addParam('priceNotFound', true);
            return false;
        }
        $this->addParam('priceNotFound', false);
        /* foreach ($values as $rowKey => $row) {
          foreach ($row as $cellKey => $cellValue) {
          echo '[' . $rowKey . '][' . $cellKey . ']=' . $cellValue . ', ';
          }
          echo '<br>';
          }exit; */

        $this->addParam('priceColumnOptions', ImportPriceManager::$COLUMNS);
        $this->addParam('allColumns', array_keys($importPriceManager->getColumnsNamesMap()));
        $this->addParam('valuesByRows', $values);
        $itemModelColumnName = $importPriceManager->getItemModelColumnName();
        if (isset($itemModelColumnName)) {
            $this->addParam('modelColumnName', $itemModelColumnName);
        }

        $itemNameColumnName = $importPriceManager->getItemNameColumnName();
        $this->addParam('itemNameColumnName', $itemNameColumnName);


        $dealerPriceColumnName = $importPriceManager->getDealerPriceColumnName();
        if (isset($dealerPriceColumnName)) {
            $this->addParam('dealerPriceColumnName', $dealerPriceColumnName);
        }
        $dealerPriceAmdColumnName = $importPriceManager->getDealerPriceAmdColumnName();
        if (isset($dealerPriceAmdColumnName)) {
            $this->addParam('dealerPriceAmdColumnName', $dealerPriceAmdColumnName);
        }
        $vatPriceColumnName = $importPriceManager->getVatPriceColumnName();
        if (isset($vatPriceColumnName)) {
            $this->addParam('vatPriceColumnName', $vatPriceColumnName);
        }
        $vatPriceAmdColumnName = $importPriceManager->getVatPriceAmdColumnName();
        if (isset($vatPriceAmdColumnName)) {
            $this->addParam('vatPriceAmdColumnName', $vatPriceAmdColumnName);
        }
    }

    /* public function getColumnsArrays($values, $colNames) {
      $ret = array();
      foreach ($colNames as $colName) {
      foreach ($values as $row) {
      if (array_key_exists($colName, $row)) {
      $ret[$colName][] = $row[$colName];
      } else {
      $ret[$colName][] = null;
      }
      }
      }
      return $ret;
      } */

    public function getTemplate() {
        return TEMPLATES_DIR . "/admin/import/import_price.tpl";
    }

}

?>