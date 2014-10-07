<?php

namespace ChartBlocks\DataSet;

use RuntimeException;

class Cell {

    protected $value;
    protected $originalValue;
    protected $column;
    protected $type;

    public function __construct($data) {
        $this->setData($data);
        $this->hasChanged = false;
    }

    public function parseRef($ref) {
        if (is_string($ref) && is_numeric($ref) === false) {
            try {
                $ref = $this->columnLetterToNumber($ref);
            } catch (RuntimeException $e) {
                return false;
            }
        }

        if ($this->isValidNumber($ref)) {
            return (int) $ref;
        }

        return false;
    }

    public function isValidNumber($number) {
        $int = (int) $number;
        return ($int > 0);
    }

    public function setData(array $data) {
        if (array_key_exists('c', $data)) {
            $this->setColumnNumber($data['c']);
        }

        if (array_key_exists('v', $data)) {
            $this->setValue($data['v']);
        }

        if (array_key_exists('t', $data)) {
            $this->setType($data['t']);
        }

        if (array_key_exists('o', $data)) {
            $this->setOriginalValue($data['o']);
        }

        return $this;
    }

    public function setColumnNumber($column) {
        if ($column !== null && false === self::isValidNumber($column)) {
            throw new \InvalidArgumentException('Invalid column number');
        }

        $this->column = ($column === null) ? null : (int) $column;
        return $this;
    }

    public function getColumnNumber() {
        return $this->column;
    }

    public function setOriginalValue($original) {
        $this->originalValue = $original;
    }

    public function getOriginalValue() {
        return $this->originalValue;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
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

    public function __toString() {
        return (string) $this->getValue();
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

}
