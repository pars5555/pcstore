<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/CustomerCartManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class ChangeUserProfileAction extends GuestAction {

	private $userManager;

	public function service() {
		$this->userManager = new UserManager($this->config, $this->args);
		$email = $this->getCustomer()->getEmail();
		$custDto = $this->getCustomer();
		
		$name = $this->secure($_REQUEST["name"]);
		$lname = $this->secure($_REQUEST["lname"]);
		$change_pass = $this->secure($_REQUEST["change_pass"]);
		$new_pass = $this->secure($_REQUEST["new_pass"]);
		$repeat_new_pass = $this->secure($_REQUEST["repeat_new_pass"]);
		$phone1 = $this->secure($_REQUEST["phone1"]);
		$phone2 = $this->secure($_REQUEST["phone2"]);
		$phone3 = $this->secure($_REQUEST["phone3"]);
		$address = $this->secure($_REQUEST["address"]);
		$region = $this->secure($_REQUEST["region"]);
		$enable_vip = intval($this->secure($_REQUEST["enable_vip"]));
		$cart_items_count = null;
		if ($enable_vip != $custDto->getEnableVip()) {
			$customerCartManager = new CustomerCartManager($this->config, $this->args);
			$customerCartManager->emptyCustomerCart($email);
			$cart_items_count = $customerCartManager->getCustomerCartTotalCount($email);
		}
		$validFields = $this->validateUserProfileFields($name, $lname, $change_pass, $new_pass, $repeat_new_pass, $phone1, $phone2, $phone3, $address);

		if ($validFields === 'ok') {

			$userId = $this->getUserId();
			$this->userManager->updateUserProfileFieldsById($userId, $name, $lname, $change_pass, $new_pass, $phone1, $phone2, $phone3, $address, $region, $enable_vip);

			$jsonArr = array('status' => "ok");
			if (isset($cart_items_count)) {
				$jsonArr['cart_items_count'] = $cart_items_count;
			}
			echo json_encode($jsonArr);
			return true;
		} else {
			$jsonArr = array('status' => "err", "errText" => $validFields);
			echo json_encode($jsonArr);
			return false;
		}
	}

	public function validateUserProfileFields($name, $lname, $change_pass, $new_pass, $repeat_new_pass, $phone1, $phone2, $phone3, $address) {

		if ($change_pass) {
			if (!$this->userManager->checkPassword($new_pass))
				return 'Invalid password';
			if ($new_pass != $repeat_new_pass)
				return 'New passwords don\'t matched';
		}
		if ($phone1 != null && (!strpos(',', $phone1) === false)) {
			return 'Phone number can not contain comma charecter';
		}
		if ($phone2 != null && (!strpos(',', $phone2) === false)) {
			return 'Phone number can not contain comma charecter';
		}
		if ($phone3 != null && (!strpos(',', $phone3) === false)) {
			return 'Phone number can not contain comma charecter';
		}
		return 'ok';
	}

	public function getRequestGroup() {
		return RequestGroups::$userRequest;
	}

}

?>