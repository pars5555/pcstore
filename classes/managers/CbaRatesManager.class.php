
<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");
require_once (CLASSES_PATH . "/dal/mappers/CbaRatesMapper.class.php");

/**
 * AdminManager class is responsible for creating,
 *
 * @author Vahagn Sookiasian
 * @package managers
 * @version 1.0
 */
class CbaRatesManager extends AbstractManager {

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
		$this->mapper = CbaRatesMapper::getInstance();
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

			self::$instance = new CbaRatesManager($config, $args);
		}
		return self::$instance;
	}

	public function addRow($datetime, $iso, $amount, $rate) {
		$dto = $this->mapper->createDto();
		$dto->setCbaDatetime($datetime);
		$dto->setIso($iso);
		$dto->setAmount($amount);
		$dto->setRate($rate);
		$dto->setDatetime(date('Y-m-d H:i:s'));
		return $this->mapper->insertDto($dto);
	}
        
        public function getLatestUSDExchange()
        {
            $this->mapper->getLatestUSDExchange();
        }

}

?>