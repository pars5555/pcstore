<?php

class InvalidParameterException extends Exception {

	public function __construct($message) {
		parent::__construct($message, 1);
	}

}

?>