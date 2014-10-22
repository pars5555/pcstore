<?php

require_once (CLASSES_PATH . "/loads/GuestLoad.class.php");

class MainLoad extends GuestLoad {

    /**
     *
     * @author Vahagn Sookiasian
     * test Load
     */
    public function load() {
        
    }

    public function getDefaultLoads($args) {
        $loads = array();
        $page = 'home';
        if (isset($args[0])) {
            $page = $args[0];
        }
        $pagePartsArray = explode('_', $page);
        if (!function_exists('_ucfirst')) {

            function _ucfirst(&$item, $key) {
                $item = ucfirst($item);
            }

        }
        $this->addParam('contentLoad', $page);
        array_walk($pagePartsArray, '_ucfirst');
        $loadName = implode('', $pagePartsArray) . "Load";
        $loads["content"]["load"] = "loads/cms/" . $loadName;
        $loads["content"]["args"] = array("mainLoad" => &$this);
        $loads["content"]["loads"] = array();
        return $loads;
    }

    public function isValidLoad($namespace, $load) {
        return true;
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/cms/main.tpl";
    }

    public function isMain() {
        return true;
    }

}

?>
