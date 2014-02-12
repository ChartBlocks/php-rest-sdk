<?php

namespace ChartBlocks\Http;

trait ClientTrait {

    protected $httpClient;

    public function setHttpClient(Client $client) {
        $this->httpClient = $client;
        return $this;
    }

    public function getHttpClient() {
        return $this->httpClient;
    }

}