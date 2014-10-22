<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/admin/DbStructureManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class TableViewMultiAction extends GuestAction {

	public function service() {
		$action = $this->secure($_REQUEST['action']);
		$table_name = $this->secure($_REQUEST['table_name']);
		$tableMapperClassName = $this->generateTableMapperClassName($table_name);
		if (empty($tableMapperClassName)) {
			$this->error(array("errText" => 'table mapper not found!'));
		}
		$tableMapper = call_user_func(array($tableMapperClassName, 'getInstance'));
		if ($tableMapper->getTableName() !== $table_name) {
			$this->error(array("errText" => 'table name confusing!'));
		}
		switch ($action) {
			case 'delete_rows':
				$pk_values = $this->secure($_REQUEST['pk_values']);
				if (empty($pk_values)) {
					$this->error(array("errText" => 'primary key missing!'));
				}
				if (empty($table_name)) {
					$this->error(array("errText" => 'table name missing!'));
				}
				$pk_values_array = explode(',', $pk_values);

				$errorsPks = array();
				foreach ($pk_values_array as $pk) {
					$res = $tableMapper->deleteByPK($pk);
					if ($res == -1) {
						$errorsPks [] = $pk;
					}
				}
				if (!empty($errorsPks)) {
					$this->error(array('errText' => "following PKs rows not removed! (", implode(', ', $errorsPks) . ")"));
				}
				$this->ok();
				break;
			case "edit_cell_value":
				$cellValue = $_REQUEST['cell_value'];
				$cellName = $this->secure($_REQUEST['cell_name']);
				$pkValue = $_REQUEST['pk_value'];
				$tableMapper->updateTextField($pkValue, $cellName, $cellValue);
				$this->ok();
				break;
			case "add_row":
				$cellValue = $_REQUEST['cell_value'];
				$cellName = $this->secure($_REQUEST['cell_name']);
				$dto = $tableMapper->createDto();
				$dto->$cellName = $cellValue;
				if ($tableMapper->insertDto($dto) !== -1) {
					$this->ok();
				} else {
					$this->error(array("errText" => 'Row is not added!'));
				}
				break;
			case "empty_table":
				$tableMapper->emptyTable();
				$this->ok();
				break;
		}
		$this->error(array('errText' => "Action not found! (" . $action . ')'));
	}

	public function getRequestGroup() {
		return RequestGroups::$guestRequest;
	}

}

?>