<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/ItemWarrantiesMapper.class.php");

/**
 * ItemWarrantiesManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class ItemWarrantiesManager extends AbstractManager {

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
		$this->mapper = ItemWarrantiesMapper::getInstance();
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

			self::$instance = new ItemWarrantiesManager($config, $args);
		}
		return self::$instance;
	}

	public function addItemWarranty($companyId, $serial_number, $item_buyer, $item_category, $warranty_period, $customer_warranty_start_date, $supplier, $supplier_warranty_start_date, $supplier_warranty_period) {
		$dto = $this->mapper->createDto();
		$dto->setCompanyId($companyId);
		$dto->setSerialNumber($serial_number);
		$dto->setBuyer($item_buyer);
		$dto->setItemCategory($item_category);
		$dto->setCustomerWarrantyPeriod($warranty_period);
		$dto->setCustomerWarrantyStartDate($customer_warranty_start_date);
		$dto->setSupplier($supplier);
		$dto->setSupplierWarrantyStartDate($supplier_warranty_start_date);
		$dto->setSupplierWarrantyPeriod($supplier_warranty_period);
		$this->mapper->insertDto($dto);
	}

	public function editItemWarranty($id, $companyId, $serial_number, $item_buyer, $item_category, $warranty_period, $customer_warranty_start_date, $supplier, $supplier_warranty_start_date, $supplier_warranty_period) {
		$dto = $this->mapper->selectByPK($id);
		$dto->setCompanyId($companyId);
		$dto->setSerialNumber($serial_number);
		$dto->setBuyer($item_buyer);
		$dto->setItemCategory($item_category);
		$dto->setCustomerWarrantyPeriod($warranty_period);
		$dto->setCustomerWarrantyStartDate($customer_warranty_start_date);
		$dto->setSupplier($supplier);
		$dto->setSupplierWarrantyStartDate($supplier_warranty_start_date);
		$dto->setSupplierWarrantyPeriod($supplier_warranty_period);
		$this->mapper->updateByPK($dto);
	}

	public function getCompanyWarrantyItems($companyId, $search_serial_number, $offset, $limit) {
		return $this->mapper->getCompanyWarrantyItems($companyId, $search_serial_number, $offset, $limit);
	}

	public function getCompanyAllWarrantyItems($companyId) {
		return $this->mapper->getCompanyAllWarrantyItems($companyId);
	}

}

?>