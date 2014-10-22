<?php

require_once (CLASSES_PATH . "/loads/admin/AdminLoad.class.php");
require_once (CLASSES_PATH . "/managers/CmsSearchRequestsManager.class.php");

class SearchStatisticsViewLoad extends AdminLoad {

	public static $days_variant = array(1 => "1 day", 2 => "2 days", 3 => "3 days", 7 => "1 week", 14 => "2 weeks", 30 => "1 month", 60 => "2 month", 90 => "3 month");

	public function load() {

		$cmsSearchRequestsManager = CmsSearchRequestsManager::getInstance($this->config, $this->args);
		$days_number = 1;
		if (!empty($_REQUEST['days_number'])) {
			$days_number = intval($this->secure($_REQUEST['days_number']));
		}

		$searchStatisticsByDays = $cmsSearchRequestsManager->getSearchStatisticsByDays($days_number);
		$groupedSearchStatistics = array();
		foreach ($searchStatisticsByDays as $dto) {
			$searchCount = intval($dto->getSearchCount());
			$searchText = strval($dto->getSearchText());
			if (!array_key_exists($dto->getSearchText(), $groupedSearchStatistics)) {
				$groupedSearchStatistics[$searchText] = $searchCount;
			} else {
				$groupedSearchStatistics[$searchText] +=$searchCount;
			}
		}
		arsort($groupedSearchStatistics, SORT_NUMERIC);
		
		$this->addParam("groupedSearchStatistics", $groupedSearchStatistics);
		$this->addParam("days_variant", self::$days_variant);
		$this->addParam("daysNumber", $days_number);
	}

	public function getTemplate() {
		return TEMPLATES_DIR . "/admin/sysconfig/search_statistics_view.tpl";
	}

}

?>