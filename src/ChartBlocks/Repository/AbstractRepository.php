<?php

namespace ChartBlocks\Repository;

use ChartBlocks\Http\Client;
use ChartBlocks\Http\ClientAwareTrait;
use ChartBlocks\Http\ClientAwareInterface;

abstract class AbstractRepository implements RepositoryInterface, ClientAwareInterface {

    use ClientAwareTrait;

    protected $singleResponseKey;
    protected $listResponseKey;

    public function __construct(Client $client) {
        $this->setHttpClient($client);
    }

    public function find($query) {
        $client = $this->getHttpClient();
        $data = $client->getJson($this->url);

        if ($this->listResponseKey && !array_key_exists($this->listResponseKey, $data)) {
            throw new Exception('Invalid response, missing field ' . $this->listResponseKey);
        }

        $itemData = ($this->listResponseKey) ? $data[$this->listResponseKey] : $data;
        
        $items = array();
        foreach ($itemData as $classData) {
            $items[] = $this->igniteClass($classData);
        }

        return $items;
    }

    public function findById($id) {
        $client = $this->getHttpClient();
        $data = $client->getJson($this->url . '/' . $id);

        if ($this->singleResponseKey && !array_key_exists($this->singleResponseKey, $data)) {
            throw new Exception('Invalid response, missing field ' . $this->singleResponseKey);
        }

        $classData = ($this->singleResponseKey) ? $data[$this->singleResponseKey] : $data;
        return $this->igniteClass($classData);
    }

    protected function igniteClass($data) {
        $class = $this->class;
        if (empty($class) || class_exists($class) === false) {
            throw new Exception('Invalid entity class');
        }

        return new $class($data, $this->getHttpClient());
    }

}
