<?php

ini_set('display_errors', true);
set_time_limit(0);
if (!$_SERVER["DOCUMENT_ROOT"]) {
	$_SERVER = array();
	defined('__DIR__') or define('__DIR__', dirname(__FILE__));
	$_SERVER["DOCUMENT_ROOT"] = __DIR__ . "/../..";

	chdir($_SERVER["DOCUMENT_ROOT"]);
}

ini_set('memory_limit', "1G");

require_once($_SERVER["DOCUMENT_ROOT"] . "/conf/constants.php");
require_once(CLASSES_PATH . "/framework/Dispatcher.class.php");
require_once(CLASSES_PATH . "/RequestGroups.class.php");
require_once(CLASSES_PATH . "/managers/SessionManager.class.php");
require_once(CLASSES_PATH . "/loads/LoadMapper.class.php");
require_once(CLASSES_PATH . "/util/db/DBMSFactory.class.php");
$config = parse_ini_file(CONF_PATH . "/config.ini");
DBMSFactory::init($config);

require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
$itemManager = ItemManager::getInstance($config, null);
$allItems = $itemManager->selectAll();
$dir = DATA_DIR . "/images/items/";
foreach ($allItems as $itemDto) {
	$picCount = $itemDto->getpicturesCount();
	if ($picCount == 0)
		continue;
	$itemId = $itemDto->getId();
	$picIndex = 1;
	$p1 = $dir . $itemId . '_' . $picIndex . '_30_30' . '.' . 'jpg';
	$p2 = $dir . $itemId . '_' . $picIndex . '_60_60' . '.' . 'jpg';
	if (file_exists($p1)) {
		$itemDto->setImage1(base64_encode(file_get_contents($p1)));
	}
	if (file_exists($p2)) {
		$itemDto->setImage2(base64_encode(file_get_contents($p2)));
	}
	$itemManager->updateByPK($itemDto);
}
?>