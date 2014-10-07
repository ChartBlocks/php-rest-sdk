<?php

namespace ChartBlocks\Repository;

use ChartBlocks\Client;
use ChartBlocks\Entity\EntityInterface;
use ChartBlocks\Entity\EntityId;

abstract class AbstractWriteableRepository extends AbstractRepository implements WriteableRepositoryInterface {

    /**
     * 
     * @param array $data
     * @return \ChartBlocks\Entity\EntityInterface
     */
    public function create(array $data = array()) {
        $response = $this->getClient()->post($this->url, $data);
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
        $this->getClient()->put($this->url . '/' . $id, $data);

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

        if (isset($json['result'])) {
            return (bool) $json['result'];
        }

        return false;
    }

}
