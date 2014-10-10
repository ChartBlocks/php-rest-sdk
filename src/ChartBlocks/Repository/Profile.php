<?php

namespace ChartBlocks\Repository;

class Profile extends AbstractRepository {

    public $url = 'user/profile/';
    public $class = '\\ChartBlocks\\Entity\\Profile';
    public $singleResponseKey = 'profile';

}