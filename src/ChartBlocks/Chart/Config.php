<?php

namespace ChartBlocks\Chart;

use JsonSerializable;

class Config implements JsonSerializable {

    protected $data = array();

    public function __construct(array $array = array()) {
        $this->setConfig($array);
    }

    public function setConfig($array) {
        foreach ($array as $k => $v) {
            if (strlen($k)) {
                $this->{$k} = $v;
            }
        }

        return $this;
    }

    public function __set($name, $value) {
        if (is_array($value)) {
            $value = new Config($value);
        }

        $this->data[$name] = $value;
    }

    public function __get($name) {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    public function __isset($name) {
        return isset($this->data[$name]);
    }

    public function toArray() {
        return $this->data;
    }

    public function jsonSerialize() {
        return $this->toArray();
    }

}
