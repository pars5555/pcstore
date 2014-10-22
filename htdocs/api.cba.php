<?php

//see http://api.cba.am/exchangerates.asmx
$soapClient = new SoapClient("http://api.cba.am/exchangerates.asmx?wsdl");
try {
	$info = $soapClient->ExchangeRatesLatest();
	$cbaOneUsdPrice = $info->ExchangeRatesLatestResult->Rates->ExchangeRate[0]->Rate;
	if (isset($info->ExchangeRatesLatestResult) && isset($info->ExchangeRatesLatestResult->Rates) && isset($info->ExchangeRatesLatestResult->Rates->ExchangeRate))
	{
		foreach ($info->ExchangeRatesLatestResult->Rates->ExchangeRate as $dto) {
			switch ($dto->ISO)
			{
				case "USD":
					var_dump($dto->ISO.$dto->Rate);
					break;
				case "EUR":
					var_dump('USD:'.$dto->Rate);
					break;
				case "RUB":
					var_dump('USD:'.$dto->Rate);
					break;
			}
		}
	}exit;
	$currentDate = $info->ExchangeRatesLatestResult->CurrentDate;	
	
	var_dump($info->ExchangeRatesLatestResult->Rates->ExchangeRate);exit;
	$date = date_create_from_format('Y-m-d\TH:i:s', $currentDate);	
	if ($date) {
		$dateStr = $date->format('Y-m-d H:i:s');		
		var_dump($dateStr);		
	}
	var_dump(round($cbaOneUsdPrice, 2));
} catch (SoapFault $fault) {
	//$fault->faultstring
}
unset($soapClient);
?>