<?php

namespace ChartBlocks\Entity;

use ChartBlocks\Chart\Config;

class Chart extends AbstractEntity {

    protected $config;

    public function getConfig() {
        if ($this->config === null) {
            $data = $this->getData();
            $this->setConfig(new Config(array_key_exists('config', $data) ? $data['config'] : array()));
        }
        return $this->config;
    }

    public function setConfig($config) {
        if ($config instanceof Config) {
            $this->config = $config;
        } else if (is_array($config)) {
            $this->config = new Config($config);
        } else {
            throw new Exception('Config given is not an instance of \ChartBlocks\Chart\Config or an array');
        }
        return $this;
    }

    public function __get($name) {
        if (strtolower($name) === 'config') {
            return $this->getConfig();
        } else if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return false;
    }

    public function __isset($name) {
        if (strtolower($name) === 'config' || array_key_exists($name, $this->data)) {
            return true;
        }
        return false;
    }

}