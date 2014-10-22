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
class NoAccessException extends Exception {

	/**
	 * Return a thingie based on $paramie
	 * @abstract  
	 * @access
	 * @param boolean $paramie 
	 * @return integer|babyclass
	 */
	public function __construct($message) {
		parent::__construct($message, 0);
	}

}

?>