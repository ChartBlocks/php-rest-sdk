<?php

namespace ChartBlocks\DataSet;

class Row implements DataSetAwareInterface {

    use DataSetTrait;

    protected $data = array();
    protected $cells = array();
    protected $id;
    protected $columnCount = 0;
    protected $row;

    public function __construct(array $data, \ChartBlocks\DataSet $dataSet) {
        $this->setDataSet($dataSet);
        $this->setConfig($data);
    }

    public function setConfig(array $data) {

        if (array_key_exists('row', $data)) {
            $this->setRow($data['row']);
        }
        if (array_key_exists('values', $data)) {
            $this->setCells($data['values']);
        }
        if (array_key_exists('columns', $data)) {
            $this->setColumnCount($data['columns']);
        }
    }

    public function setCells(array $cells) {
        foreach ($cells as $index => $cell) {
            $this->setCell($index, $cell);
        }
    }

    public function setCell($index, $cell) {
        if (!($cell instanceof Cell)) {

            if (!is_array($cell)) {
                $cell = array('value' => $cell);
            }
            $cell['column'] = $index;
            $cell = new Cell($cell, $this->getDataSet());
        }
        $this->cells[$index] = $cell;
        return $this;
    }

    public function setColumnCount($count) {
        $this->columnCount = $count;
        return $this;
    }

    public function getColumnCount() {
        return $this->columnCount;
    }

    public function getCells() {
        $columns = $this->getColumnCount();

        $data = array();
        $i = 0;

        while ($i < $columns) {
            $data[$i] = $this->getCell($i);
            $i++;
        }
        $this->cells = $data;
        return $this->cells;
    }

    public function getCell($index) {
        if (array_key_exists($index, $this->cells)) {
            return $this->cells[$index];
        }

        $cell = new Cell(array(
            'column' => $index,
                ), $this->getDataSet());
        $this->setCell($index, $cell);
        return $cell;
    }

    public function save() {

        $dataSet = $this->getDataSet();
        $client = $dataSet->getHttpClient();
        $id = $dataSet->getId();
        $row = $this->getRow();


        if ($row === null) {
            $json = $client->putJson('data/append/' . $id, array(
                'data' => $this->toArray()
            ));
            return !!$json['success'];
        } else {
            //    $client->putJson('/data/' . $id);
            //    return !!$json['success'];
        }
    }

    public function toArray() {
        $data = array();
        foreach ($this->getCells() as $cell) {

            if ($cell->hasChanged()) {
                $data = array($cell->getColumn() => $cell->getValue()) + $data;
            }
        }
        return array(
            $this->getRow()? : 0 => $data
        );
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getRow() {
        return $this->row;
    }

    public function setRow($row) {
        $this->row = (int) $row;
        return $this;
    }

}