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

class ExceptionHandler {

    public function handle($exception) {
        if ($exception instanceof BadResponseException) {
            $this->handleBadResponse($exception);
        }
    }

    protected function handleBadResponse($exception) {
        $response = $exception->getResponse();

        $code = $response->getStatusCode();

        if ($code == '401') {
            $exception = new Exception\UnauthorizedException($code . ' : ' . $response->getReasonPhrase());
        }
        if ($code == '404') {
            $exception = new Exception\NotFoundException($code . ' : ' . $response->getReasonPhrase());
        }
        if ($code == '500') {
            $exception = new Exception\InternalServerErrorException($code . ' : ' . $response->getReasonPhrase());
        }

        if (!$exception) {
            $exception = new Exception\BadResponseException($code . ' : ' . $response->getReasonPhrase());
        }
        $exception->setResponse($response);
        throw $exception;
    }

}