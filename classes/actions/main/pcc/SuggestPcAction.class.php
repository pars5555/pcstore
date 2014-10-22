<?php

require_once (CLASSES_PATH . "/actions/GuestAction.class.php");
require_once (CLASSES_PATH . "/managers/pcc_managers/PcAutoConfiguratorManager.class.php");

/**
 * @author Vahagn Sookiasian
 */
class SuggestPcAction extends GuestAction {

    public function service() {
        $pcacm = PcAutoConfiguratorManager::getInstance($this->config, $this->args, $this->getUser());
        $total_price = floatval($this->secure($_REQUEST['total_price']));
        $total_price /= $this->getCmsVar('us_dollar_exchange');
        $gaming = $this->secure($_REQUEST['gaming_pc']);
        $onlyCase = $this->secure($_REQUEST['only_case']);
        list($case, $mb, $cpu, $cooler, $ram, $hdd, $opt, $monitor, $graphics, $power, $keyboard, $mouse, $speaker) = $pcacm->suggestPcByPrice($total_price, $gaming == 1, $onlyCase == 1);

        $jsonArr = array('status' => "ok", "chassis" => $case, "mb" => $mb, "cpu" => $cpu, "cooler" => $cooler,
            "ram" => $ram, "hdd" => $hdd, "opt" => $opt, "monitor" => $monitor,
            "graphics" => $graphics, "power" => $power, "keyboard" => $keyboard,
            "mouse" => $mouse, "speaker" => $speaker);
        echo json_encode($jsonArr);
        return true;
    }

    public function getRequestGroup() {
        return RequestGroups::$guestRequest;
    }

}

?>