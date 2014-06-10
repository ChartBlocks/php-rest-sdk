<?php

namespace ChartBlocks\Repository;

use ChartBlocks\Entity\User as UserEntity;

class User extends AbstractRepository {

    protected $url = '/user';
    protected $class = '\\ChartBlocks\\Entity\\User';
    protected $singleResponseKey = 'user';
    protected $listResponseKey = 'users';

    public function create(array $data = array()) {
        return $this->igniteClass($data);
    }

    public function save(UserEntity $user) {
        if ($id = $user->getId()) {
            Throw new Exception('User entity must NOT have an ID to be saved');
        }
        $userData = $user->toArray();

        $plan = $user->getAccount()->getPlan();
        unset($userData['account']);
        $userData['plan'] = $plan;

        $response = $this->getHttpClient()->postJson('/register/', $userData);

        $data = isset($response['session']['user']) ? $response['session']['user'] : array();

        $user->setData($data);
        return $user;
    }

    public function update(UserEntity $user) {
        if (!$id = $user->getId()) {
            Throw new Exception('User entity MUST have an ID to be updated');
        }

        $userData = $user->toArray();
        unset($userData['account']);

        $response = $this->getHttpClient()->putJson('/user/' . $id, $userData);
        var_dump($response);
        exit;
    }

    public function delete($user) {
        if ($user instanceof UserModel) {
            $id = $user->getId();
        } else {
            $id = $user;
        }
    }

}
