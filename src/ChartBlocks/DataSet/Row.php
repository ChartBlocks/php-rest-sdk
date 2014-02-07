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
            $cell['row'] = $this->getRow();
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
        $i = 1;

        while ($i <= $columns) {
            $data[$i] = $this->getCell($i);
            $i++;
        }
        $this->cells = $data;
        return $this->cells;
    }

    public function getCell($index) {
        if ($index > 0) {
            if (array_key_exists($index, $this->cells)) {
                return $this->cells[$index];
            }

            $cell = new Cell(array(
                'column' => $index,
                'row' => $this->getRow()
                    ), $this->getDataSet());
            $this->setCell($index, $cell);
            return $cell;
        }
        return null;
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
        $this->row = $row;
        return $this;
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
        //	Using a lookup cache adds a slight memory overhead, but boosts speed
        //	caching using a static within the method is faster than a class static,
        //		though it's additional memory overhead
        static $_indexCache = array();

        if (isset($_indexCache[$pString]))
            return $_indexCache[$pString];

        //	It's surprising how costly the strtoupper() and ord() calls actually are, so we use a lookup array rather than use ord()
        //		and make it case insensitive to get rid of the strtoupper() as well. Because it's a static, there's no significant
        //		memory overhead either
        static $_columnLookup = array(
    'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13,
    'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26,
    'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6, 'g' => 7, 'h' => 8, 'i' => 9, 'j' => 10, 'k' => 11, 'l' => 12, 'm' => 13,
    'n' => 14, 'o' => 15, 'p' => 16, 'q' => 17, 'r' => 18, 's' => 19, 't' => 20, 'u' => 21, 'v' => 22, 'w' => 23, 'x' => 24, 'y' => 25, 'z' => 26
        );

        //	We also use the language construct isset() rather than the more costly strlen() function to match the length of $pString
        //		for improved performance
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

}