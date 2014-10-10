<?php

namespace ChartBlocks\Entity;

class EntityId {

    static public function isValid($id) {
        if (is_string($id) === false) {
            return false;
        }

        if (strlen($id) !== 24) {
            return false;
        }

        if (preg_match('/[^a-zA-Z0-9]/', $id)) {
            return false;
        }

        return true;
    }

}
