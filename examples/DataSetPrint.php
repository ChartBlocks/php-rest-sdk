<?php

include('_boostrap.php');

$config = array(
    'token' => '54326600c9a61d1025d2d25a',
    'secret' => '7c7917c8d1ae3fe8c9aea94fb49221be',
);

$dataSetId = '542bd8ebc9a61d2d03d5c969';

$client = new \ChartBlocks\Client($config);

/* @var $dataSet \ChartBlocks\Entity\DataSet */
$dataSet = $client->dataSet->findById($dataSetId);

echo $dataSet->name . PHP_EOL;
echo '=======================' . PHP_EOL;

$rows = $dataSet->getData()->select();

foreach ($rows as $row) {
    echo '#' . $row->getRowNumber() . ' ';

    foreach ($row as $cell) {
        echo ' | ';
        echo $cell;
    }

    echo PHP_EOL;
}