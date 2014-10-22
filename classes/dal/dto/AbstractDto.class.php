<?php

/* !
 * 	ConfigDto class is extended class from AbstractDto.
 * 	Year 2012
 * 	@author	Zara Ohanyan
 */

abstract class AbstractDto {

    public function __construct() {
        
    }

    public abstract function getMapArray();

    /*
      The first letter of input string changes to Lower case
     */

    public static function lowerFirstLetter($str) {
        $first = substr($str, 0, 1);
        $asciiValue = ord($first);
        if ($asciiValue >= 65 && $asciiValue <= 90) {
            $asciiValue += 32;
            return chr($asciiValue) . substr($str, 1);
        }
        return $str;
    }

    public function toJSON($htmlSpecialChar = false) {

        return self::dtoToJSON($this, $htmlSpecialChar);
    }

    public static function dtoToJSON($dto, $htmlSpecialChar = false, $fieldsMap = array()) {
        $josn_text = "{";
        if (empty($fieldsMap)) {
            $mapArray = $dto->getMapArray();
        } else {
            $mapArray = $fieldsMap;
        }
        foreach ($mapArray as $key => $value) {
            $josn_text .= '"' . $key . '"' . ':' . '"' . ($htmlSpecialChar == true ? htmlspecialchars($dto->$value) : $dto->$value) . '"' . ',';
        }
        $len = strlen($josn_text);
        if ($len > 1) {
            $josn_text = substr($josn_text, 0, -1);
        }
        $josn_text .= "}";
        return $josn_text;
    }

    public static function dtosToJSON($dtos, $htmlSpecialChar = false) {

        if (empty($dtos)) {
            return "[]";
        }
        $ret = "[";
        foreach ($dtos as $dto) {
            $json = self::dtoToJSON($dto, $htmlSpecialChar);
            $ret .=$json . ',';
        }
        $ret = substr($ret, 0, -1) . ']';
        return $ret;
    }

    public function toArray() {
        return self::dtoToArray($this);
    }

    public static function dtosToArray($dtos, $fieldNames = array()) {

        $ret = array();
        foreach ($dtos as $dto) {
            $ret [] = self::dtoToArray($dto, $fieldNames);
        }
        return $ret;
    }

    public static function dtoToArray($dto, $fieldNames = array()) {
        $ret = array();
        if (empty($fieldNames)) {
            $mapArray = $dto->getMapArray();
        } else {
            $mapArray = $fieldNames;
        }
        foreach ($mapArray as $key => $value) {
            $ret [$key] = $dto->$value;
        }
        return $ret;
    }

    /*
      Overloads getter and setter methods
     */

    public function __call($m, $a) {
        // retrieving the method type (setter or getter)
        $type = substr($m, 0, 3);

        // retrieving the field name
        $fieldName = self::lowerFirstLetter(substr($m, 3));

        if ($type == 'set') {
            $this->$fieldName = $a[0];
        } else if ($type == 'get') {
            if (isset($this->$fieldName)) {
                return $this->$fieldName;
            } else {
                return null;
            }
        }
    }

    /*
      function __call($method, $arguments) {
      $prefix = strtolower(substr($method, 0, 3));
      $property = strtolower(substr($method, 3));

      if (empty($prefix) || empty($property)) {
      return;
      }

      if ($prefix == "get" && isset($this->$property)) {
      return $this->$property;
      }

      if ($prefix == "set") {
      $this->$property = $arguments[0];
      }
      }
     */
}

?>