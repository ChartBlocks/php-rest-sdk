<?php

namespace ChartBlocks\Repository;

class User extends AbstractWriteableRepository {

    public $url = 'user/';
    public $class = '\\ChartBlocks\\Entity\\User';
    public $singleResponseKey = 'user';
    public $listResponseKey = 'users';

}