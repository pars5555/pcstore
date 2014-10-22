<?php

ini_set('display_errors', false);
if (!$_SERVER["DOCUMENT_ROOT"]) {
    $_SERVER = array();
    defined('__DIR__') or define('__DIR__', dirname(__FILE__));
    $_SERVER["DOCUMENT_ROOT"] = __DIR__ . "/../..";

    chdir($_SERVER["DOCUMENT_ROOT"]);
}
ini_set('memory_limit', '1G');

require_once($_SERVER["DOCUMENT_ROOT"] . "/conf/constants.php");
require_once(CLASSES_PATH . "/framework/Dispatcher.class.php");
require_once(CLASSES_PATH . "/loads/LoadMapper.class.php");
require_once(CLASSES_PATH . "/util/db/DBMSFactory.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/CompaniesPriceListManager.class.php");

$config = parse_ini_file(CONF_PATH . "/config.ini");
DBMSFactory::init($config);

$companiesPriceListManager = CompaniesPriceListManager::getInstance($config, null);
$companyIds = $argv[1];
$priceIndex = 0;
if (isset($argv[2])) {
    $priceIndex = $argv[2];
}
$companyIdsArray = explode(',', $companyIds);
foreach ($companyIdsArray as $cid) {
    $companiesPriceListManager->cachePriceInTables($cid, $priceIndex);
}
?>