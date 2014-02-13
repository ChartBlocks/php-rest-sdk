<?php

namespace ChartBlocks\Repository;

class SessionToken extends AbstractRepository {

    protected $url = '/session/token';
    protected $class = '\\ChartBlocks\\Entity\\SessionToken';
    protected $singleResponseKey = 'session';
    protected $listResponseKey = 'sessions';

}