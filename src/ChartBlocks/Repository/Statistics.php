<?php

namespace ChartBlocks\Repository;

class Statistics extends AbstractRepository {

    protected $url = '/chart/statistics';
    protected $class = '\\ChartBlocks\\Entity\\Statistics';
    protected $listResponseKey = 'statistics';

    public function find($query = array()) {
        $client = $this->getHttpClient();
        $data = $client->getJson($this->url, $query);

        $itemData = $this->extractListItemData($data);

        $class = $this->igniteEntity($itemData);

        return $class;
    }

}
