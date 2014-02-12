<?php

namespace ChartBlocks\Http;

trait ClientAwareTrait {

    protected $httpClient;

    public function setHttpClient(Client $client) {
        $this->httpClient = $client;
        return $this;
    }

    public function getHttpClient() {
        return $this->httpClient;
    }

}