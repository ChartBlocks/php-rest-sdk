<?php

namespace ChartBlocks\DataSet;

use ChartBlocks\Entity\DataSet;
use ChartBlocks\DataSet\Query\Query;
use ChartBlocks\DataSet\Query\QueryFactory;

class Data {

    /**
     *
     * @var \ChartBlocks\Entity\DataSet
     */
    protected $dataSet;

    /**
     * 
     * @param \ChartBlocks\DataSet\DataSet $dataSet
     */
    public function __construct(DataSet $dataSet) {
        $this->dataSet = $dataSet;
    }

    /**
     * 
     * @param mixed $query
     * @return \ChartBlocks\DataSet\RowSetCursor
     * @throws DataSetException
     */
    public function select($query = array()) {
        if (false === ($query instanceof Query)) {
            $factory = new QueryFactory();
            $query = $factory->createService($query);
        }

        return new RowSetCursor($this->getDataSet(), $query);
    }

    /**
     * Remove all rows from the dataset
     * 
     * @return boolean
     */
    public function truncate() {
        $json = $this->getClient()->delete('data/' . $this->getDataSet()->id);
        return (isset($json['success']) && $json['success']);
    }

    /**
     * Remove a row from the dataset
     * 
     * @param \ChartBlocks\DataSet\Row|int $numberOrRow
     * @return boolean
     */
    public function removeRow($numberOrRow) {
        if ($numberOrRow instanceof Row) {
            $id = $numberOrRow->getRowNumber();
        } elseif (Row::isValidNumber($numberOrRow)) {
            $id = $numberOrRow;
        } else {
            throw new \InvalidArgumentException('Invalid row identifier');
        }

        $putData = array(
            'method' => 'shiftRow',
            'index' => $id - 1,
            'amount' => 1
        );

        $setId = $this->getDataSet()->id;
        $result = $this->getClient()->put('data/alter/' . $setId, $putData);

        return (isset($result['success']) && $result['success']);
    }

    /**
     * Remove a column from the dataset
     * 
     * @param int $number
     * @return boolean
     */
    public function removeColumn($number) {
        if (false === Cell::parseRef($number)) {
            throw new \InvalidArgumentException('Invalid column identifier');
        }

        $putData = array(
            'method' => 'shiftCol',
            'index' => $number - 1,
            'amount' => 1
        );

        $setId = $this->getDataSet()->id;
        $result = $this->getClient()->put('data/alter/' . $setId, $putData);

        return (isset($result['success']) && $result['success']);
    }

    public function append(array $rows) {
        $rows = $this->parseRowData($rows, false);
        if (count($rows) === 0) {
            throw new Exception('Must append at least one row');
        }

        $putData = array(
            'data' => array()
        );

        foreach ($rows as $row) {
            $putData['data'][] = $row->toArray();
        }

        $setId = $this->getDataSet()->id;
        $result = $this->getClient()->put('data/append/' . $setId, $putData);

        return (isset($result['success']) && $result['success']);
    }

    /**
     * 
     * @param \ChartBlocks\DataSet\Row[] $rowData
     * @return boolean
     */
    public function update(array $rows) {
        $rows = $this->parseRowData($rows, true);
        if (count($rows) === 0) {
            throw new Exception('Must update at least one row');
        }

        $putData = array(
            'data' => array()
        );

        foreach ($rows as $row) {
            $putData['data'][$row->getRowNumber()] = $row->toArray();
        }

        $setId = $this->getDataSet()->id;
        $result = $this->getClient()->put('data/' . $setId, $putData);

        return (isset($result['success']) && $result['success']);
    }

    public function parseRowData(array $rowData, $useKeyAsRowNumber = false) {
        $rows = array();
        foreach ($rowData as $rowKey => $aRow) {
            $rowNumber = ($useKeyAsRowNumber) ? $rowKey : null;

            if ($aRow instanceof Row) {
                if ($rowNumber && $aRow->getRowNumber() === null) {
                    $aRow->setRowNumber($rowNumber);
                }

                $rows[] = $aRow;
            } else if (is_array($aRow)) {

                $rows[] = new Row($rowNumber, $aRow);
            } else {
                throw new \InvalidArgumentException('Invalid row data');
            }
        }

        return $rows;
    }

    /**
     * 
     * @return \ChartBlocks\Client
     */
    public function getClient() {
        return $this->getDataSet()->getRepository()->getClient();
    }

    /**
     * 
     * @return \ChartBlocks\Entity\DataSet;
     */
    public function getDataSet() {
        return $this->dataSet;
    }

}
