<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ShoppingCartLoad extends GuestLoad {

	public function load() {
		
	}

	public function getDefaultLoads($args) {
		$loads = array();
		$loadName = "CartStepInnerLoad";
		$loads["cart_step_inner"]["load"] = "loads/main/checkout/" . $loadName;
		$loads["cart_step_inner"]["args"] = array("parentLoad" => &$this);
		$loads["cart_step_inner"]["loads"] = array();
		return $loads;
	}

	public function isValidLoad($namespace, $load) {
		return true;
	}

	public function getTemplate() {
		return TEMPLATES_DIR . "/main/checkout/shopping_cart.tpl";
	}

	public function getRequestGroup() {
		return RequestGroups::$guestRequest;
	}

}

?>