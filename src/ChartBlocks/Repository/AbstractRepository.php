<?php

namespace ChartBlocks\Repository;

use ChartBlocks\Client;
use ChartBlocks\Entity\EntityInterface;

abstract class AbstractRepository implements RepositoryInterface {

    protected $client;
    protected $singleResponseKey;
    protected $listResponseKey;

    public function __construct(Client $client) {
        $this->setClient($client);
    }

    /**
     * 
     * @param \ChartBlocks\Client
     */
    public function setClient(Client $client) {
        $this->client = $client;
        return $this;
    }

    /**
     * 
     * @return \ChartBlocks\Client
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * 
     * @param array $data
     * @return \ChartBlocks\Entity\EntityInterface
     */
    public function create(array $data = array()) {
        $response = $this->getClient()->postJson($this->url, $data);
        $item = $this->extractSingleItemData($response);
        return $this->igniteEntity($item);
    }

    /**
     * 
     * @param array $query
     * @return \ChartBlocks\Repository\ResultSet
     */
    public function find($query = array()) {
        $response = $this->getClient()->get($this->url, $query);
        $items = $this->extractListItemData($response);

        $resultSet = new ResultSet();
        foreach ($items as $item) {
            $entity = $this->igniteEntity($item);
            $resultSet->append($entity);
        }

        if (isset($response['state']['totalRecords'])) {
            $resultSet->setTotalRecords($response['state']['totalRecords']);
        }

        return $resultSet;
    }

    /**
     * 
     * @param string $id
     * @param array $query
     * @return \ChartBlocks\Entity\EntityInterface
     */
    public function findById($id, array $query = array()) {
        if (EntityId::isValid($id) === false) {
            throw new \InvalidArgumentException('Invalid entity ID');
        }

        $response = $this->getClient()->get($this->url . '/' . $id, $query);
        $item = $this->extractSingleItemData($response);
        return $this->igniteEntity($item);
    }

    /**
     * 
     * @param \ChartBlocks\Entity\EntityInterface $entity
     * @return \ChartBlocks\Repository\AbstractRepository
     * @throws Exception
     */
    public function update(EntityInterface $entity) {
        $id = $entity->getId();
        if (empty($id)) {
            throw new Exception('Entity has no ID, is it new?');
        }

        $data = $entity->toArray();
        $this->getHttpClient()->putJson($this->url . '/' . $id, $data);

        return $this;
    }

    /**
     * 
     * @param ChartBlocks\Entity\EntityInterface|string $idOrEntity
     * @return boolean
     */
    public function delete($idOrEntity) {
        $id = $this->extractIdFromParameter($idOrEntity);
        $json = $this->getClient()->delete($this->url . '/' . $id);

        if ($json) {
            return (bool) $json['result'];
        }

        return false;
    }

    /**
     * @param \ChartBlocks\Entity\EntityInterface|string $parameter
     * @throws \InvalidArgumentException
     * @return string $setId
     */
    protected function extractIdFromParameter($idOrEntity) {
        if (is_string($idOrEntity) && EntityId::isValid($idOrEntity)) {
            return $idOrEntity;
        }

        if ($idOrEntity instanceof EntityInterface) {
            return $idOrEntity->getId();
        }

        throw new \InvalidArgumentException('Entity or Entity ID required');
    }

    /**
     * Takes the raw JSON response and extracts the item data
     * 
     * @param array $data
     * @return array
     * @throws Exception
     */
    protected function extractSingleItemData(array $data) {
        if ($this->singleResponseKey && !array_key_exists($this->singleResponseKey, $data)) {
            throw new Exception('Invalid response, missing field ' . $this->singleResponseKey);
        }

        return ($this->singleResponseKey) ? $data[$this->singleResponseKey] : $data;
    }

    /**
     * Takes the raw JSON response and extracts the array of items
     * 
     * @param array $data
     * @return array
     * @throws Exception
     */
    protected function extractListItemData(array $data) {
        if ($this->listResponseKey && !array_key_exists($this->listResponseKey, $data)) {
            throw new Exception('Invalid response, missing field ' . $this->listResponseKey);
        }

        return ($this->listResponseKey) ? $data[$this->listResponseKey] : $data;
    }

    /**
     * 
     * @param array $data
     * @return \ChartBlocks\Entity\EntityInterface
     * @throws Exception
     */
    protected function igniteEntity(array $data) {
        $class = $this->class;
        if (empty($class) || class_exists($class) === false) {
            throw new Exception("Invalid entity class '$class'");
        }

        return new $class($this, $data);
    }

}
