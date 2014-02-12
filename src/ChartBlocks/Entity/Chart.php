<?php

namespace ChartBlocks;

use ChartBlocks\Http\Client as HttpClient;
use ChartBlocks\Http\ClientAwareInterface;
use ChartBlocks\Chart\Config;

class Chart implements ClientAwareInterface {

    use Http\ClientTrait;

    protected $id;
    protected $config;

    public function __construct(array $data, HttpClient $httpClient = null) {
        if ($httpClient) {
            $this->setHttpClient($httpClient);
        }

        if (array_key_exists('config', $data)) {
            $this->setConfig($data['config']);
        }
    }

    public function getConfig() {
        if ($this->config === null) {
            $this->config = new Config();
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