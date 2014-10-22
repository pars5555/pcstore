<?php

require_once(CLASSES_PATH . "/framework/Dispatcher.class.php");
require_once(CLASSES_PATH . "/framework/AbstractRequest.class.php");
require_once(CLASSES_PATH . "/framework/AbstractSessionManager.class.php");

abstract class AbstractAction extends AbstractRequest {

    public function initialize($smarty, $sessionManager, $config, $loadMapper, $args) {
        parent::initialize($smarty, $sessionManager, $config, $loadMapper, $args);
    }

    public function load() {
        $this->service();
    }

    public abstract function service();
}

?>