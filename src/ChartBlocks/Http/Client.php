<?php

namespace ChartBlocks\Http;

use Guzzle\Http\Client as HttpClient;

class Client extends HttpClient {

    protected $exceptionHandler;

    public function get($uri = null, $headers = null, $options = array()) {
        $request = parent::get($uri, $headers, $options);

        try {
            $response = $request->send();
        } catch (ClientErrorResponseException $e) {
            $handler = $this->getExceptionHandler();
            $handler->handle($e);
        }
        return $response->json();
    }

    public function getExceptionHandler() {
        if ($this->exceptionHandler === null) {
            $this->exceptionHandler = new ExceptionHandler();
        }
        return $this->exceptionHandler;
    }

}