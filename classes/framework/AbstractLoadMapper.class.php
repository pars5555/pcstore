<?php

abstract class AbstractLoadMapper {

    private $params;
    protected $config;
    protected $smarty;

    public function __construct($config) {
        $this->config = $config;
    }

    public final function setSmarty($smarty) {
        $this->smarty = $smarty;
    }

    public abstract function initialize();

    public abstract function getCurrentLoads();

    public abstract function getDynamicLoad($url, $matches);

    public function getParam($name) {
        $param = null;
        if (!$this->params) {
            $ns = $this->smarty->get_template_vars("ns");
            $param = $ns[$name];
        } else {
            $param = $this->params[$name];
        }

        return $param;
    }

    public function setParam($name, $value) {
        $ns = $this->smarty->get_template_vars("ns");
        return $ns[$name];
    }

    public abstract function __get($nm);

    public abstract function __call($name, $arguments);

    public function setParams($params) {
        $this->params = $params;
    }

}

?>