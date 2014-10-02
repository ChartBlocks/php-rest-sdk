<?php

include('_boostrap.php');


$config = array(
    'token' => '5395981b054ff3a207c6fd33',
    'secret' => '2e30dc68b780d6ed26c34cc438395177',
);

putenv('CB_API_URL=http://192.168.100.100/data-server');

$client = new \ChartBlocks\Client($config);
$usersRepo = $client->getRepository('user');

$newUser = $usersRepo->create(array(
    'email' => uniqid() . '@test.com',
    'password' => 'test1234',
    'accountId' => '5370f15f054ff36d178b457a'
        ));

var_dump($newUser->getId());