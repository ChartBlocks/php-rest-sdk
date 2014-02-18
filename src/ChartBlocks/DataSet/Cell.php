<?php

namespace ChartBlocks\DataSet;

use ChartBlocks\Entity\DataSet;

class Cell implements DataSetAwareInterface {

    use DataSetAwareTrait;

    protected $value;
    protected $column;
    protected $row;
    protected $id;
    protected $hasChanged = false;

    public function __construct(DataSet $dataSet, array $data) {
        $this->setDataSet($dataSet);
        $this->setData($data);
    }

    public function setData(array $data) {
        if (array_key_exists('value', $data)) {
            $this->setValue($data['value']);
        }

        if (array_key_exists('column', $data)) {
            $this->setColumn($data['column']);
        }

        if (array_key_exists('row', $data)) {
            $this->setRow($data['row']);
        }
    }

    public function setValue($value) {
        $this->value = $value;
        $this->hasChanged = true;
        return $this;
    }

    public function getValue() {
        return $this->value;
    }

    public function getColumn() {
        if (!$this->column) {
            throw new Exception('Column not set');
        }
        return $this->column;
    }

    public function setColumn($column) {
        $this->column = (int) $column;
        $this->hasChanged = true;
        return $this;
    }

    public function getRow() {
        return $this->row;
    }

    public function setRow($row) {
        $this->row = $row;
        $this->hasChanged = true;
        return $this;
    }

    public function hasChanged() {
        return $this->hasChanged;
    }

}
