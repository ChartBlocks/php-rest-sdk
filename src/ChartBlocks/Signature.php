<?php

namespace ChartBlocks;

use Psr\Http\Message\RequestInterface;

class Signature {

    public function fromRequest(RequestInterface $request, $secretKey): string
    {
        $method = strtolower($request->getMethod());

        switch ($method) {
            case 'post':
            case 'patch':
            case 'put':
            case 'delete':
                $body = (string) $request->getBody();
                break;
            default:
                $body = $request->getUri()->getQuery();
        }
        return $this->generate($body, $secretKey);
    }

    public function generate($body, $secretKey): string
    {
        return base64_encode(sha1(sha1($body) . $secretKey));
    }

}
