<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Client
 *
 * @author jay
 */

namespace ChartBlocks;

use Guzzle\Http\Client as HttpClient;
use Guzzle\Http\Message\Request as HttpRequest;

class Client {

    protected $config;
    protected $signature;
    protected $exceptionHandler;
    protected $httpClient;
    protected $defaultApiUrl = 'https://api.chartblocks.com/v1';
    protected $respositories = array();

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
        if (!array_key_exists($name, $this->respositories)) {
            $className = '\\ChartBlocks\\Repository\\' . ucfirst($name);
            if (class_exists($className)) {
                $this->respositories[$name] = new $className($this->getHttpClient());
            } else {
                throw new Exception("Repository $name could not be found.");
            }
        }
        
        return $this->respositories[$name];
    }

    /**
     * 
     * @return string
     */
    public function getApiUrl() {
        if (array_key_exists('api_url', $this->config)) {
            return $this->config['api_url'];
        }

        $env = getenv('CB_API_URL');
        if (!empty($env)) {
            return $env;
        }

        return $this->defaultApiUrl;
    }

    public function get($uri, array $params = array()) {
        $request = $this->get($uri);
        foreach ($params as $key => $value) {
            $request->getQuery()->set($key, $value);
        }

        $response = $request->send();
        return $response->json();
    }

    public function put($uri, $data = array()) {
        $request = $this->put($uri, null, json_encode($data));

        $response = $request->send();
        return $response->json();
    }

    public function post($uri, $data = array()) {
        $request = $this->post($uri, null, json_encode($data));

        $response = $request->send();
        return $response->json();
    }

    public function delete($uri, $data = array()) {
        $request = $this->delete($uri, null, json_encode($data));

        $response = $request->send();
        return $response->json();
    }

    public function postFile($uri, $file) {
        $request = $this->post($uri);

        if ($file) {
            $request->addPostFile('upload', $file);
        }

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
        }
    }

    /**
     * 
     * @return string
     * @throws Exception
     */
    public function getAuthToken() {
        if (array_key_exists('token', $this->config)) {
            return $this->config['token'];
        } else {
            $env = getenv('CB_AUTH_TOKEN');
            if (!empty($env)) {
                return $env;
            }
        }
    }

    /**
     * 
     * @return string
     * @throws Exception
     */
    public function getAuthSecret() {
        if (array_key_exists('secret', $this->config)) {
            return $this->config['secret'];
        } else {
            $env = getenv('CB_AUTH_SECRET');
            if (!empty($env)) {
                return $env;
            }
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
     * @param array $config
     * @return \ChartBlocks\Client
     */
    public function setConfig(array $config) {
        $this->config = $config;
        return $this;
    }

    /**
     * 
     * @return \Guzzle\Http\Client
     */
    protected function getHttpClient() {
        if ($this->httpClient === null) {
            $this->httpClient = $this->createHttpClient();
        }

        return $this->httpClient;
    }

    /**
     * 
     * @return \Guzzle\Http\Client
     */
    protected function createHttpClient() {
        $client = new HttpClient($this->getApiUrl(), array());

        $client->getEventDispatcher()->addListener('request.before_send', function($event) use ($that) {
            $that->bindAccept($event['request']);
            $that->bindAuth($event['request']);
        });

        return $client;
    }

    /**
     * 
     * @return \ChartBlocks\Signature
     */
    protected function getSignature() {
        if ($this->signature === null) {
            $this->signature = new Signature();
        }

        return $this->signature;
    }

}
