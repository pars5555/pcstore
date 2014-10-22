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
class RedirectException extends Exception {

    private $redirectTo;

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    public function __construct($redirectTo, $message) {
        $this->redirectTo = $redirectTo;
        parent::__construct($message, 1);
    }

    public function getRedirectTo() {
        return $this->redirectTo;
    }

}

?>