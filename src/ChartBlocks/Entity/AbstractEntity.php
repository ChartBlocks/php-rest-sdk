<?php

namespace ChartBlocks\Entity;

use ChartBlocks\Repository\RepositoryInterface;

class AbstractEntity implements EntityInterface {

    /**
     *
     * @var \ChartBlocks\Repository\RepositoryInterface
     */
    protected $repository;
    protected $data;

    /**
     * 
     * @param \ChartBlocks\Repository\RepositoryInterface $repository
     * @param type $data
     */
    public function __construct(RepositoryInterface $repository, $data = null) {
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

        if (!array_key_exists('id', $data)) {
            throw new Exception('Entity not populated with ID');
        }

        return $data['id'];
    }

}
