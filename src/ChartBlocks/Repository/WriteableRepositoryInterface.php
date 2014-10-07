<?php

namespace ChartBlocks\Repository;

use ChartBlocks\Entity\EntityInterface;

interface WriteableRepositoryInterface {

    public function create(array $data = array());

    public function update(EntityInterface $entity);

    public function delete($idOrEntity);
}