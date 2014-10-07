<?php

namespace ChartBlocks\Entity;

use ChartBlocks\User\Group\Igniter as GroupIgniter;
use ChartBlocks\User\Account;

class User extends AbstractEntity {

    /**
     * 
     * @param \ChartBlocks\User\Account $account
     * @return self
     */
    public function setAccount(Account $account) {
        $this->store('account', $account);
        return $this;
    }

    /**
     * 
     * @param array $groups
     * @return self
     */
    public function setGroups(array $groups) {
        $groups = GroupIgniter::igniteGroups($groups);
        $this->store('groups', $groups);
        return $this;
    }

    /**
     * 
     * @param boolean $active
     * @return self
     */
    public function setActive($active) {
        $this->store('active', (boolean) $active);
        return $this;
    }

}
