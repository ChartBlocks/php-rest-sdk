<?php

namespace ChartBlocks;

use ChartBlocks\Http\Client as HttpClient;
use ChartBlocks\Http\ClientAwareInterface;

class Chart implements ClientAwareInterface {

    use Http\ClientTrait;

    protected $id;
    protected $data;

    public function __construct(array $data, HttpClient $httpClient = null) {
        $this->setData($data);
        if ($httpClient) {
            $this->setHttpClient($httpClient);
        }
    }

    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    public function getData() {
        return $this->data;
    }

}