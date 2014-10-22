<?php

/**
 * Sample File 2, phpDocumentor Quickstart
 *
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Vahagn Sookiasian <vahagnsookaisyan@gmail.com>
 * @version 1.2
 * @package framework
 */
abstract class AbstractUser {

	private $sessionParams = array();
	private $cookieParams = array();

	/**
	 * Return a thingie based on $paramie
	 * @abstract
	 * @access
	 * @param boolean $paramie
	 * @return integer|babyclass
	 */
	public function __construct() {
		
	}

	/**
	 * Return a thingie based on $paramie
	 * @abstract
	 * @access
	 * @param boolean $paramie
	 * @return integer|babyclass
	 */
	public function setSessionParam($name, $value) {
		$this->sessionParams[$name] = $value;
	}

	/**
	 * Return a thingie based on $paramie
	 * @abstract
	 * @access
	 * @param boolean $paramie
	 * @return integer|babyclass
	 */
	public function getSessionParam($name) {
		return $this->sessionParams[$name];
	}

	/**
	 * Return a thingie based on $paramie
	 * @abstract
	 * @access
	 * @param boolean $paramie
	 * @return integer|babyclass
	 */
	public function getSessionParams() {
		return $this->sessionParams;
	}

	/**
	 * Return a thingie based on $paramie
	 * @abstract
	 * @access
	 * @param boolean $paramie
	 * @return integer|babyclass
	 */
	public function setCookieParam($name, $value) {
		$this->cookieParams[$name] = $value;
	}

	/**
	 * Return a thingie based on $paramie
	 * @abstract
	 * @access
	 * @param boolean $paramie
	 * @return integer|babyclass
	 */
	public function getCookieParam($name) {
		return $this->cookieParams[$name];
	}

	/**
	 * Return a thingie based on $paramie
	 * @abstract
	 * @access
	 * @param boolean $paramie
	 * @return integer|babyclass
	 */
	public function getCookieParams() {
		return $this->cookieParams;
	}

	/**
	 * Return a thingie based on $paramie
	 * @abstract
	 * @access
	 * @param boolean $paramie
	 * @return integer|babyclass
	 */
	public abstract function validate();

	/**
	 * Return a thingie based on $paramie
	 * @abstract
	 * @access
	 * @param boolean $paramie
	 * @return integer|babyclass
	 */
	public abstract function getLevel();
}

?>