<?php

namespace ChartBlocks\Repository;

class Chart extends AbstractWriteableRepository {

    public $url = 'chart/';
    public $class = '\\ChartBlocks\\Entity\\Chart';
    public $singleResponseKey = 'chart';
    public $listResponseKey = 'charts';

}