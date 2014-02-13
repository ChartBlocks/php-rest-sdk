<?php

include('_boostrap.php');

// $config = array(
//     'token' => '52de8d29054ff3600a000001',
//     'secret' => '26dc572367b3fcebe3ef8607c63c01cb',
// );

$chartId = '52de8d38054ff38907000000';

$client = new \ChartBlocks\Client($config);

$chart = $client->getChart($chartId);


var_dump($chart->getConfig()->canvas);
exit;

