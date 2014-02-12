<?php

namespace ChartBlocks\Repository;

class SessionToken extends AbstractRepository {

    protected $url = '/session/token';
    protected $class = '\\ChartBlocks\\SessionToken';
    protected $singleResponseKey = 'chart';
    protected $listResponseKey = 'charts';

}