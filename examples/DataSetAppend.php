<?php

include('_boostrap.php');

$config = array(
    'token' => 'YOUR_TOKEN',
    'secret' => 'YOUR_SECRET',
);

$dataSetId = '542bd8ebc9a61d2d03d5c969';

$client = new \ChartBlocks\Client($config);

/* @var $dataSet \ChartBlocks\Entity\DataSet */
$dataSet = $client->dataSet->findById($dataSetId);

$dataSet->data->append(array(
    array(time(), rand(1, 100))
));