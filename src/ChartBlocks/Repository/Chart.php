<?php

namespace ChartBlocks\Repository;

class Chart extends AbstractRepository {

    public $url = 'chart/';
    public $class = '\\ChartBlocks\\Entity\\Chart';
    public $singleResponseKey = 'chart';
    public $listResponseKey = 'charts';

}