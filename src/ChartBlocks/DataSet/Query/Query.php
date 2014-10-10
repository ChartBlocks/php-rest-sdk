<?php

namespace ChartBlocks\DataSet\Query;

class Query {

    protected $offset;
    protected $limit;
    protected $version;
    protected $fromRow = 1;
    protected $toRow;

    public function __construct(array $params = array()) {
        $this->setParams($params);
    }

    public function setParams(array $params) {
        if (array_key_exists('offset', $params)) {
            $this->setOffset($params['offset']);
        }
        if (array_key_exists('limit', $params)) {
            $this->setLimit($params['limit']);
        }
        if (array_key_exists('version', $params)) {
            $this->setVersion($params['version']);
        }
        if (array_key_exists('fromRow', $params)) {
            $this->setFromRow($params['fromRow']);
        }
        if (array_key_exists('toRow', $params)) {
            $this->setToRow($params['toRow']);
        }
    }

    public function getOffset() {
        return $this->offset;
    }

    public function setOffset($offset) {
        $this->offset = (int) $offset;
        return $this;
    }

    public function getLimit() {
        return $this->limit;
    }

    public function setLimit($limit) {
        $this->limit = (int) $limit;
        return $this;
    }

    public function getVersion() {
        return $this->version;
    }

    public function setVersion($version) {
        $this->version = (int) $version;
        return $this;
    }

    public function getFromRow() {
        return $this->fromRow;
    }

    public function setFromRow($fromRow) {
        $this->fromRow = (int) $fromRow;
        return $this;
    }

    public function getToRow() {
        return $this->toRow;
    }

    public function setToRow($toRow) {
        $this->toRow = (int) $toRow;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function toArray() {
        return array(
            'offset' => $this->getOffset(),
            'limit' => $this->getLimit(),
            'version' => $this->getVersion(),
            'fromRow' => $this->getFromRow(),
            'toRow' => $this->getToRow()
        );
    }

}
