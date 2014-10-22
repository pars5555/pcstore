<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/CustomerAlertsMapper.class.php");

/**
 * AdminManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class CustomerAlertsManager extends AbstractManager {

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
        $this->mapper = CustomerAlertsMapper::getInstance();
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

            self::$instance = new CustomerAlertsManager($config, $args);
        }
        return self::$instance;
    }

    public function getCustomerAlertsByCustomerLogin($customerLogin, $win_uid) {
        $alerts = $this->mapper->getCustomerAlertsByCustomerLogin($customerLogin, $win_uid);
        foreach ($alerts as $dto) {
            $this->addDeliveredToUid($dto->getId(), $win_uid);
        }
        return $alerts;
    }

    public function addDeliveredToUid($id, $uid) {
        $dto = $this->mapper->selectByPK($id);
        if (isset($dto)) {
            $deliveredToUid = $dto->getDeliveredToUid();
            if (!empty($deliveredToUid)) {
                $deliveredToUid .= ',' . $uid;
            } else {
                $deliveredToUid = $uid;
            }
        }
        return $this->mapper->updateTextField($id, "delivered_to_uid", $deliveredToUid);
    }

    public function addPriceUploadCustomerAlert($customerLogin, $company, $lngCode = 'en') {
        $dto = $this->mapper->createDto();
        $dto->setToLogin($customerLogin);
        $dto->setType("price_upload");
        $dto->setTitle($this->getPhrase(482, $lngCode));
        $dto->setMessage(sprintf($this->getPhrase(469, $lngCode), $company->getName()));
        $dto->setMetadata($company->getId());
        $dto->setDatetime(date('Y-m-d H:i:s'));
        return $this->mapper->insertDto($dto);
    }

    public function addNewEmailCustomerAlert($customerLogin, $fromName, $subject, $customerInboxUnreadEmailsCount, $lngCode = 'en') {
        $dto = $this->mapper->createDto();
        $dto->setToLogin($customerLogin);
        $dto->setType("new_email");
        $dto->setTitle($this->getPhrase(481, $lngCode));
        $dto->setMessage(sprintf($this->getPhrase(470, $lngCode), $fromName) . '<br/><br/>' . $subject);
        $dto->setDatetime(date('Y-m-d H:i:s'));
        $dto->setMetadata($customerInboxUnreadEmailsCount);
        return $this->mapper->insertDto($dto);
    }

    public function addUnreadEmailsCustomerAlert($customerLogin, $customerInboxUnreadEmailsCount) {
        $dto = $this->mapper->createDto();
        $dto->setToLogin($customerLogin);
        $dto->setType("info");
        $dto->setTitle($this->getPhraseSpan(553));
        $messageDiv = $this->getPhraseSpan(554) . ' ' . $customerInboxUnreadEmailsCount . ' ' . $this->getPhraseSpan(555);
        $messageDiv .= "<br/><br/><br/><a href='javascript:void(0);' onclick='jQuery(\"#tab_link_your_mails\").trigger(\"click\");'>" . $this->getPhraseSpan(556) . "</a>";
        $dto->setMessage($messageDiv);
        $dto->setDatetime(date('Y-m-d H:i:s'));
        $dto->setMetadata('');
        return $this->mapper->insertDto($dto);
    }

    public function addNewEmailFromCompanyAlert($customerLogin, $companyName) {

        $dto = $this->mapper->createDto();
        $dto->setToLogin($customerLogin);
        $dto->setType("new_email");
        $dto->setTitle($this->getPhraseSpan(644));
        $messageDiv = $companyName;
        $dto->setMessage($messageDiv);
        $dto->setDatetime(date('Y-m-d H:i:s'));
        $dto->setMetadata('');
        return $this->mapper->insertDto($dto);
    }

    /**
     * 
     * @param type $minute
     * @return type
     */
    public function removeOldAlerts($minute) {
        return $this->mapper->removeOldAlerts($minute);
    }

}

?>