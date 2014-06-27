<?php

namespace ChartBlocks\Entity;

use ChartBlocks\Repository\RepositoryInterface;

class Statistics extends AbstractEntity {

    protected $typesStats = array();

    function __construct(RepositoryInterface $repository, $data = array()) {
        parent::__construct($repository, $data);
    }

    public function setData($data) {

        if (array_key_exists('types', $data)) {
            $this->typesStats = $data['types'];
        }
        return parent::setData($data);
    }

    public function getTypesStats() {
        return $this->typesStats;
    }

    public function getTypeStats($type) {
        if (array_key_exists($type, $this->typesStats)) {
            return $this->typesStats[$type];
        }
        return null;
    }

}
