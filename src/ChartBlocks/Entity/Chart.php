<?php

namespace ChartBlocks\Entity;

use ChartBlocks\Chart\Config;

class Chart extends AbstractEntity {

    public function setCreator($creator) {
        $profile = $this->getEntityFactory()->createInstanceOf('Profile', $creator);
        $this->store('creator', $profile);
        return $this;
    }

    public function setImages(array $images) {
        $this->store('images', $images);
        return $this;
    }

    public function setConfig($config) {
        if (is_array($config)) {
            $config = new Config($config);
        }

        if (false === ($config instanceof Config)) {
            throw new Exception('Config given is not an instance of \ChartBlocks\Chart\Config or an array');
        }

        $this->store('config', $config);
        return $this;
    }

}
