<?php

namespace ChartBlocks;

use ChartBlocks\Http\Client as HttpClient;
use ChartBlocks\Http\ClientAwareInterface;

class DataSet implements ClientAwareInterface {

    use Http\ClientTrait;

    protected $id;
    protected $data;

    public function __construct(array $meta, HttpClient $httpClient = null) {
        $this->setMeta($meta);
        if ($httpClient) {
            $this->setHttpClient($httpClient);
        }
    }

    public function setMeta($data) {
        $this->data = $data;
        return $this;
    }

    public function getMeta() {
        return $this->data;
    }

    public function select($query = array()) {
        if (is_array($query)) {
            $query = new DataSet\Query($query);
        } elseif (!$query instanceof DataSet\Query) {
            throw new DataSet\Exception('Unknown item given to select');
        }

        $meta = $this->getMeta();

        if (!array_key_exists('id', $meta)) {
            throw new DataSet\Exception('Could not find dataSet ID');
        }

        $rowSet = new DataSet\RowSet($meta, $query, $this->getHttpClient());

        return $rowSet;
    }

}