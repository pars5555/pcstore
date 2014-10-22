<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/CmsSettingsManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class ChangeSystemVariablesAction extends AdminAction {

	public function service() {
		$variable_names = json_decode($_REQUEST['variable_names']);
		$variable_values = json_decode($_REQUEST['variable_values']);
		$cmsSettingsManager = CmsSettingsManager::getInstance($this->config, $this->args);
		foreach ($variable_names as $key => $varName) {
			$cmsSettingsManager->saveVariableValue($varName, $variable_values[$key]);
		}
		$jsonArr = array('status' => "ok");
		echo json_encode($jsonArr);
		return true;
	}

}

?>