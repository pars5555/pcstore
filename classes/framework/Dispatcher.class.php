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
require_once(CLASSES_PATH . "/util/FAZSmarty.class.php");
require_once(CLASSES_PATH . "/framework/exceptions/ClientException.class.php");

require_once(CLASSES_PATH . "/framework/exceptions/RedirectException.class.php");
require_once(CLASSES_PATH . "/framework/exceptions/NoAccessException.class.php");

class Dispatcher {

    protected $config;
    protected $toCache = false;
    protected $smarty;
    private $isAjax = false;

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    public function __construct($config, $sessionManager, $loadMapper, $packages, $notFoundURL = false) {
        $this->config = $config;
        $this->sessionManager = $sessionManager;
        $this->loadMapper = $loadMapper;
        $this->actionPackage = $packages["actions"];
        $this->loadsPackage = $packages["loads"];
        $this->notFoundURL = $notFoundURL;
        $this->smarty = new FAZSmarty($this->config["VERSION"]);
        $this->loadMapper->setSmarty($this->smarty);
        $this->loadMapper->initialize();

        $command = "";
        $isAjax = false;
        $args = array();
        if (preg_match_all("/(\/([^\/]+))/", $_REQUEST["_url"], $matches)) {
            if ($matches[2][0] == "dynamic") {
                array_shift($matches[2]);
                $dynamicIndex = 9; //--/dynamic/
                $loadArr = $this->loadMapper->getDynamicLoad(substr($_REQUEST["_url"], $dynamicIndex), $matches[2]);

                if (is_array($loadArr)) {
                    $package = $loadArr["package"];
                    $command = $loadArr["command"];
                    $args = $loadArr["args"];
                } else {

                    $this->showNotFound();
                    return false;
                }
            } else {
                $package = array_shift($matches[2]);
                $command = array_shift($matches[2]);
                $args = $matches[2];
                if (isset($args[count($args) - 1])) {
                    if (preg_match("/(.+?)\.ajax/", $args[count($args) - 1], $matches1)) {
                        $this->isAjax = true;
                        $args[count($args) - 1] = $matches1[1];
                    }
                }
                $package = str_replace("_", "/", $package);
            }
        }
        //--replacing separarators for getting real package's path


        $this->sessionManager->setArgs($args);
        $this->sessionManager->setDispatcher($this);

        $this->dispatch($package, $command, $args);
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    public function setIsAjax($isAjax) {
        $this->isAjax = $isAjax;
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    public function isAjax() {
        return $this->isAjax;
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    public function dispatch($package, $command, &$args) {

        $this->args = &$args;
        if ($command == "") {
            $command = "default";
        }
        $isCommand = false;
        if (strripos($command, "do_") === 0) {
            $isCommand = true;
            $command = substr($command, 3);
        }
        $command = ucfirst($command);

        function callbackhandler($matches) {
            return strtoupper(ltrim($matches[0], "_"));
        }

        $command = preg_replace_callback("/_(\w)/", "callbackhandler", $command);
        try {
            if ($command) {
                if ($isCommand) {
                    $this->doAction($package, $command);
                } else {
                    $this->loadPage($package, $command);
                }
            }
        } catch (ClientException $ex) {
            $errorArr = $ex->getErrorParams();
            $ret = "[{";
            if (is_array($errorArr)) {
                $delim = "";

                foreach ($errorArr as $key => $value) {
                    $ret .= $delim;
                    $ret .= "'" . $key . "': {code: '" . $value["code"] . "', message: '" . $value["message"] . "'}";
                    $delim = ",";
                }
            }
            $ret .= "}]";

            header("HTTP/1.0 403 Forbidden");
            echo($ret);
            exit();
        } catch (RedirectException $ex) {
            $this->redirect($ex->getRedirectTo());
        } catch (Exception $ex) {
            $this->showNotFound();
        }
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    public function loadPage($package, $command, $args = false) {
        $loadName = $command . "Load";
        $actionFileName = CLASSES_PATH . "/" . $this->loadsPackage . "/" . $package . "/" . $loadName . ".class.php";
        try {
            if (file_exists($actionFileName)) {
                require_once($actionFileName);
                $loadObj = new $loadName();

                if ($args) {
                    $this->args = array_merge($this->args, $args);
                }
                $loadObj->initialize($this->smarty, $this->sessionManager, $this->config, $this->loadMapper, $this->args);

                $loadObj->setDispatcher($this);

                if ($this->validateRequest($loadObj)) {
                    $this->toCache = $loadObj->toCache();
                    if (!$this->toCache) {
                        $this->dontCache();
                    }

                    $loads = $loadObj->getDefaultLoads($this->args);
                    $loadObj->service($loads); //passing arguments
                    $params = $loadObj->getParams();
                    header("Content-Type: text/html; charset=UTF-8");
                    $templateName = $loadObj->getTemplate();
                    if ($templateName != null) {

                        $this->displayResult($templateName, $params);
                    }
                    return;
                }

                if ($loadObj->onNoAccess()) {
                    return;
                }
            }
        } catch (NoAccessException $ex) {
            $loadObj->onNoAccess();
        }


        $this->showNotFound();
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    private function doAction($package, $action) {
        $actionName = $action . "Action";
        $actionFileName = CLASSES_PATH . "/" . $this->actionPackage . "/" . $package . "/" . $actionName . ".class.php";

        if (file_exists($actionFileName)) {
            require_once($actionFileName);
            $actionObj = new $actionName();
            $actionObj->initialize(null, $this->sessionManager, $this->config, $this->loadMapper, $this->args);
            $actionObj->setDispatcher($this);
            if ($this->validateRequest($actionObj)) {
                $this->toCache = $actionObj->toCache();
                if (!$this->toCache) {
                    $this->dontCache();
                }
                $actionObj->service();
                return;
            }

            if ($actionObj->onNoAccess()) {
                return;
            }
        }

        $this->showNotFound();
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    private function validateRequest($request) {
        $user = $this->sessionManager->getUser();
        if ($user->validate()) {

            if ($this->sessionManager->validateRequest($request, $user)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    private function displayResult($dispatcherIndex, $params) {
        if (!$this->toCache) {
            $this->dontCache();
        }
        if ($this->isAjax) {
            $ret = $this->createJSON($params);
            echo($ret);
        } else {

            $this->smarty->assign("ns", $params);
            $this->smarty->assign("pm", $this->loadMapper);
            $this->smarty->display($dispatcherIndex);
        }
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    private function createJSON($arr) {
        $ret = "[{";
        if (is_array($arr)) {
            $delim = "";
            if (isset($arr["inc"])) {
                foreach ($arr["inc"] as $key => $value) {
                    $ret .= $delim;
                    $innerObj = $this->createJSON($value);
                    $ret .= $key . ":" . $innerObj;
                    $delim = ",";
                }
                unset($arr["inc"]);
            }

            foreach ($arr as $key => $value) {
                $ret .= $delim;
                $ret .= $key . ":" . $value;
                $delim = ",";
            }
        }
        $ret .= "}]";
        return $ret;
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    public function redirect($url) {
        $protocol = "http://";
        if (isset($_SERVER["HTTPS"])) {
            $protocol = "https://";
        }
        header("location: " . $protocol . HTTP_HOST . "/$url");
        exit;
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    protected function showNotFound() {
        if ($this->notFoundURL) {
            header("location: " . $this->notFoundURL);
        } else {
            header("HTTP/1.0 404 Not Found");
        }
        exit();
    }

    /**
     * Return a thingie based on $paramie
     * @abstract  
     * @access
     * @param boolean $paramie 
     * @return integer|babyclass
     */
    protected function dontCache() {
        Header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        Header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        Header("Pragma: no-cache"); // HTTP/1.0
        Header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
    }

}

?>