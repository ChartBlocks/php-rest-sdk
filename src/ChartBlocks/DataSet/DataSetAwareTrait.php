<?php

namespace ChartBlocks\DataSet;

trait DataSetTrait {

    protected $dataSet;

    /**
     * 
     * @param \ChartBlocks\DataSet $dataSet
     * @return self
     */
    public function setDataSet(\ChartBlocks\DataSet $dataSet) {
        $this->dataSet = $dataSet;
        return $this;
    }

    /**
     * 
     * @return \ChartBlocks\DataSet
     */
    public function getDataSet() {
        return $this->dataSet;
    }

}