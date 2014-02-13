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

    public function create($data = array()) {
        $client = $this->getHttpClient();
        $response = $client->postJson($this->url, $data);

        $classData = $this->extractSingleKeyData($response);
        return $this->igniteClass($classData);
    }

    public function find($query = array()) {
        $client = $this->getHttpClient();
        $data = $client->getJson($this->url, $query);

        $itemData = $this->extractListKeyData($data);
        $items = array();
        foreach ($itemData as $classData) {
            $items[] = $this->igniteClass($classData);
        }

        return $items;
    }

    public function findById($id) {
        $client = $this->getHttpClient();
        $data = $client->getJson($this->url . '/' . $id);

        $classData = $this->extractSingleKeyData($data);
        return $this->igniteClass($classData);
    }

    protected function extractSingleKeyData(array $data) {
        if ($this->singleResponseKey && !array_key_exists($this->singleResponseKey, $data)) {
            throw new Exception('Invalid response, missing field ' . $this->singleResponseKey);
        }

        return ($this->singleResponseKey) ? $data[$this->singleResponseKey] : $data;
    }

    protected function extractListKeyData(array $data) {
        if ($this->listResponseKey && !array_key_exists($this->listResponseKey, $data)) {
            throw new Exception('Invalid response, missing field ' . $this->listResponseKey);
        }

        return ($this->listResponseKey) ? $data[$this->listResponseKey] : $data;
    }

    protected function igniteClass($data) {
        $class = $this->class;
        if (empty($class) || class_exists($class) === false) {
            throw new Exception('Invalid entity class');
        }

        return new $class($this, $data);
    }

}
