<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/CompanyManager.class.php");
require_once (CLASSES_PATH . "/managers/ItemWarrantiesManager.class.php");
require_once (CLASSES_PATH . "/util/php_excel/excel_xml.php");

/**
 * @author Vahagn Sookiasian
 * @site http://naghashyan.com
 * @mail vahagnsookaisyan@gmail.com
 * @year 2010-2012
 */
class GetCompanyItemsWarrantiesAction extends GuestAction {

	public $imgType;

	public function service() {
		//todo check if user have access to given company

		$customer = $this->sessionManager->getUser();
		$cId = $customer->getId();
		$companyId = $this->args[0];
		if ($companyId != $cId) {
			return false;
		}
		$companyManager = CompanyManager::getInstance($this->config, $this->args);
		$company = $companyManager->selectByPK($companyId);
		if (!$company) {
			return false;
		}

		$ex = new excel_xml();
		$header_style = array('bold' => 1, 'size' => '12', 'color' => '#FFFFFF', 'bgcolor' => '#4F81BD');
		$ex->add_style('header', $header_style);
		$ex->add_row(array('Serial Number', 'Category', 'Customer', 'Customer Warranty Period', 'Customer Purchase Date', 'Supplier', 'Purchase Date From Supplier', 'Supplier Warranty Period'), 'header');

		$itemWarrantiesManager = ItemWarrantiesManager::getInstance($this->config, $this->args);
		$allItemsWarrantiesDtos = $itemWarrantiesManager->getCompanyAllWarrantyItems($companyId);
		foreach ($allItemsWarrantiesDtos as $key => $itemWarrantyDto) {
			$c1 = $itemWarrantyDto->getSerialNumber();
			$c2 = $itemWarrantyDto->getItemCategory();
			$c3 = $itemWarrantyDto->getBuyer();
			$c4 = $itemWarrantyDto->getCustomerWarrantyPeriod();
			$c5 = $itemWarrantyDto->getCustomerWarrantyStartDate();
			$c6 = $itemWarrantyDto->getSupplier();
			$c7 = $itemWarrantyDto->getSupplierWarrantyStartDate();
			$c8 = $itemWarrantyDto->getSupplierWarrantyPeriod();
			$ex->add_row(array($c1, $c2, $c3, $c4, $c5, $c6, $c7, $c8));
		}


		$ex->create_worksheet('Items Warranties');
		$ex->generate();
		$ex->download($this->args[1]);
	}

	public function getRequestGroup() {
		return RequestGroups::$companyRequest;
	}

}

?>