<?php

require_once (CLASSES_PATH . "/loads/admin/AdminLoad.class.php");
require_once (CLASSES_PATH . "/managers/NewslettersManager.class.php");
require_once (CLASSES_PATH . "/managers/EmailAccountsManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class NewsletterSendersLoad extends AdminLoad {

    public function load() {

        $newslettersManager = NewslettersManager::getInstance($this->config, $this->args);
        $selected_email_accounts_ids = $_REQUEST['selected_email_accounts_ids'];
        
        $emailAccountsManager = EmailAccountsManager::getInstance($this->config, $this->args);
        $allEmailAccounts = $emailAccountsManager->selectAll();
        $emailAccountsNameIdMap = array();
        foreach ($allEmailAccounts as $dto) {
            $emailAccountsNameIdMap [$dto->getid()] = $dto->getLogin();
        }
        $this->addParam('email_accounts_logins', $emailAccountsNameIdMap);
        $this->addParam('selected_email_accounts_ids', explode(',', $selected_email_accounts_ids));
        
        
        $include_all_active_users = $_REQUEST['include_all_active_users'];
        $this->addParam('include_all_active_users', $include_all_active_users);
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/admin/newsletters/senders.tpl";
    }

}

?>