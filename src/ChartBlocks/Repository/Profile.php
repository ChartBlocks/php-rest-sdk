<?php

namespace ChartBlocks\Repository;

class Profile extends AbstractRepository {

    protected $url = '/user/profile';
    protected $class = '\\ChartBlocks\\Entity\\Profile';
    protected $singleResponseKey = 'profile';

}