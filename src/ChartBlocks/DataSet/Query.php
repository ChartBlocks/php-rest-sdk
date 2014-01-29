<?php

namespace ChartBlocks\DataSet;

class Query {

    protected $offset = false;
    protected $limit = false;
    protected $version = false;

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

}
