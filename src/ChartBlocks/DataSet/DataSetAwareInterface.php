<?php

namespace ChartBlocks\DataSet;

use ChartBlocks\Entity\DataSet;

interface DataSetAwareInterface {

    public function setDataSet(DataSet $dataSet);

    public function getDataSet();
}
