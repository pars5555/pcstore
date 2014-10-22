<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class CmsconfigActionsGroupAction extends AdminAction {

    public function service() {
        $action = $_REQUEST['action'];
        $itemManager = ItemManager::getInstance($this->config, $this->args);

        switch ($action) {
            case 'find_similar_items':
                $requestedItemId = $this->secure($_REQUEST['item_id']);
                $itemDto = $itemManager->selectByPK($requestedItemId);
                $allItemsMappedArray = $itemManager->getAllItemsTitleAndModelAndBrandMappedArray();
                unset($allItemsMappedArray[$itemDto->getId()]);
                $mostSimillarItemsMappedArray = $this->getItemsSimillarityPercentArrayFromList($itemDto, $allItemsMappedArray);
                $limit = 30;
                $items = array();
                $limitCounter = 0;
                foreach ($mostSimillarItemsMappedArray as $itemId => $percent) {
                    $itemDataArray = $allItemsMappedArray[$itemId];
                    $itemTitle = $itemDataArray[0];
                    $itemCompanyName = $itemDataArray[3];
                    $itemPicturesCount = $itemDataArray[4];
                    $items [] = array($itemId, $itemTitle, $itemCompanyName, $itemPicturesCount);
                    $limitCounter ++;
                    if ($limitCounter >= $limit) {
                        break;
                    }
                }
                $this->ok(array('items' => $items));
                break;
            case 'get_item_attributes':
                $itemId = $this->secure($_REQUEST['item_id']);
                $itemDto = $itemManager->selectByPK($itemId);
                $this->ok(array('item' => AbstractDto::dtoToArray($itemDto)));
                break;
            case 'copy_item_pictures':
                $source_item_id = $this->secure($_REQUEST['source_item_id']);
                $target_item_id = $this->secure($_REQUEST['target_item_id']);
                $sourceItemDto = $itemManager->selectByPK($source_item_id);
                $targetItemDto = $itemManager->selectByPK($target_item_id);
                $itemDto = $itemManager->copyItemPictures($sourceItemDto, $targetItemDto);
                $this->ok();
                break;
            case 'copy_item_short_spec':
                $source_item_id = $this->secure($_REQUEST['source_item_id']);
                $target_item_id = $this->secure($_REQUEST['target_item_id']);
                $sourceItemDto = $itemManager->selectByPK($source_item_id);
                $targetItemDto = $itemManager->selectByPK($target_item_id);
                $itemDto = $itemManager->copyItemShortDescription($sourceItemDto, $targetItemDto);
                $this->ok();
                break;
            case 'copy_item_full_spec':
                $source_item_id = $this->secure($_REQUEST['source_item_id']);
                $target_item_id = $this->secure($_REQUEST['target_item_id']);
                $sourceItemDto = $itemManager->selectByPK($source_item_id);
                $targetItemDto = $itemManager->selectByPK($target_item_id);
                $itemDto = $itemManager->copyItemFullDescription($sourceItemDto, $targetItemDto);
                $this->ok();
                break;
            case 'copy_item_model':
                $source_item_id = $this->secure($_REQUEST['source_item_id']);
                $target_item_id = $this->secure($_REQUEST['target_item_id']);
                $sourceItemDto = $itemManager->selectByPK($source_item_id);
                $targetItemDto = $itemManager->selectByPK($target_item_id);
                $itemDto = $itemManager->copyItemModel($sourceItemDto, $targetItemDto);
                $this->ok();
                break;
            default:
                break;
        }
    }

    private function getItemsSimillarityPercentArrayFromList($itemDto, $allItemsMappedArray) {
        $displayName = $itemDto->getDisplayName();
        $model = $itemDto->getModelName();
        $ret = array();
        foreach ($allItemsMappedArray as $itemId => $itemsArray) {
            $itemDisplayName = $itemsArray[0];
            $itemModel = $itemsArray[1];
            $itemBrand = $itemsArray[2];
            $simillarityPercent = $this->getSimillarityPercent($displayName, $model, $itemDisplayName, $itemModel, $itemBrand);
            $ret[$itemId] = $simillarityPercent;
        }
        arsort($ret);
        return $ret;
    }

    private function getSimillarityPercent($title1, $model1, $title2, $model2, $brand2) {
        $text1 = $title1 . ' ' . $model1;
        $words1 = $this->getWordsArray($text1);
        $text2 = $title2 . ' ' . $model2 . ' ' . $brand2;
        $words2 = $this->getWordsArray($text2);
        $matchedWordsCount = count(array_intersect($words1, $words2));
        $percent = $matchedWordsCount * 100 / count($words1);
        return $percent;
    }

    private function getWordsArray($str) {
        return preg_split('/([\s,\.;\?\!]+)/', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
    }

}

?>