<?php

include(__DIR__ . '/../vendor/autoload.php');


$config = array(
    'secret' => '26dc572367b3fcebe3ef8607c63c01cb',
    'token' => '52de8d29054ff3600a000001'
);

$dataSetId = '52de8bc0054ff3530a00001a';

$client = new \ChartBlocks\Client($config);

$dataSet = $client->getDataSet($dataSetId);

$rowSet = $dataSet->select(new ChartBlocks\DataSet\Query(array(
    'offset' => '0',
        ))
);


foreach ($rowSet as $row) {
    var_dump($row->data);
}
