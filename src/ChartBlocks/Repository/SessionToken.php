<?php

namespace ChartBlocks\Repository;

class SessionToken extends AbstractRepository {

    public $url = 'session/token/';
    public $class = '\\ChartBlocks\\Entity\\SessionToken';
    public $singleResponseKey = 'session';
    public $listResponseKey = 'sessions';

}