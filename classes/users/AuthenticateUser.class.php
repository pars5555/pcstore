<?php

require_once(CLASSES_PATH . "/framework/AbstractUser.class.php");

/**
 * This class is a template for all authorized user classes.
 * 
 * @author Vahagn Sookiasian, Yerem Khalatyan
 * @package users
 */
abstract class AuthenticateUser extends AbstractUser {

	/**
	 * @var - unique identifier per session
	 */
	protected $uniqueId;

	/**
	 * @var - user's invariant identifier
	 */
	protected $id;

	/**
	 * Each authorized user should have a identifier, 
	 * which will be passed to constructor when creating instance.
	 * 
	 * @param object $id - user identifier
	 * @return 
	 */
	public function __construct($id) {
		$this->setId($id);
	}

	/**
	 * Set unique identifier
	 * 
	 * @param object $uniqueId
	 * @return 
	 */
	public function setUniqueId($uniqueId) {
		$this->setCookieParam("uh", $uniqueId);
	}

	/**
	 * Set permanent identifier
	 * 
	 * @param object $id
	 * @return 
	 */
	public function setId($id) {
		$this->setCookieParam("ud", $id);
	}

	/**
	 * Returns unique identifier
	 * 
	 * @return
	 */
	public function getUniqueId() {
		return $this->getCookieParam("uh");
	}

	/**
	 * Returns permanent identifier 
	 * 
	 * @return 
	 */
	public function getId() {
		return $this->getCookieParam("ud");
	}

	/**
	 * Returns ADMIN level.
	 * 
	 * @return int
	 */
	public function getLevel() {
		return $this->getCookieParam("ut");
	}

}

?>