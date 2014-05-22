<?php

namespace ChartBlocks\DataSet;

use ChartBlocks\Entity\DataSet;

class RowSetDynamic implements RowSetInterface, DataSetAwareInterface {

    protected $dataSet;
    protected $rows = array();

    public function __construct(DataSet $dataSet) {
        $this->setDataSet($dataSet);
    }

    /**
     * 
     * @return \ChartBlocks\DataSet\Row[]
     */
    public function getRows() {
        return $this->rows;
    }

    /**
     * 
     * @param \ChartBlocks\DataSet\Row $row
     * @return \ChartBlocks\DataSet\RowSetDynamic
     */
    public function addRow(Row $row) {
        $row->setDataSet($this->getDataSet());

        $this->rows[$row->getRow()] = $row;
        return $this;
    }

    /**
     * 
     * @param type $index
     * @return \ChartBlocks\DataSet\Row
     */
    public function getRow($index) {
        if (!$this->isRowLoaded($index)) {
            $this->createRow($index);
        }

        return $this->rows[$index];
    }

    /**
     * 
     * @return boolean
     */
    public function save() {
        $client = $this->getDataSet()->getRepository()->getHttpClient();
        $dataSetId = $this->getDataSet()->getId();

        $json = $client->putJson('data/' . $dataSetId, array(
            'data' => $this->toArray()
        ));

        return !!$json['success'];
    }

    /**
     * 
     * @param boolean $changesOnly
     * @return array
     */
    public function toArray($changesOnly = false) {
        $data = array();
        foreach ($this->getRows() as $row) {
            if (!$changesOnly || $row->hasChanged()) {
                $data[$row->getRow()] = $row->toArray();
            }
        }

        return $data;
    }

    /**
     * 
     * @param int $index
     * @return boolean
     */
    public function isRowLoaded($index) {
        if (array_key_exists($index, $this->rows)) {
            return true;
        }
        return false;
    }

    /**
     * 
     * @param int $index
     * @return \ChartBlocks\DataSet\RowSetDynamic
     */
    protected function createRow($index) {
        $this->rows[$index] = new Row($this->getDataSet(), array(
            'row' => $index
        ));

        return $this;
    }

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
