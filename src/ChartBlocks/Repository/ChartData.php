<?php

namespace ChartBlocks\Repository;

class ChartData extends AbstractWriteableRepository {

    public $url = 'chart/data/';
    public $class = '\\ChartBlocks\\Entity\\ChartData';
    public $singleResponseKey = 'data';

}