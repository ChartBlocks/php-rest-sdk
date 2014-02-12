<?php

include('_boostrap.php');

$config = array(
    'secret' => '26dc572367b3fcebe3ef8607c63c01cb',
    'token' => '52de8d29054ff3600a000001'
);

$chartId = '52de8d38054ff38907000000';

$client = new \ChartBlocks\Client($config);

$chart = $client->getChart($chartId);


var_dump($chart->getConfig()->canvas);
exit;

