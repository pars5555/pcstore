<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");
require_once (CLASSES_PATH . "/managers/checkout/CreditManager.class.php");
require_once (CLASSES_PATH . "/managers/CreditSuppliersManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class PccCreditCalculationLoad extends GuestLoad {

    public function load() {

        if (isset($this->args['pcGrandTotalAmd']) && isset($this->args['pcGrandTotalUsd'])) {
            $_REQUEST['pcGrandTotalAmd'] = $this->args['pcGrandTotalAmd'];
            $_REQUEST['pcGrandTotalUsd'] = $this->args['pcGrandTotalUsd'];
        }

        //credit supplier
        $creditSuppliersManager = CreditSuppliersManager::getInstance($this->config, $this->args);
        $allCreditSuppliers = $creditSuppliersManager->getAllCreditSuppliers();
        $allCreditSuppliers = $creditSuppliersManager->getCreditSuppliersInMapArrayById($allCreditSuppliers);
        $creditSuppliersDisplayNamesIds = $creditSuppliersManager->getSuppliersDisplayNameIdsArray($allCreditSuppliers);
        $creditSuppliersDisplayNames = $this->getPhrases($creditSuppliersDisplayNamesIds);
        $defaultCreditSupplierDto = reset($allCreditSuppliers);
        $selected_credit_supplier_id = $defaultCreditSupplierDto->getId();
        if (isset($_REQUEST['pcc_credit_supplier_id'])) {
            $selected_credit_supplier_id = $_REQUEST['pcc_credit_supplier_id'];
        }
        $_REQUEST['pcc_credit_supplier_id'] = $selected_credit_supplier_id;
        $this->addParam("creditSuppliersIds", array_keys($allCreditSuppliers));
        $this->addParam("creditSuppliersDisplayNames", $creditSuppliersDisplayNames);

        $selectedCreditSupplierDto = $allCreditSuppliers[$selected_credit_supplier_id];

        //credit supplier possible months
        $possibleCreditMonths = explode(',', $selectedCreditSupplierDto->getPossibleCreditMonths());

        $pcc_selected_credit_months = $possibleCreditMonths[0];
        if (isset($_REQUEST['pcc_selected_credit_months'])) {
            $pcc_selected_credit_months = $_REQUEST['pcc_selected_credit_months'];
        }
        if (!in_array($pcc_selected_credit_months, $possibleCreditMonths)) {
            $pcc_selected_credit_months = $possibleCreditMonths[0];
        }
        $_REQUEST['pcc_selected_credit_months'] = $pcc_selected_credit_months;

        $this->addParam("possibleCreditMonths", $possibleCreditMonths);

        //deposit amount
        $pcc_selected_deposit_amount = 0;
        if (isset($_REQUEST['pcc_selected_deposit_amount'])) {
            $pcc_selected_deposit_amount = intval($_REQUEST['pcc_selected_deposit_amount']);
        }

        $_REQUEST['pcc_selected_deposit_amount'] = $pcc_selected_deposit_amount;

        //credit supplier interest
        $commission = $selectedCreditSupplierDto->getCommission();
        $annualInterestPercent = floatval($selectedCreditSupplierDto->getAnnualInterestPercent());
        $credit_supplier_annual_commision = floatval($selectedCreditSupplierDto->getAnnualCommision());
        $this->addParam("credit_supplier_interest_percent", $annualInterestPercent);
        $this->addParam("credit_supplier_annual_commision", $credit_supplier_annual_commision);

        //credit monthly payment calculation
        $creditManager = CreditManager::getInstance($this->config, $this->args);
        $grandTotalAmd = intval($this->secure($_REQUEST['pcGrandTotalAmd']));
        $grandTotalAmdWithCommission = intval($grandTotalAmd / (1 - $commission / 100));
        $monthlyPayment = $creditManager->calcCredit($grandTotalAmd, $pcc_selected_deposit_amount, $annualInterestPercent + $credit_supplier_annual_commision, $pcc_selected_credit_months, $commission);
        $this->addParam("credit_monthly_payment", round($monthlyPayment));
        $this->addParam("minimum_credit_amount", intval($selectedCreditSupplierDto->getMinimumCreditAmount()));
        $this->addParam('req_params', $_REQUEST);
        $this->addParam('grandTotalAmdWithCommission', $grandTotalAmdWithCommission);
    }

    public function getDefaultLoads($args) {
        $loads = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/main/pc_configurator/pcc_credit_calculation.tpl";
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

}

?>