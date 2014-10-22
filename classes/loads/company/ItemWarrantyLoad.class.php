<?php

require_once (CLASSES_PATH . "/loads/company/CompanyLoad.class.php");
require_once (CLASSES_PATH . "/managers/ItemWarrantiesManager.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class ItemWarrantyLoad extends CompanyLoad {

    public function load() {
        $item_warranty_options = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 'Lifetime');
        $item_warranty_categories_options = array('Not Specified', 'Laptop', 'HDD', 'DVD/Blu-ray', 'Motherboard', 'Memory (RAM)', 'Processor (CPU)', 'Monitor', 'Graphics Card', 'UPS', 'Printer', 'Network', 'Other');

        $this->addParam('item_warranty_options', $item_warranty_options);
        $this->addParam('item_warranty_categories_options', $item_warranty_categories_options);

        $companyId = $this->sessionManager->getUser()->getId();
        $itemWarrantiesManager = ItemWarrantiesManager::getInstance($this->config, $this->args);

        $search_serial_number = null;
        if (isset($_REQUEST["search_serial_number"])) {
            $search_serial_number = $this->secure($_REQUEST["search_serial_number"]);
        }

        $ItemsWarranties = $itemWarrantiesManager->getCompanyWarrantyItems($companyId, $search_serial_number, 0, 500);

        $this->addParam('ItemsWarranties', $ItemsWarranties);
        $this->addParam('search_serial_number', $search_serial_number);

        if (isset($_REQUEST["warranty_item_id"])) {
            $warranty_item_id = $this->secure($_REQUEST["warranty_item_id"]);
            $itemWarrantyDto = $itemWarrantiesManager->selectByPK($warranty_item_id);
            $itemCategory = $itemWarrantyDto->getItemCategory();
            $serialNumber = $itemWarrantyDto->getSerialNumber();
            $buyer = $itemWarrantyDto->getBuyer();
            $customerWarrantyPeriod = $itemWarrantyDto->getCustomerWarrantyPeriod();
            $customerWarrantyStartDate = $itemWarrantyDto->getCustomerWarrantyStartDate();
            $supplier = $itemWarrantyDto->getSupplier();
            $supplierWarrantyStartDate = $itemWarrantyDto->getSupplierWarrantyStartDate();

            $supplierWarrantyPeriod = $itemWarrantyDto->getSupplierWarrantyPeriod();

            $this->addParam('item_warranty_categories_options_selected', $itemCategory);
            $this->addParam('item_warranty_options_selected', $customerWarrantyPeriod);
            $this->addParam('serial_number', $serialNumber);
            $this->addParam('buyer', $buyer);
            $this->addParam('customer_warranty_start_date', $customerWarrantyStartDate);
            $this->addParam('supplier', $supplier);
            $this->addParam('supplier_warranty_start_date', $supplierWarrantyStartDate);
            $this->addParam('supplier_warranty_period', $supplierWarrantyPeriod);
            $this->addParam('warranty_item_id', $warranty_item_id);
        } else {
            $this->addParam('item_warranty_categories_options_selected', $item_warranty_categories_options[0]);
            $this->addParam('item_warranty_options_selected', $item_warranty_options[12]);
            $this->addParam('supplier_warranty_period', $item_warranty_options[12]);
        }
        $this->addParam('company_id', $companyId);
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/company/item_warranty.tpl";
    }

}

?>