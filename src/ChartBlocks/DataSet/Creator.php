<?php

namespace ChartBlocks\DataSet;

use ChartBlocks\Http\Client;

class Creator {

    protected $client;

    public function __construct(Client $client = null) {
        if ($client) {
            $this->setClient($client);
        }
    }

    public function create($name, $options = array()) {

    }

    public function setClient(Client $client) {
        $this->client = $client;
        return $this;
    }

    public function getClient() {
        if ($this->client === null) {
            throw new Exception('Client not set.');
        }
        return $this->client;
    }

}
