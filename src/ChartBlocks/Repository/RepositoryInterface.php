<?php

namespace ChartBlocks\Repository;

interface RepositoryInterface {

    public function find($query);
    
    public function findById($id);
}