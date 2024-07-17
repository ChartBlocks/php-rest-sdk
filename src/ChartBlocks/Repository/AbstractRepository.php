<?php

namespace ChartBlocks\Repository;

use ChartBlocks\Client;
use ChartBlocks\Entity\EntityInterface;
use ChartBlocks\Entity\EntityId;
use GuzzleHttp\Exception\BadResponseException;

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
     * @var Client
     */
    protected $client;

    public function __construct(Client $client) {
        $this->setClient($client);
    }

    public function setClient(Client $client): AbstractRepository
    {
        $this->client = $client;
        return $this;
    }

    /**
     * 
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     *
     * @param array $query
     * @return ResultSet
     * @throws Exception
     */
    public function find(array $query = array()): ResultSet
    {
        try {
            $response = $this->getClient()->get($this->url, $query);
        } Catch (BadResponseException $e) {
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
     * @return EntityInterface
     * @throws Exception
     */
    public function findById($id, array $query = array()): EntityInterface
    {
        if (EntityId::isValid($id) === false) {
            throw new \InvalidArgumentException('Invalid entity ID');
        }

        try {
            $response = $this->getClient()->get($this->url . '/' . $id, $query);
        } Catch (BadResponseException $e) {
            $this->handleResponseException($e);
        }

        $item = $this->extractSingleItemData($response);
        return $this->igniteEntity($item);
    }

    /**
     * 
     * @param array $data
     * @return EntityInterface
     * @throws Exception
     */
    public function igniteEntity(array $data): EntityInterface
    {
        $class = $this->class;
        if (empty($class) || class_exists($class) === false) {
            throw new Exception("Invalid entity class '$class'");
        }

        return new $class($this, $data);
    }

    /**
     * @param $idOrEntity
     * @return string $setId
     */
    protected function extractIdFromParameter($idOrEntity): string
    {
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
    protected function extractSingleItemData(array $data): array
    {
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
    protected function extractListItemData(array $data): array
    {
        if ($this->listResponseKey && !array_key_exists($this->listResponseKey, $data)) {
            throw new Exception('Invalid response, missing field ' . $this->listResponseKey);
        }

        return ($this->listResponseKey) ? $data[$this->listResponseKey] : $data;
    }

    /**
     * 
     * @param BadResponseException $e
     * @throws Exception\NotFoundException
     * @throws BadResponseException
     */
    protected function handleResponseException(BadResponseException $e) {
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
