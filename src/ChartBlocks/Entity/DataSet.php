<?php

namespace ChartBlocks\Entity;

use ChartBlocks\DataSet\Query;
use ChartBlocks\DataSet\Exception as DataSetException;
use ChartBlocks\DataSet\RowSetCursor;
use ChartBlocks\DataSet\RowSet;
use ChartBlocks\DataSet\Row;

class DataSet extends AbstractEntity {

    /**
     * 
     * @param mixed $query
     * @return \ChartBlocks\DataSet\RowSet
     * @throws DataSetException
     */
    public function select($query = array()) {
        if (is_array($query)) {
            $query = new Query($query);
        } elseif (!$query instanceof Query) {
            throw new DataSetException('Unknown item given to select');
        }
        $that = $this;

        $callback = function() use($query, $that) {
            $params = $query->toArray();
            $client = $that->getRepository()->getHttpClient();

            $rowsJson = $client->getJson('data/' . $that->getId(), $params);

            $rows = array();

            foreach ($rowsJson['data'] as $rowNum => $row) {
                $row = array(
                    'rowNumber' => $rowNum,
                    'cells' => array_key_exists('cells', $row) ? $row['cells'] : array()
                );
                $rows[$rowNum] = new Row($row);
            }
            return $rows;
        };


        return new RowSet($callback);
    }

    public function update($rows) {
        if (!is_array($rows)) {
            $rows = array($rows);
        }
        $data = array();

        foreach ($rows as $row) {
            if (!($rowNum = $row->getRowNumber())) {
                throw new Exception('Rows without a row number cannot be updated.');
            }

            $data[$rowNum] = $row->toArray(true);
        }

        if (count($data) > 0) {
            $json = $this->getRepository()->getHttpClient()->putJson('data/' . $this->getId(), array(
                'data' => $data
            ));

            return !!$json['success'];
        }
        return false;
    }

    public function append($rows) {
        if (!is_array($rows)) {
            $rows = array($rows);
        }
        $data = array();

        foreach ($rows as $row) {
            if ($row->getRowNumber()) {
                throw new Exception('Rows with a row number cannot be appeneded.');
            }

            $data[] = $row->toArray(true);
        }
        if (count($data) > 0) {
            $json = $this->getRepository()->getHttpClient()->putJson('data/append/' . $this->getId(), array(
                'data' => $data
            ));

            return !!$json['success'];
        }
        return false;
    }

    /**
     * 
     * @param type $rowId
     * @return \ChartBlocks\DataSet\Row
     */
    public function get($rowId) {

        $params = array(
            'fromRow' => $rowId,
            'toRow' => $rowId
        );

        if ($json = $this->getRepository()->getHttpClient()->getJson('data/' . $this->getId(), $params)) {

            $data = array(
                'rowNumber' => $rowId,
                'columnCount' => $json['meta']['columns'],
                'cells' => array_key_exists($rowId, $json['data']) && array_key_exists('cells', $json['data'][$rowId]) ? $json['data'][$rowId]['cells'] : array()
            );

            $row = new Row($data);
            return $row;
        }
        return false;
    }

    /**
     * 
     * @param \ChartBlocks\DataSet\Row|string $row
     */
    public function delete($row) {
        if ($row instanceof Row) {
            $id = $row->getRowNumber();
        } else {
            $id = $row;
        }

        $params = array(
            'method' => 'shiftRow',
            'index' => $id - 1,
            'amount' => -1
        );

        $setId = $this->getId();
        $json = $this->getRepository()->getClient()->put('data/alter/' . $setId, $params);

        return (isset($json['success']) && $json['success']);
    }

    /**
     * 
     * @return boolean
     */
    public function truncate() {
        $json = $this->getRepository()->getClient()->delete('data/' . $this->id);
        return (isset($json['success']) && $json['success']);
    }

    /**
     * 
     * @return array
     */
    public function getLatestVersionMeta() {
        foreach ($this->versions as $meta) {
            if ($meta['version'] == $this->latestVersionNumber) {
                return $meta;
            }
        }

        return false;
    }

    public function isImporting() {
        $latestVersionMeta = $this->getLatestVersionMeta();
        return $latestVersionMeta['isImporting'];
    }

}
