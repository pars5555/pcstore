<?php

require_once(CLASSES_PATH . "/framework/AbstractAction.class.php");

/**
 * General parent action for all XBLOG actions.
 * @author Vahagn Sookiasian
 */
abstract class Action extends AbstractAction {

	public function __construct() {
		
	}

	protected function getRedirectLoad() {
		$loadName = $_REQUEST['load'];
		if (!isset($loadName)) {
			return null;
		}
		$nameParts = split("_", $loadName);
		$result = "";
		foreach ($nameParts as $part) {
			$result.=ucfirst($part);
		}
		return $result;
	}

	public function error($errorParams = array()) {
		$errorParams['status'] = "err";
		echo json_encode($errorParams);
		exit;
	}

	public function ok($params = array()) {
		$params['status'] = "ok";
		echo json_encode($params);
		exit;
	}

}

?>