<?php

namespace ChartBlocks\DataSet;

interface RowSetInterface {

    public function addRow(Row $row);

    public function getRow($index);
}
