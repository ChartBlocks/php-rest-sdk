<?php

namespace ChartBlocks\Repository;

class Chart extends AbstractRepository {

    protected $url = '/chart';
    protected $class = '\\ChartBlocks\\Entity\\Chart';
    protected $singleResponseKey = 'chart';
    protected $listResponseKey = 'charts';

}