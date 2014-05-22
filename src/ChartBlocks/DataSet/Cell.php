<?php

namespace ChartBlocks\DataSet;

use ChartBlocks\Entity\DataSet;

class Cell extends AbstractData {

    protected $value;
    protected $column;
    protected $type;
    protected $original;
    protected $rowNumber;
    protected $hasChanged = false;

    public function __construct(array $data) {
        $this->setData($data);
        $this->hasChanged = false;
    }

    public function setData(array $data) {

        if (array_key_exists('o', $data) && $data['o']) {
            $this->setOriginalValue($data['o']);
        } else if (array_key_exists('v', $data)) {
            $this->setOriginalValue($data['v']);
        }

        if (array_key_exists('v', $data)) {
            $this->setValue($data['v']);
        }

        if (array_key_exists('c', $data)) {
            $this->setColumnNumber($data['c']);
        }

        if (array_key_exists('r', $data)) {
            $this->setRowNumber($data['r']);
        }

        if (array_key_exists('t', $data)) {
            $this->setType($data['t']);
        }
    }

    public function setOriginalValue($original) {
        $this->original = $original;
    }

    public function getOriginalValue() {
        return $this->original;
    }

    public function setType($type) {
        return $this->type = $type;
        $this->hasChanged = true;
    }

    public function getType() {
        return $this->type;
    }

    public function setValue($value) {
        $this->value = $value;
        $this->hasChanged = true;
        return $this;
    }

    public function getValue() {
        return $this->value;
    }

    public function getColumnNumber() {
        if ($this->column === null) {
            throw new Exception('Column not set');
        }
        return (int) $this->column;
    }

    public function setColumnNumber($column) {
        $this->column = (int) $column;
        $this->hasChanged = true;
        return $this;
    }

    /**
     * 
     * @return int
     */
    public function getRowNumber() {
        return $this->rowNumber;
    }

    /**
     * 
     * @param int
     * @return \ChartBlocks\DataSet\Row
     */
    public function setRowNumber($row) {
        $this->rowNumber = (int) $row;
        return $this;
    }

    /**
     * returns if item has changed since construct
     * @return bool
     */
    public function hasChanged() {
        return (bool) $this->hasChanged;
    }

    public function __toString() {
        return (string) $this->getOriginalValue();
    }

}
