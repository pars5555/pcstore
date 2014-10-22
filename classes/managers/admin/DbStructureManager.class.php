<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");

/**
 * AdminManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class DbStructureManager {

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
		$this->config = $config;
		$this->args = $args;
		$this->dbms = DBMSFactory::getDBMS();
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

			self::$instance = new DbStructureManager($config, $args);
		}
		return self::$instance;
	}

	public function getTablesNames() {
		$res = $this->dbms->query("SHOW TABLES");
		$tablesNamesArray = array();
		if ($res && $this->dbms->getResultCount($res) > 0) {
			$results = $this->dbms->getResultArray($res);
			foreach ($results as $result) {
				$tablesNamesArray[] = current($result);
			}
		}
		return $tablesNamesArray;
	}

	/**
	 * @return array   ["Field"=> "" ,"Type"=> "", "Null"=> "YES/NO" ,"Key"=> "PRI/MUL" ,"Default"=> "NULL"/ANYVALUE ,"Extra"=> "auto_increment"] 
	 */
	public function getTableColumns($tableName) {
		$res = $this->dbms->query("SHOW COLUMNS FROM `" . $tableName . "`");
		if ($res && $this->dbms->getResultCount($res) > 0) {
			return $this->dbms->getResultArray($res);
		}
		return null;
	}

}

?>