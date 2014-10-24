<?php

namespace ChartBlocks\Repository;

class Account extends AbstractWriteableRepository {

    public $url = 'account/';
    public $class = '\\ChartBlocks\\Entity\\Account';
    public $singleResponseKey = 'account';
    public $listResponseKey = 'accounts';

}