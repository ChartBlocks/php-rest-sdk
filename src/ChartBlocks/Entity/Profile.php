<?php

namespace ChartBlocks\Entity;

use ChartBlocks\Repository\RepositoryInterface;
use ChartBlocks\User\Group\Igniter as GroupIgniter;
use ChartBlocks\User\Account;

class Profile extends AbstractEntity {

    protected $id;
    protected $nickname;
    protected $avatarUrl;
    protected $account;

    function __construct(RepositoryInterface $repository, $data = array()) {
        parent::__construct($repository, $data);
    }

    public function setData($data) {
        if (array_key_exists('id', $data)) {
            $this->setId($data['id']);
        }
        if (array_key_exists('nickname', $data)) {
            $this->setNickname($data['nickname']);
        }
        if (array_key_exists('avatarUrl', $data)) {
            $this->setAvatarUrl($data['avatarUrl']);
        }
        if (array_key_exists('password', $data)) {
            $this->setPassword($data['password']);
        }

        if (array_key_exists('account', $data)) {
            $account = $this->getAccount();
            $account->setData($data['account']);
        }
        return parent::setData($data);
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getId() {
        return $this->id;
    }

    public function setNickname($nickname) {
        $this->nickname = $nickname;
    }

    public function getNickname() {
        return $this->nickname;
    }

    public function setAvatarUrl($url) {
        $this->avatarUrl = $url;
        return $this;
    }

    public function getAvatarUrl() {
        return $this->avatarUrl;
    }

    public function setAccount(Account $account) {
        $this->account = $account;
        return $this;
    }

    public function getAccount() {
        if ($this->account === null) {
            $this->account = new Account();
        }
        return $this->account;
    }

}
