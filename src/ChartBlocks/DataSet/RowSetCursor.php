<?php

namespace ChartBlocks\DataSet;

use ChartBlocks\Entity\DataSet;
use ChartBlocks\DataSet\Query;

class RowSetCursor extends \ArrayObject implements RowSetInterface, DataSetAwareInterface {

    protected $dataSet;
    protected $meta;
    protected $query;
    protected $rows = array();
    protected $maxLoad = 50;

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
        $this->rows[$row->getRowNumber()] = $row;
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

    public function isRowLoaded($index) {
        if (array_key_exists($index, $this->rows)) {
            return true;
        }
        return false;
    }

    public function isValid($index) {
        $query = $this->getQuery();
        $versionMeta = $this->getVersionMeta();

        $limit = $query->getLimit();
        $max = $limit + ($query->getOffset()? : 0);

        return ($index <= $max || !$limit) && ($index <= $versionMeta['rows']);
    }

    public function loadRowChunk($index = 1) {
        $dataSet = $this->getDataSet();

        $params = $this->getQueryParams($index);
        $uri = 'data/' . $dataSet->getId();

        $client = $dataSet->getRepository()->getHttpClient();
        $rows = $client->getJson($uri, $params);

        $i = $index;


        while ($i <= $params['limit'] + $params['offset']) {
            $row = array(
                'rowNumber' => $i,
                'cells' => array_key_exists($i, $rows['data']) && array_key_exists('cells', $rows['data'][$i]) ? $rows['data'][$i]['cells'] : array()
            );
            $this->addRow(new Row($row));
            $i++;
        }
    }

    protected function getQueryParams($index) {
        $versionMeta = $this->getVersionMeta();
        $offset = $index - 1;
        $query = $this->getQuery();

        $params = array(
            'offset' => $offset,
            'version' => $versionMeta['version']
        );
        $limit = $query->getLimit() > ($versionMeta['rows'] - $offset) ? $versionMeta['rows'] - $offset : $query->getLimit();

        if ($limit) {
            $params ['offset'] = $offset;
        }
        return $params;
    }

    protected function getVersionMeta() {
        $version = $this->getQuery()->getVersion();
        $dataSet = $this->getDataSet();
        $data = $dataSet->getData();

        if ($version === false) {
            $version = $data['latestVersionNumber'];
        }

        if ($version > $data['latestVersionNumber']) {
            throw new Exception('Version requested is greater than the latest version.');
        }

        $versionsMeta = $data['versions'];

        foreach ($versionsMeta as $versionMeta) {
            if ($versionMeta['version'] == $version) {
                return $versionMeta;
            }
        }

        return false;
    }

    /**
     * 
     * @param \ChartBlocks\Entity\DataSet $dataSet
     * @return self
     */
    public function setDataSet(DataSet $dataSet) {
        $this->dataSet = $dataSet;
        return $this;
    }

    /**
     * 
     * @return \ChartBlocks\Entity\DataSet
     */
    public function getDataSet() {
        return $this->dataSet;
    }

}
