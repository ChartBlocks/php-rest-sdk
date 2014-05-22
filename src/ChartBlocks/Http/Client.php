<?php

namespace ChartBlocks\Http;

use Guzzle\Http\Client as HttpClient;
use Guzzle\Http\Exception\ClientErrorResponseException;

class Client extends HttpClient {

    protected $exceptionHandler;

    public function getJson($uri = null, array $params = array()) {
        $path = ltrim($uri, '/');

        $request = $this->get($path);
        foreach ($params as $key => $value) {
            $request->getQuery()->set($key, $value);
        }

        $response = $this->sendRequest($request);
        return $response->json();
    }

    public function putJson($uri = null, $data = array()) {
        $path = ltrim($uri, '/');

        $request = $this->put($path, null, json_encode($data));
        $response = $this->sendRequest($request);
        return $response->json();
    }

    public function postJson($uri = null, $data = array()) {
        $path = ltrim($uri, '/');

        $request = $this->post($path, null, json_encode($data));
        $response = $this->sendRequest($request);
        return $response->json();
    }

    public function deleteJson($uri = null, $data = array()) {
        $path = ltrim($uri, '/');

        $request = $this->delete($path, null, json_encode($data));
        $response = $this->sendRequest($request);
        return $response->json();
    }

    public function postFile($uri = null, $file = null) {
        $path = ltrim($uri, '/');

        $request = $this->post($path);

        if ($file) {
            $request->addPostFile('upload', $file);
        }
        $response = $this->sendRequest($request);
        return $response->json();
    }

    public function sendRequest($request) {
        try {
            $response = $request->send();
        } catch (ClientErrorResponseException $e) {
            $handler = $this->getExceptionHandler();
            $handler->handle($e);
        }
        return $response;
    }

    public function getExceptionHandler() {
        if ($this->exceptionHandler === null) {
            $this->exceptionHandler = new ExceptionHandler();
        }
        return $this->exceptionHandler;
    }

}
