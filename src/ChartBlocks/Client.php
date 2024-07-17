<?php

namespace ChartBlocks;

use ChartBlocks\Repository\Chart;
use ChartBlocks\Repository\ChartData;
use ChartBlocks\Repository\DataSet;
use ChartBlocks\Repository\Profile;
use ChartBlocks\Repository\RepositoryInterface;
use ChartBlocks\Repository\SessionToken;
use ChartBlocks\Repository\Statistics;
use ChartBlocks\Repository\User;
use Closure;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use function GuzzleHttp\choose_handler;

/**
 * Client for managing connection to ChartBlocks REST API
 * 
 * @param Chart $chart
 * @param DataSet $dataSet
 * @param ChartData $chartData
 * @param Profile $profile
 * @param SessionToken $sessionToken
 * @param Statistics $statistics
 * @param User $user
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
     * @return RepositoryInterface
     */
    public function getRepository($name) {
        $repo = lcfirst(trim($name));
        if (false === array_key_exists($repo, $this->repositories)) {
            throw new InvalidArgumentException("Repository $repo does not exist");
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
     * @return RepositoryInterface
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
    public function getApiUrl(): string
    {
        if (array_key_exists('api_url', $this->config)) {
            return $this->parseApiUrl($this->config['api_url']);
        }

        $env = getenv('CB_API_URL');
        if (!empty($env)) {
            return $this->parseApiUrl($env);
        }

        return $this->defaultApiUrl;
    }

    protected function parseApiUrl($url): string
    {
        return rtrim($url, '/') . '/';
    }

    protected function parseApiPath($path): string
    {
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
        $response = $this->getHttpClient()->get($path, [
            'query' => $params
        ]);

        return json_decode($response->getBody(), true);
    }

    public function put($uri, $data = array()) {
        $path = $this->parseApiPath($uri);
        $json = empty($data) ? null : json_encode($data);

        $response = $this->getHttpClient()->put($path, [
            'body' => $json
        ]);

        return json_decode($response->getBody(), true);
    }

    public function post($uri, $data = array()) {
        $path = $this->parseApiPath($uri);
        $json = empty($data) ? null : json_encode($data);

        $response = $this->getHttpClient()->post($path, [
            'body' => $json
        ]);

        return json_decode($response->getBody(), true);
    }

    public function delete($uri, $data = array()) {
        $json = empty($data) ? null : json_encode($data);

        $path = $this->parseApiPath($uri);
        $response = $this->getHttpClient()->delete($path, [
            'body' => $json
        ]);

        return json_decode($response->getBody(), true);
    }

    public function postFile($uri, $file, $contentType = null) {
        $path = $this->parseApiPath($uri);
        $body = Psr7\Utils::streamFor(fopen($file, 'r'));
        $response = $this->getHttpClient()->post($path, [
            'body' => $body,
            'headers' => [
                'Content-Type' => $contentType ?: 'application/octet-stream'
            ]
        ]);
        return json_decode($response->getBody(), true);
    }

    /**
     * 
     * @return Signature
     */
    public function getSignature(): Signature
    {
        if ($this->signature === null) {
            $this->signature = new Signature();
        }

        return $this->signature;
    }

    /**
     *
     * @return HttpClient
     */
    public function getHttpClient(): HttpClient
    {
        if ($this->httpClient === null) {
            $this->httpClient = $this->createHttpClient();
        }

        return $this->httpClient;
    }

    /**
     * 
     * @param array $config
     * @return Client
     */
    protected function setConfig(array $config): Client
    {
        $this->config = $config;
        return $this;
    }

    /**
     *
     * @return HttpClient
     */
    protected function createHttpClient(): HttpClient
    {
        $stack = new HandlerStack();
        $stack->setHandler(choose_handler());
        $stack->push($this->handleHeaders());

        return new HttpClient([
            'base_uri' => $this->getApiUrl(),
            'handler' => $stack,
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);
    }

    /**
     * @return Closure
     * @throws RuntimeException
     */
    public function handleHeaders(): Closure
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $token = $this->getAuthToken();
                $secret = $this->getAuthSecret();

                if ($token && $secret) {
                    $signature = $this->getSignature()->fromRequest($request, $secret);
                    $request = $request->withHeader('Authorization', 'Basic ' . base64_encode($token . ':' . $signature));
                } elseif ($token xor $secret) {
                    throw new RuntimeException('Both token and secret must be set');
                }

                return $handler($request, $options);
            };
        };
    }

}
