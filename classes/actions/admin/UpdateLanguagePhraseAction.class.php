<?php

require_once (CLASSES_PATH . "/actions/admin/AdminAction.class.php");
require_once (CLASSES_PATH . "/managers/LanguageManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class UpdateLanguagePhraseAction extends AdminAction {

	public function service() {
		$languageManager = LanguageManager::getInstance($this->config, $this->args);
		$phrase_id = $this->secure($_REQUEST["phrase_id"]);
		$phrase_text = $_REQUEST["phrase_text"];
		$languageManager->updatePhrase($phrase_id, $phrase_text);
		$jsonArr = array('status' => "ok");
		echo json_encode($jsonArr);
		return true;
	}

}

?>