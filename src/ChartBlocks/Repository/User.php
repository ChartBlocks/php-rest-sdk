<?php

namespace ChartBlocks\Repository;

class User extends AbstractRepository {

    protected $url = '/user';
    protected $class = '\\ChartBlocks\\Entity\\User';
    protected $singleResponseKey = 'user';
    protected $listResponseKey = 'users';

}
