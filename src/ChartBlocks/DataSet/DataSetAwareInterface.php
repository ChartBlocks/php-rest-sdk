<?php

namespace ChartBlocks\DataSet;

interface DataSetAwareInterface {

    public function setDataSet(\ChartBlocks\DataSet $dataSet);

    public function getDataSet();
}