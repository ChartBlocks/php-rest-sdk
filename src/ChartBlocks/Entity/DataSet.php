<?php

namespace ChartBlocks\Entity;

use ChartBlocks\DataSet\Query;
use ChartBlocks\DataSet\Exception as DataSetException;
use ChartBlocks\DataSet\RowSetCursor;
use ChartBlocks\DataSet\Row;

class DataSet extends AbstractEntity {

    /**
     * 
     * @param mixed $query
     * @return \ChartBlocks\DataSet\RowSetCursor
     * @throws DataSetException
     */
    public function select($query = array()) {
        if (is_array($query)) {
            $query = new Query($query);
        } elseif (!$query instanceof Query) {
            throw new DataSetException('Unknown item given to select');
        }

        $this->getId();
        return new RowSetCursor($this, $query);
    }

    /**
     * 
     * @return \ChartBlocks\DataSet\Row
     */
    public function createRow() {
        $latestVersionMeta = $this->getLatestVersionMeta();
        $row = array(
            'id' => $this->getId(),
            'columns' => $latestVersionMeta['columns'],
        );

        return new Row($this, $row);
    }

    /**
     * 
     * @return array
     */
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
