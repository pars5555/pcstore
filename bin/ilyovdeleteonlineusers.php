<?php

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', true);
defined('__DIR__') or define('__DIR__', dirname(__FILE__));
$_dir = str_replace("/bin", "", __DIR__);

require_once ($_dir . "/conf/constants.php");
require_once (CLASSES_PATH . "/util/db/DBMSFactory.class.php");
require_once (CLASSES_PATH . "/managers/OnlineUsersManager.class.php");
require_once (CLASSES_PATH . "/managers/CustomerAlertsManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyItemCheckListManager.class.php");
require_once (CLASSES_PATH . "/managers/CmsSearchRequestsManager.class.php");
require_once (CLASSES_PATH . "/managers/CbaRatesManager.class.php");
require_once (CLASSES_PATH . "/managers/ReceiveEmailManager.class.php");

$config = parse_ini_file(CONF_PATH . "/config.ini");
DBMSFactory::init($config);

$onlineUsersManager = new OnlineUsersManager($config, null);
$onlineUsersManager->removeTimeOutedUsers(120); //2 minutes	

$customerAlertsManager = new CustomerAlertsManager($config, null);
$customerAlertsManager->removeOldAlerts(10); //10 minute

$companyItemCheckListManager = new CompanyItemCheckListManager($config, null);
$companyItemCheckListManager->removeOldRowsBySeconds(120); //2 minutes

$requestHistoryManager = new RequestHistoryManager($config, null);
$requestHistoryManager->removeOldRowsByDays(90); // 90 days

$cmsSearchRequestsManager = new CmsSearchRequestsManager($config, null);
$cmsSearchRequestsManager->removeOldRowsByDays(90); // 90 days
//chaching cba rates for all exchanges
$rates = getCbaRates();
if ($rates !== false) {
    $datetime = $rates[1];
    $cbaRatesManager = new CbaRatesManager($config, null);
    $selectByField = $cbaRatesManager->selectByField('cba_datetime', $datetime);
    if (empty($selectByField)) {
        foreach ($rates[0] as $rate) {
            $cbaRatesManager->addRow($datetime, $rate[0], $rate[1], $rate[2]);
        }
    }
}

$receiveEmailManager = ReceiveEmailManager::getInstance();
$receiveEmailManager->checkPriceEmailsAndAddAlertsToOnlineAdmins();

/**
 * 
 * @return array(datetime, array(array(iso, amount, rate),...)) or FALSE
 */
function getCbaRates() {
    $soapClient = new SoapClient("http://api.cba.am/exchangerates.asmx?wsdl");
    $ret = array();
    try {
        $info = $soapClient->ExchangeRatesLatest();
        if (!isset($info->ExchangeRatesLatestResult) || !isset($info->ExchangeRatesLatestResult->Rates) || !isset($info->ExchangeRatesLatestResult->Rates->ExchangeRate)) {
            return false;
        }
        foreach ($info->ExchangeRatesLatestResult->Rates->ExchangeRate as $dto) {
            $ret[] = array($dto->ISO, $dto->Amount, $dto->Rate);
        }
        $currentDate = $info->ExchangeRatesLatestResult->CurrentDate;
        $date = date_create_from_format('Y-m-d\TH:i:s', $currentDate);
        if (!$date) {
            return false;
        }
        $dateStr = $date->format('Y-m-d H:i:s');
        return array($ret, $dateStr);
    } catch (SoapFault $fault) {
        return false;
    }
    unset($soapClient);
}

?>
