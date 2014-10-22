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
require_once(CLASSES_PATH . "/framework/exceptions/NoAccessException.class.php");
require_once(CLASSES_PATH . "/framework/AbstractRequest.class.php");
require_once(CLASSES_PATH . "/util/FAZSmarty.class.php");

abstract class AbstractLoad extends AbstractRequest {

    protected $smarty;

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    public function initialize($smarty, $sessionManager, $config, $loadMapper, $args) {
        parent::initialize($smarty, $sessionManager, $config, $loadMapper, $args);
        $this->smarty = $smarty;
        $this->params = array();
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    public final function service($loads) {

        $templateName = $this->getTemplate();
        $this->params["inc"] = array();
        $this->load();
        $defaultLoads = $this->getDefaultLoads($this->args);
        $defaultLoads = array_merge($defaultLoads, $loads);
        foreach ($defaultLoads as $key => $value) {
            $this->nest($key, $value);
        }
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    protected final function addParam($name, $value) {
        $this->params[$name] = $value;
    }

    /**
     * Return a param value
     * @abstract  
     * @access
     * @param  $name paramerter name
     * @return  value of the given param
     */
    protected final function getParam($name) {
        return $this->params[$name];
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    public function nest($namespace, $loadArr, $isSecur = true) {
        $loadFileName = CLASSES_PATH . "/" . $loadArr["load"] . ".class.php";
        $loadName = substr($loadArr["load"], strrpos($loadArr["load"], "/") + 1);

        if (file_exists($loadFileName) && $this->isValidLoad($namespace, $loadArr["load"])) {
            require_once($loadFileName);
            $loadObj = new $loadName();
            if (isset($loadArr["args"])) {
                $args = array_merge($this->args, $loadArr["args"]);
            }
            $loadObj->initialize($this->smarty, $this->sessionManager, $this->config, $this->loadMapper, $args);
            $allowLoad = false;
            if ($isSecur) {
                if ($this->sessionManager->validateRequest($loadObj, $this->sessionManager->getUser())) {
                    $allowLoad = true;
                }
            }
            if (!$isSecur || $allowLoad) {

                $loadObj->service($loadArr["loads"]);

                $this->params["inc"][$namespace]["filename"] = $loadObj->getTemplate();
                $this->params["inc"][$namespace]["params"] = $loadObj->getParams();
            } else {
                $loadObj->onNoAccess();
            }
        } else {
            throw new NoAccessException("User hasn't access to the load: " . $loadFileName);
        }
        /*
          string(7) "window1"
          array(3) {
          ["load"]=>
          string(21) "snippets/Snippet1Load"
          ["args"]=>
          array(1) {
          ["name"]=>
          string(4) "zzzz"
          }
          ["loads"]=>
          array(0) {
          }
          } */
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    public function includeTemplate($namespace, $template) {

        $this->params["inc"][$namespace]["filename"] = $template;
        $this->params["inc"][$namespace]["ns_parent"] = $this->params;
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    public function toCache() {
        return false;
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    public function getDefaultLoads($args) {
        return array();
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    public function isValidLoad($namespace, $load) {
        return false;
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    public function getTemplate() {
        return null;
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    public abstract function load();
}

?>