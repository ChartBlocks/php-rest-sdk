<?php

namespace ChartBlocks\DataSet;

use ChartBlocks\DataSet\Query;

class RowSet extends \ArrayObject implements DataSetAwareInterface {

    use DataSetTrait;

    protected $meta;
    protected $query;
    protected $rows = array();
    protected $maxLoad = 100;

    public function __construct(Query $query = null, \ChartBlocks\DataSet $dataSet = null) {
        if ($dataSet) {
            $this->setDataSet($dataSet);
        }
        if ($query) {
            $this->setQuery($query);
        }
    }

    public function setQuery($query) {
        $this->query = $query;
        return $this;
    }

    public function getQuery() {
        if ($this->query === null) {
            $this->query = new Query();
        }
        return $this->query;
    }

    public function getIterator() {
        $iterator = new \ChartBlocks\DataSet\RowSetIterator($this);
        return $iterator;
    }

    public function setRow(Row $row, $index = null) {
        if ($index !== null) {
            $this->rows[$index] = $row;
        } else {
            $this->rows[] = $row;
        }
        return $this;
    }

    public function getRow($index) {
        if (array_key_exists($index, $this->rows)) {
            return $this->rows[$index];
        }
        if ($this->isValid($index)) {
            $this->load($index);
        }
        if (array_key_exists($index, $this->rows)) {
            return $this->rows[$index];
        } else {
            throw new Exception('Could not load row');
        }
    }

    public function isValid($index) {
        $query = $this->getQuery();
        $versionMeta = $this->getVersionMeta();

        $limit = $query->getLimit();
        $max = $limit + ($query->getOffset()? : 0) + 1;

        if (($index < $max || !$limit) && ($index < $versionMeta['rows'])) {
            return true;
        }
        return false;
    }

    public function load($index = 1) {
        $dataSet = $this->getDataSet();
        $setMeta = $dataSet->getMeta();
        $versionMeta = $this->getVersionMeta();
        $client = $dataSet->getHttpClient();

        $offset = $index - 1;


        $query = $this->getQuery();
        $maxLoad = ($query->getLimit() > $this->maxLoad || !$query->getLimit()) ? $this->maxLoad : $query->getLimit();
        $limit = $maxLoad > ($versionMeta['rows'] - $offset) ? $versionMeta['rows'] - $offset : $maxLoad;



        $uri = 'data/' . $setMeta['id'] . '?offset=' . $offset . '&limit=' . $limit . '&version=' . $versionMeta['version'];

        $rows = $client->getJson($uri);
        $i = $offset;

        while ($i < $limit + $offset) {
            $meta = array('id' => $setMeta['id'], 'row' => $i, 'columns' => $versionMeta['columns']);
            if (array_key_exists($i, $rows['data'])) {
                $this->setRow(new Row(array_merge($rows['data'][$i], $meta), $dataSet), $i);
            } else {

                $this->setRow(new Row($meta, $dataSet), $i);
            }
            $i++;
        }
    }

    public function getVersionMeta() {
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

    public function isRowLoaded($index) {
        if (array_key_exists($index, $this->rows)) {
            return true;
        }
        return false;
    }

}