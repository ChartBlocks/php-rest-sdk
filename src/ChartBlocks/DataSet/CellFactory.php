<?php

namespace ChartBlocks\DataSet;

class CellFactory {

    public function createService($parameters) {
        if (is_scalar($parameters)) {
            $parameters = array(
                'v' => $parameters,
                't' => 's'
            );
        }

        if (is_array($parameters) && array_key_exists('v', $parameters) === false) {
            throw new \InvalidArgumentException('Cell array must contain a value key');
        }

        return new Cell($parameters);
    }

}
