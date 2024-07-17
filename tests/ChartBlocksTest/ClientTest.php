<?php

namespace ChartBlocksTest;

use ChartBlocks\Client;
use ChartBlocks\Exception;
use Guzzle\Common\Event;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use GuzzleHttp\Client as GuzzleClient;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase {

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
     * @expectedException InvalidArgumentException
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
     * @expectedException Exception
     */
    public function testGetRepositoryShorthandUnknown() {
        $client = new Client();
        $this->assertInstanceOf('\ChartBlocks\Repository\Chart', $client->qwijibo);
    }

    public function testGetSignature() {
        $client = new Client();
        $this->assertInstanceOf('\ChartBlocks\Signature', $client->getSignature());
    }

    public function testBindAuthWithTokenAndSignature() {
        $token = '52de8d29054ff3600a000001';
        $secret = '26dc572367b3fcebe3ef8607c63c01cb';
        $fakeSignature = 'gvds0g86g89g66g89';
        $auth = 'Basic ' . base64_encode($token . ':' . $fakeSignature);

        $request = $this->createMock('\GuzzleHttp\Psr7\Request');
        $request->expects($this->once())
                ->method('withHeader')
                ->with('Authorization', $auth);

        $signature = $this->createMock('\ChartBlocks\Signature');
        $signature->expects($this->once())
                ->method('fromRequest')
                ->with($request, $secret)
                ->will($this->returnValue($fakeSignature));

        $config = array('token' => $token, 'secret' => $secret);
        $client = $this->createConfiguredMock('\ChartBlocks\Client', $config);

        $client->expects($this->once())
                ->method('getSignature')
                ->will($this->returnValue($signature));
        $client->get('chart');
    }

    public function testGetHttpClient() {
        $client = new Client();
        $this->assertInstanceOf('\GuzzleHttp\Client', $client->getHttpClient());
    }

    public function testOurMethodsGetFiredOnBeforeSendEvent() {
        $client = $this->getMockClientWithMockHttpClient();

        $client->expects($this->once())
                ->method('handleHeaders');

        $client->get('chart');
    }

    public function testGet() {
        $uri = '/chart';
        $params = array('public' => 1, 'order' => 'name');

        $client = $this->getMockClientWithMockHttpClient();

        $response = $client->get($uri, $params);
        $this->assertEquals(array('success' => true), $response);
    }

    public function testPut() {
        $uri = '/chart';
        $data = array('name' => 'Test', 'isPublic' => true);

        $client = $this->getMockClientWithMockHttpClient();

        $response = $client->put($uri, $data);
        $this->assertEquals(array('success' => true), $response);
    }

    public function testPost() {
        $uri = '/chart';
        $data = array('name' => 'Test', 'isPublic' => true);

        $client = $this->getMockClientWithMockHttpClient();

        $response = $client->post($uri, $data);
        $this->assertEquals(array('success' => true), $response);
    }

    public function testDelete() {
        $uri = '/chart';
        $data = array('name' => 'Test', 'isPublic' => true);

        $client = $this->getMockClientWithMockHttpClient();

        $response = $client->delete($uri, $data);
        $this->assertEquals(array('success' => true), $response);
    }

    public function testPostFile() {
        $uri = '/chart';
        $file = '/path/to/file';

        $client = $this->getMockClientWithMockHttpClient();

        $response = $client->postFile($uri, $file);
        $this->assertEquals(array('success' => true), $response);
    }

    /**
     * 
     * @return Client
     */
    protected function getMockClientWithMockHttpClient(): Client
    {
        $mock = new MockHandler([
            new GuzzleResponse(200, [], json_encode(array('success' => true)))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $httpClient = new GuzzleClient(['handler' => $handlerStack]);

        $client = $this->createMock('\ChartBlocks\Client');
        $client->expects($this->any())
                ->method('getHttpClient')
                ->will($this->returnValue($httpClient));


        return $client;
    }

}
