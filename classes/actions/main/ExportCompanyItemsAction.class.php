<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemManager.class.php");
require_once (CLASSES_PATH . "/util/php_excel/excel_xml.php");

/**
 * @author Vahagn Sookiasian
 * @site http://naghashyan.com
 * @mail vahagnsookaisyan@gmail.com
 * @year 2010-2012
 */
class ExportCompanyItemsAction extends GuestAction {

    public $imgType;

    public function service() {
        //todo check if user have access to given company
        $adminManager = new AdminManager($this->config, $this->args);
        $adminId = $this->sessionManager->getUser()->getId();
        $adminDto = $adminManager->selectByPK($adminId);
        if (!$adminDto) {
            return false;
        }

        $companyId = $this->args[0];
        $companyManager = CompanyManager::getInstance($this->config, $this->args);
        $company = $companyManager->selectByPK($companyId);
        if (!$company) {
            return false;
        }

        $ex = new excel_xml();
        $header_style = array('bold' => 1, 'size' => '12', 'color' => '#FFFFFF', 'bgcolor' => '#4F81BD');
        $ex->add_style('header', $header_style);
        $ex->add_row(array('Name', 'Price', 'VAT Price'), 'header');

        $itemManager = ItemManager::getInstance($this->config, $this->args);
        $items = $itemManager->getCompanyItems($companyId);


        foreach ($items as $key => $itemDto) {
            $row = array();
            $name = $itemDto->getDisplayName();
            $row [] = $name;
            $price_usd = $itemDto->getDealerPrice();
            $row [] = '$' . $price_usd;
            if ($itemDto->getVatPrice() > 0) {
                $price_vat_usd = $itemDto->getVatPrice();
                $row [] = '$' . $price_vat_usd;
            }
            //$price_amd = $itemManager->exchangeFromUsdToAMD($itemDto->getDealerPrice());			


            $ex->add_row($row);
        }
        $ex->create_worksheet('Items');
        $ex->generate();
        $ex->download($company->getName());
    }

    public function getRequestGroup() {
        return RequestGroups::$companyRequest;
    }

}

?>