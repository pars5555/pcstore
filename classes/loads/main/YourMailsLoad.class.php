<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/CustomerLocalEmailsManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class YourMailsLoad extends GuestLoad {

	private $folder;

	public function load() {
		$this->addParam("mailsNavigationBarWidth", 200);
		switch ($_REQUEST['folder']) {
			case 'trash':
			case 'sent':
			case 'inbox':
				$this->folder = $_REQUEST['folder'];
				break;
			default:
				$this->folder = 'inbox';
				break;
		}
		$this->addParam("active_folder_to_show", $this->folder);

		//unread email count
		$customerLocalEmailsManager = CustomerLocalEmailsManager::getInstance($this->config, $this->args);
		$customer = $this->getCustomer();
		$customerEmail = $customer->getEmail();
		$unreadCount = $customerLocalEmailsManager->getCustomerInboxUnreadEmailsCountCustomerEmail($customerEmail);
		$this->addParam("unread_mails_count", $unreadCount);
	}

	public function getDefaultLoads($args) {
		$loads = array();
		if (!empty($this->folder)) {
			$loadName = ucfirst($this->folder) . "Load";
			$loads["mails_main_bar"]["load"] = "loads/main/mails/" . $loadName;
			$loads["mails_main_bar"]["args"] = array("parentLoad" => &$this);
			$loads["mails_main_bar"]["loads"] = array();
		}
		return $loads;
	}

	public function isValidLoad($namespace, $load) {
		return true;
	}

	public function getTemplate() {
		return TEMPLATES_DIR . "/main/your_mails.tpl";
	}

	public function getRequestGroup() {
		return RequestGroups::$userCompanyRequest;
	}

}

?>