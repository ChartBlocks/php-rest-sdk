<?php

namespace ChartBlocks;

use Guzzle\Http\Client as HttpClient;
use Guzzle\Http\Message\Request as HttpRequest;

/**
 * Client for managing connection to ChartBlocks REST API
 * 
 * @param \ChartBlocks\Repository\Chart $chart
 * @param \ChartBlocks\Repository\DataSet $dataSet
 * @param \ChartBlocks\Repository\ChartData $chartData
 * @param \ChartBlocks\Repository\Profile $profile
 * @param \ChartBlocks\Repository\SessionToken $sessionToken
 * @param \ChartBlocks\Repository\Statistics $statistics
 * @param \ChartBlocks\Repository\User $user
 * 
 */
class Client {

    const REPO_ACCOUNT = 'account';
    const REPO_CHART = 'chart';
    const REPO_CHARTDATA = 'chartData';
    const REPO_DATASET = 'dataSet';
    const REPO_PROFILE = 'profile';
    const REPO_SESSIONTOKEN = 'sessionToken';
    const REPO_STATISTICS = 'statistics';
    const REPO_USER = 'user';

    protected $config;
    protected $signature;
    protected $exceptionHandler;
    protected $httpClient;
    protected $defaultApiUrl = 'https://api.chartblocks.com/v1/';
    protected $repositories = array(
        self::REPO_ACCOUNT => '\\ChartBlocks\Repository\Account',
        self::REPO_CHART => '\\ChartBlocks\Repository\Chart',
        self::REPO_CHARTDATA => '\\ChartBlocks\Repository\ChartData',
        self::REPO_DATASET => '\\ChartBlocks\Repository\DataSet',
        self::REPO_PROFILE => '\\ChartBlocks\Repository\Profile',
        self::REPO_SESSIONTOKEN => '\\ChartBlocks\Repository\SessionToken',
        self::REPO_STATISTICS => '\\ChartBlocks\Repository\Statistics',
        self::REPO_USER => '\\ChartBlocks\Repository\User',
    );

    /**
     * 
     * @param array $config
     */
    public function __construct(array $config = array()) {
        $this->setConfig($config);
    }

    /**
     * 
     * @param string $name
     * @return \ChartBlocks\Repository\RepositoryInterface
     * @throws Exception
     */
    public function getRepository($name) {
        $repo = lcfirst(trim($name));
        if (false === array_key_exists($repo, $this->repositories)) {
            throw new \InvalidArgumentException("Repository $repo does not exist");
        }

        if (is_string($this->repositories[$repo])) {
            $className = $this->repositories[$repo];
            $this->repositories[$repo] = new $className($this);
        }

        return $this->repositories[$repo];
    }

    /**
     * Tries to load a repository using the syntax $this->chart
     * 
     * @param string $name
     * @return \ChartBlocks\Repository\RepositoryInterface
     * @throws Exception
     */
    public function __get($name) {
        if (array_key_exists($name, $this->repositories)) {
            return $this->getRepository($name);
        }

        throw new Exception("Property '$name' does not exist");
    }

    /**
     * 
     * @return string
     */
    public function getApiUrl() {
        if (array_key_exists('api_url', $this->config)) {
            return $this->parseApiUrl($this->config['api_url']);
        }

        $env = getenv('CB_API_URL');
        if (!empty($env)) {
            return $this->parseApiUrl($env);
        }

        return $this->defaultApiUrl;
    }

    protected function parseApiUrl($url) {
        return rtrim($url, '/') . '/';
    }

    protected function parseApiPath($path) {
        return ltrim($path, '/');
    }

    /**
     * 
     * @return string|null
     */
    public function getAuthToken() {
        if (isset($this->config['token'])) {
            return $this->config['token'];
        } else {
            $env = getenv('CB_AUTH_TOKEN');
            if (!empty($env)) {
                return $env;
            }
        }

        return null;
    }

    /**
     * 
     * @return string|null
     */
    public function getAuthSecret() {
        if (isset($this->config['secret'])) {
            return $this->config['secret'];
        } else {
            $env = getenv('CB_AUTH_SECRET');
            if (!empty($env)) {
                return $env;
            }
        }

        return null;
    }

    public function get($uri, array $params = array()) {
        $path = $this->parseApiPath($uri);
        $request = $this->getHttpClient()->get($path);
        foreach ($params as $key => $value) {
            $request->getQuery()->set($key, $value);
        }

        $response = $request->send();
        return $response->json();
    }

    public function put($uri, $data = array()) {
        $path = $this->parseApiPath($uri);
        $json = empty($data) ? null : json_encode($data);

        $request = $this->getHttpClient()->put($path, null, $json);
        $response = $request->send();
        return $response->json();
    }

    public function post($uri, $data = array()) {
        $path = $this->parseApiPath($uri);
        $json = empty($data) ? null : json_encode($data);

        $request = $this->getHttpClient()->post($path, null, $json);
        $response = $request->send();

        return $response->json();
    }

    public function delete($uri, $data = array()) {
        $json = empty($data) ? null : json_encode($data);

        $path = $this->parseApiPath($uri);
        $request = $this->getHttpClient()->delete($path, null, $json);

        $response = $request->send();
        return $response->json();
    }

    public function postFile($uri, $file, $contentType = null) {
        $path = $this->parseApiPath($uri);
        $request = $this->getHttpClient()->post($path);

        $request->addPostFile('upload', $file, $contentType);

        $response = $request->send();
        return $response->json();
    }

    /**
     * 
     * @param \Guzzle\Http\Message\Request $request
     * @throws Exception
     */
    public function bindAuth(HttpRequest $request) {
        $token = $this->getAuthToken();
        $secret = $this->getAuthSecret();

        if ($token && $secret) {
            $signature = $this->getSignature()->fromRequest($request, $secret);
            $request->setHeader('Authorization', 'Basic ' . base64_encode($token . ':' . $signature));
        } elseif ($token xor $secret) {
            throw new \RuntimeException('Both token and secret must be set');
        }
    }

    /**
     * 
     * @param \Guzzle\Http\Message\Request $request
     * @return \ChartBlocks\Client
     */
    public function bindAccept(HttpRequest $request) {
        $request->setHeader('Accept', 'application/json');
        return $this;
    }

    /**
     * 
     * @return \ChartBlocks\Signature
     */
    public function getSignature() {
        if ($this->signature === null) {
            $this->signature = new Signature();
        }

        return $this->signature;
    }

    /**
     * 
     * @return \Guzzle\Http\Client
     */
    public function getHttpClient() {
        if ($this->httpClient === null) {
            $this->httpClient = $this->createHttpClient();
        }

        return $this->httpClient;
    }

    /**
     * 
     * @param array $config
     * @return \ChartBlocks\Client
     */
    protected function setConfig(array $config) {
        $this->config = $config;
        return $this;
    }

    /**
     * 
     * @return \Guzzle\Http\Client
     */
    protected function createHttpClient() {
        $client = new HttpClient($this->getApiUrl(), array());

        $that = $this;
        $client->getEventDispatcher()->addListener('request.before_send', function($event) use ($that) {
            $that->bindAccept($event['request']);
            $that->bindAuth($event['request']);
        });

        return $client;
    }

}
