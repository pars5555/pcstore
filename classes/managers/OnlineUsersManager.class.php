<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/OnlineUsersMapper.class.php");
require_once (CLASSES_PATH . "/managers/LoginHistoryManager.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/AdminManager.class.php");
require_once (CLASSES_PATH . "/managers/CustomerMessagesAfterLoginManager.class.php");

/**
 * OnlineUsersManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class OnlineUsersManager extends AbstractManager {

    /**
     * @var app config
     */
    private $config;

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
        $this->mapper = OnlineUsersMapper::getInstance();
        $this->config = $config;
        $this->args = $args;
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

            self::$instance = new OnlineUsersManager($config, $args);
        }
        return self::$instance;
    }

    public function getOnlineRegisteredCustomers() {
        return $this->mapper->getOnlineRegisteredCustomers();
    }

    public function removeOnlineUserByEmail($email) {
        $dto = $this->getOnlineUserByEmail($email);
        if ($dto) {
            $this->deleteByPK($dto->getId());
        }
    }

    public function getOnlineUserByEmail($email) {
        return $this->mapper->getOnlineUserByEmail($email);
    }

    public function addOnlineUser($userLevel, $customerDto) {
        $browserInfo = $this->getBrowser();
//		if ($browserInfo["name"] === 'Unknown') {
//			return false;
//		}


        $userEmail = '';
        if (isset($customerDto)) {
            $userEmail = $customerDto->getEmail();
        }

        //last ping time set for customer		
        $previousPing = null;
        switch ($userLevel) {
            case UserGroups::$ADMIN:
                $adminManager = AdminManager::getInstance($this->config, $this->args);
                $previousPing = $customerDto->getLastPing();
                $adminManager->setLastPingToNow($customerDto->getId());
                break;

            case UserGroups::$COMPANY:
                $companyManager = CompanyManager::getInstance($this->config, $this->args);
                $previousPing = $customerDto->getLastPing();
                $companyManager->setLastPingToNow($customerDto->getId($customerDto->getId()));
                break;
            case UserGroups::$USER:
                $userManager = UserManager::getInstance($this->config, $this->args);
                $previousPing = $customerDto->getLastPing();
                $userManager->setLastPingToNow($customerDto->getId($customerDto->getId()));
                break;
        }

        if (isset($customerDto)) {
            $onlineUser = $this->getOnlineUserByEmail($userEmail);
            if (isset($onlineUser)) {
                $this->updateOnlineUserAttributes($onlineUser);
                return false;
            }
        }
        $guest_online_table_id = $_COOKIE['guest_online_table_id'];
        $oldRow = $this->selectByPK($guest_online_table_id);

        $ip = $_SERVER["REMOTE_ADDR"];
        $host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $country = $_SERVER["GEOIP_COUNTRY_NAME"];
        $browser = $browserInfo["name"];
        $browserVersion = $browserInfo["version"];
        $platform = $browserInfo["platform"];
        $status = "online";

        if ($browser != 'Unknown' && $platform != 'Unknown' && $browserVersion != '?') {
            if (!isset($oldRow)) {
                $dto = $this->mapper->createDto();
            } else {
                $dto = $oldRow;
            }
            $dto->setEmail($userEmail);
            $dto->setIp($ip);
            $dto->setHost($host);
            $dto->setCountry($country);
            $dto->setBrowserName($browser);
            $dto->setBrowserVersion($browserVersion);
            $dto->setBrowserPlatform($platform);
            $dto->setLoginDateTime(date('Y-m-d H:i:s'));
            $dto->setStatus($status);
            if (!isset($oldRow)) {
                $id = $this->mapper->insertDto($dto);
            } else {
                $this->mapper->updateByPK($dto);
                $id = $dto->getId();
            }
        }
        $loginHistoryManager = LoginHistoryManager::getInstance($this->config, $this->args);
        $ulstring = $this->getUserLevelString($userLevel);
        $loginHistoryManager->addRow($userEmail, $ulstring, $ip, $host, $country, $browser, $browserVersion, $platform);

        $customerMessagesAfterLoginManager = CustomerMessagesAfterLoginManager::getInstance($this->config, $this->args);
        $customerMessagesAfterLoginManager->addCustomerMessagesAfterLoginByPreviousPing($customerDto, $userLevel, $previousPing);
        return $id;
    }

    public function getUserLevelString($level) {
        switch ($level) {
            case UserGroups::$USER:return "user";
            case UserGroups::$COMPANY:return "company";
            case UserGroups::$SERVICE_COMPANY:return "service_company";
            case UserGroups::$ADMIN:return "admin";
            case UserGroups::$GUEST:return "guest";
        }
        return 'unknown';
    }

    public function updateOnlineUserAttributes($onlineUser) {
        $browserInfo = $this->getBrowser();
        if ($browserInfo["name"] === 'Unknown') {
            return false;
        }
        if (!$onlineUser) {
            return false;
        }
        $ip = $_SERVER["REMOTE_ADDR"];
        $host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $country = $_SERVER["GEOIP_COUNTRY_NAME"];
        $browser = $browserInfo["name"];
        $browserVersion = $browserInfo["version"];
        $platform = $browserInfo["platform"];
        $status = "online";

        $onlineUser->setIp($ip);
        $onlineUser->setHost($host);
        $onlineUser->setCountry($country);
        $onlineUser->setBrowserName($browser);
        $onlineUser->setBrowserVersion($browserVersion);
        $onlineUser->setBrowserPlatform($platform);
        $onlineUser->setStatus($status);
        $onlineUser->setLastPingTimeStamp(date('Y-m-d H:i:s'));
        $this->mapper->updateByPK($onlineUser);
    }

    public function getBrowser() {

        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == "") {
            $version = "?";
        }

        return array('userAgent' => $u_agent, 'name' => $bname, 'version' => $version, 'platform' => $platform, 'pattern' => $pattern);
    }

    public function removeTimeOutedUsers($timoutSeconds) {
        return $this->mapper->removeTimeOutedUsers($timoutSeconds);
    }

}

?>