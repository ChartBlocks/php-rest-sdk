<?php

namespace ChartBlocks\Http;

interface ClientAwareInterface {

    public function setHttpClient(Client $client);

    public function getHttpClient();
}