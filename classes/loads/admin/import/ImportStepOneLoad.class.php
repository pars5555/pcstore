<?php

require_once (CLASSES_PATH . "/loads/admin/AdminLoad.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/ImportPriceManager.class.php");
require_once (CLASSES_PATH . "/managers/ImportItemsTempManager.class.php");
require_once (CLASSES_PATH . "/managers/PriceTranslationsManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ImportStepOneLoad extends AdminLoad {

    public function load() {
        ini_set('max_execution_time', '120');

        $itemManager = ItemManager::getInstance($this->config, $this->args);
        $importItemsTempManager = ImportItemsTempManager::getInstance($this->config, $this->args);
        $importPriceManager = ImportPriceManager::getInstance($this->config, $this->args);

        $company_id = $_REQUEST['company_id'];
        $selectSheetIndex = intval($_REQUEST['sheet_index']);
        $selectPriceIndex = intval($_REQUEST['price_index']);
        if (!isset($_REQUEST['aceptable_simillarity_percent'])) {
            $acepableItemSimillarityPercent = 50;
        } else {
            $acepableItemSimillarityPercent = intval($_REQUEST['aceptable_simillarity_percent']);
        }
        $this->addParam('company_id', $company_id);
        $this->addParam('acepableItemSimillarityPercent', $acepableItemSimillarityPercent);
        $this->addParam('acepableItemSimillarityPercentOptions', array('20', '25', '30', '35', '40', '45', '50', '55', '60', '65', '70', '75', '80', '85', '90'));
        $customerLogin = $this->getCustomerLogin();
        $used_columns_indexes_array = explode(',', $_REQUEST['used_columns_indexes']);
        $this->addParam('used_columns_indexes_array', implode(',', $used_columns_indexes_array));
        if (!(isset($_REQUEST['dont_recalculate']) && $_REQUEST['dont_recalculate'] == 1)) {
            $values = $importPriceManager->loadCompanyPriceFromCache($company_id, $selectPriceIndex, $selectSheetIndex);

            $importItemsTempManager->deleteCustomerRows($customerLogin);
            $used_columns_ids_array = explode(',', $_REQUEST['used_columns_ids']);


            $selected_rows_index = array();
            //following strlen is important to accept "0" value
            if (isset($_REQUEST['selected_rows_index']) && strlen($_REQUEST['selected_rows_index']) > 0) {
                $selected_rows_index = explode(',', $_REQUEST['selected_rows_index']);
            }

//            $itemNamesArray = array();
//            foreach ($values as $index => $row) {
//                if (!in_array($index, $selected_rows_index)) {
//                    continue;
//                }
//                $nameColumn = "";
//                foreach ($used_columns_ids_array as $key => $cellId) {
//                    if ($used_columns_indexes_array[$key] == 2) {
//                        $nameColumn .= ($row[$cellId] . ' ');
//                    }
//                }
//                $itemNamesArray[$index] = $nameColumn;
//            }
//            $keys = array_keys($itemNamesArray);
//            $itemNames = implode('!@<>?$#|', $itemNamesArray);
//            $itemNames = LanguageManager::translateItemDisplayNameNonEnglishWordsToEnglish($itemNames);
//            $itemNames = preg_replace('/[^(\x20-\x7F)]*/', '', $itemNames);
//            if (count($keys) > 0) {
//                $itemNamesArray = explode('!@<>?$#|', $itemNames);
//            } else {
//                $itemNamesArray = array();
//            }
//            $itemNamesArray = array_combine($keys, $itemNamesArray);

            $brand_model_name_concat_method = $_REQUEST['brand_model_name_concat_method'];
            $priceTranslationsManager = PriceTranslationsManager::getInstance($this->config, $this->args);
            foreach ($values as $index => $row) {
                if (!in_array($index, $selected_rows_index)) {
                    continue;
                }
                $nameColumn = "";
                foreach ($used_columns_ids_array as $key => $cellId) {
                    if ($used_columns_indexes_array[$key] == 1) {
                        $modelColumn = $row[$cellId];
                    }
                    if ($used_columns_indexes_array[$key] == 2) {

                        $nameColumn .= $priceTranslationsManager->translateItemDisplayNameNonEnglishWordsToEnglish($row[$cellId]) . ' ';
                        $nameColumn = preg_replace('/[^(\x20-\x7F)]*/', '', $nameColumn);
                    }

                    if ($used_columns_indexes_array[$key] == 3) {
                        $dealerPriceColumn = $row[$cellId];
                    }
                    if ($used_columns_indexes_array[$key] == 4) {
                        $dealerPriceAmdColumn = $row[$cellId];
                    }
                    if ($used_columns_indexes_array[$key] == 5) {
                        $vatPriceColumn = $row[$cellId];
                    }
                    if ($used_columns_indexes_array[$key] == 6) {
                        $vatPriceAmdColumn = $row[$cellId];
                    }
                    if ($used_columns_indexes_array[$key] == 7) {
                        $warrantyMonthColumn = $row[$cellId];
                    }
                    if ($used_columns_indexes_array[$key] == 8) {
                        $warrantyYearColumn = $row[$cellId];
                    }
                    if ($used_columns_indexes_array[$key] == 9) {
                        $brandColumn = $row[$cellId];
                    }
                }
                if ($brand_model_name_concat_method === 'bmn') {
                    $nameColumn = $brandColumn . ' ' . $modelColumn . ' ' . $nameColumn;
                } elseif ($brand_model_name_concat_method === 'bn') {
                    $nameColumn = $brandColumn . ' ' . $nameColumn;
                } elseif ($brand_model_name_concat_method === 'mn') {
                    $nameColumn = $modelColumn . ' ' . $nameColumn;
                }
                $importItemsTempManager->addRow($customerLogin, $modelColumn, $nameColumn, $dealerPriceColumn, $dealerPriceAmdColumn, $vatPriceColumn, $vatPriceAmdColumn, $warrantyMonthColumn, $warrantyYearColumn, $brandColumn);
            }
        }

        $priceRowsDtos = $importItemsTempManager->getUserCurrentRows($customerLogin);
        $columnNames = $importPriceManager->getColumnNamesMap($used_columns_indexes_array);

        $this->addParam('columnNames', $columnNames);


        $companyAllItems = $itemManager->getCompanyItems($company_id, true);
        $convertDtosArrayToArrayMapById = $this->convertDtosArrayToArrayMapById($companyAllItems);
        $this->addParam('stockItemsDtosMappedByIds', $convertDtosArrayToArrayMapById);

        //$t = microtime(true);
        if (!(isset($_REQUEST['dont_recalculate']) && $_REQUEST['dont_recalculate'] == 1)) {
            $stockAndPriceItemsMatchingMap = $this->getStockAndPriceItemsMatchingMap($priceRowsDtos, $companyAllItems, $acepableItemSimillarityPercent);
            $cycleCount = 0;
            while ($this->getStockAndPriceItemsMatchingMap($priceRowsDtos, $companyAllItems, $acepableItemSimillarityPercent, true, $stockAndPriceItemsMatchingMap)) {
                $cycleCount++;
            }
            foreach ($stockAndPriceItemsMatchingMap as $stockItemId => $priceItemIdAndSimilarPercentPairArray) {
                $rowId = $priceItemIdAndSimilarPercentPairArray[0];
                $importItemsTempManager->setMatchedItemId($rowId, $stockItemId);
                $shortSpec = $convertDtosArrayToArrayMapById[$stockItemId]->getShortDescription();
                $fullSpec = $convertDtosArrayToArrayMapById[$stockItemId]->getFullDescription();
                $importItemsTempManager->updateTextField($rowId, 'short_spec', $shortSpec);
                $importItemsTempManager->updateTextField($rowId, 'full_spec', $fullSpec);
            }
        }
        $priceRowsDtos = $importItemsTempManager->getUserCurrentRows($customerLogin);


        //getting metched stock items ids
        $matchedStockItemIdsArray = array();
        foreach ($priceRowsDtos as $priceRowDto) {
            $matchedItemId = $priceRowDto->getMatchedItemId();
            if (intval($matchedItemId) > 0) {
                $matchedStockItemIdsArray [] = intval($matchedItemId);
            }
        }

        //getting company items which are not matched to any item in price table
        $unmatchedCompanyStockItems = array();
        foreach ($companyAllItems as $stockItemDto) {
            $itemId = intval($stockItemDto->getId());
            if (!in_array($itemId, $matchedStockItemIdsArray)) {
                $unmatchedCompanyStockItems [$itemId] = $stockItemDto;
            }
        }
        $this->addParam('unmatchedCompanyItems', $unmatchedCompanyStockItems);


        $this->addParam('priceRowsDtos', $priceRowsDtos);

        $this->addParam('matched_price_items_count', count($stockAndPriceItemsMatchingMap));
        $this->addParam('unmatched_price_items_count', count($priceRowsDtos) - count($stockAndPriceItemsMatchingMap));
    }

    private function convertDtosArrayToArrayMapById($dtos) {
        $ret = array();
        foreach ($dtos as $dto) {
            $ret[intval($dto->getId())] = $dto;
        }
        return $ret;
    }

    private function isPriceItemAlreadyMatechedToStockItem($priceItemId, $matchItemsIdsMap) {
        $ret = false;
        foreach ($matchItemsIdsMap as $stId => $pair) {
            if ($pair[0] == $priceItemId) {
                $ret = true;
                break;
            }
        }
        return $ret;
    }

    private function getStockAndPriceItemsMatchingMap($priceItemsDtos, $stockItemsDtos, $acepableItemSimillarityPercent, $checkDuplication = false, &$matchItemsIdsMap = null) {
        if (!isset($matchItemsIdsMap)) {
            $matchItemsIdsMap = array();
        }
        $atLeastOneMatched = false;
        foreach ($priceItemsDtos as $priceItemDto) {

            if ($checkDuplication && $this->isPriceItemAlreadyMatechedToStockItem($priceItemDto->getId(), $matchItemsIdsMap)) {
                //checks if item already matched then skip this item
                continue;
            }
            $maxSimilarItemPercent = 0;
            foreach ($stockItemsDtos as $stockItemDto) {
                if ($checkDuplication && array_key_exists($stockItemDto->getId(), $matchItemsIdsMap)) {
                    continue;
                }
                $similarItemsPercent = ImportPriceManager::getSimilarItemsPercent($stockItemDto, $priceItemDto);
                if ($similarItemsPercent > $maxSimilarItemPercent) {
                    $maxSimilarItemPercent = $similarItemsPercent;
                    $maxSimilarStockItemDto = $stockItemDto;
                }
            }

            if ($maxSimilarItemPercent > $acepableItemSimillarityPercent) {
                if (!array_key_exists($maxSimilarStockItemDto->getId(), $matchItemsIdsMap)) {
                    $atLeastOneMatched = true;
                    $matchItemsIdsMap[$maxSimilarStockItemDto->getId()] = array($priceItemDto->getId(), $maxSimilarItemPercent);
                } else {
                    if ($maxSimilarItemPercent > $matchItemsIdsMap[$maxSimilarStockItemDto->getId()][1]) {
                        $atLeastOneMatched = true;
                        $matchItemsIdsMap[$maxSimilarStockItemDto->getId()] = array($priceItemDto->getId(), $maxSimilarItemPercent);
                    }
                }
            }
        }
        if ($checkDuplication) {
            return $atLeastOneMatched;
        }
        return $matchItemsIdsMap;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/admin/import/import_step_one.tpl";
    }

}

?>