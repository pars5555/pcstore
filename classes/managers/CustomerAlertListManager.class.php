<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/managers/CompanyItemCheckListManager.class.php");
require_once (CLASSES_PATH . "/managers/UserManager.class.php");

/**
 * CustomerAlertListManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class CustomerAlertListManager extends AbstractManager {

    /**
     * @var app config
     */
    private $config;
    private $companyItemCheckListManager;

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

        $this->companyItemCheckListManager = CompanyItemCheckListManager::getInstance($this->config, $this->$args);
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

            self::$instance = new CustomerAlertListManager($config, $args);
        }
        return self::$instance;
    }

    public function getRegisteredCustomerAlerts($customerDto, $winUid) {

        $alerts_array = array();

        //company pending questions about item availability
        $customerType = UserManager::getCustomerTypeFromDto($customerDto);

        if ($customerType === UserGroups::$COMPANY) {
            $companyId = $customerDto->getId();
            $companyItemCheckListDtos = $this->companyItemCheckListManager->getCompanyQuestionsFromCustomers($companyId, $winUid);
            if (!empty($companyItemCheckListDtos)) {
                $alerts_array["check_items_availability"] = AbstractDto::dtosToArray($companyItemCheckListDtos);
                foreach ($companyItemCheckListDtos as $dto) {
                    $dto = $this->companyItemCheckListManager->selectByPK($dto->getId());
                    $sentToCompanyUid = $dto->getSentToCompanyUid();
                    if (empty($sentToCompanyUid)) {
                        $sentToCompanyUid = $winUid;
                    } else {
                        $sentToCompanyUid .= ',' . $winUid;
                    }
                    $this->companyItemCheckListManager->setSentToCompanyFieldValue($dto->getId(), $sentToCompanyUid);
                }
            }
        }

        //company replies about item availability for customers
        $customerEmail = $customerDto->getEmail();
        $companyRespondedItemAvailabilityDtos = $this->companyItemCheckListManager->getCustomerSentQuestionsResponses($customerEmail, $winUid);
        if (!empty($companyRespondedItemAvailabilityDtos)) {
            $alerts_array["responded_items_availability"] = AbstractDto::dtosToArray($companyRespondedItemAvailabilityDtos);
            foreach ($companyRespondedItemAvailabilityDtos as $dto) {
                $dto = $this->companyItemCheckListManager->selectByPK($dto->getId());
                $responseSentToCustomerUid = $dto->getResponseSentToCustomerUid();
                if (empty($responseSentToCustomerUid)) {
                    $responseSentToCustomerUid = $winUid;
                } else {
                    $responseSentToCustomerUid .= ',' . $winUid;
                }
                $this->companyItemCheckListManager->setResponseSentToCustomerFieldValue($dto->getId(), $responseSentToCustomerUid);
            }
        }

        return $alerts_array;
    }

    public function getMapper() {
        return $this->companyItemCheckListManager;
    }

}

?>