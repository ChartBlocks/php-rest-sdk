<?php

namespace ChartBlocks\Entity;

use ChartBlocks\DataSet\Data;

/**
 * 
 * @param \ChartBlocks\DataSet\Data $data
 */
class DataSet extends AbstractEntity {

    /**
     *
     * @var \ChartBlocks\DataSet\Data
     */
    protected $dataObject;

    public function getData() {
        if (null === $this->dataObject) {
            $this->dataObject = new Data($this);
        }

        return $this->dataObject;
    }

    /**
     * 
     * @return array
     */
    public function getLatestVersionMeta() {
        foreach ($this->versions as $meta) {
            if ($meta['version'] == $this->latestVersionNumber) {
                return $meta;
            }
        }

        return false;
    }

    public function isImporting() {
        $latestVersionMeta = $this->getLatestVersionMeta();
        return $latestVersionMeta['isImporting'];
    }

}
