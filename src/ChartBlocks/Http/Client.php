<?php

namespace ChartBlocks\Http;

use Guzzle\Http\Client as HttpClient;

class Client extends HttpClient {

    protected $exceptionHandler;

    public function getJson($uri = null) {
        $request = $this->get($uri);
        $response = $this->sendRequest($request);
        return $response->json();
    }

    public function putJson($uri = null, $data = array()) {
        $request = $this->put($uri, null, json_encode($data));
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