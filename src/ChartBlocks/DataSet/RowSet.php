<?php

namespace ChartBlocks\DataSet;

use ChartBlocks\Entity\DataSet;
use ChartBlocks\DataSet\Query;

class RowSet extends \ArrayObject {

    protected $callback;
    protected $patchRows;
    protected $iterator;

    /**
     * 
     * @param \ChartBlocks\Entity\DataSet $dataSet
     * @param \ChartBlocks\DataSet\Query $query
     */
    public function __construct($callback, $patchRows = true) {
        $this->callback = $callback;
        $this->patchRows = $patchRows;
    }

    public function getIterator() {
        if ($this->iterator === null) {
            $callback = $this->callback;
            $rows = $callback();

            if ($this->patchRows === true) {
                $rows = $this->patchRows($rows);
            }
            $this->iterator = new \ArrayIterator($rows);
        }
        return $this->iterator;
    }

    public function patchRows(array $rows) {
        $keys = array_keys($rows);
        krsort($keys);
        $rowCount = reset($keys);

        if ($rowCount) {
            $i = 1;
            while ($i <= $rowCount) {
                if (!array_key_exists($i, $rows)) {
                    $rows[$i] = new Row(array(
                        'rowNumber' => $i
                    ));
                }
                $i++;
            }

            ksort($rows);
        }
        return $rows;
    }

}
