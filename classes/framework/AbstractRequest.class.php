<?php

/**
 * Sample File 2, phpDocumentor Quickstart
 *
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Vahagn Sookiasian <vahagnsookaisyan@gmail.com>
 * @version 1.2
 * @package framework
 */
require_once (CLASSES_PATH . "/framework/AbstractSessionManager.class.php");
require_once (CLASSES_PATH . "/managers/RequestHistoryManager.class.php");
require_once (CLASSES_PATH . "/managers/LanguageManager.class.php");
require_once (CLASSES_PATH . "/managers/CmsSettingsManager.class.php");

abstract class AbstractRequest {

    protected $config;
    protected $args;
    protected $sessionManager;
    protected $loadMapper;
    protected $requestGroup;
    protected $customer;

    /**
     * Return a thingie based on $paramie
     * @abstract
     * @access
     * @param boolean $paramie
     * @return integer|babyclass
     */
    public function initialize($smarty, $sessionManager, $config, $loadMapper, $args) {
        $this->sessionManager = $sessionManager;
        $this->loadMapper = $loadMapper;
        $this->smarty = $smarty;
        $this->config = $config;
        $this->args = $args;
        $this->logRequestInDB();
    }

    /**
     * Return a thingie based on $paramie
     * @abstract
     * @access
     * @param boolean $paramie
     * @return integer|babyclass
     */
    public function setRequestGroup($requestGroup) {
        $this->requestGroup = $requestGroup;
    }

    /**
     * Return a thingie based on $paramie
     * @abstract
     * @access
     * @param boolean $paramie
     * @return integer|babyclass
     */
    public function getRequestGroup() {
        return $this->requestGroup;
    }

    /**
     * Return a thingie based on $paramie
     * @abstract
     * @access
     * @param boolean $paramie
     * @return integer|babyclass
     */
    public function toCache() {
        return false;
    }

    public function setDispatcher($dispatcher) {
        $this->dispatcher = $dispatcher;
    }

    public function redirectToLoad($package, $load, $args, $statusCode = 200) {
        if ($statusCode > 200 && $statusCode < 300) {
            header("HTTP/1.1 $statusCode Exception");
        }
        $this->dispatcher->loadPage($package, $load, $args);
        exit();
    }

    protected function cancel() {
        throw new NoAccessException("Load canceled request ");
    }

    /**
     * Return a thingie based on $paramie
     * @abstract
     * @access
     * @param boolean $paramie
     * @return integer|babyclass
     */
    public function onNoAccess() {
        return false;
    }

    /**
     * Return a thingie based on $paramie
     * @abstract
     * @access
     * @param boolean $paramie
     * @return integer|babyclass
     */
    protected function redirect($url = '') {
        $protocol = "http://";
        if (isset($_SERVER["HTTPS"])) {
            $protocol = "https://";
        }
        header("location: " . $protocol . HTTP_HOST . "/$url");
        exit;
    }

    protected function getUserLevel() {
        return $this->sessionManager->getUser()->getLevel();
    }

    protected function getUserId() {
        return $this->sessionManager->getUser()->getId();
    }

    protected function getUser() {
        return $this->sessionManager->getUser();
    }

    public function getCustomer() {
        if (!$this->customer) {
            if ($this->getUserLevel() != UserGroups::$GUEST) {
                $userId = $this->getUserId();
                if ($this->getUserLevel() == UserGroups::$USER) {
                    $userManager = new UserManager($this->config, $this->args);
                    $this->customer = $userManager->selectByPK($userId);
                } else if ($this->getUserLevel() == UserGroups::$COMPANY) {
                    $customerManager = new CompanyManager($this->config, $this->args);
                    $this->customer = $customerManager->selectByPK($userId);
                } else if ($this->getUserLevel() == UserGroups::$SERVICE_COMPANY) {
                    $customerManager = new ServiceCompanyManager($this->config, $this->args);
                    $this->customer = $customerManager->selectByPK($userId);
                } else if ($this->getUserLevel() == UserGroups::$ADMIN) {
                    $adminManager = new AdminManager($this->config, $this->args);
                    $this->customer = $adminManager->selectByPK($userId);
                }
            }
        }
        return $this->customer;
    }

    public function getCustomerLogin() {
        $customer = $this->getCustomer();
        if (isset($customer)) {
            return $customer->getEmail();
        }
    }

    public function isLoggedInCustomer() {
        return $this->getUserLevel() !== UserGroups::$GUEST;
    }

    public function getUserLevelString() {
        $level = $this->getUserLevel();
        if ($level === UserGroups::$USER) {
            return "user";
        }
        if ($level === UserGroups::$COMPANY) {
            return "company";
        }
        if ($level === UserGroups::$SERVICE_COMPANY) {
            return "service_company";
        }
        if ($level === UserGroups::$ADMIN) {
            return "admin";
        }
        if ($level === UserGroups::$GUEST) {
            return "guest";
        }
        return 'unknown';
    }

    public function logRequestInDB() {
        if ($this->logRequest()) {
            $requestHistoryManager = RequestHistoryManager::getInstance($this->config, $this->args);
            $cust = $this->getCustomer();
            $requestHistoryManager->addRow($this->getUserLevelString(), $cust ? $cust->getEmail() : '', get_class($this), $_REQUEST);
        }
    }

    protected function logRequest() {
        return true;
    }

    public function getCmsVar($var) {
        return CmsSettingsManager::getInstance()->getValue($var);
    }

    public function getPhrase($phrase_id, $ul = null) {
        return LanguageManager::getInstance($this->config, $this->args)->getPhrase($phrase_id, $ul);
    }

    public function getPhraseSpan($phrase_id, $ul = null) {
        return LanguageManager::getInstance($this->config, $this->args)->getPhraseSpan($phrase_id, $ul);
    }

    public function getPhrases($phraseIds, $ul = null) {
        $ret = array();
        foreach ($phraseIds as $pid) {
            $ret[] = $this->getPhrase($pid);
        }
        return $ret;
    }

    public function secure($var) {
        if (isset($var)) {
            return trim(htmlspecialchars(strip_tags($var)));
        } else {
            return null;
        }
    }

    public function setCookie($key, $value, $expire = 0) {
        if (HTTP_ROOT_HOST === HTTP_HOST) {
            $domain = "." . HTTP_HOST;
        } else {
            $domain = HTTP_ROOT_HOST;
        }
        setcookie($key, $value, $expire, "/", $domain);
    }

    public static function generateTableManagerClassName($tableName) {
        $tableManagerName = strtoupper($tableName[0]) . substr($tableName, 1);
        while (strpos($tableManagerName, '_') !== false) {
            $_pos = strpos($tableManagerName, '_');
            $letter = strtoupper($tableManagerName[$_pos + 1]);
            $tableManagerName = substr($tableManagerName, 0, $_pos) . $letter . substr($tableManagerName, $_pos + 2);
        }
        $managerPathForm = CLASSES_PATH . "/managers/%s.class.php";
        if (file_exists(sprintf($managerPathForm, $tableManagerName . 'Manager'))) {
            $tableManagerName = $tableManagerName . 'Manager';
        } else
        if (file_exists(sprintf($managerPathForm, $tableManagerName . 'sManager'))) {
            $tableManagerName = $tableManagerName . 'sManager';
        } else
        if (file_exists(sprintf($managerPathForm, substr($tableManagerName, 0, -1) . "Manager"))) {
            $tableManagerName = substr($tableManagerName, 0, -1) . "Manager";
        }
        if (file_exists(sprintf($managerPathForm, $tableManagerName))) {
            require_once sprintf($managerPathForm, $tableManagerName);
            return $tableManagerName;
        }
        return "";
    }

    public static function generateTableMapperClassName($tableName) {
        $tableMapperName = strtoupper($tableName[0]) . substr($tableName, 1);
        while (strpos($tableMapperName, '_') !== false) {
            $_pos = strpos($tableMapperName, '_');
            $letter = strtoupper($tableMapperName[$_pos + 1]);
            $tableMapperName = substr($tableMapperName, 0, $_pos) . $letter . substr($tableMapperName, $_pos + 2);
        }
        $mapperPathForm = CLASSES_PATH . "/dal/mappers/%s.class.php";
        if (file_exists(sprintf($mapperPathForm, $tableMapperName . 'Mapper'))) {
            $tableMapperName = $tableMapperName . 'Mapper';
        } else
        if (file_exists(sprintf($mapperPathForm, $tableMapperName . 'sMapper'))) {
            $tableMapperName = $tableMapperName . 'sMapper';
        } else
        if (file_exists(sprintf($mapperPathForm, substr($tableMapperName, 0, -1) . "Mapper"))) {
            $tableMapperName = substr($tableMapperName, 0, -1) . "Mapper";
        } else
        if (file_exists(sprintf($mapperPathForm, substr($tableMapperName, 0, -3) . "yMapper"))) {
            $tableMapperName = substr($tableMapperName, 0, -3) . "yMapper";
        }
        if (file_exists(sprintf($mapperPathForm, $tableMapperName))) {
            require_once sprintf($mapperPathForm, $tableMapperName);
            return $tableMapperName;
        }
        return "";
    }

}

?>