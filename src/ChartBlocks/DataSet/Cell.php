<?php

namespace ChartBlocks\DataSet;

use ChartBlocks\Entity\DataSet;

class Cell extends AbstractData {

    protected $value;
    protected $column;
    protected $type;
    protected $formatted;
    protected $hasChanged = false;

    public function __construct(array $data) {
        $this->setData($data);
        $this->hasChanged = false;
    }

    public function setData(array $data) {

        if (array_key_exists('o', $data) && $data['o']) {
            $this->setValue($data['o']);
        } else if (array_key_exists('v', $data)) {
            $this->setValue($data['v']);
        }

        if (array_key_exists('v', $data)) {
            $this->setFormattedValue($data['v']);
        }

        if (array_key_exists('c', $data)) {
            $this->setColumnNumber($data['c']);
        }

        if (array_key_exists('t', $data)) {
            $this->setType($data['t']);
        }
    }

    public function setFormattedValue($original) {
        $this->formatted = $original;
        $this->hasChanged = true;
    }

    public function getFormattedValue() {
        return $this->formatted;
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

    public function hasChanged() {
        return (bool) $this->hasChanged;
    }

    public function __toString() {
        return (string) $this->getValue();
    }

}
