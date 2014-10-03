<?php

namespace ChartBlocksTest;

use ChartBlocks\Signature;

class SignatureTest extends \PHPUnit_Framework_TestCase {

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

        $signature = $this->getMock('\ChartBlocks\Signature', array('generate'));
        $signature->expects($this->once())
                ->method('generate')
                ->with($expected, $secretKey)
                ->will($this->returnValue('SIGNATURE'));

        $result = $signature->fromRequest($request, $secretKey);
        $this->assertEquals('SIGNATURE', $result);
    }

    protected function getMockRequest($method, $body, $query) {
        switch (strtoupper($method)) {
            case 'GET':
                $request = $this->getMock('\Guzzle\Http\Message\Request', array(), array(), '', false);
                $request->expects($this->once())
                        ->method('getQuery')
                        ->with(true)
                        ->will($this->returnValue($query));
                break;
            default:
                $request = $this->getMock('\Guzzle\Http\Message\EntityEnclosingRequest', array(), array(), '', false);
                $request->expects($this->once())
                        ->method('getBody')
                        ->will($this->returnValue($body));
        }

        $request->expects($this->once())
                ->method('getMethod')
                ->will($this->returnValue($method));

        return $request;
    }

}
