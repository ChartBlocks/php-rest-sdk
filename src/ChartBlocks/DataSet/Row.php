<?php

namespace ChartBlocks\DataSet;

use ArrayObject;
use ArrayIterator;

class Row extends ArrayObject {

    /**
     *
     * @var array
     */
    protected $cells = array();

    /**
     *
     * @var \ArrayIterator
     */
    protected $cellIterator;

    /**
     *
     * @var integer|null
     */
    protected $rowNumber;

    /**
     *
     * @var \ChartBlocks\DataSet\CellFactory
     */
    protected $cellFactory;

    public static function isValidNumber($number) {
        $int = (int) $number;
        return ($int > 0);
    }

    public function __construct($rowNumber = null, array $cells = array()) {
        $this->setRowNumber($rowNumber);
        $this->setCells($cells);
    }

    public function getIterator() {
        if (null === $this->cellIterator) {
            $this->cellIterator = new ArrayIterator($this->cells);
        }

        return $this->cellIterator;
    }

    /**
     * 
     * @param int
     * @return \ChartBlocks\DataSet\Row
     */
    public function setRowNumber($row) {
        if ($row !== null && false === Row::isValidNumber($row)) {
            throw new \InvalidArgumentException('Invalid row number');
        }

        $this->rowNumber = ($row === null) ? null : (int) $row;
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
     * @param array $cells
     * @return \ChartBlocks\DataSet\Row
     */
    public function setCells(array $cells) {
        $this->cells = array();
        $this->addCells($cells);
        return $this;
    }

    /**
     * 
     * @param array $cells
     * @return \ChartBlocks\DataSet\Row
     */
    public function addCells(array $cells) {
        foreach ($cells as $cell) {
            $this->addCell($cell);
        }

        return $this;
    }

    /**
     * 
     * @param \ChartBlocks\DataSet\Cell $cell
     * @return \ChartBlocks\DataSet\Row
     */
    public function addCell($cell) {
        if (false === ($cell instanceof Cell)) {
            $cell = $this->getCellFactory()->createService($cell);
        }

        if (null === $cell->getColumnNumber()) {
            $number = $this->getNextCellNumber();
            $cell->setColumnNumber($number);
        }

        $this->cells[$cell->getColumnNumber()] = $cell;
        return $this;
    }

    public function getCellFactory() {
        if (null === $this->cellFactory) {
            $this->cellFactory = new CellFactory();
        }

        return $this->cellFactory;
    }

    /**
     * 
     * @return integer
     */
    public function getNextCellNumber() {
        return (count($this->cells) > 0) ? $this->getHighestCellNumber() + 1 : 1;
    }

    /**
     * 
     * @return integer
     */
    public function getHighestCellNumber() {
        return max(array_keys($this->cells));
    }

    /**
     * 
     * @return \ChartBlocks\DataSet\Cell[]
     */
    public function getCells() {
        $cells = $this->cells;
        ksort($cells);

        return $cells;
    }

    /**
     * 
     * @param mixed $ref
     * @return \ChartBlocks\DataSet\Cell
     * @throws Exception
     */
    public function getCell($ref) {
        if (false === $this->hasCell($ref)) {
            $row = $this->getRowNumber();
            throw new Exception("Cell $ref does not exist on row $row");
        }

        $number = Cell::parseRef($ref);
        return $this->cells[$number];
    }

    /**
     * 
     * @param mixed $ref
     * @return boolean
     */
    public function hasCell($ref) {
        $number = Cell::parseRef($ref);
        if (false === $number) {
            throw new \InvalidArgumentException('Invalid cell reference');
        }

        return array_key_exists($number, $this->cells);
    }

    /**
     * 
     * @param boolean $full Whether to return column data or just value
     * @return array
     */
    public function toArray($full = false) {
        $data = array();
        foreach ($this->getCells() as $cell) {
            $data[$cell->getColumnNumber()] = ($full) ? $cell->toArray() : $cell->getValue();
        }

        return $data;
    }

}
