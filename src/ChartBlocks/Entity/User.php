<?php

namespace ChartBlocks\Entity;

use ChartBlocks\Repository\RepositoryInterface;
use ChartBlocks\User\Group\Igniter as GroupIgniter;
use ChartBlocks\User\Account;

class User extends AbstractEntity {

    /**
     *
     * @var array 
     */
    protected $groups;

    /**
     *
     * @var string 
     */
    protected $id;

    /**
     *
     * @var string 
     */
    protected $firstname;

    /**
     *
     * @var string 
     */
    protected $lastname;

    /**
     *
     * @var string 
     */
    protected $email;

    /**
     *
     * @var string 
     */
    protected $password;

    /**
     *
     * @var bool 
     */
    protected $active;

    /**
     *
     * @var \ChartBlocks\User\Account 
     */
    protected $account;

    function __construct(RepositoryInterface $repository, $data = array()) {
        parent::__construct($repository, $data);
    }

    public function setData($data) {
        if (array_key_exists('id', $data)) {
            $this->setId($data['id']);
        }
        if (array_key_exists('firstname', $data)) {
            $this->setFirstname($data['firstname']);
        }
        if (array_key_exists('lastname', $data)) {
            $this->setLastname($data['lastname']);
        }
        if (array_key_exists('password', $data)) {
            $this->setPassword($data['password']);
        }
        if (array_key_exists('email', $data)) {
            $this->setEmail($data['email']);
        }
        if (array_key_exists('active', $data)) {
            $this->setActive($data['active']);
        }

        if (array_key_exists('groups', $data)) {
            $groups = GroupIgniter::igniteGroups($data['groups']);
            $this->setGroups($groups);
        }

        if (array_key_exists('account', $data)) {
            $account = $this->getAccount();
            $account->setData($data['account']);
        }
    }

    /**
     * 
     * @param \ChartBlocks\User\Account $account
     * @return \ChartBlocks\Entity\User
     */
    public function setAccount(Account $account) {
        $this->account = $account;
        return $this;
    }

    /**
     * 
     * @param string $name
     * @return \ChartBlocks\User\Account|null
     */
    public function getAccount() {
        if ($this->account === null) {
            $this->account = new Account();
        }
        return $this->account;
    }

    /**
     * 
     * @param array $groups
     * @return \ChartBlocks\Entity\User
     */
    public function setGroups(array $groups) {
        $this->groups = $groups;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getGroups() {
        return $this->groups;
    }

    /**
     * 
     * @param string $name
     * @return \ChartBlocks\User\Group|null
     */
    public function getGroup($name) {
        if (array_key_exists($name, $this->groups)) {
            return $this->groups[$name];
        }
        return null;
    }

    /**
     * 
     * @param type $firstname
     * @return \ChartBlocks\Entity\User
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * 
     * @param type $lastname
     * @return \ChartBlocks\Entity\User
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * 
     * @param type $email
     * @return \ChartBlocks\Entity\User
     */
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * 
     * @param type $password
     * @return \ChartBlocks\Entity\User
     */
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    /**
     * 
     * @param type $password
     * @return \ChartBlocks\Entity\User
     */
    private function getPassword() {
        return $this->password;
    }

    /**
     * 
     * @param type $id
     * @return \ChartBlocks\Entity\User
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getId() {
        return $this->id;
    }

    /**
     * 
     * @param type $active
     * @return \ChartBlocks\Entity\User
     */
    public function setActive($active) {
        $this->active = $active;
        return $this;
    }

    /**
     * 
     * @return type
     */
    public function getActive() {
        return $this->active;
    }

    public function toArray() {
        $data = array();

        if ($account = $this->getAccount()) {
            $data['account'] = $account->toArray();
        }
        if ($firstname = $this->getFirstname()) {
            $data['firstname'] = $firstname;
        }
        if ($lastname = $this->getLastname()) {
            $data['lastname'] = $lastname;
        }
        if ($email = $this->getEmail()) {
            $data['email'] = $email;
        }
        if ($password = $this->getPassword()) {
            $data['password'] = $password;
        }
        return $data;
    }

}
