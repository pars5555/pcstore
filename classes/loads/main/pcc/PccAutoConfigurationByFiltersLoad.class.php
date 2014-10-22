<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/pcc_managers/PcAutoConfiguratorManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class PccAutoConfigurationByFiltersLoad extends GuestLoad {

	public function load() {
		
	}

	public function getDefaultLoads($args) {
		$loads = array();
		return $loads;
	}

	public function isValidLoad($namespace, $load) {
		return true;
	}

	public function getTemplate() {
		return TEMPLATES_DIR . "/main/pc_configurator/pcc_auto_configuration_by_filters.tpl";
	}

	public function getRequestGroup() {
		return RequestGroups::$guestRequest;
	}

	protected function logRequest() {
		return false;
	}

}

?>