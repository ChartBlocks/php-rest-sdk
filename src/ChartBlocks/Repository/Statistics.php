<?php

namespace ChartBlocks\Repository;

class Statistics extends AbstractRepository {

    public $url = 'chart/statistics/';
    public $class = '\\ChartBlocks\\Entity\\Statistics';
    public $listResponseKey = 'statistics';

    public function find($query = array()) {
        $client = $this->getHttpClient();
        $data = $client->getJson($this->url, $query);

        $itemData = $this->extractListItemData($data);

        $class = $this->igniteEntity($itemData);

        return $class;
    }

}
