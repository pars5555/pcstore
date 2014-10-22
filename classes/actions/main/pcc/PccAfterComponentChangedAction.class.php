<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/managers/pcc_managers/PcConfiguratorManager.class.php");
require_once (CLASSES_PATH . "/managers/LanguageManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class PccAfterComponentChangedAction extends GuestAction {

	public function service() {
		$pccm = PcConfiguratorManager::getInstance($this->config, $this->args);
		$retFieldsArray = array();
		$retFieldsArray['selected_components_ids'] = $pccm->getRequestComponentSelectedComponents();
		$retFieldsArray['required_components_ids'] = $pccm->getRequestComponentRequiredComponents($this->sessionManager->getUser());
		echo json_encode($retFieldsArray);
		return true;
	}

	public function getRequestGroup() {
		return RequestGroups::$guestRequest;
	}

	protected function logRequest() {
		return false;
	}

}

?>