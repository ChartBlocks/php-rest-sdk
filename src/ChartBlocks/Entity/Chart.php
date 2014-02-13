<?php

namespace ChartBlocks\Entity;

use ChartBlocks\Chart\Config;

class Chart extends AbstractEntity {

    protected $config;

    public function getConfig() {
        if ($this->config === null) {
            $this->setConfig(new Config());
        }
        return $this->config;
    }

    public function setConfig($config) {
        if ($config instanceof Config) {
            $this->config = $config;
        } else if (is_array($config)) {
            $this->config = new Config($config);
        } else {
            throw new Exception('Config given is not an instance of \ChartBlocks\Chart\Config or an array()');
        }
        return $this;
    }

}
