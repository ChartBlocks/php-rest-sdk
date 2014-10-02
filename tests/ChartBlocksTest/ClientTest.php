<?php

namespace ChartBlocksTest;

use ChartBlocks\Client;

class ClientTest extends \PHPUnit_Framework_TestCase {

    public function testDefaultApiUrl() {
        $client = new Client();
        $this->assertEquals('https://api.chartblocks.com/v1', $client->getApiUrl());
    }

}
