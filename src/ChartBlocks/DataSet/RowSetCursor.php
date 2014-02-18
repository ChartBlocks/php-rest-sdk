<?php

namespace ChartBlocks\DataSet;

use ChartBlocks\Entity\DataSet;
use ChartBlocks\DataSet\Query;

class RowSetCursor extends \ArrayObject implements RowSetInterface, DataSetAwareInterface {

    use DataSetAwareTrait;

    protected $meta;
    protected $query;
    protected $rows = array();
    protected $maxLoad = 100;

    /**
     * 
     * @param \ChartBlocks\Entity\DataSet $dataSet
     * @param \ChartBlocks\DataSet\Query $query
     */
    public function __construct(DataSet $dataSet, Query $query) {
        $this->setDataSet($dataSet);
        $this->setQuery($query);
    }

    /**
     * 
     * @param \ChartBlocks\DataSet\Query $query
     * @return \ChartBlocks\DataSet\RowSetCursor
     */
    public function setQuery(Query $query) {
        $this->query = $query;
        return $this;
    }

    /**
     * 
     * @return \ChartBlocks\DataSet\Query
     */
    public function getQuery() {
        if ($this->query === null) {
            $this->query = new Query();
        }
        return $this->query;
    }

    /**
     * 
     * @return \ChartBlocks\DataSet\RowSetIterator
     */
    public function getIterator() {
        $iterator = new \ChartBlocks\DataSet\RowSetIterator($this);
        return $iterator;
    }

    /**
     * 
     * @param \ChartBlocks\DataSet\Row $row
     * @return \ChartBlocks\DataSet\RowSetCursor
     */
    public function addRow(Row $row) {
        $this->rows[$row->getRow()] = $row;
        return $this;
    }

    /**
     * 
     * @param int $index
     * @return \ChartBlocks\DataSet\Row
     * @throws Exception
     */
    public function getRow($index) {
        if ($this->isRowLoaded($index)) {
            return $this->rows[$index];
        }

        if ($this->isValid($index)) {
            $this->loadRowChunk($index);
        }

        if ($this->isRowLoaded($index)) {
            return $this->rows[$index];
        } else {
            throw new Exception('Could not load row');
        }
    }

    protected function isRowLoaded($index) {
        if (array_key_exists($index, $this->rows)) {
            return true;
        }
        return false;
    }

    protected function isValid($index) {
        $query = $this->getQuery();
        $versionMeta = $this->getVersionMeta();

        $limit = $query->getLimit();
        $max = $limit + ($query->getOffset()? : 0) + 1;

        if (($index < $max || !$limit) && ($index < $versionMeta['rows'])) {
            return true;
        }

        return false;
    }

    public function loadRowChunk($index = 1) {
        $dataSet = $this->getDataSet();
        $versionMeta = $this->getVersionMeta();

        $offset = $index - 1;

        $query = $this->getQuery();
        $maxLoad = ($query->getLimit() > $this->maxLoad || !$query->getLimit()) ? $this->maxLoad : $query->getLimit();
        $limit = $maxLoad > ($versionMeta['rows'] - $offset) ? $versionMeta['rows'] - $offset : $maxLoad;

        $uri = 'data/' . $dataSet->getId();
        $params = array(
            'offset' => $offset,
            'limit' => $limit,
            'version' => $versionMeta['version']
        );

        $client = $dataSet->getRepository()->getHttpClient();
        $rows = $client->getJson($uri, $params);
        $i = $offset;

        while ($i < $limit + $offset) {
            $row = array(
                'row' => $i,
                'columns' => $versionMeta['columns'],
                'values' => array_key_exists($i, $rows['data']) ? $rows['data'][$i] : array()
            );

            $this->addRow(new Row($dataSet, $row));
            $i++;
        }
    }

    protected function getVersionMeta() {
        $version = $this->getQuery()->getVersion();
        $dataSet = $this->getDataSet();
        $meta = $dataSet->getMeta();

        if ($version === false) {
            $version = $meta['latestVersionNumber'];
        }

        if ($version > $meta['latestVersionNumber']) {
            throw new Exception('Version requested is greater than the latest version.');
        }

        $versionsMeta = $meta['versions'];

        foreach ($versionsMeta as $versionMeta) {
            if ($versionMeta['version'] == $version) {
                return $versionMeta;
            }
        }

        return false;
    }

}
