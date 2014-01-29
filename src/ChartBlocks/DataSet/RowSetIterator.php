<?php

namespace ChartBlocks\DataSet;

class RowSetIterator extends \ArrayIterator {

    protected $current = 0;
    protected $rows = array();
    protected $rowSet;
    protected $versionMeta;
    protected $maxLoad = 50;

    public function __construct(RowSet $rowSet) {
        $this->setRowSet($rowSet);
    }

    public function next() {
        $this->current++;
        return $this;
    }

    public function getArrayCopy() {
        return $this->rows;
    }

    public function valid() {

        $rowSet = $this->getRowSet();
        $query = $rowSet->getQuery();
        $versionMeta = $this->getVersionMeta();

        $currentIndex = $this->current + ($query->getOffset()? : 0);

        if (($this->current < $query->getLimit()) && ($currentIndex <= ($versionMeta['rows'] - 1))) {
            return true;
        }
        return false;
    }

    public function rewind() {
        $this->current = 0;
    }

    public function key() {
        return $this->current;
    }

    public function count() {
        return count($this->rows);
    }

    public function current() {
        if (!array_key_exists($this->current, $this->rows)) {
            $this->loadRows();
        }
        return $this->rows[$this->current];
    }

    public function loadRows() {
        $rowSet = $this->getRowSet();
        $client = $rowSet->getHttpClient();
        $query = $rowSet->getQuery();
        $setMeta = $rowSet->getMeta();
        $versionMeta = $this->getVersionMeta();

        $offset = $this->current + $query->getOffset();

        $maxLoad = $query->getLimit() > $this->maxLoad ? $this->maxLoad : $query->getLimit();
        $limit = ($offset + $maxLoad) > $versionMeta['rows'] ? $versionMeta['rows'] : $offset + $maxLoad;

        $uri = 'data/' . $setMeta['id'] . '?limit=' . $limit . '&' . 'offset=' . $offset . '&' . 'version=' . $versionMeta['version'];

        $rows = $client->get($uri);

        foreach ($rows['data'] as $row) {
            $this->rows[] = new Row($row, $client);
        }
    }

    public function setRowSet($rowSet) {
        $this->rowSet = $rowSet;
        return $this;
    }

    /**
     * 
     * @return \ChartBlocks\DataSet\RowSet
     * @throws Exception
     */
    public function getRowSet() {
        if (!$this->rowSet) {
            throw new Exception('RowSet has not been set');
        }
        return $this->rowSet;
    }

    public function getVersionMeta() {
        if (!$this->versionMeta) {
            $query = $this->getRowSet()->getQuery();
            $version = $query->getVersion();
            $meta = $this->findVersionMeta($version);

            if (!$meta) {
                if ($version) {
                    throw new Exception('Meta information for version' . $version . ' could not be found');
                } else {
                    throw new Exception('Latest version meta could not be found');
                }
            }

            $this->versionMeta = $meta;
        }
        return $this->versionMeta;
    }

    public function setVersionMeta($meta) {
        $this->versionMeta = $meta;
        return $this;
    }

    public function findVersionMeta($version = false) {
        $meta = $this->getRowSet()->getMeta();

        if (array_key_exists('versions', $meta)) {
            $versionsMeta = $meta['versions'];

            if ($version === false) {
                usort($versionsMeta, array($this, 'sortVersionMeta'));
                return reset($versionsMeta);
            } else {
                foreach ($versionsMeta as $versionMeta) {
                    if ($versionMeta['version'] == $version) {
                        return $versionMeta;
                    }
                }
            }
        }
        return false;
    }

    private function sortVersionMeta($a, $b) {
        if ($a['version'] > $b['version']) {
            return 1;
        } else if ($a['version'] > $b['version']) {
            return -1;
        }
        return 0;
    }

}