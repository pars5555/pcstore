<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/util/PHPExcel_1.7.9_doc/Classes/PHPExcel.php");

/**
 * CategoryManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class ImportPriceManager extends AbstractManager {

    public static $COLUMNS = array(0 => "None", 1 => "Model", 2 => "Name",
        3 => "Dealer $", 4 => "Dealer Դր", 5 => "VAT $",
        6 => "VAT Դր", 7 => "Warranty Months", 8 => "Warranty Year", 9 => "Brand");

    /**
     * @var app config
     */
    private $config;

    /**
     * @var passed arguemnts
     */
    private $args;

    /**
     * @var singleton instance of class
     */
    private static $instance = null;
    private $columnsNamesMap;
    private $itemModelColumnName;
    private $dealerPriceColumnName;
    private $vatPriceColumnName;
    private $dealerPriceAmdColumnName;
    private $vatPriceAmdColumnName;

    /**
     * Initializes DB mappers
     *
     * @param object $config
     * @param object $args
     * @return
     */
    function __construct($config, $args) {
        $this->config = $config;
        $this->args = $args;
    }

    /**
     * Returns an singleton instance of this class
     *
     * @param object $config
     * @param object $args
     * @return
     */
    public static function getInstance($config, $args) {

        if (self::$instance == null) {

            self::$instance = new ImportPriceManager($config, $args);
        }
        return self::$instance;
    }

    public function getMapper() {
        return null;
    }

    /**
     * 
     * @param type $companyId
     * @param int $priceIndex 0 base
     * @return boolean
     */
    public function getCompanyPriceSheetsNames($objPHPExcel) {

        $sheetNames = $objPHPExcel->getSheetNames();
        $sheetStates = array();
        foreach ($sheetNames as $sheetIndex => $sheetName) {
            $sheetStates[] = $objPHPExcel->getSheet($sheetIndex)->getSheetState() === PHPExcel_Worksheet::SHEETSTATE_VISIBLE ? 1 : 0;
        }
        return array($sheetNames, $sheetStates);
    }

    public function getCompanyPriceSheetsNamesFromCache($companyId, $priceIndex) {
        $priceSheetsManager = PriceSheetsManager::getInstance($this->config, $this->args);
        $dtos = $priceSheetsManager->selectByField('company_id', $companyId);
        $sheetNames = array();
        foreach ($dtos as $dto) {
            if (intval($dto->getPriceIndex()) == $priceIndex) {
                $sheetNames[] = $dto->getSheetTitle();
            }
        }
        return $sheetNames;
    }

    public function loadCompanyPrice($objPHPExcel, $sheetIndex) {
        $sheet = $objPHPExcel->getSheet($sheetIndex);
        $rowIterator = $sheet->getRowIterator();
        $values = array();
        $mt = microtime(true);
        while ($rowIterator->valid()) {
            $row = $rowIterator->current();
            if ($row->getRowIndex() > 3000) {
                break;
            }
            if (microtime(true) - $mt > 300) {
                break;
            }
            $rowDimension = $sheet->getRowDimension($row->getRowIndex());
            $rowVisible = $rowDimension->getVisible();
            if (!$rowVisible) {
                $rowIterator->next();
                continue;
            }
            $cellIterator = $row->getCellIterator();
            $values[$row->getRowIndex()] = array('A' => '', 'B' => '', 'C' => '', 'D' => '', 'E' => '', 'F' => '', 'G' => '',
                'H' => '', 'I' => '', 'J' => '', 'K' => '', 'L' => '', 'M' => '', 'N' => '', 'O' => '', 'P' => '', 'Q' => ''
                , 'R' => '', 'S' => '', 'T' => '', 'U' => '', 'V' => '', 'W' => '', 'X' => '', 'Y' => '', 'Z' => '');
            while ($cellIterator->valid()) {
                $cell = $cellIterator->current();
                $cellFormatedValue = trim($cell->getFormattedValue());
                $values[$cell->getRow()][$cell->getColumn()] = $cellFormatedValue;
                $values[$cell->getRow()] = array_intersect_key($values[$cell->getRow()], array('A' => '', 'B' => '', 'C' => '', 'D' => '', 'E' => '', 'F' => '', 'G' => '',
                    'H' => '', 'I' => '', 'J' => '', 'K' => '', 'L' => '', 'M' => '', 'N' => '', 'O' => '', 'P' => ''
                    , 'Q' => '', 'R' => '', 'S' => '', 'T' => '', 'U' => '', 'V' => '', 'W' => '', 'X' => '', 'Y' => '', 'Z' => ''));
                ksort($values[$cell->getRow()]);
                $cellIterator->next();
            }
            $rowIterator->next();
        }

        $values = self::clearEmptyColumns($values);
        $values = self::clearOneColumnItemRows($values);
        $values = self::clearNonPriceIncludeRows($values);
        if (!empty($values)) {
            $this->parsePrice($values);
        } else {
            return false;
        }
        return $values;
    }

    public function loadCompanyPriceFromCache($companyId, $selectPriceIndex, $sheetIndex) {
        $priceValuesManager = PriceValuesManager::getInstance($this->config, $this->args);
        $values = $priceValuesManager->getCompanyPriceSheetValues($companyId, $selectPriceIndex, $sheetIndex);
        if (!empty($values)) {
            $this->parsePrice($values);
        } else {
            return false;
        }
        return $values;
    }

    /**
     * 
     * @param type $itemDto
     * @param type $priceRowDto
     * @return int [0-100] percent.
     * 80% for the item title+model matching, 20% for price matching	
     */
    public static function getSimilarItemsPercent($itemDto, $priceRowDto) {

        $titleModelriority = 0.8;
        $pricePriority = 0.2;

        $ret = 0;
        $stockItemModel = $itemDto->getModel();
        $priceItemModel = $priceRowDto->getModel();
        $stockItemName = $itemDto->getDisplayName();
        $priceItemName = $priceRowDto->getDisplayName();

        if (!empty($priceItemModel)) {
            $priceItemModel = $priceItemModel;
        }
        if (!empty($stockItemModel)) {
            $stockItemModel = $stockItemModel;
        }

        if (!empty($stockItemModel) && strlen($stockItemModel) > 2) {
            //case TWO: model is exists in stock 
            $stockItemName .= (' ' . $stockItemModel);
        }
        if (!empty($priceItemModel) && strlen($priceItemModel) > 2) {
            //case THREE: model is exists in price
            $priceItemName .= (' ' . $priceItemModel);
        }

        $similarItemNamesPercent = self::similarItemNamesPercent($stockItemName, $priceItemName);
        $ret+=($similarItemNamesPercent * $titleModelriority);

        if ($itemDto->getDealerPriceAmd() > 0) {
            $stockItemDealerPriceAmd = $itemDto->getDealerPriceAmd();
            $priceItemDealerPriceAmd = $priceRowDto->getDealerPriceAmd();
            if (max($priceItemDealerPriceAmd, $stockItemDealerPriceAmd) == 0) {
                $priceSimilarPercent = 0;
            } else {
                $priceSimilarPercent = min($priceItemDealerPriceAmd, $stockItemDealerPriceAmd) * 100 / max($priceItemDealerPriceAmd, $stockItemDealerPriceAmd);
            }
        } else {
            $stockItemDealerPrice = $itemDto->getDealerPrice();
            $priceItemDealerPrice = $priceRowDto->getDealerPrice();
            if (max($priceItemDealerPrice, $stockItemDealerPrice) == 0) {
                $priceSimilarPercent = 0;
            } else {
                $priceSimilarPercent = min($priceItemDealerPrice, $stockItemDealerPrice) * 100 / max($priceItemDealerPrice, $stockItemDealerPrice);
            }
        }
        if ($priceSimilarPercent > 80) {
            $ret+= ($priceSimilarPercent * $pricePriority);
        }
        return $ret;
    }

    private static function similarItemNamesPercent($name1, $name2) {
        //removing double whitespaces
        $name1 = preg_replace('/\s\s+/', ' ', trim($name1));
        $name2 = preg_replace('/\s\s+/', ' ', trim($name2));
        $name1Words = explode(' ', $name1);
        $name2Words = explode(' ', $name2);
        $name1Words = self::trimArrayElementsAndRemoveSmallWords($name1Words, " \t\n\r\0/\\-'\"", 1);
        $name2Words = self::trimArrayElementsAndRemoveSmallWords($name2Words, " \t\n\r\0/\\-'\"", 1);
        $intersectedWords = array_intersect($name1Words, $name2Words);
        return count($intersectedWords) * 100 / max(count($name1Words), count($name2Words));
    }

    private static function trimArrayElementsAndRemoveSmallWords($wordsArray, $trimCharecters, $smallWordCharsCount) {
        $nameWordsTrimed = array();
        foreach ($wordsArray as $word) {
            $trimmedWord = trim($word, $trimCharecters);
            if (strlen($trimmedWord) > $smallWordCharsCount) {
                $nameWordsTrimed[] = $trimmedWord;
            }
        }
        return $nameWordsTrimed;
    }

    private function parsePrice($values) {
        if (empty($values)) {
            return false;
        }
        $this->columnsNamesMap = self::getColumnsNamesCountMap($values);
        ksort($this->columnsNamesMap);
        $average = intval(array_sum($this->columnsNamesMap) / count($this->columnsNamesMap));
        list($this->itemModelColumnName, $this->itemNameColumnName) = $this->findItemModelNameColumnNames($values, $average);
        list($this->dealerPriceColumnName, $this->dealerPriceAmdColumnName, $this->vatPriceColumnName, $this->vatPriceAmdColumnName) = $this->findDealerPriceVatPriceColumnNames($values, $average, $this->itemNameColumnName);
    }

    public function getColumnsNamesMap() {
        return $this->columnsNamesMap;
    }

    public function getItemModelColumnName() {
        return $this->itemModelColumnName;
    }

    public function getItemNameColumnName() {
        return $this->itemNameColumnName;
    }

    public function getDealerPriceColumnName() {
        return $this->dealerPriceColumnName;
    }

    public function getVatPriceColumnName() {
        return $this->vatPriceColumnName;
    }

    public function getDealerPriceAmdColumnName() {
        return $this->dealerPriceAmdColumnName;
    }

    public function getVatPriceAmdColumnName() {
        return $this->vatPriceAmdColumnName;
    }

    private static function clearEmptyColumns($values) {
        $columnItemsCount = array();
        foreach ($values as $row) {
            foreach ($row as $colName => $cellValue) {
                $cellValue = trim($cellValue);
                if (!array_key_exists($colName, $columnItemsCount)) {
                    $columnItemsCount[$colName] = 0;
                }
                if (!empty($cellValue)) {
                    if (array_key_exists($colName, $columnItemsCount)) {
                        $columnItemsCount[$colName] ++;
                    }
                }
            }
        }
        $ret = array();
        foreach ($values as $row) {
            foreach ($columnItemsCount as $columnName => $count) {
                if ($count == 0) {
                    unset($row[$columnName]);
                }
            }
            $ret[] = $row;
        }
        return $ret;
    }

    private static function clearNonPriceIncludeRows($values) {
        $rows = array();
        foreach ($values as $row) {
            $hasPriceColumn = false;
            $columnCount = 0;
            $includeThisRow = false;
            foreach ($row as $colName => $cellValue) {
                $cellValue = trim($cellValue);
                $similarToPriceField = self::isSimilarToPriceField($cellValue);
                if ($similarToPriceField) {
                    $hasPriceColumn = true;
                }
                if (!empty($cellValue) > 0) {
                    $columnCount++;
                }
                if ($hasPriceColumn && $columnCount > 1) {
                    $includeThisRow = true;
                    break;
                }
            }
            if ($includeThisRow) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    private static function clearOneColumnItemRows($values) {
        $rows = array();
        foreach ($values as $row) {
            if (count($row) > 1) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    /**
     * 
     * @param type $values
     * @return array('A'=>rowsCount, 'B'=>rowsCount,....)
     */
    private static function getColumnsNamesCountMap($values) {
        $columns = array();
        foreach ($values as $row) {
            $clumnNamesArray = array_keys($row);
            foreach ($clumnNamesArray as $columnName) {
                if (array_key_exists($columnName, $columns)) {
                    $columns[$columnName] ++;
                } else {
                    $columns[$columnName] = 1;
                }
            }
        }
        return $columns;
    }

    private static function isSimilarToPriceField($value) {
        $value = preg_replace('/\s+/', '', $value);
        $clearCellValue = str_replace(array('$', 'dollar', 'Dollar', 'Dram', 'dram', 'Drams', 'Dram', 'ԴՐ', 'Դր', 'դր', 'դր.', 'ԴՐ.', 'Դր.', 'AMD', 'amd', 'Amd', ',', '.'), '', $value);
        $valueFloat = floatval($clearCellValue);
        return ($valueFloat > 0 && preg_match('/^-?(?:\d+|\d*\.\d+)$/', $clearCellValue));
    }

    public static function convertCurrencyToDollar($currency) {
        $currency = preg_replace('/\s+/', '', $currency);
        $clearCellValue = str_replace(array('$', 'dollar', 'Dollar', 'Dram', 'dram', 'Drams', 'Dram', 'ԴՐ', 'Դր', 'դր', 'դր.', 'ԴՐ.', 'Դր.', 'AMD', 'amd', 'Amd'), '', $currency);
        if (strpos($clearCellValue, ',') !== false && strpos($clearCellValue, '.') !== false) {
            $clearCellValue = str_replace(',', '', $clearCellValue);
        }
        return floatval($clearCellValue);
    }

    public static function convertCurrencyToAmd($currency) {
        $currency = preg_replace('/\s+/', '', $currency);
        $clearCellValue = str_replace(array('$', 'dollar', 'Dollar', 'Dram', 'dram', 'Drams', 'Dram', 'ԴՐ', 'Դր', 'դր', 'դր.', 'ԴՐ.', 'Դր.', 'AMD', 'amd', 'Amd', ',', '.'), '', $currency);
        return intval($clearCellValue);
    }

    public static function convertWarrantyMonthsFieldToWarrantyMonths($warrantyMonths) {
        $warrantyMonths = preg_replace("/[^0-9]/", "", $warrantyMonths);
        return intval($warrantyMonths);
    }

    public static function convertWarrantyYearsFieldToWarrantyMonths($warrantyYeras) {
        $warrantyYeras = preg_replace("/[^0-9]/", "", $warrantyYeras);
        return intval($warrantyYeras) * 12;
    }

    public static function getColumnNamesMap($used_columns_indexes_array) {
        $columnNames = array();
        if (in_array(1, $used_columns_indexes_array)) {
            $columnNames['model'] = "Model";
        }
        if (in_array(2, $used_columns_indexes_array)) {
            $columnNames['displayName'] = "Title";
        }
        if (in_array(3, $used_columns_indexes_array)) {
            $columnNames['dealerPrice'] = "$";
        }
        if (in_array(4, $used_columns_indexes_array)) {
            $columnNames['dealerPriceAmd'] = "Դր";
        }
        if (in_array(5, $used_columns_indexes_array)) {
            $columnNames['vatPrice'] = "VAT $";
        }
        if (in_array(6, $used_columns_indexes_array)) {
            $columnNames['vatPriceAmd'] = "VAT Դր";
        }
        if (in_array(7, $used_columns_indexes_array) || in_array(8, $used_columns_indexes_array)) {
            $columnNames['warrantyMonths'] = "Warranty";
        }
        if (in_array(9, $used_columns_indexes_array)) {
            $columnNames['brand'] = "Brand";
        }
        return $columnNames;
    }

    /**
     * 
     * @param type $values
     * @param type $averageRows
     * @param type $itemNameColName
     * @return type array($dealerPriceColumnName,$dealerPriceAmdColumnName, $vatPriceColumnName,$vatPriceAmdColumnName)
     */
    private static function findDealerPriceVatPriceColumnNames($values, $averageRows, $itemNameColName) {
        $priceCellProbabilitiesMap = array();
        foreach ($values as $row) {
            foreach ($row as $columnName => $cellValue) {
                $similarToPriceField = self::isSimilarToPriceField($cellValue);
                if ($similarToPriceField) {
                    if (array_key_exists($columnName, $priceCellProbabilitiesMap)) {
                        $priceCellProbabilitiesMap[$columnName] ++;
                    } else {
                        $priceCellProbabilitiesMap[$columnName] = 1;
                    }
                }
            }
        }

        ksort($priceCellProbabilitiesMap);
        $itemDealerPriceColName = null;
        foreach ($priceCellProbabilitiesMap as $colName => $count) {
            if ($count > intval($averageRows * 0.8) && $colName > $itemNameColName) {
                $itemDealerPriceColName = $colName;
                break;
            }
        }
        $itemVatPriceColName = null;
        foreach ($priceCellProbabilitiesMap as $colName => $count) {
            if ($count > intval($averageRows * 0.2) && $colName > $itemDealerPriceColName) {
                $itemVatPriceColName = $colName;
                break;
            }
        }
        $ret = array();
        if ($itemDealerPriceColName) {
            $priceColumnValuesAverage = self::getPriceColumnValuesAverage($values, $itemDealerPriceColName);
            if ($priceColumnValuesAverage > 2000) {
                $ret [] = null;
                $ret [] = $itemDealerPriceColName;
            } else {
                $ret [] = $itemDealerPriceColName;
                $ret [] = null;
            }
        }
        if ($itemVatPriceColName) {
            $priceColumnValuesAverage = self::getPriceColumnValuesAverage($values, $itemVatPriceColName);
            if ($priceColumnValuesAverage > 2000) {
                $ret [] = null;
                $ret [] = $itemVatPriceColName;
            } else {
                $ret [] = $itemVatPriceColName;
                $ret [] = null;
            }
        }
        return $ret;
    }

    private static function findItemModelNameColumnNames($values, $averageRowsCount) {
        $averageRowsCount = $averageRowsCount * 0.8;
        $itemNameCellProbabilitiesMap = array();
        foreach ($values as $row) {
            foreach ($row as $columnName => $cellValue) {
                $cellValue = trim($cellValue);
                if (empty($cellValue)) {
                    continue;
                }
                $similarToPriceField = self::isSimilarToPriceField($cellValue);
                if (!$similarToPriceField) {
                    if (array_key_exists($columnName, $itemNameCellProbabilitiesMap)) {
                        $itemNameCellProbabilitiesMap[$columnName] ++;
                    } else {
                        $itemNameCellProbabilitiesMap[$columnName] = 1;
                    }
                }
            }
        }
        ksort($itemNameCellProbabilitiesMap);
        $itemNameModelColumnNames = array();
        foreach ($itemNameCellProbabilitiesMap as $colName => $count) {
            if ($count > $averageRowsCount) {
                $itemNameModelColumnNames[] = $colName;
            }
        }
        if (empty($itemNameModelColumnNames)) {
            return null;
        }
        if (count($itemNameModelColumnNames) === 1) {
            return array(null, $itemNameModelColumnNames[0]);
        }
        if (count($itemNameModelColumnNames) === 2) {
            if (self::getColumnAverageValuesLength($values, $itemNameModelColumnNames[0]) < self::getColumnAverageValuesLength($values, $itemNameModelColumnNames[1])) {
                return array($itemNameModelColumnNames[0], $itemNameModelColumnNames[1]);
            } else {
                return array($itemNameModelColumnNames[1], $itemNameModelColumnNames[0]);
            }
        }
    }

    private static function getColumnAverageValuesLength($values, $colName) {
        $ret = 0;
        $count = 0;
        foreach ($values as $row) {
            if (!empty($row[$colName])) {
                $ret += strlen($row[$colName]);
                $count++;
            }
        }
        return $ret / $count;
    }

    private static function getPriceColumnValuesAverage($values, $colName) {
        $ret = 0;
        $count = 0;
        foreach ($values as $row) {
            if (!empty($row[$colName])) {
                $ret += intval($row[$colName]);
                $count++;
            }
        }
        return $count > 0 ? intval($ret / $count) : 0;
    }

}

?>