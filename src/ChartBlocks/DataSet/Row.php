<?php

namespace ChartBlocks\DataSet;

use ChartBlocks\Http\ClientAwareInterface;
use ChartBlocks\Http\ClientTrait;
use ChartBlocks\Http\Client;

class Row implements ClientAwareInterface {

    use ClientTrait;

    public $data;

    public function __construct($data, Client $client) {
        $this->data = $data;
        $this->setHttpClient($client);
    }

}