<?php

namespace ChartBlocksTest;

use ChartBlocks\Client;

class ClientTest extends \PHPUnit_Framework_TestCase {

    private $envs = array('CB_API_URL', 'CB_AUTH_TOKEN', 'CB_AUTH_SECRET');
    private $originalEnvs = array();

    public function setUp() {
        foreach ($this->envs as $env) {
            $this->originalEnvs[$env] = getenv($env);
            putenv($env . '=');
        }
    }

    public function tearDown() {
        foreach ($this->envs as $env) {
            putenv($env . '=' . $this->originalEnvs[$env]);
        }
    }

    public function testDefaultApiUrl() {
        $client = new Client();
        $this->assertEquals('https://api.chartblocks.com/v1/', $client->getApiUrl());
    }

    public function testUseEnvApiUrlIfAvailable() {
        putenv('CB_API_URL=http://localhost/');

        $client = new Client();
        $this->assertEquals('http://localhost/', $client->getApiUrl());
    }

    public function testUseConfigApiUrlIfSet() {
        $client = new Client(array(
            'api_url' => 'http://custom/'
        ));

        $this->assertEquals('http://custom/', $client->getApiUrl());
    }

    /**
     * @depends testUseConfigApiUrlIfSet
     */
    public function testGetApiUrlAddsTrailingSlash() {
        $client = new Client(array(
            'api_url' => 'http://custom'
        ));

        $this->assertEquals('http://custom/', $client->getApiUrl());
    }

    /**
     * @depends testUseConfigApiUrlIfSet
     * @depends testUseEnvApiUrlIfAvailable
     */
    public function testUseConfigApiUrlOverEnv() {
        putenv('CB_API_URL=http://localhost/');
        $client = new Client(array(
            'api_url' => 'http://custom/'
        ));

        $this->assertEquals('http://custom/', $client->getApiUrl());
    }

    public function testGetAuthTokenDefault() {
        $client = new Client();
        $this->assertNull($client->getAuthToken());
    }

    public function testGetAuthTokenEnv() {
        putenv('CB_AUTH_TOKEN=qwijibo');

        $client = new Client();
        $this->assertEquals('qwijibo', $client->getAuthToken());
    }

    /**
     * @depends testGetAuthTokenEnv
     */
    public function testGetAuthTokenConfig() {
        putenv('CB_AUTH_TOKEN=qwijibo');

        $client = new Client(array('token' => 'custom'));
        $this->assertEquals('custom', $client->getAuthToken());
    }

    public function testGetAuthSecretDefault() {
        $client = new Client();
        $this->assertNull($client->getAuthSecret());
    }

    public function testGetAuthSecretEnv() {
        putenv('CB_AUTH_SECRET=qwijibo');

        $client = new Client();
        $this->assertEquals('qwijibo', $client->getAuthSecret());
    }

    /**
     * @depends testGetAuthSecretEnv
     */
    public function testGetAuthSecretConfig() {
        putenv('CB_AUTH_SECRET=qwijibo');

        $client = new Client(array('secret' => 'custom'));
        $this->assertEquals('custom', $client->getAuthSecret());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetUnknownRepository() {
        $client = new Client();
        $client->getRepository('qwijibo');
    }

    public function testGetKnownRepositories() {
        $repositories = array(
            Client::REPO_CHART,
            Client::REPO_CHARTDATA,
            Client::REPO_DATASET,
            Client::REPO_PROFILE,
            Client::REPO_SESSIONTOKEN,
            Client::REPO_STATISTICS,
            Client::REPO_USER
        );

        $client = new Client();

        foreach ($repositories as $repositoryName) {
            $this->assertInstanceOf('\ChartBlocks\Repository\RepositoryInterface', $client->getRepository($repositoryName));
        }
    }

    public function testGetRepositoryReturnsSameInstance() {
        $client = new Client();
        $first = $client->getRepository('chart');
        $second = $client->getRepository('chart');

        $this->assertEquals(spl_object_hash($first), spl_object_hash($second));
    }

    public function testGetRepositoryShorthand() {
        $client = new Client();
        $this->assertInstanceOf('\ChartBlocks\Repository\Chart', $client->chart);
    }

    /**
     * @expectedException \ChartBlocks\Exception
     */
    public function testGetRepositoryShorthandUnknown() {
        $client = new Client();
        $this->assertInstanceOf('\ChartBlocks\Repository\Chart', $client->qwijibo);
    }

    public function testBindAccept() {
        $request = $this->getMock('\Guzzle\Http\Message\Request', array('setHeader'), array(), '', false);

        $request->expects($this->once())
                ->method('setHeader')
                ->with('Accept', 'application/json');

        $client = new Client();
        $client->bindAccept($request);
    }

    public function testGetSignature() {
        $client = new Client();
        $this->assertInstanceOf('\ChartBlocks\Signature', $client->getSignature());
    }

    public function testBindAuthNoTokenOrSecret() {
        $request = $this->getMock('\Guzzle\Http\Message\Request', array('setHeader'), array(), '', false);

        $request->expects($this->never())
                ->method('setHeader');

        $client = new Client();
        $client->bindAuth($request);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBindAuthOnlyToken() {
        $request = $this->getMock('\Guzzle\Http\Message\Request', array(), array(), '', false);

        $client = new Client(array('token' => 'qwijibo'));
        $client->bindAuth($request);
    }

    public function testBindAuthWithTokenAndSignature() {
        $token = '52de8d29054ff3600a000001';
        $secret = '26dc572367b3fcebe3ef8607c63c01cb';
        $fakeSignature = 'gvds0g86g89g66g89';
        $auth = 'Basic ' . base64_encode($token . ':' . $fakeSignature);

        $request = $this->getMock('\Guzzle\Http\Message\Request', array('setHeader'), array(), '', false);
        $request->expects($this->once())
                ->method('setHeader')
                ->with('Authorization', $auth);

        $signature = $this->getMock('\ChartBlocks\Signature', array('fromRequest'));
        $signature->expects($this->once())
                ->method('fromRequest')
                ->with($request, $secret)
                ->will($this->returnValue($fakeSignature));

        $config = array('token' => $token, 'secret' => $secret);
        $client = $this->getMock('\ChartBlocks\Client', array('getSignature'), array($config));

        $client->expects($this->once())
                ->method('getSignature')
                ->will($this->returnValue($signature));
        $client->bindAuth($request);
    }

    public function testGetHttpClient() {
        $client = new Client();
        $this->assertInstanceOf('\Guzzle\Http\Client', $client->getHttpClient());
    }

    public function testOurMethodsGetFiredOnBeforeSendEvent() {
        /* @var $client \ChartBlocks\Client */
        $client = $this->getMock('\ChartBlocks\Client', array('bindAccept', 'bindAuth'));

        $mockRequest = $this->getMock('\Guzzle\Http\Message\Request', array(), array(), '', false);
        $event = new \Guzzle\Common\Event(array('request' => $mockRequest));

        $client->expects($this->once())
                ->method('bindAccept')
                ->with($this->identicalTo($mockRequest));

        $client->expects($this->once())
                ->method('bindAuth')
                ->with($this->identicalTo($mockRequest));

        $client->getHttpClient()->getEventDispatcher()->dispatch('request.before_send', $event);
    }

    public function testGet() {
        $uri = '/chart';
        $params = array('public' => 1, 'order' => 'name');

        $client = $this->getMockClientWithMockHttpClient();

        $mockRequest = $this->getMockHttpRequest('GET');
        $mockRequest->getQuery()->expects($this->exactly(2))
                ->method('set')
                ->withConsecutive(
                        array('public', 1), array('order', 'name')
        );

        $client->getHttpClient()->expects($this->once())
                ->method('get')
                ->with('chart')
                ->will($this->returnValue($mockRequest));

        $client->get($uri, $params);
    }

    public function testPut() {
        $uri = '/chart';
        $data = array('name' => 'Test', 'isPublic' => true);

        $client = $this->getMockClientWithMockHttpClient();
        $mockRequest = $this->getMockHttpRequest('PUT');

        $client->getHttpClient()->expects($this->once())
                ->method('put')
                ->with('chart', null, json_encode($data))
                ->will($this->returnValue($mockRequest));

        $client->put($uri, $data);
    }

    public function testPost() {
        $uri = '/chart';
        $data = array('name' => 'Test', 'isPublic' => true);

        $client = $this->getMockClientWithMockHttpClient();
        $mockRequest = $this->getMockHttpRequest('POST');

        $client->getHttpClient()->expects($this->once())
                ->method('post')
                ->with('chart', null, json_encode($data))
                ->will($this->returnValue($mockRequest));

        $client->post($uri, $data);
    }

    public function testDelete() {
        $uri = '/chart';
        $data = array('name' => 'Test', 'isPublic' => true);

        $client = $this->getMockClientWithMockHttpClient();
        $mockRequest = $this->getMockHttpRequest('DELETE');

        $client->getHttpClient()->expects($this->once())
                ->method('delete')
                ->with('chart', null, json_encode($data))
                ->will($this->returnValue($mockRequest));

        $client->delete($uri, $data);
    }

    public function testPostFile() {
        $uri = '/chart';
        $file = '/path/to/file';

        $client = $this->getMockClientWithMockHttpClient();
        $mockRequest = $this->getMockHttpRequest('POST');

        $mockRequest->expects($this->once())
                ->method('addPostFile')
                ->with('upload', $file);

        $client->getHttpClient()->expects($this->once())
                ->method('post')
                ->with('chart')
                ->will($this->returnValue($mockRequest));

        $client->postFile($uri, $file);
    }

    /**
     * 
     * @return \ChartBlocks\Client
     */
    protected function getMockClientWithMockHttpClient() {
        $httpClient = $this->getMock('\Guzzle\Http\Client');

        $client = $this->getMock('\ChartBlocks\Client', array('getHttpClient'));
        $client->expects($this->any())
                ->method('getHttpClient')
                ->will($this->returnValue($httpClient));


        return $client;
    }

    protected function getMockHttpRequest($method = 'GET') {
        switch (strtoupper($method)) {
            case 'GET':
                $request = $this->getMock('\Guzzle\Http\Message\Request', array(), array(), '', false);
                break;
            default:
                $request = $this->getMock('\Guzzle\Http\Message\EntityEnclosingRequest', array(), array(), '', false);
        }


        $query = $this->getMock('\Guzzle\Http\QueryString');
        $request->expects($this->any())
                ->method('getQuery')
                ->will($this->returnValue($query));

        $response = $this->getMock('Guzzle\Http\Message\Response', array(), array(), '', false);
        $response->expects($this->once())
                ->method('json')
                ->will($this->returnValue(json_encode(array('success' => true))));

        $request->expects($this->once())
                ->method('send')
                ->will($this->returnValue($response));

        return $request;
    }

}
