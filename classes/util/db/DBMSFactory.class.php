<?php

require_once(CLASSES_PATH."/util/db/DBMS.class.php");
require_once(CLASSES_PATH."/util/db/ImprovedDBMS.class.php");

/**
 * Returns an instance of DBMS class.
 */
class DBMSFactory {
	
	/**
	 * Indicates which instance of DBMS classes to use.
	 */
	public static $IMPROVED = true;
	
	private static $config;
	
	private static $dbmsInstance = null;
	
	/**
	 * Not used. 
	 */
	function __construct() {
    }
	
	/**
	 * Should be called before getDBMS() function to initialize 
	 * $config property.
	 * 
	 * @param object $config
	 * @return 
	 */
	public static function init($config) {
		self::$config = $config;
	}
	
	/**
	 * Returns corresponding instance of DBMS class.
	 * 
	 * @return 
	 */
	public static function getDBMS(){            
		if(is_null(self::$dbmsInstance)) {
			if(self::$IMPROVED){
				ImprovedDBMS::init(self::$config);
				self::$dbmsInstance = ImprovedDBMS::getInstance();
			}else{
				DBMS::init(self::$config);
				self::$dbmsInstance = DBMS::getInstance();
			}
		}
		return self::$dbmsInstance;
	}
}
?>