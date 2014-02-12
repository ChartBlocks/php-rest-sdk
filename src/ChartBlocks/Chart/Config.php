<?php

namespace ChartBlocks\Chart;

class Config extends \ArrayObject implements ChartAwareInterface {

    use ChartTrait;

    public function __construct($data) {
        parent::__construct($data, 0, '\RecursiveArrayIterator');
    }

}