<?php

namespace ChartBlocks\DataSet;

use ChartBlocks\Http\ClientAwareInterface;
use ChartBlocks\Http\ClientTrait;
use ChartBlocks\Http\Client;

class Cell implements DataSetAwareInterface {

    use DataSetTrait;

    protected $value;
    protected $column;
    protected $row;
    protected $id;
    protected $hasChanged = false;

    public function __construct(array $data, \ChartBlocks\DataSet $dataSet) {
        $this->setDataSet($dataSet);
        $this->setConfig($data);
    }

    public function setConfig(array $data) {

        if (array_key_exists('value', $data)) {
            $this->setValue($data['value'], true);
        }

        if (array_key_exists('column', $data)) {
            $this->setColumn($data['column']);
        }
        if (array_key_exists('row', $data)) {
            $this->setRow($data['row']);
        }
        if (array_key_exists('new', $data)) {
            $this->isNew = (bool) $data['new'];
        }
    }

    public function setValue($value, $silent = false) {
        if (!$silent) {
            $this->hasChanged = true;
        }
        $this->value = $value;
        return $this;
    }

    public function getValue() {
        return $this->value;
    }

    public function getColumn() {
        return $this->column;
    }

    public function setColumn($column) {
        $this->column = (int) $column;
        return $this;
    }

    public function getRow() {
        return $this->row;
    }

    public function setRow($row) {
        $this->row = (int) $row;
        return $this;
    }

    public function isNew() {
        return $this->isNew;
    }

    public function hasChanged() {
        return $this->hasChanged;
    }

    public function save() {
        $dataSet = $this->getDataSet();
        $client = $dataSet->getHttpClient();
        $id = $dataSet->getId();
        $row = $this->getRow();

        if ($row === null) {
            $json = $client->putJson('data/append/' . $id, array(
                'data' => array(
                    array(
                        $this->getColumn() => $this->getValue()
                    )
                )
            ));
            return !!$json['success'];
        } else {

            $client->putJson('/data/' . $id);
            return !!$json['success'];
        }
    }

}