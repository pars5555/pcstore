<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/managers/SmsGatewaysManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/SentSmsMapper.class.php");

/**
 * CompanyManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class SentSmsManager extends AbstractManager {

    /**
     * @var app config
     */
    private $config;
    private $armeniaTelCode;
    public $validArmenianPrefix;

    /**
     * @var passed arguemnts
     */
    private $args;

    /**
     * @var singleton instance of class
     */
    private static $instance = null;

    /**
     * Initializes DB mappers
     *
     * @param object $config
     * @param object $args
     * @return
     */
    function __construct($config, $args) {
        $this->mapper = SentSmsMapper::getInstance();
        $this->config = $config;
        $this->args = $args;
        $this->armeniaTelCode = "374";
        $this->validArmenianPrefix = explode(',', $this->getCmsVar('valid_cell_phone_numbers_prefix'));
    }

    /**
     * Returns an singleton instance of this class
     *
     * @param object $config
     * @param object $args
     * @return
     */
    public static function getInstance($config, $args) {

        if (self::$instance == null) {

            self::$instance = new SentSmsManager($config, $args);
        }
        return self::$instance;
    }

    /**
     * @param $toPhoneNumbers can be array or only one number
     */
    public function sendSmsToArmenia($toPhoneNumbers, $message, $sms_gateway_name = null) {

        if (!is_array($toPhoneNumbers)) {
            $toPhoneNumbers = array($toPhoneNumbers);
        }
        $smsGatewaysManager = SmsGatewaysManager::getInstance($this->config, $this->args);
        if (!isset($sms_gateway_name)) {
            $sms_gateway_name = $from = $this->getCmsVar('sms_gateway_name');
        }
        $smsGatewayDto = $smsGatewaysManager->getByName($sms_gateway_name);
        if (!isset($smsGatewayDto)) {
            return false;
        }
        $from = $this->getCmsVar('sms_from_phone_number');
        foreach ($toPhoneNumbers as $key => $pn) {
            $vpn = self::getValidArmenianNumber($pn, $smsGatewayDto->getInternationalCodePrefix());
            if ($vpn != null) {
                $httpSmsUrl = $smsGatewayDto->getHttpSmsUrl();
                $settingsMetadataJson = $smsGatewayDto->getSettingsMetadata();
                $settingsMetadata = json_decode($settingsMetadataJson, true);
                foreach ($settingsMetadata as $key => $value) {
                    $httpSmsUrl = str_replace('{' . trim($key) . '}', $value, $httpSmsUrl);
                }
                $httpSmsUrl = str_replace('{to}', $vpn, $httpSmsUrl);
                $httpSmsUrl = str_replace('{from}', urlencode($from), $httpSmsUrl);
                $httpSmsUrl = str_replace('{message}', urlencode($message), $httpSmsUrl);
                if ($this->getCmsVar('enable_sms') == 1) {
                    file_get_contents($httpSmsUrl);
                }
                $dto = $this->mapper->createDto();
                $dto->setTo($vpn);
                $dto->setFrom($from);
                $dto->setMessage($message);
                $this->mapper->insertDto($dto);
            }
        }
        return $message;
    }

    public static function getValidArmenianNumber($number, $internationalCodePrefix = '+') {
        $manager = SentSmsManager::getInstance(null, null);
        if (!isset($number)) {
            return null;
        }
        $number = strval($number);
        $number = preg_replace("/[^0-9]/", "", $number);
        if (strlen($number) >= 8) {
            $number = substr($number, -8, 8);
            if (in_array(substr($number, 0, 2), $manager->validArmenianPrefix)) {
                return $internationalCodePrefix . $manager->armeniaTelCode . $number;
            }
        }
        return null;
    }

}

?>