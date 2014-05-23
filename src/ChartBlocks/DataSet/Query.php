<?php

namespace ChartBlocks\DataSet;

class Query {

    protected $offset = false;
    protected $limit = false;
    protected $version = false;
    protected $toRow = false;
    protected $fromRow = false;

    public function __construct(array $params = array()) {
        $this->setParams($params);
    }

    public function setParams(array $params) {
        if (array_key_exists('limit', $params)) {
            $this->setLimit($params['limit']);
        }
        if (array_key_exists('offset', $params)) {
            $this->setOffset($params['offset']);
        }
        if (array_key_exists('towRow', $params)) {
            $this->setToRow($params['towRow']);
        }
        if (array_key_exists('fromRow', $params)) {
            $this->setFromRow($params['fromRow']);
        }
        if (array_key_exists('version', $params)) {
            $this->setVersion($params['version']);
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

    public function toArray() {
        $params = array();
        if ($limit = $this->getLimit()) {
            $params['limit'] = $limit;
        }
        if ($offset = $this->getOffset()) {
            $params['offset'] = $offset;
        }
        if ($version = $this->getVersion()) {
            $params['version'] = $version;
        }
        if ($toRow = $this->getToRow()) {
            $params['toRow'] = $toRow;
        }
        if ($fromRow = $this->getFromRow()) {
            $params['fromRow'] = $fromRow;
        }
        return $params;
    }

}
