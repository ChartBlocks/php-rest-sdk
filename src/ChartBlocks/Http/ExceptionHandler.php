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

namespace ChartBlocks\Http;

use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\ClientErrorResponseException;

class ExceptionHandler {

    /**
     * 
     * @param \Guzzle\Http\Exception\BadResponseException $exception
     */
    public function handle($exception) {
        if ($exception instanceof BadResponseException || $exception instanceof ClientErrorResponseException) {
            $this->handleBadResponse($exception);
        }
    }

    /**
     * 
     * @param \Guzzle\Http\Exception\BadResponseException $exception
     * @throws type
     */
    protected function handleBadResponse($exception) {
        $response = $exception->getResponse();

        $code = $response->getStatusCode();

        if ($code == '400') {
            $json = json_decode($response->getBody(true));
            if ($json) {
                echo '400 : Invalid request error' . PHP_EOL;
                var_dump($json);
                exit(1);
            }
        } else if ($code == '401') {
            $exception = new Exception\UnauthorizedException($code . ' : ' . $response->getReasonPhrase());
        } else if ($code == '404') {
            $exception = new Exception\NotFoundException($code . ' : ' . $response->getReasonPhrase());
        } else if ($code == '500') {
            $exception = new Exception\InternalServerErrorException($code . ' : ' . $response->getReasonPhrase());
        } else if (!$exception) {
            $exception = new Exception\BadResponseException($code . ' : ' . $response->getReasonPhrase());
        }

        $exception->setResponse($response);
        throw $exception;
    }

}
