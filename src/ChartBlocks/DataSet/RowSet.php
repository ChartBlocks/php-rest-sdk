<?php

namespace Chartblocks\DataSet;

use ChartBlocks\Http\ClientAwareInterface;
use ChartBlocks\Http\Client;
use ChartBlocks\DataSet\Query;
use ChartBlocks\Http\ClientTrait;

class RowSet extends \ArrayObject implements ClientAwareInterface {

    use ClientTrait;

    protected $meta;
    protected $query;

    public function __construct($meta = array(), Query $query = null, Client $httpClient = null) {

        $this->setMeta($meta);

        if ($httpClient) {
            $this->setHttpClient($httpClient);
        }
        if ($query) {
            $this->setQuery($query);
        }
    }

    public function setMeta(array $data) {
        $this->data = $data;
    }

    public function getMeta() {
        return $this->data;
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

}