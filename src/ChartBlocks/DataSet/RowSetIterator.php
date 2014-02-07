<?php

namespace ChartBlocks\DataSet;

class RowSetIterator extends \ArrayIterator {

    protected $index = 1;
    protected $rows = array();
    protected $rowSet;

    public function __construct(RowSet $rowSet) {
        $this->setRowSet($rowSet);
        $this->rewind();
    }

    public function next() {
        $this->index++;
        return $this;
    }

    public function valid() {
        return $rowSet = $this->getRowSet()->isValid($this->index);
    }

    public function rewind() {
        $rowSet = $this->getRowSet();
        $query = $rowSet->getQuery();
        $this->index = ($query->getOffset()? : 0 ) + 1;
    }

    public function key() {
        return $this->index;
    }

    public function current() {
        $rowSet = $this->getRowSet();

        if (!$rowSet->isRowLoaded($this->index)) {
            $rowSet->load($this->index);
        }
        return $rowSet->getRow($this->index);
    }

    public function setRowSet($rowSet) {
        $this->rowSet = $rowSet;
        return $this;
    }

    /**
     * 
     * @return \ChartBlocks\DataSet\RowSet
     * @throws Exception
     */
    public function getRowSet() {
        if (!$this->rowSet) {
            throw new Exception('RowSet has not been set');
        }
        return $this->rowSet;
    }

}