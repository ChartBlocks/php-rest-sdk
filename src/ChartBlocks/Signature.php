<?php

namespace ChartBlocks;

use Guzzle\Http\Message\Request;

class Signature {

    public function fromRequest(Request $request, $secretKey) {
        $method = strtolower($request->getMethod());

        switch ($method) {
            case 'post':
            case 'patch':
            case 'put':
            case 'delete':
                $body = (string) $request->getBody();
                break;
            default:
                $body = $request->getQuery(true);
        }
        return $this->generate($body, $secretKey);
    }

    public function generate($body, $secretKey) {
        return base64_encode(sha1(sha1($body) . $secretKey));
    }

}
