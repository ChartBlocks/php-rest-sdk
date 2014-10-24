<?php

namespace ChartBlocks\Entity;

use ChartBlocks\Repository\RepositoryInterface;
use JsonSerializable;

class AbstractEntity implements EntityInterface, JsonSerializable {

    /**
     *
     * @var \ChartBlocks\Repository\RepositoryInterface
     */
    protected $repository;
    protected $data = array();

    /**
     * 
     * @param \ChartBlocks\Repository\RepositoryInterface $repository
     * @param type $data
     */
    public function __construct(RepositoryInterface $repository, $data = array()) {
        $this->setRepository($repository);
        $this->setData($data);
    }

    /**
     * 
     * @param \ChartBlocks\Repository\RepositoryInterface $repository
     * @return \ChartBlocks\Entity\AbstractEntity
     */
    public function setRepository(RepositoryInterface $repository) {
        $this->repository = $repository;
        return $this;
    }

    /**
     * 
     * @return \ChartBlocks\Repository\RepositoryInterface
     */
    public function getRepository() {
        return $this->repository;
    }

    /**
     * 
     * @param array $data
     * @return \ChartBlocks\Entity\AbstractEntity
     */
    public function setData(array $data) {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * 
     * @return array
     */
    public function toArray() {
        return $this->data;
    }

    /**
     * 
     * @return array
     */
    public function jsonSerialize() {
        return $this->toArray();
    }

    /**
     * 
     * @return string|null
     */
    public function getId() {
        return $this->retrieve('id');
    }

    /**
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return $this->retrieve($name);
    }

    /**
     * 
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function __set($name, $value) {
        $method = 'set' . ucfirst($name);
        if (method_exists($this, $method)) {
            call_user_func(array($this, $method), $value);
        } else {
            $this->store($name, $value);
        }

        return $this;
    }

    /**
     * 
     * @param string $name
     * @return boolean
     */
    public function __isset($name) {
        return array_key_exists($name, $this->data);
    }

    public function __call($name, $arguments) {
        $prefix = substr($name, 0, 3);
        if ($prefix === 'get') {
            $property = lcfirst(substr($name, 3));
            return $this->retrieve($property);
        }

        if ($prefix === 'set') {
            $property = lcfirst(substr($name, 3));
            return $this->store($property, reset($arguments));
        }

        throw new Exception("Method '$name' does not exist");
    }

    /**
     * 
     * @return \ChartBlocks\Entity\EntityFactory
     */
    public function getEntityFactory() {
        return new EntityFactory($this->getRepository()->getClient());
    }

    /**
     * 
     * @param string $name
     * @param mixed $value
     * @return self
     */
    protected function store($name, $value) {
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * 
     * @param string $name
     */
    protected function retrieve($name) {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

}
