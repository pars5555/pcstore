<?php

require_once (CLASSES_PATH . "/util/PHPExcel_1.7.9_doc/Classes/PHPExcel.php");
require_once (CLASSES_PATH . "/managers/ImportPriceManager.class.php");

class ExcelUtils {

    public static function getPHPExcelObject($file) {
        if (!file_exists($file)) {
            return false;
        }
        /**  Identify the type of $inputFileName  * */
        $inputFileType = PHPExcel_IOFactory::identify($file);
        /**  Create a new Reader of the type that has been identified  * */
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        /**  Load $inputFileName to a PHPExcel Object  * */
        $objPHPExcel = $objReader->load($file);
        return $objPHPExcel;
    }

    private static function convertSheetArray($sheet) {
        if (!isset($sheet)) {
            return "";
        }
        $rowIterator = $sheet->getRowIterator();

        $ret = array();
        $rowIndex = 0;
        $mt = microtime(true);
        while ($rowIterator->valid()) {
            $rowIndex++;
            if ($rowIndex > 3000) {
                break;
            }
            $row = $rowIterator->current();
            $cellIterator = $row->getCellIterator();
            $colIndex = 0;
            $rowValuesArray = array();
            while ($cellIterator->valid()) {
                $colIndex++;
                if ($colIndex > 100) {
                    break;
                }
                $cell = $cellIterator->current();
                $cellFormatedValue = $cell->getFormattedValue();
                $rowValuesArray [] = $cellFormatedValue;
                $cellIterator->next();
            }
            $ret[] = $rowValuesArray;
            $rowIterator->next();
            if (microtime(true) - $mt > 300) {
                break;
            }
        }
        return $ret;
    }

    private static function convertSheetToText($sheet, $cellDelimiter, $rowDelimiter, $includeSheetsTitle) {
        if (!isset($sheet)) {
            return "";
        }
        $rowIterator = $sheet->getRowIterator();

        $ret = $includeSheetsTitle ? $sheet->getTitle() . $rowDelimiter : $rowDelimiter;
        $rowIndex = 0;
        $mt = microtime(true);
        while ($rowIterator->valid()) {
            $rowIndex++;
            if ($rowIndex > 3000) {
                break;
            }
            $row = $rowIterator->current();
            $rowDimension = $sheet->getRowDimension($row->getRowIndex());
            $rowVisible = $rowDimension->getVisible();
            if (!$rowVisible) {
                $rowIterator->next();
                continue;
            }
            $cellIterator = $row->getCellIterator();
            $colIndex = 0;
            while ($cellIterator->valid()) {
                $colIndex++;
                if ($colIndex > 100) {
                    break;
                }
                $cell = $cellIterator->current();
                $cellFormatedValue = $cell->getFormattedValue();
                $ret .= $cellFormatedValue . $cellDelimiter;
                $cellIterator->next();
            }
            $ret .= $rowDelimiter;
            $rowIterator->next();
            if (microtime(true) - $mt > 300) {
                break;
            }
        }
        return $ret;
    }

    public static function convertToText($pHPExcelObject, $cellDelimiter = "\t", $rowDelimiter = "\r\n", $includeSheetsTitle = true) {
        if (!$pHPExcelObject) {
            return "";
        }
        $sheetCount = $pHPExcelObject->getSheetCount();
        $ret = "";
        for ($sheetIndex = 0; $sheetIndex < $sheetCount; $sheetIndex++) {
            $sheet = $pHPExcelObject->getSheet($sheetIndex);
            $ret .= self::convertSheetToText($sheet, $cellDelimiter, $rowDelimiter, $includeSheetsTitle) . $rowDelimiter . $rowDelimiter;
        }
        return $ret;
    }

    public static function convertPriceToValuesArray($pHPExcelObject) {
        if (!$pHPExcelObject) {
            return "";
        }
        
        $importPriceManager = ImportPriceManager::getInstance(null, null);
        $ret = array();                
        $sheetCount = $pHPExcelObject->getSheetCount();
        for ($sheetIndex = 0; $sheetIndex < $sheetCount; $sheetIndex++) {
            $ret[$sheetIndex] = $importPriceManager->loadCompanyPrice($pHPExcelObject, $sheetIndex);
        }
        return $ret;
    }

}

?>