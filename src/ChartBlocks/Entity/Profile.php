<?php

namespace ChartBlocks\Entity;

use ChartBlocks\User\Account;

class Profile extends AbstractEntity {

    public function setAccount(Account $account) {
        $this->store('account', $account);
        return $this;
    }

}
