<?php

require_once (CLASSES_PATH . "/loads/admin/AdminLoad.class.php");
require_once (CLASSES_PATH . "/managers/NewslettersManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ManageNewslettersLoad extends AdminLoad {

	public function load() {
		$newslettersManager = NewslettersManager::getInstance($this->config, $this->args);
		$allNewsletters = $newslettersManager->getAllNewslettersMap();
		$this->addParam('all_newsletters', $allNewsletters);
		$newslettersIds = array_keys($allNewsletters);
		$this->addParam('selected_newsletter_id', $newslettersIds[0]);
	}

	public function getTemplate() {
		return TEMPLATES_DIR . "/admin/newsletters/manage_newsletters.tpl";
	}

}

?>