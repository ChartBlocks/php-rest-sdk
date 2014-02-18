<?php

namespace ChartBlocks\Repository;

use ChartBlocks\DataSet as CbDataSet;

class DataSet extends AbstractRepository {

    protected $url = '/set';
    protected $class = '\\ChartBlocks\\Entity\\DataSet';
    protected $singleResponseKey = 'set';
    protected $listResponseKey = 'sets';

}
