<?php

namespace ChartBlocks\Entity;

class Profile extends AbstractEntity {

    public function setAccount($account) {
        $account = $this->getEntityFactory()->createInstanceOf('Account', $account);
        $this->store('account', $account);
        return $this;
    }

//    public function nickname() {
//        $nickname = $this->get('nickname');
//        return empty($nickname) ? 'anonymous' : $nickname;
//    }

}