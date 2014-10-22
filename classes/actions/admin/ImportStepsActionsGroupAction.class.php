<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/ImportItemsTempManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class ImportStepsActionsGroupAction extends AdminAction {

    public function service() {
        $action = $_REQUEST['action'];
        $importItemsTempManager = ImportItemsTempManager::getInstance($this->config, $this->args);
        switch ($action) {
            case 'step_1_unbind_price_row':
                $price_item_id = $_REQUEST['price_item_id'];
                $importItemsTempManager->setMatchedItemId($price_item_id, 0);
                $importItemsTempManager->updateTextField($price_item_id, 'short_spec', '');
                $importItemsTempManager->updateTextField($price_item_id, 'full_spec', '');
                $this->ok();
                break;
            case 'step_1_link_stock_item_to_price_item':
                $price_item_id = $_REQUEST['price_item_id'];
                $stock_item_id = $_REQUEST['stock_item_id'];

                $importItemsTempManager->setMatchedItemId($price_item_id, $stock_item_id);
                $itemManager = ItemManager::getInstance($this->config, $this->args);
                $stockItem = $itemManager->selectByPK($stock_item_id);
                $importItemsTempManager->updateTextField($price_item_id, 'short_spec', $stockItem->getShortDescription());
                $importItemsTempManager->updateTextField($price_item_id, 'full_spec', $stockItem->getFullDescription());
                $this->ok();
                break;
            case 'edit_cell_value':
                $cell_value = $_REQUEST['cell_value'];
                $field_name = $_REQUEST['field_name'];
                $pk_value = $_REQUEST['pk_value'];
                $importItemsTempManager->updateTextField($pk_value, $importItemsTempManager->getFieldKeyByFieldName($field_name), $cell_value);
                $dto = $importItemsTempManager->selectByPK($pk_value);
                $this->ok(array('cell_value' => $dto->$field_name));
                break;
            case 'import':
                $new_items_row_ids = $this->secure($_REQUEST['new_items_row_ids']);
                if (!empty($new_items_row_ids)) {
                    $new_items_row_ids = explode(',', $new_items_row_ids);
                } else {
                    $new_items_row_ids = array();
                }
                $changed_rows_ids = $this->secure($_REQUEST['changed_rows_ids']);
                if (!empty($changed_rows_ids)) {
                    $changed_rows_ids = explode(',', $changed_rows_ids);
                } else {
                    $changed_rows_ids = array();
                }
                list($newItemsCount, $updatedItemsCount) = $importItemsTempManager->importToItemsTable($this->getCustomerLogin(), $this->secure($_REQUEST['company_id']), $new_items_row_ids, $changed_rows_ids);
                $this->ok(array('new_items_count' => $newItemsCount, 'updated_items_count' => $updatedItemsCount));
                break;
            case 'find_similar_items':
                $searchText = $this->secure($_REQUEST['search_text']);
                $itemManager = ItemManager::getInstance($this->config, $this->args);
                $itemsDtos = $itemManager->findSimillarItems($searchText, 10);
                $dtosToJSON = AbstractDto::dtosToJSON($itemsDtos);
                $this->ok(array('items' => $dtosToJSON));
                break;
            case 'get_item_cat_spec':
                $item_id = intval($_REQUEST['item_id']);
                if ($item_id > 0) {
                    $itemManager = ItemManager::getInstance($this->config, $this->args);
                    $itemDto = $itemManager->selectByPK($item_id);
                    $this->ok(array('short_description' => $itemDto->getShortDescription(), 'full_description' => $itemDto->getFullDescription()
                        , 'categories_ids' => $itemDto->getCategoriesIds()));
                } else {
                    $this->ok(array('short_description' => '', 'full_description' => '', 'categories_ids' => ''));
                }
                break;
            case 'upload_new_item_picture':
                $row_id = intval($_REQUEST['row_id']);
                $this->uploadNewIntemPicture($row_id);
                break;

            default:
                break;
        }
    }

    private function uploadNewIntemPicture($row_id) {
        $file_name = $_FILES['item_picture']['name'];
        $tmp_name = $_FILES['item_picture']['tmp_name'];
        $pictureExt = explode(".", $file_name);
        $pictureExt = strtolower(end($pictureExt));
        $supported_file_formats = explode(',', 'jpg,png,jpeg');

        if (!in_array(strtolower($pictureExt), $supported_file_formats)) {
            $jsonArr = array('status' => "error", "errText" => 'Picture format is not supported! supported formats are jpg and png only.');
            echo "<script>var l = new parent.ngs.ImportStepsActionsGroupAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
            return false;
        }

        $dir = HTDOCS_TMP_DIR;
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        $dir = HTDOCS_TMP_DIR.'/import';
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }

        $fileName = $row_id;
        $pictureName = $fileName . '.' . $pictureExt;
        $pictureFullName = $dir . '/' . $pictureName;
        move_uploaded_file($tmp_name, $pictureFullName);


        $jsonArr = array('status' => "ok", "action" => 'upload_new_item_picture','row_id'=>$row_id, 'picture_name'=>$pictureName);
        echo "<script>var l = new parent.ngs.ImportStepsActionsGroupAction(); l.afterAction('" . json_encode($jsonArr) . "'); </script>";
        return false;
    }

}

?>