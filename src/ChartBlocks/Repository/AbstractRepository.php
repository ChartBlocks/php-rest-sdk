<?php

namespace ChartBlocks\Repository;

use ChartBlocks\Http\Client;
use ChartBlocks\Http\ClientAwareInterface;

abstract class AbstractRepository implements RepositoryInterface, ClientAwareInterface {

    protected $httpClient;
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

    public function findById($id, $query = array()) {
        $client = $this->getHttpClient();
        $data = $client->getJson($this->url . '/' . $id, $query);

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
