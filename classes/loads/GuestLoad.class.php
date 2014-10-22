<?php

require_once (CLASSES_PATH . "/loads/Load.class.php");
require_once (CLASSES_PATH . "/managers/CustomerCartManager.class.php");

/**
 * General parent load for all FAZ MainlLoad classes
 */
abstract class GuestLoad extends Load {

    public function __construct() {
        
    }

    public function setCustomerCartItemsCount() {
        $customer = $this->getCustomer();
        if ($customer) {
            $customerEmail = strtolower($customer->getEmail());
            $customerCartManager = CustomerCartManager::getInstance($this->config, $this->args);
            $cartTotal = $customerCartManager->getCustomerCartTotalCount($customerEmail);
            $this->addParam('customer_cart_items_count', $cartTotal);
        }
    }

    public function initialize($smarty, $sessionManager, $config, $loadMapper, $args) {
        parent::initialize($smarty, $sessionManager, $config, $loadMapper, $args);
        $this->config = $config;
        $this->putAllPhrasesInSmarty();

        $allVarsArray = CmsSettingsManager::getInstance()->getAllVarsArray();
        $this->addParam("cms_vars", json_encode($allVarsArray));

        $this->setCustomerCartItemsCount();

        $userLevel = $this->getUserLevel();
        $customer = $this->getCustomer();
        $this->addParam("user", $customer);
        $this->addParam("userId", $this->getUserId());
        $this->addParam('userLevel', $userLevel);
        if ($userLevel == UserGroups::$USER) {
            $this->addParam('userLoginType', $customer->getLoginType());
        }
        $this->addParam('userGroupsCompany', UserGroups::$COMPANY);
        $this->addParam('userGroupsServiceCompany', UserGroups::$SERVICE_COMPANY);
        $this->addParam('userGroupsUser', UserGroups::$USER);
        $this->addParam('userGroupsGuest', UserGroups::$GUEST);
        $this->addParam('userGroupsAdmin', UserGroups::$ADMIN);
        if ($this->getUserLevel() === UserGroups::$ADMIN) {
            $this->addParam('admin_price_group', $this->getCustomer()->getPriceGroup());
        }

        $this->addParam('DOCUMENT_ROOT', DOCUMENT_ROOT);

        $this->addParam("salesPhone", $this->getCmsVar("pcstore_sales_phone_number"));
        $this->addParam("salesPhone1", $this->getCmsVar("pcstore_sales_phone_number1"));

        $this->addParam("wholePageWidth", $this->getCmsVar("whole_page_width"));

        $this->addParam("passRegexp", $this->getCmsVar("password_regexp"));

        $this->addParam("us_dollar_exchange", floatval($this->getCmsVar("us_dollar_exchange")));

        if ($userLevel == UserGroups::$COMPANY) {
            $this->addParam("customer_ping_pong_timeout_seconds", $this->getCmsVar("company_ping_pong_timeout_seconds"));
            $this->addParam("company_item_check_message_timeout_seconds", $this->getCmsVar("company_item_check_message_timeout_seconds"));
        } elseif ($userLevel == UserGroups::$USER) {
            $this->addParam("customer_ping_pong_timeout_seconds", $this->getCmsVar("user_ping_pong_timeout_seconds"));
        } elseif ($userLevel == UserGroups::$ADMIN) {
            $this->addParam("customer_ping_pong_timeout_seconds", $this->getCmsVar("admin_ping_pong_timeout_seconds"));
        } else {
            $this->addParam("customer_ping_pong_timeout_seconds", $this->getCmsVar("guest_ping_pong_timeout_seconds"));
        }
    }

    public function putAllPhrasesInSmarty() {
        $lm = LanguageManager::getInstance($this->config, $this->args);
        $this->addParam("all_phrases_dtos_mapped_by_id", json_encode($lm->getAllPhrasesDtosMappedById()));
        $this->addParam("langManager", $lm);
        $ul = $_COOKIE['ul'];
        if (!($ul === 'en' || $ul === 'ru' || $ul === 'am')) {
            $ul = 'en';
        }
        if (!isset($_COOKIE['ul'])) {
            $this->setcookie('ul', $ul);
        }
        $this->addParam("ul", $ul);
    }

    public function getDefaultLoads($args) {
        $loads = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

    public function initPaging($page, $itemsCount, $limit, $pagesShowed) {
        //	  1 ,       301 ,     20,       20

        $pageCount = ceil($itemsCount / $limit);

        $centredPage = ceil($pagesShowed / 2);
        $pStart = 0;
        if (($page - $centredPage) > 0) {
            $pStart = $page - $centredPage;
        }
        if (($page + $centredPage) >= $pageCount) {
            $pEnd = $pageCount;
            if (($pStart - ($page + $centredPage - $pageCount)) > 0) {
                $pStart = $pStart - ($page + $centredPage - $pageCount);
            }
        } else {
            $pEnd = $pStart + $pagesShowed;
            if ($pageCount < $pagesShowed) {
                $pEnd = $pageCount;
            }
        }

        $this->addParam("pageCount", $pageCount);
        $this->addParam("page", $page);
        $this->addParam("pStart", $pStart);
        $this->addParam("pEnd", $pEnd);

        return true;
    }

    public function onNoAccess() {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo '<script>ngs.reloadPage();</script>';
            exit;
        } else {
            $this->redirect();
        }
    }

    public function generateLoadClassName($load_name) {
        $loadClassName = strtoupper($load_name[0]) . substr($load_name, 1);
        while (strpos($loadClassName, '_') !== false) {
            $_pos = strpos($loadClassName, '_');
            $letter = strtoupper($loadClassName[$_pos + 1]);
            $loadClassName = substr($loadClassName, 0, $_pos) . $letter . substr($loadClassName, $_pos + 2);
        }
        return $loadClassName . 'Load';
    }

    public function countryGeoInfoFromIP($ipAddr) {
        return get_meta_tags('http://www.geobytes.com/IpLocator.htm?GetLocation&template=php3.txt&IpAddress=' . $ipAddr);
    }

    public function setDescriptionTagValue($value) {
        if (isset($this->args['mainLoad'])) {
            $this->args['mainLoad']->addParam('description_tag_value', $value);
        }
    }

    public function setKeywordsTagValue($value) {
        if (isset($this->args['mainLoad'])) {
            $this->args['mainLoad']->addParam('keywords_tag_value', $value);
        }
    }

    public function setTitleTagValue($value) {
        if (isset($this->args['mainLoad'])) {
            $this->args['mainLoad']->addParam('title_tag_value', $value);
        } else {
            echo "<script>document.title='$value';</script>";
        }
    }

}

?>