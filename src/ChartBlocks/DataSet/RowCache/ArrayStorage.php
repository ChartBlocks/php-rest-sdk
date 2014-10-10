<?php

namespace ChartBlocks\DataSet\RowCache;

use ChartBlocks\DataSet\Row;

class ArrayStorage {

    public $maxSize = 500;
    protected $cache = array();

    public function has($rowNumber) {
        return isset($this->cache[$rowNumber]);
    }

    public function get($rowNumber) {
        if ($this->has($rowNumber) === false) {
            throw new \RuntimeException('Row not in cache');
        }

        return $this->cache[$rowNumber];
    }

    public function store(Row $row) {
        if (null === $row->getRowNumber()) {
            throw new \InvalidArgumentException("Can't cache rows without a number");
        }

        $this->checkSize();
        $this->cache[$row->getRowNumber()] = $row;
        return $this;
    }

    public function checkSize() {
        if (count($this->cache) === $this->maxSize) {
            array_shift($this->cache);
        }

        return $this;
    }

}
