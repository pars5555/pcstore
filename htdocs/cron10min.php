<?php

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', true);
defined('__DIR__') or define('__DIR__', dirname(__FILE__));


require_once (__DIR__ . "/../conf/constants.php");
require_once (CLASSES_PATH . "/util/db/DBMSFactory.class.php");
require_once (CLASSES_PATH . "/managers/OnlineUsersManager.class.php");
require_once (CLASSES_PATH . "/managers/CustomerAlertsManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyItemCheckListManager.class.php");

$config = parse_ini_file(CONF_PATH . "/config.ini");
DBMSFactory::init($config);

$onlineUsersManager = new OnlineUsersManager($config, null);
$onlineUsersManager->removeTimeOutedUsers(180); //3 minutes	

$customerAlertsManager = new CustomerAlertsManager($config, null);
$customerAlertsManager->removeOldAlerts(60); //1 hour

$companyItemCheckListManager = new CompanyItemCheckListManager($config, null);
$companyItemCheckListManager->removeOldRowsBySeconds(180); 

echo "ok";

?>
