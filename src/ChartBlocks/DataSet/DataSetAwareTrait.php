<?php

namespace ChartBlocks\DataSet;

use ChartBlocks\Entity\DataSet;

trait DataSetAwareTrait {

    protected $dataSet;

    /**
     * 
     * @param \ChartBlocks\Entity\DataSet $dataSet
     * @return self
     */
    public function setDataSet(DataSet $dataSet) {
        $this->dataSet = $dataSet;
        return $this;
    }

    /**
     * 
     * @return \ChartBlocks\Entity\DataSet
     */
    public function getDataSet() {
        return $this->dataSet;
    }

}
