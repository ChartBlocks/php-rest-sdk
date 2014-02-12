<?php

namespace ChartBlocks\Repository;

use ChartBlocks\DataSet as CbDataSet;

class DataSet extends AbstractRepository {

    protected $url = '/set';
    protected $class = '\\ChartBlocks\\DataSet';
    protected $singleResponseKey = 'set';
    protected $listResponseKey = 'sets';

}
