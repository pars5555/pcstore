<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/ItemWarrantiesManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class SoundOnAction extends GuestAction {

	public function service() {
		$sound_on = $_REQUEST['on'] == 1 ? 1 : 0;
		if ($this->getUserLevel() != UserGroups::$GUEST) {
			$customerId = $this->getUserId();
			switch ($this->getUserLevel()) {
				case UserGroups::$USER:
					$userManager = UserManager::getInstance($this->config, $this->args);
					$userManager->enableSound($customerId, $sound_on);
					break;
				case UserGroups::$COMPANY:
					$companyManager = CompanyManager::getInstance($this->config, $this->args);
					$companyManager->enableSound($customerId, $sound_on);
					break;
				case UserGroups::$ADMIN:
					$adminManager = AdminManager::getInstance($this->config, $this->args);
					$adminManager->enableSound($customerId, $sound_on);

					break;
			}
			$this->ok();
		}
	}

	public function getRequestGroup() {
		return RequestGroups::$guestRequest;
	}

}

?>