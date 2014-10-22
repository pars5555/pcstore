<?php

require_once (CLASSES_PATH . "/loads/company/CompanyLoad.class.php");

/**
 *
 * @author Vahagn Sookiasian
 *
 */
class Next24HoursSelectLoad extends CompanyLoad {

    public function load() {

        if ($this->args['start_time']) {
            $start_time = $this->args['start_time'];
        } else {
            $start_time = $this->secure($_REQUEST['start_time']);
        }
        $minutes_block = 30;
        $total_day_minutes = 24 * 60;
        $cycleIndex = 0;
        $values = array();
        $timesDisplayNames = array();
        while (++$cycleIndex * $minutes_block <= $total_day_minutes) {
            $timestamp = strtotime($start_time);
            $mins = $cycleIndex * $minutes_block;
            $time = strtotime("+$mins minutes", $timestamp);
            $values[] = $mins;
            if ($time < strtotime("23:59:00")) {
                $timesDisplayNames[] = date('H:i', $time) . ' ' . $this->getPhrase(402);
            } else {
                $timesDisplayNames[] = date('H:i', $time) . ' ' . $this->getPhrase(401);
            }
        }
        $this->addParam('values', $values);
        $this->addParam('timesDisplayNames', $timesDisplayNames);
    }

    public function getTemplate() {
        return TEMPLATES_DIR . "/company/next_24_hours_select.tpl";
    }

}

?>