<?php

namespace ChartBlocks\Entity;

use ChartBlocks\Http\Client as HttpClient;
use ChartBlocks\Http\ClientAwareInterface;
use ChartBlocks\Http\ClientAwareTrait;

class DataSet implements ClientAwareInterface {

    use ClientAwareTrait;

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

        $rowSet = new DataSet\RowSet($query, $this);

        return $rowSet;
    }

    public function createRow() {
        $meta = $this->getMeta();
        $latestVersionMeta = $this->getLatestVersionMeta();
        return new DataSet\Row(
                array(
            'id' => $meta['id'],
            'columns' => $latestVersionMeta['columns']
                ), $this);
    }

    public function getLatestVersionMeta() {
        $meta = $this->getMeta();

        $versionsMeta = $meta['versions'];

        foreach ($versionsMeta as $versionMeta) {
            if ($versionMeta['version'] == $meta['latestVersionNumber']) {
                return $versionMeta;
            }
        }

        return false;
    }

    public function getId() {
        $meta = $this->getMeta();
        return $meta['id'];
    }

}
