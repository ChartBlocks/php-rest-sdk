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

        $data = $client->getJson(trim($this->url, '/') . '/' . $id);

        if (!array_key_exists($this->responseKey, $data)) {
            throw new Exception("Key $this->responseKey data could not be found in the response");
        }

        $dataSet = new $this->class($data[$this->responseKey], $client);
        return $dataSet;
    }

}