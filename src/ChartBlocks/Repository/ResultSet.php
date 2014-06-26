<?php

namespace ChartBlocks\Repository;

class ResultSet extends \ArrayObject {

    protected $totalRecords;

    public function getTotalRecords() {
        return $this->totalRecords;
    }

    public function setTotalRecords($total) {
        $this->totalRecords = $total;
        return $this;
    }

}
