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
    protected $baseUrl = 'https://api.chartblocks.com/v1';
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

    public function bindAuth(Request $request) {

        if (array_key_exists('token', $this->config) && array_key_exists('secret', $this->config)) {
            $token = $this->config['token'];
            $secret = $this->config['secret'];

            $signature = $this->getSignature()->fromRequest($request, $secret);
            $request->setHeader('Authorization', 'Basic ' . base64_encode($token . ':' . $signature));
        }
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
            $this->httpClient = new Http\Client($this->baseUrl, array());
        }
        return $this->httpClient;
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

    public function getRepository($name) {
        if (!array_key_exists($name, $this->respositories)) {

            $className = '\\ChartBlocks\\Repository\\' . ucfirst($name);
            if (class_exists($className)) {
                $this->respositories[$name] = new $className($this->getHttpClient());
            } else {
                throw new Exception("respository $name could not be found.");
            }
        }
        return $this->respositories[$name];
    }

}