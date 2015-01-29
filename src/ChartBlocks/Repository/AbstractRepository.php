<?php

namespace ChartBlocks\Repository;

use ChartBlocks\Client;
use ChartBlocks\Entity\EntityInterface;
use ChartBlocks\Entity\EntityId;
use Guzzle\Http\Exception\ClientErrorResponseException;

abstract class AbstractRepository implements RepositoryInterface {

    /**
     *
     * @var string|null
     */
    public $singleResponseKey;

    /**
     *
     * @var string|null
     */
    public $listResponseKey;

    /**
     *
     * @var \ChartBlocks\Client
     */
    protected $client;

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
     * @param array $query
     * @return \ChartBlocks\Repository\ResultSet
     */
    public function find(array $query = array()) {
        try {
            $response = $this->getClient()->get($this->url, $query);
        } Catch (ClientErrorResponseException $e) {
            $this->handleResponseException($e);
        }

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

        try {
            $response = $this->getClient()->get($this->url . '/' . $id, $query);
        } Catch (ClientErrorResponseException $e) {
            $this->handleResponseException($e);
        }

        $item = $this->extractSingleItemData($response);
        return $this->igniteEntity($item);
    }

    /**
     * 
     * @param array $data
     * @return \ChartBlocks\Entity\EntityInterface
     * @throws Exception
     */
    public function igniteEntity(array $data) {
        $class = $this->class;
        if (empty($class) || class_exists($class) === false) {
            throw new Exception("Invalid entity class '$class'");
        }

        return new $class($this, $data);
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
     * @param \Guzzle\Http\Exception\ClientErrorResponseException $e
     * @throws Exception\NotFoundException
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     */
    protected function handleResponseException(ClientErrorResponseException $e) {
        switch ($e->getResponse()->getStatusCode()) {
            case 400:
                throw new Exception\InvalidRequestException('Invalid request', 400, $e);
            case 403:
                throw new Exception\PermissionDeniedException('Permission denied', 403, $e);
            case 404:
                throw new Exception\NotFoundException('Item does not exist', 404, $e);
            default:
                throw $e;
        }
    }

}
