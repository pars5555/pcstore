<?php

require_once (CLASSES_PATH . "/managers/AbstractManager.class.php");

/**
 * PcConfiguratorManager class is responsible for creating,
 */
class ItemSearchManager extends AbstractManager {

    /**
     * @var app config
     */
    private $config;

    /**
     * @var passed arguemnts
     */
    private $args;

    /**
     * @var singleton instnce of class
     */
    private static $instance = null;

    /**
     * Initializes DB mappers
     *
     * @param object $config
     * @param object $args
     * @return
     */
    function __construct($config, $args) {
        $this->config = $config;
        $this->args = $args;
    }

    /**
     * Returns an singleton instance of this class
     *
     * @param object $config
     * @param object $args
     * @return
     */
    public static function getInstance($config, $args) {

        if (self::$instance == null) {

            self::$instance = new ItemSearchManager($config, $args);
        }
        return self::$instance;
    }

    /* this is for smarty 3 to pass map array
      public function getUrlParams($changedParams = null) {

      if (isset($_REQUEST["spg"])) {
      $spg = intval($_REQUEST["spg"]);
      }
      if (isset($_REQUEST["sci"]) && intval($_REQUEST["sci"]) > 0) {
      $sci = intval($_REQUEST["sci"]);
      }
      $tmp1 = $this->secure($_REQUEST["cid"]);
      if (!empty($tmp1)) {
      $cid = $tmp1;
      }
      $tmp2 = $this->secure($_REQUEST["scpids"]);
      if (!empty($tmp2)) {
      $scpids = $tmp2;
      }
      if (isset($_REQUEST["prmin"]) && intval($_REQUEST["prmin"]) > 0) {
      $prmin = intval($_REQUEST["prmin"]);
      }
      if (isset($_REQUEST["prmax"]) && intval($_REQUEST["prmax"]) > 0) {
      $prmax = intval($_REQUEST["prmax"]);
      }
      $tmp3 = $this->secure($_REQUEST["srt"]);
      if (!empty($tmp3)) {
      $srt = $tmp3;
      }
      $tmp4 = $this->secure($_REQUEST["st"]);
      if (!empty($tmp4)) {
      $st = $tmp4;
      }
      $tmp5 = $this->secure($_REQUEST["shov"]);
      if (!empty($tmp5)) {
      $shov = $tmp5;
      }
      if (isset($changedParams) && is_array($changedParams)) {
      foreach ($changedParams as $key => $value) {
      $$key = $value;
      }
      }
      return http_build_query(array("spg" => $spg, "sci" => $sci, "cid" => $cid, "scpids" => $scpids
      , "prmin" => $prmin, "prmax" => $prmax, "srt" => $srt, "st" => $st, "shov" => $shov));
      }
     */

    public function getUrlParams($paramName = null, $paramValue = null, $paramsToRest = null) {
//var_dump('sss');
        if (isset($_REQUEST["spg"])) {
            $spg = intval($_REQUEST["spg"]);
        }
        if (isset($_REQUEST["sci"]) && intval($_REQUEST["sci"]) > 0) {
            $sci = intval($_REQUEST["sci"]);
        }
        $tmp1 = $this->secure($_REQUEST["cid"]);
        if (!empty($tmp1)) {
            $cid = $tmp1;
        }
        $tmp2 = $this->secure($_REQUEST["scpids"]);
        if (!empty($tmp2)) {
            $scpids = $tmp2;
        }
        if (isset($_REQUEST["prmin"]) && intval($_REQUEST["prmin"]) > 0) {
            $prmin = intval($_REQUEST["prmin"]);
        }
        if (isset($_REQUEST["prmax"]) && intval($_REQUEST["prmax"]) > 0) {
            $prmax = intval($_REQUEST["prmax"]);
        }
        $tmp3 = $this->secure($_REQUEST["srt"]);
        if (!empty($tmp3)) {
            $srt = $tmp3;
        }
        $tmp4 = $this->secure($_REQUEST["st"]);
        if (!empty($tmp4)) {
            $st = $tmp4;
        }
        $tmp5 = $this->secure($_REQUEST["shov"]);
        if (!empty($tmp5)) {
            $shov = $tmp5;
        }
        if (isset($paramsToRest) && is_array($paramsToRest)) {
            foreach ($paramsToRest as $paramNameToUnset) {
                unset($$paramNameToUnset);
            }
        }
        if (isset($paramName)) {
            $$paramName = $paramValue;
        }

        return http_build_query(array("spg" => $spg, "sci" => $sci, "cid" => $cid, "scpids" => $scpids
            , "prmin" => $prmin, "prmax" => $prmax, "srt" => $srt, "st" => $st, "shov" => $shov));
    }

    public function getMapper() {
        return null;
    }

}

?>