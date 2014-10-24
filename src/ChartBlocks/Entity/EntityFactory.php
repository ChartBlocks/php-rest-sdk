<?php

namespace ChartBlocks\Entity;

use ChartBlocks\Client;

class EntityFactory {

    /**
     *
     * @var \ChartBlocks\Client
     */
    protected $client;

    /**
     * 
     * @param \ChartBlocks\Client $client
     */
    public function __construct(Client $client) {
        $this->client = $client;
    }

    /**
     * 
     * @param string $class
     * @param string $repository
     * @param mixed $object
     * @return \ChartBlocks\Entity\EntityInterface
     * @throws Exception
     */
    public function createInstanceOf($entityName, $object) {
        $class = '\ChartBlocks\Entity\\' . $entityName;

        if ($object instanceof $class) {
            return $object;
        }

        if (false === class_exists($class)) {
            throw new \InvalidArgumentException("$class not found");
        }

        if (is_array($object)) {
            $repo = $this->client->getRepository($entityName);
            return new $class($repo, $object);
        }

        throw new Exception("Invalid object for creating $class");
    }

}
