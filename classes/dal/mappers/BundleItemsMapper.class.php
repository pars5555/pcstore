<?php

require_once (CLASSES_PATH . "/dal/mappers/AbstractMapper.class.php");
require_once (CLASSES_PATH . "/dal/dto/BundleItemsDto.class.php");

/**
 *
 * 	@author	Vahagn Sookiasian
 */
class BundleItemsMapper extends AbstractMapper {

	/**
	 * @var table name in DB
	 */
	private $tableName;

	/**
	 * @var an instance of this class
	 */
	private static $instance = null;

	/**
	 * Initializes DBMS pointers and table name private class member.
	 */
	function __construct() {
		// Initialize the dbms pointer.
		AbstractMapper::__construct();

		// Initialize table name.
		$this->tableName = "bundle_items";
	}

	/**
	 * Returns an singleton instance of this class
	 * @return
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new BundleItemsMapper();
		}
		return self::$instance;
	}

	/**
	 * @see AbstractMapper::createDto()
	 */
	public function createDto() {
		return new BundleItemsDto();
	}

	/**
	 * @see AbstractMapper::getPKFieldName()
	 */
	public function getPKFieldName() {
		return "id";
	}

	/**
	 * @see AbstractMapper::getTableName()
	 */
	public function getTableName() {
		return $this->tableName;
	}

	public static $GET_LAST_BUNDLE_ID = "SELECT bundle_id FROM `%s` ORDER BY bundle_id DESC LIMIT 1";

	/**
	 * return maximum bundle_id in the table, or 0 if table is empty
	 */
	public function getLastBundleId() {
		$sqlQuery = sprintf(self::$GET_LAST_BUNDLE_ID, $this->getTableName());
		$result = $this->fetchRows($sqlQuery);
		if (count($result) === 1) {
			return $this->fetchField($sqlQuery, "bundle_id");
		} else {
			return 0;
		}
	}

	public static $DELETE_BUNDLE = "DELETE FROM `%s` WHERE `bundle_id` = %d";

	public function deleteBundle($bundleId) {
		$sqlQuery = sprintf(self::$DELETE_BUNDLE, $this->getTableName(), $bundleId);
		return $this->dbms->query($sqlQuery);
	}

	public static $DELETE_BUNDLES = "DELETE FROM `%s` WHERE `bundle_id` IN (%s)";

	public function deleteBundles($bundlesIds) {
		$sqlQuery = sprintf(self::$DELETE_BUNDLES, $this->getTableName(), $bundlesIds);
		return $this->dbms->query($sqlQuery);
	}

	public static $CHANGE_ITEM_LAST_PRICE = "UPDATE `%s` SET `item_last_dealer_price` = %f WHERE `bundle_id` = %d AND `item_id` = %d";

	public function changeBundleItemLastDealerPriceByItemId($bundleId, $itemId, $newPrice) {
		$sqlQuery = sprintf(self::$CHANGE_ITEM_LAST_PRICE, $this->getTableName(), $newPrice, $bundleId, $itemId);
		return $this->dbms->query($sqlQuery);
	}

}

?>