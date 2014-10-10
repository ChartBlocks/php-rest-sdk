<?php

namespace ChartBlocks\DataSet;

use ChartBlocks\Entity\DataSet;
use ChartBlocks\DataSet\Query\Query;
use Iterator;

class RowSetCursor implements Iterator {

    /**
     *
     * @var \ChartBlocks\Entity\DataSet
     */
    protected $dataSet;

    /**
     *
     * @var \ChartBlocks\DataSet\Query\Query
     */
    protected $query;

    /**
     *
     * @var integer
     */
    protected $position;

    /**
     *
     * @var \ChartBlocks\DataSet\RowCache\ArrayStorage
     */
    protected $rowCache;

    /**
     *
     * @var integer
     */
    protected $maxLoad = 50;

    /**
     * 
     * @param \ChartBlocks\Entity\DataSet $dataSet
     * @param \ChartBlocks\DataSet\Query $query
     */
    public function __construct(DataSet $dataSet, Query $query) {
        $this->setDataSet($dataSet);
        $this->setQuery($query);
        $this->rewind();
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

    public function rewind() {
        $offset = (int) $this->getQuery()->getOffset();
        $from = (int) $this->getQuery()->getFromRow();

        $this->position = $from + $offset;
    }

    public function current() {
        $row = $this->ensureRowIsLoaded($this->position);
        return $row;
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        $query = $this->getQuery();
        $limit = $query->getLimit();
        $toRow = $query->getToRow();

        $meta = $this->getVersionMeta();
        $rowsInSet = $meta['rows'];

        $totalRows = ($toRow > 0) ? min($toRow, $rowsInSet) : $rowsInSet;
        $max = ($limit > 0) ? min($limit, $totalRows) : $totalRows;

        return ($this->position <= $max);
    }

    public function getRowCache() {
        if (null === $this->rowCache) {
            $this->rowCache = new RowCache\ArrayStorage();
        }

        return $this->rowCache;
    }

    public function ensureRowIsLoaded($rowNumber) {
        $cache = $this->getRowCache();
        if ($cache->has($rowNumber)) {
            return $cache->get($rowNumber);
        }

        $rows = $this->loadRowChunk($rowNumber, $this->maxLoad);

        if (false === array_key_exists($rowNumber, $rows)) {
            throw new Exception("Row $rowNumber was not loaded in chunk");
        }

        return $rows[$rowNumber];
    }

    public function loadRowChunk($fromRow = 1, $max = 50) {
        $data = $this->requestRowsFromServer($fromRow, $max);
        $cache = $this->getRowCache();

        $rows = array();
        foreach ($data as $rowNumber => $rowData) {
            $row = new Row($rowNumber, $rowData['cells']);
            $rows[$rowNumber] = $row;

            $cache->store($row);
        }

        return $rows;
    }

    public function requestRowsFromServer($fromRow, $max) {
        $params = $this->getLoadParams($fromRow, $max);
        $uri = 'data/' . $this->getDataSet()->id;

        $result = $this->getClient()->get($uri, $params);
        if (false === array_key_exists('data', $result)) {
            throw new Exception('Invalid response requesting rows');
        }

        return $result['data'];
    }

    public function getMaxColumns() {
        return $this->getVersionMeta()['columns'];
    }

    /**
     * 
     * @return \ChartBlocks\Client
     */
    public function getClient() {
        return $this->getDataSet()->getRepository()->getClient();
    }

    protected function getLoadParams($fromRow, $max) {
        $params = array(
            'fromRow' => $fromRow,
            'limit' => $max
        );

        return $params;
    }

    protected function getVersionMeta() {
        $versionNumber = $this->getQuery()->getVersion();
        $dataSet = $this->getDataSet();

        if (empty($versionNumber)) {
            $versionNumber = $dataSet->latestVersionNumber;
        } elseif ($versionNumber > $dataSet->latestVersionNumber) {
            throw new Exception('Query version is higher than the latest available version');
        }

        foreach ($dataSet->versions as $version) {
            if ($version['version'] == $versionNumber) {
                return $version;
            }
        }

        throw new Exception("Version $versionNumber not found");
    }

}
