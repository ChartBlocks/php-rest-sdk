<?php

namespace ChartBlocks\Http;

trait ClientAwareTrait {

    protected $httpClient;

    /**
     * 
     * @param \ChartBlocks\Http\Client
     */
    public function setHttpClient(Client $client) {
        $this->httpClient = $client;
        return $this;
    }

    /**
     * 
     * @return \ChartBlocks\Http\Client
     */
    public function getHttpClient() {
        return $this->httpClient;
    }

}
