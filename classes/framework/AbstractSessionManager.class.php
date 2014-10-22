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
abstract class AbstractSessionManager {

	protected $args;
	protected $dispatcher;

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
	public function setArgs(&$args) {
		$this->args = $args;
	}

	/**
	 * Return a thingie based on $paramie
	 * @abstract  
	 * @access
	 * @param boolean $paramie 
	 * @return integer|babyclass
	 */
	public function setDispatcher($dispatcher) {
		$this->dispatcher = $dispatcher;
	}

	/**
	 * Return a thingie based on $paramie
	 * @abstract  
	 * @access
	 * @param boolean $paramie 
	 * @return integer|babyclass
	 */
	public abstract function getUser();

	/**
	 * Return a thingie based on $paramie
	 * @abstract  
	 * @access
	 * @param boolean $paramie 
	 * @return integer|babyclass
	 */
	public abstract function validateRequest($request, $user);

	/**
	 * Return a thingie based on $paramie
	 * @abstract  
	 * @access
	 * @param boolean $paramie 
	 * @return integer|babyclass
	 */
	public function setUser($user, $remember = false, $useDomain = true) {
		$sessionTimeout = $remember ? 2078842581 : null;
		$domain = false;

		if ($useDomain) {
			if (HTTP_ROOT_HOST === HTTP_HOST) {
				$domain = "." . HTTP_HOST;
			} else {
				$domain = HTTP_ROOT_HOST;
			}
		}

		$cookieParams = $user->getCookieParams();

		foreach ($cookieParams as $key => $value) {
			setcookie($key, $value, $sessionTimeout, "/", $domain);
		}
		$sessionParams = $user->getSessionParams();

		foreach ($sessionParams as $key => $value) {
			$_SESSION[$key] = $value;
		}
	}

	/**
	 * Update user hash code
	 * @abstract  
	 * @access
	 * @param boolean $paramie 
	 * @return integer|babyclass
	 */
	public function updateUserUniqueId($user) {

		if (HTTP_ROOT_HOST === HTTP_HOST) {
			$domain = "." . HTTP_HOST;
		} else {
			$domain = HTTP_ROOT_HOST;
		}

		$cookieParams = $user->getCookieParams();

		setcookie("uh", $cookieParams["uh"], null, "/", $domain);
	}

	/**
	 * Return a thingie based on $paramie
	 * @abstract  
	 * @access
	 * @param boolean $paramie 
	 * @return integer|babyclass
	 */
	public function removeUser(AuthenticateUser $user, $useDomain = true) {
		$sessionTimeout = time() - 42000;

		$domain = false;

		if ($useDomain) {
			if (HTTP_ROOT_HOST === HTTP_HOST) {
				$domain = "." . HTTP_HOST;
			} else {
				$domain = HTTP_ROOT_HOST;
			}
		}

		$cookieParams = $user->getCookieParams();

		foreach ($cookieParams as $key => $value) {
			setcookie($key, '', $sessionTimeout, "/", $domain);
		}


		//$this->deleteSession();
	}

	// supprime la session en cours ...
	private function deleteSession() {
		$_SESSION = array();

		if (isset($_COOKIE[session_name()])) {
			@setcookie(session_name(), '', time() - 42000, '/');
		}
		session_destroy();
	}

}

?>