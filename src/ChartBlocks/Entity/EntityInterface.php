<?php

namespace ChartBlocks\Entity;

use ChartBlocks\Repository\RepositoryInterface;

interface EntityInterface {

    public function __construct(RepositoryInterface $repository, $data = null);

    public function setRepository(RepositoryInterface $repository);

    public function getRepository();

    public function setData(array $data);

    public function toArray();

    public function getId();
}
