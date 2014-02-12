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

    public function bindAuth(Request $request) {
        if (array_key_exists('token', $this->config)) {
            $token = $this->config['token'];
        } else {
            throw new Exception('token could not be found in config');
        }
        if (array_key_exists('secret', $this->config)) {
            $secret = $this->config['secret'];
        } else {
            throw new Exception('secret key could not be found in config');
        }

        $secret = $this->config['secret'];
        $signature = $this->getSignature()->fromRequest($request, $secret);
        $request->setHeader('Authorization', 'Basic ' . base64_encode($token . ':' . $signature));
    }

    public function bindAccept(Request $request) {
        $request->setHeader('Accept', 'application/json');
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

    public function getBaseUrl() {
        if ($this->baseUrl === null) {
            $env = getenv('CB_API_URL');
            $this->baseUrl = empty($env) ? $this->defaultBaseUrl : $env;
        }

        return $this->baseUrl;
    }

    /**
     * 
     * 
     */
    public function getSignature() {
        if ($this->signature === null) {
            $this->signature = new Signature();
        }
        return $this->signature;
    }

    /**
     * 
     * @param string $id
     */
    public function getDataSet($id) {
        $client = $this->getHttpClient();

        $data = $client->getJson('set/' . $id);

        if (!array_key_exists('set', $data)) {
            throw new Exception('Key "set" data could not be found in the response');
        }

        $dataSet = new DataSet($data['set'], $client);
        return $dataSet;
    }

}
