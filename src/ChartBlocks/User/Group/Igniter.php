<?php

namespace ChartBlocks\User\Group;

use ChartBlocks\User\Group;

class Igniter {

    /**
     * 
     * @param array $groupData
     * @return array
     */
    public static function igniteGroups(array $groupData = array()) {
        $groups = array();

        foreach ($groupData as $data) {
            $groups[] = self::igniteGroup($data);
        }
        return $groups;
    }

    /**
     * 
     * @param array $data
     * @return \ChartBlocks\User\Group
     */
    public static function igniteGroup(array $data = array()) {
        return new Group($data);
    }

}
