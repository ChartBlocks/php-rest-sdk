<?php

namespace ChartBlocks\DataSet\Query;

class QueryFactory {

    public function createService($parameters) {
        if (is_array($parameters)) {
            return new Query($parameters);
        }

        throw new \InvalidArgumentException('Invalid query parameters');
    }

}
