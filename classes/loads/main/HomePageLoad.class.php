<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/LanguageManager.class.php");
require_once (CLASSES_PATH . "/managers/CustomerLocalEmailsManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class HomePageLoad extends GuestLoad {

    private $activeLoadName = null;
    private $activeLoadPath = null;

    public function load() {
        if ($this->getUserLevel() === UserGroups::$ADMIN) {
            $this->addParam('root_pass_md5', md5($this->getCmsVar('root_pass')));
        }
    }

    public function getDefaultLoads($args) {
        $loads = array();

        $selectedFooterLinkLoadIndex = $this->manageFooterLinksExternalReload();
        if ($selectedFooterLinkLoadIndex != null) {
            $this->addParam("static_body_content", true);
            $this->static_body_content = true;
            $loadName = $this->generateLoadClassName("footer_links_content");
            $loads["footer_links_content"]["load"] = "loads/static/" . $loadName;
            $loads["footer_links_content"]["args"] = array("parentLoad" => &$this, "selected_footer_link_load_index" => $selectedFooterLinkLoadIndex);
            $loads["footer_links_content"]["loads"] = array();
        } else {
            if (!$this->activeLoadName) {
                $this->manageTabs();
            }
            $loadName = $this->generateLoadClassName($this->activeLoadName);
            $loads['active_load_to_nest']["load"] = "loads/" . $this->activeLoadPath . $loadName;
            $loads['active_load_to_nest']["args"] = array("parentLoad" => &$this);
            $loads['active_load_to_nest']["loads"] = array();
        }

        $userId = $this->getUserId();
        if (!$userId) {
            //show login block	
            $loadName = "LoginLoad";
            $loads["login"]["load"] = "loads/main/" . $loadName;
            $loads["login"]["args"] = array("parentLoad" => &$this);
            $loads["login"]["loads"] = array();

            //show registration dialog if page requested			
            if ($_REQUEST["p"] == 'registration' || isset($_GET["invc"])) {
                $this->addParam('show_registration_dialog', 1);
                $loadName = "UserRegistrationLoad";
                $loads["user_registration"]["load"] = "loads/main/" . $loadName;
                $loads["user_registration"]["args"] = array("parentLoad" => &$this);
                $loads["user_registration"]["loads"] = array();
            }
        }

        return $loads;
    }

    public function manageFooterLinksExternalReload() {
        switch ($_REQUEST["p"]) {
            case 'help' :
            case 'register' :
            case 'invite' :
            case 'about' :
            case 'policy' :
                return $_REQUEST["p"];
        }
        return null;
    }

    public function manageTabs() {
        $userLevel = $this->sessionManager->getUser()->getLevel();
        $tabTitles = array();
        $customer = $this->getCustomer();
        $lm = LanguageManager::getInstance($this->config, $this->args);
        if ($this->isLoggedInCustomer()) {
            $customerLocalEmailsManager = CustomerLocalEmailsManager::getInstance($this->config, $this->args);
            $unreadEmailsCount = $customerLocalEmailsManager->getCustomerInboxUnreadEmailsCountCustomerEmail($this->getCustomerLogin());
        }

        $tabsPath = array("item_search" => "main", "companies_list" => "main", "company_profile" => "company", "service_company_profile" => "servicecompany",
            "service_company_profile" => "servicecompany", "manage_items" => "company/stock", "pc_configurator" => "main", "upload_price" => "company/uploadprice", "service_upload_price" => "servicecompany/uploadprice",
            "user_management" => "main", "your_orders" => "main", "your_profile" => "main", "item_warranty" => "company", "dealers_list" => "company", "your_mails" => "main");
        $this->addParam("tabs_path", $tabsPath);

        if ($userLevel === UserGroups::$ADMIN) {
            $type = $customer->getType();
            if ($type === 'price_manager') {
                $tabTitles['companies_list'] = $lm->getPhraseSpan(89);
                $tabTitles['upload_price'] = $lm->getPhraseSpan(90);
                $tabTitles['service_upload_price'] = "Service upload";
                $tabTitles['item_search'] = $lm->getPhraseSpan(184);
                $this->activeLoadName = "upload_price";
            } elseif ($type === 'item_manager') {
                $tabTitles['companies_list'] = $lm->getPhraseSpan(89);
                $tabTitles['manage_items'] = $lm->getPhraseSpan(92);
                $tabTitles['item_search'] = $lm->getPhraseSpan(184);
                $this->activeLoadName = "manage_items";
            } elseif ($type === 'order_manager') {
                $tabTitles['your_orders'] = 'Customer Orders';
                $tabTitles['item_search'] = $lm->getPhraseSpan(184);
                $this->activeLoadName = "your_orders";
            } elseif ($type === 'admin') {
                $tabTitles['companies_list'] = $lm->getPhraseSpan(89);
                $tabTitles['upload_price'] = $lm->getPhraseSpan(90);
                $tabTitles['service_upload_price'] = "Service upload";
                $tabTitles['items_categories'] = 'Manage Categories';
                $tabTitles['manage_items'] = $lm->getPhraseSpan(92);
                $tabTitles['your_orders'] = 'Customer Orders';
                $tabTitles['item_search'] = $lm->getPhraseSpan(184);
                $tabTitles['admin_statistics'] = '***';
                $this->activeLoadName = "companies_list";
            } elseif ($type === 'all') {
                $tabTitles['companies_list'] = $lm->getPhraseSpan(89);
                $tabTitles['upload_price'] = $lm->getPhraseSpan(90);
                $tabTitles['service_upload_price'] = "Service upload";
                $tabTitles['manage_items'] = $lm->getPhraseSpan(92);
                $tabTitles['your_orders'] = 'Customer Orders';
                $tabTitles['item_search'] = $lm->getPhraseSpan(184);
                $this->activeLoadName = "companies_list";
            }
            $tabTitles['your_mails'] = $lm->getPhraseSpan(468) . ($unreadEmailsCount > 0 ? '*' : '');
        } elseif ($userLevel === UserGroups::$COMPANY) {
            $tabTitles['companies_list'] = $lm->getPhraseSpan(89);
            $tabTitles['company_profile'] = $lm->getPhraseSpan(94);
            $tabTitles['upload_price'] = $lm->getPhraseSpan(90);
            $tabTitles['dealers_list'] = $lm->getPhraseSpan(93);
            $tabTitles['your_orders'] = $lm->getPhraseSpan(142);
            $tabTitles['item_warranty'] = $lm->getPhraseSpan(82);
            $tabTitles['manage_items'] = $lm->getPhraseSpan(92);
            $tabTitles['item_search'] = $lm->getPhraseSpan(184);
            $tabTitles['your_mails'] = $lm->getPhraseSpan(468) . ($unreadEmailsCount > 0 ? '*' : '');
            $this->activeLoadName = "companies_list";
        } elseif ($userLevel === UserGroups::$SERVICE_COMPANY) {
            $tabTitles['companies_list'] = $lm->getPhraseSpan(89);
            $tabTitles['service_company_profile'] = $lm->getPhraseSpan(94);
            $serviceCompany = $this->getCustomer();
            if ($serviceCompany->getHasPrice() == 1) {
                $tabTitles['service_upload_price'] = $lm->getPhraseSpan(90);
                $tabTitles['service_dealers_list'] = $lm->getPhraseSpan(93);
            }
            $tabTitles['your_orders'] = $lm->getPhraseSpan(142);
            $tabTitles['item_search'] = $lm->getPhraseSpan(184);
            $tabTitles['your_mails'] = $lm->getPhraseSpan(468) . ($unreadEmailsCount > 0 ? '*' : '');
            $this->activeLoadName = "companies_list";
        } elseif ($userLevel === UserGroups::$USER) {
            $tabTitles['companies_list'] = $lm->getPhraseSpan(89);
            if ($this->getCustomer()->getHidden() != 1) {
                $tabTitles['your_profile'] = $lm->getPhraseSpan(20);
                $tabTitles['user_management'] = $lm->getPhraseSpan(141);
            }
            $tabTitles['your_orders'] = $lm->getPhraseSpan(142);
            $tabTitles['item_search'] = $lm->getPhraseSpan(184);
            $tabTitles['your_mails'] = $lm->getPhraseSpan(468) . ($unreadEmailsCount > 0 ? '*' : '');
            $this->activeLoadName = "pc_configurator";
        } elseif ($userLevel === UserGroups::$GUEST) {
            $tabTitles['item_search'] = $lm->getPhraseSpan(184);
            $activeTabsArray = array('item_search', 'pc_configurator');
            shuffle($activeTabsArray);
            $this->activeLoadName = $activeTabsArray[0];
        }
        $tabTitles['pc_configurator'] = $lm->getPhraseSpan(226);
        $this->activeLoadPath = 'main/';
        $activeTab = $this->getActiveTab($_REQUEST["p"]);
        if ($activeTab != null) {
            $this->activeLoadName = $activeTab[0];
            $this->activeLoadPath = $activeTab[1];
        }
        //var_dump($tabTitles);exit;
        $this->addParam("tabTitles", $tabTitles);
        $this->addParam('active_load_name', $this->activeLoadName);
        $pagesClone = $this->pages;
        unset($pagesClone['item']);
        $_allLoadNames = array_values($pagesClone);
        $allLoadNames = array();
        foreach ($_allLoadNames as $value) {
            if (is_array($value)) {
                $allLoadNames [] = $value[0];
            } else {
                $allLoadNames [] = $value;
            }
        }
        $allLoadPageUrls = array_keys($pagesClone);
        $this->addParam('load_url', array_combine($allLoadNames, $allLoadPageUrls));
    }

    public $pages = array("search" => "item_search", "item" => "item_search", "companies" => "companies_list",
        "cprofile" => array("company_profile", "company/"), "scprofile" => array("service_company_profile", "company/"),
        "stock" => array("manage_items", "company/stock/"), "configurator" => "pc_configurator",
        "upload" => array("upload_price", "company/uploadprice/"),
        "serviceupload" => array("service_upload_price", "servicecompany/uploadprice/"),
        "subusers" => "user_management",
        "orders" => "your_orders", "uprofile" => "your_profile", "warranty" => array("item_warranty", "company/"),
        "dealers" => array("dealers_list", "company/"),
        "servicedealers" => array("service_dealers_list", "servicecompany/"), "mails" => "your_mails");

    public function getActiveTab($tab) {
        if (isset($this->pages[$tab])) {
            if (is_array($this->pages[$tab])) {
                return $this->pages[$tab];
            } else {
                return array($this->pages[$tab], 'main/');
            }
        }
        return null;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/home_page.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

    public function validateUser($id, $hash) {
        $this->addParam('valid_user', 'true');
        return true;
    }

    protected function logRequest() {
        return false;
    }

}

?>