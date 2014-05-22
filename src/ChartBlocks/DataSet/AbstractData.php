<?php

namespace ChartBlocks\DataSet;

use ChartBlocks\Entity\DataSet;

abstract class AbstractData {

    protected $rowNumber;
    protected $hasChanged = false;

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

    /**
     * 
     * @param bool $new
     * @return \ChartBlocks\DataSet\AbstractData
     */
    protected function setIsNew($new) {
        $this->isNew = (bool) $new;
        return $this;
    }

    /**
     * 
     * @return bool
     */
    public function isNew() {
        return (bool) $this->isNew;
    }

}
