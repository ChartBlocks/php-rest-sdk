<?php

namespace ChartBlocks\DataSet;

use ChartBlocks\Entity\DataSet;

class Row extends \ArrayObject {

    protected $data = array();
    protected $cells = array();
    protected $columnCount = 0;
    protected $rowNumber;
    protected $hasChanged = false;

    public function __construct(array $data = array()) {
        $this->setData($data);
        $this->hasChanged = false;
        
        parent::__construct($this->getCells(true));
    }

    protected function setData(array $data) {
        if (array_key_exists('rowNumber', $data)) {
            $this->setRowNumber($data['rowNumber']);
        }
        if (array_key_exists('columnCount', $data)) {
            $this->setColumnCount($data['columnCount']);
        }
        if (array_key_exists('cells', $data)) {
            $cells = $this->igniteCells($data['cells']);
            $this->setCells($cells);
        }
    }

    protected function igniteCells(array $cells) {
        $ignitedCells = array();
        foreach ($cells as $key => $cell) {
            $cell['c'] = $key; // temp fix !!!!!
            $cell = $this->igniteCell($cell);
            $ignitedCells[$cell->getColumnNumber()] = $cell;
        }

        return $ignitedCells;
    }

    protected function igniteCell(array $cell) {
        $cell['r'] = $this->getRowNumber();
        return new Cell($cell);
    }

    public function setCells(array $cells) {
        foreach ($cells as $cell) {
            $this->setCell($cell);
        }
    }

    public function setCell(Cell $cell) {
        $number = $cell->getColumnNumber();
        if (!array_key_exists($number, $this->cells) || $this->cells[$number] !== $cell) {
            $this->cells[$number] = $cell;
            $this->hasChanged = true;
        }
        return $this;
    }

    public function getCells($createPaddingCells = false) {
        $colCount = $this->getColumnCount();


        if ($createPaddingCells) {
            $cells = array();
            $i = 1;
            while ($i <= $colCount) {
                $cells[$i] = $this->getCell($i);
                $i++;
            }
        } else {
            $cells = $this->cells;
            ksort($cells);
        }

        return $cells;
    }

    public function getCell($index) {
        if ($index >= 0) {
            if (!array_key_exists($index, $this->cells)) {
                $cell = $this->igniteCell(array(
                    'c' => $index,
                ));
                $this->setCell($cell);
            }
            return $this->cells[$index];
        } else {
            throw new \Exception('Index must be a positive number');
        }
    }

    public function hasChanged() {
        if ($this->hasChanged) {
            return true;
        }

        foreach ($this->getCells() as $cell) {
            if ($cell->hasChanged()) {
                return true;
            }
        }

        return false;
    }

    public function toArray($changesOnly = false) {
        $data = array();
        foreach ($this->getCells() as $cell) {
            if (!$changesOnly || $cell->hasChanged()) {
                $data[$cell->getColumnNumber()] = $cell->getValue();
            }
        }

        return $data;
    }

    public function columnNumberToLetter($num) {
        $numeric = ($num - 1) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval(($num - 1) / 26);
        if ($num2 > 0) {
            return $this->columnNumberToLetter($num2) . $letter;
        } else {
            return $letter;
        }
    }

    public function columnLetterToNumber($pString) {
        static $_indexCache = array();
        if (isset($_indexCache[$pString])) {
            return $_indexCache[$pString];
        }
        static $_columnLookup = array(
            'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13,
            'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26,
            'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6, 'g' => 7, 'h' => 8, 'i' => 9, 'j' => 10, 'k' => 11, 'l' => 12, 'm' => 13,
            'n' => 14, 'o' => 15, 'p' => 16, 'q' => 17, 'r' => 18, 's' => 19, 't' => 20, 'u' => 21, 'v' => 22, 'w' => 23, 'x' => 24, 'y' => 25, 'z' => 26
        );

        if (isset($pString{0})) {
            if (!isset($pString{1})) {
                $_indexCache[$pString] = $_columnLookup[$pString];
                return $_indexCache[$pString];
            } elseif (!isset($pString{2})) {
                $_indexCache[$pString] = $_columnLookup[$pString{0}] * 26 + $_columnLookup[$pString{1}];
                return $_indexCache[$pString];
            } elseif (!isset($pString{3})) {
                $_indexCache[$pString] = $_columnLookup[$pString{0}] * 676 + $_columnLookup[$pString{1}] * 26 + $_columnLookup[$pString{2}];
                return $_indexCache[$pString];
            }
        }

        throw new RuntimeException("Column string index can not be " . ((isset($pString{0})) ? "longer than 3 characters" : "empty"));
    }

    public function setColumnCount($count) {
        $this->columnCount = $count;
        return $this;
    }

    public function getColumnCount() {
        return $this->columnCount;
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

}
