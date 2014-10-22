<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/checkout/CheckoutManager.class.php");
require_once (CLASSES_PATH . "/managers/checkout/CreditManager.class.php");
require_once (CLASSES_PATH . "/managers/CreditSuppliersManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class PaymentStepInnerLoad extends GuestLoad {

    public function load() {

        $payment_option_values = explode(',', $this->getCmsVar('payment_option_values'));
        $payment_options_display_names_ids = explode(',', $this->getCmsVar('payment_options_display_names_ids'));
        $this->addParam('payment_options_display_names_ids', $payment_options_display_names_ids);
        $this->addParam('payment_option_values', $payment_option_values);

        if (isset($_REQUEST['cho_payment_type']) && $_REQUEST['cho_payment_type'] == 'credit') {
            $checkoutManager = CheckoutManager::getInstance($this->config, $this->args);
            list($grandTotalAMD, $grandTotalUSD) = $checkoutManager->getCustomerCartGrandTotals($this->getCustomer(), $this->sessionManager->getUser(), $this->getUserLevel());
            $this->addParam("grandTotalAMD", $grandTotalAMD);
            $this->addParam("grandTotalUSD", $grandTotalUSD);

            //credit supplier
            $creditSuppliersManager = CreditSuppliersManager::getInstance($this->config, $this->args);
            $allCreditSuppliers = $creditSuppliersManager->getAllCreditSuppliers();
            $allCreditSuppliers = $creditSuppliersManager->getCreditSuppliersInMapArrayById($allCreditSuppliers);
            $creditSuppliersDisplayNamesIds = $creditSuppliersManager->getSuppliersDisplayNameIdsArray($allCreditSuppliers);
            $creditSuppliersDisplayNames = $this->getPhrases($creditSuppliersDisplayNamesIds);
            $defaultCreditSupplierDto = reset($allCreditSuppliers);
            $selected_credit_supplier_id = $defaultCreditSupplierDto->getId();
            if (isset($_REQUEST['cho_credit_supplier_id'])) {
                $selected_credit_supplier_id = $_REQUEST['cho_credit_supplier_id'];
            }
            $_REQUEST['cho_credit_supplier_id'] = $selected_credit_supplier_id;
            $this->addParam("creditSuppliersIds", array_keys($allCreditSuppliers));
            $this->addParam("creditSuppliersDisplayNames", $creditSuppliersDisplayNames);

            $selectedCreditSupplierDto = $allCreditSuppliers[$selected_credit_supplier_id];

            //credit supplier possible months
            $possibleCreditMonths = explode(',', $selectedCreditSupplierDto->getPossibleCreditMonths());

            $cho_selected_credit_months = $possibleCreditMonths[0];
            if (isset($_REQUEST['cho_selected_credit_months'])) {
                $cho_selected_credit_months = $_REQUEST['cho_selected_credit_months'];
            }
            if (!in_array($cho_selected_credit_months, $possibleCreditMonths)) {
                $cho_selected_credit_months = $possibleCreditMonths[0];
            }
            $_REQUEST['cho_selected_credit_months'] = $cho_selected_credit_months;

            $this->addParam("possibleCreditMonths", $possibleCreditMonths);

            //deposit amount
            $cho_selected_deposit_amount = 0;
            if (isset($_REQUEST['cho_selected_deposit_amount'])) {
                $cho_selected_deposit_amount = intval($this->secure($_REQUEST['cho_selected_deposit_amount']));
            }

            $_REQUEST['cho_selected_deposit_amount'] = $cho_selected_deposit_amount;

            //credit supplier interest
            $commission = $selectedCreditSupplierDto->getCommission();
            $annualInterestPercent = floatval($selectedCreditSupplierDto->getAnnualInterestPercent());
            $credit_supplier_annual_commision = floatval($selectedCreditSupplierDto->getAnnualCommision());
            $this->addParam("credit_supplier_interest_percent", $annualInterestPercent);
            $this->addParam("credit_supplier_annual_commision", $credit_supplier_annual_commision);

            //credit monthly payment calculation
            $creditManager = CreditManager::getInstance($this->config, $this->args);
            $monthlyPayment = $creditManager->calcCredit($grandTotalAMD, $cho_selected_deposit_amount, $annualInterestPercent + $credit_supplier_annual_commision, $cho_selected_credit_months, $commission);
            $this->addParam("credit_monthly_payment", round($monthlyPayment));

            $this->addParam("minimum_credit_amount", intval($selectedCreditSupplierDto->getMinimumCreditAmount()));
            $grandTotalAmdWithCommission = intval($grandTotalAMD / (1 - $commission / 100));
            $this->addParam("grandTotalAmdWithCommission", $grandTotalAmdWithCommission);
        }

        $this->addParam('req_params', $_REQUEST);
    }

    public function getDefaultLoads($args) {
        $loads = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/checkout/payment_step_inner.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$userCompanyRequest;
    }

}

?>