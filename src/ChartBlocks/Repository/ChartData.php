<?php

namespace ChartBlocks\Repository;

class ChartData extends AbstractRepository {

    protected $url = '/chart/data';
    protected $class = '\\ChartBlocks\\Entity\\ChartData';
    protected $singleResponseKey = 'data';

}
