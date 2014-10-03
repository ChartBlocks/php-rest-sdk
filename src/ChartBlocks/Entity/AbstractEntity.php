<?php

namespace ChartBlocks\Entity;

use ChartBlocks\Repository\RepositoryInterface;

class AbstractEntity implements EntityInterface {

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
    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * 
     * @return string|null
     */
    public function getId() {
        $data = $this->getData();

        if (array_key_exists('id', $data)) {
            return $data['id'];
        }

        return null;
    }

    public function __get($name) {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }
    }

    public function __set($name, $value) {

        $method = 'set' . ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method), $value);
        }
    }

    public function __isset($name) {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return true;
        }
        return false;
    }

}
