<?php

namespace ChartBlocks\Repository;

use ChartBlocks\Http\Client;
use ChartBlocks\Http\ClientTrait;
use ChartBlocks\Http\ClientAwareInterface;

abstract class AbstractRepository implements RepositoryInterface, ClientAwareInterface {

    use ClientTrait;

    public function __construct(Client $client) {
        $this->setHttpClient($client);
    }

    public function find($id) {
        $client = $this->getHttpClient();

        $data = $client->getJson($this->url . '/' . $id);

        if (!array_key_exists($this->responseKey, $data)) {
            throw new Exception("Response key '$this->responseKey' could not be found");
        }

        $class = new $this->class($data[$this->responseKey], $client);
        return $class;
    }

}