<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
ini_set('display_errors', true);
ini_set('post_max_size', '7M');
ini_set('upload_max_filesize', '7M');
session_start();
require_once("../conf/constants.php");
require_once(CLASSES_PATH . "/framework/Dispatcher.class.php");
require_once(CLASSES_PATH . "/RequestGroups.class.php");
require_once(CLASSES_PATH . "/managers/SessionManager.class.php");
require_once(CLASSES_PATH . "/loads/LoadMapper.class.php");
require_once(CLASSES_PATH . "/util/db/DBMSFactory.class.php");

$config = parse_ini_file(CONF_PATH . "/config.ini");
DBMSFactory::init($config);
$sessionManager = new SessionManager($config);
$urlMapper = new LoadMapper($config);

$packages = array();
$packages["actions"] = "actions";
$packages["loads"] = "loads";
$dispatcher = new Dispatcher($config, $sessionManager, $urlMapper, $packages);

?>