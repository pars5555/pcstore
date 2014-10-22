<?php

//ini_set('display_errors', true);
//error_reporting(E_ALL);
if (!$_SERVER["DOCUMENT_ROOT"]) {

    $_SERVER = array();
    defined('__DIR__') or define('__DIR__', dirname(__FILE__));
    $_SERVER["DOCUMENT_ROOT"] = __DIR__ . "/../..";

    chdir($_SERVER["DOCUMENT_ROOT"]);
}
ini_set('memory_limit', '3G');

require_once($_SERVER["DOCUMENT_ROOT"] . "/conf/constants.php");
require_once(CLASSES_PATH . "/framework/Dispatcher.class.php");
require_once(CLASSES_PATH . "/loads/LoadMapper.class.php");
require_once(CLASSES_PATH . "/util/db/DBMSFactory.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");

$config = parse_ini_file(CONF_PATH . "/config.ini");
DBMSFactory::init($config);

$itemManager = ItemManager::getInstance($config, null);
$allItems = $itemManager->selectAll();
foreach ($allItems as $itemDto) {
    $itemForOrderDto = $itemManager-> getItemWithCustomerPrice($itemDto->getId());
    $customerItemPriceAmd = $itemManager->exchangeFromUsdToAMD($itemForOrderDto->getCustomerItemPrice());
    $itemRandomDiscountPercent = rand(20, 28);
    $itemRandomDiscountParam = 1 - $itemRandomDiscountPercent / 100;
    $itemListPrice = intval($customerItemPriceAmd/ $itemRandomDiscountParam);    
    $itemDto->setListPriceAmd($itemListPrice);
    var_dump(intval((1-$customerItemPriceAmd/$itemListPrice)*100));
    $itemManager->updateByPK($itemDto);
}
?>