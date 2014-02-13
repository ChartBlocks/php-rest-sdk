<?php

namespace ChartBlocks\Entity;

class DataSet extends AbstractEntity {

    use ClientAwareTrait;

    public function select($query = array()) {
        if (is_array($query)) {
            $query = new DataSet\Query($query);
        } elseif (!$query instanceof DataSet\Query) {
            throw new DataSet\Exception('Unknown item given to select');
        }

        $id = $this->getId();
        $rowSet = new DataSet\RowSet($query, $this);
        return $rowSet;
    }

    public function createRow() {
        $latestVersionMeta = $this->getLatestVersionMeta();
        $row = array(
            'id' => $this->getId(),
            'columns' => $latestVersionMeta['columns'],
        );
        return new DataSet\Row($this, $row);
    }

    public function getLatestVersionMeta() {
        $data = $this->getData();
        $versionsMeta = $data['versions'];

        foreach ($versionsMeta as $versionMeta) {
            if ($versionMeta['version'] == $data['latestVersionNumber']) {
                return $versionMeta;
            }
        }

        return false;
    }

}
