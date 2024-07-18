<?php

namespace ChartBlocksTest;

use ChartBlocks\Signature;
use PHPUnit\Framework\TestCase;

class SignatureTest extends TestCase {

    public function testGenerate() {
        $secretKey = '26dc572367b3fcebe3ef8607c63c01cb';
        $expecting = array(
            'NmRmMGU0OWE0MTgyODZkMjU1OTc3OWM5NDYwNjQyNDNkYzRlMzE4OQ==' => 'sort=name&order=asc',
            'NmNlMjBhODNmOGRlZGRkN2UxNzNhNjBkODIwODFjMGVlYWQ1ZmY3OQ==' => '{ "name": "Testing" }'
        );

        $signature = new Signature();
        foreach ($expecting as $expected => $toSign) {
            $result = $signature->generate($toSign, $secretKey);
            $this->assertEquals($expected, $result);
        }
    }

    public function testFromRequestEnclosing() {
        $secretKey = '26dc572367b3fcebe3ef8607c63c01cb';
        $body = 'Request Body';
        $query = 'sort=name&order=asc';

        $methods = array('post', 'POST', 'put', 'delete');
        foreach ($methods as $method) {
            $this->doFromRequestWith($method, $secretKey, $body, $query, $body);
        }
    }

    public function testFromRequestGet() {
        $secretKey = '26dc572367b3fcebe3ef8607c63c01cb';
        $body = 'Request Body';
        $query = 'sort=name&order=asc';

        $this->doFromRequestWith('get', $secretKey, $body, $query, $query);
    }

    protected function doFromRequestWith($method, $secretKey, $body, $query, $expected) {
        $request = $this->getMockRequest($method, $body, $query);

        $signature = $this->createMock('\ChartBlocks\Signature');
        $signature->expects($this->once())
                ->method('generate')
                ->with($expected, $secretKey)
                ->will($this->returnValue('SIGNATURE'));

        $result = $signature->fromRequest($request, $secretKey);
        $this->assertEquals('SIGNATURE', $result);
    }

    protected function getMockRequest($method, $body, $query) {
        $request = $this->createMock('\GuzzleHttp\Psr7\Request');

        $request->expects($this->once())
                ->method('getMethod')
                ->will($this->returnValue($method));

        return $request;
    }

}
