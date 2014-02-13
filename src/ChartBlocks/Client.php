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

use Guzzle\Http\Message\Request;

class Client {

    protected $config;
    protected $signature;
    protected $exceptionHandler;
    protected $httpClient;
    protected $baseUrl;
    protected $defaultBaseUrl = 'https://api.chartblocks.com/v1';
    protected $respositories = array();

    /**
     * 
     * @param array $config
     */
    public function __construct($config = array()) {
        $this->setConfig($config);
        $this->bindEvents();
    }

    public function bindEvents() {
        $that = $this;
        $client = $this->getHttpClient();
        $client->getEventDispatcher()->addListener('request.before_send', function($event) use ($that) {
            $that->bindAuth($event['request']);
        });

        $client->getEventDispatcher()->addListener('request.before_send', function($event) use ($that) {
            $that->bindAccept($event['request']);
        });
    }

    /**
     * 
     * @param \Guzzle\Http\Message\Request $request
     * @throws Exception
     */
    public function bindAuth(Request $request) {
        $token = $this->getAuthToken();
        $secret = $this->getAuthSecret();

        $signature = $this->getSignature()->fromRequest($request, $secret);
        $request->setHeader('Authorization', 'Basic ' . base64_encode($token . ':' . $signature));
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

        throw new Exception('No auth token set');
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

        throw new Exception('No auth secret set');
    }

    /**
     * 
     * @param \Guzzle\Http\Message\Request $request
     * @return \ChartBlocks\Client
     */
    public function bindAccept(Request $request) {
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
    public function getHttpClient() {
        if ($this->httpClient === null) {
            $this->httpClient = new Http\Client($this->getBaseUrl(), array());
        }
        return $this->httpClient;
    }

    /**
     * 
     * @return string
     */
    public function getBaseUrl() {
        if ($this->baseUrl === null) {
            $env = getenv('CB_API_URL');
            $this->baseUrl = empty($env) ? $this->defaultBaseUrl : $env;
        }

        return $this->baseUrl;
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
     * @param string $name
     * @return \ChartBlocks\Entity\EntityInterface
     * @throws Exception
     */
    public function getRepository($name) {
        if (!array_key_exists($name, $this->respositories)) {
            $className = '\\ChartBlocks\\Repository\\' . ucfirst($name);
            if (class_exists($className)) {
                $this->respositories[$name] = new $className($this->getHttpClient());
            } else {
                throw new Exception("Respository $name could not be found.");
            }
        }
        return $this->respositories[$name];
    }

}
